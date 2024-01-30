<?php

namespace App\Http\Controllers\Api\English;

use App\Enums\English\RequestType;
use App\Enums\English\Skill;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\English\GenReadingTopicRequest;
use App\Http\Requests\Api\English\ReadingGenerateRequest;
use App\Http\Resources\English\ReadingRequestResource;
use App\Models\EnglishRequest;
use App\Models\ListenMark;
use App\Models\Reading;
use App\Services\English\APIService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;

class ReadingController extends Controller
{
    use APIResponse;

    public function article(GenReadingTopicRequest $request)
    {
        $topic = $request->get('topic');

        try {
            $data = APIService::genReadingArticle($topic);

            if (Arr::has($data, 'body')) {
                $article = $data['body'];

                $converter = new CommonMarkConverter([
                    'renderer' => [
                        'block_separator' => "\n",
                        'inner_separator' => "\n",
                        'soft_break' => "\n",
                    ],
                    'commonmark' => [
                        'enable_em' => true,
                        'enable_strong' => true,
                        'use_asterisk' => true,
                        'use_underscore' => true,
                        'unordered_list_markers' => ['-', '*', '+'],
                    ],
                    'html_input' => 'escape',
                    'allow_unsafe_links' => false,
                    'max_nesting_level' => PHP_INT_MAX,
                    'slug_normalizer' => [
                        'max_length' => 255,
                    ],
                ]);

                $article = trim($article, '"');
                $article = Str::replace('\n', "\n", $article);
                $convertedArticle = $converter->convert($article);
                $article = Str::replace("\n", '<br>', $convertedArticle->getContent());
                return $this->responseSuccess(null, [
                    'article' => $article,
                ]);
            } else {
                $error = $data['error'];
                Log::channel('english_api_server_error')->error('Lỗi API server', $error);
                return $this->responseServerError();
            }
        } catch (\Throwable $e) {
            Log::channel('server_error')->error($e->getMessage(), $e->getTrace());
            return $this->responseServerError();
        }
    }

    public function reading(ReadingGenerateRequest $request)
    {
        $mode = $request->get('mode');
        $topic = $request->get('topic', '');
        $paragraph = $request->get('paragraph', '');

        try {
            $hash = md5($mode . $topic . $paragraph . Str::random(32));
            $reading = EnglishRequest::create([
                'type' => RequestType::GENERATING,
                'skill' => Skill::READING,
                'user_id' => Auth::id(),
                'hash' => $hash,
                'extra_data' => [
                    'topic' => $topic,
                    'paragraph' => $paragraph,
                ],
            ]);

            $data = APIService::genReadingQuizz($topic, $paragraph);

            if (Arr::has($data, 'body')) {
                $body = $data['body'];

                $reading->update([
                    'response' => $body,
                    'completed_at' => now(),
                ]);

                return $this->responseSuccess(null, [
                    'body' => [
                        'id' => $body['id'],
                        'form' => $body['form'],
                        'paragraph' => $paragraph,
                    ],
                    'hash' => $hash,
                    'remaining_accounting_charge' => 0,
                    'history' => [],
                ]);
            } else {
                $error = $data['error'];
                Log::channel('english_api_server_error')->error('Lỗi API server', $error);
                return $this->responseServerError();
            }
        } catch (\Throwable $e) {
            Log::channel('server_error')->error($e->getMessage(), $e->getTrace());
            return $this->responseServerError();
        }
    }

    public function submitReading(Request $request)
    {
        $hash = $request->input('hash');
        $submit = $request->input('submit');
        $user_id = 1;
        $validator = Validator::make(\request()->all(), [
            'hash' => 'required',
            'submit' => 'required|array',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages(),

            ], 422);
        } else {
            try {
                $dataFind = Reading::where('hash', $hash)->first();
                $dataResponse = json_decode($dataFind['response']);
                $results = collect($dataResponse->form)->map(function ($ques, $key) use ($submit) {
                    return [
                        'question' => $ques->question,
                        'explanation' => $ques->explanation,
                        'answer' => $ques->answer,
                        'user_answer' => $submit[$key],
                        'is_correct' => $submit[$key] == $ques->answer,
                    ];
                });

                $point = $results->pluck('is_correct')->filter(fn($is_correct) => $is_correct)->count();
                $markListening = ListenMark::create([
                    'user_id' => $user_id,
                    'skill' => Reading::class,
                    'request_id' => $dataFind->id,
                    'mark' => $results,
                    'score' => $point,
                ]);

                return $this->responseSuccess(null, compact('results', 'point'));
            } catch (\Exception $e) {
                if ($e instanceof \Illuminate\Http\Client\RequestException && $e->response) {
                    // If there's a response, decode its JSON content
                    $body = $e->response->json();
                    $statusCode = $e->response->status();
                } else {
                    // If there's no response, create a generic error message
                    $body = ['error' => $e->getMessage()];
                    $statusCode = $e->getCode() ?: 500; // Default to 500 if no code is available
                }
                Log::channel('server_error')->error('Lỗi Server', $body);
                return response()->json([
                    'statusCode' => $statusCode,
                    'body' => $body,
                ], 500);
            }
        }
    }

    public function readingTest(string $hash)
    {
        $reading = Reading::where('hash', $hash)->first();

        if (!$reading) {
            Log::channel('server_error')->error('Không tìm thấy bài đọc', [
                'hash' => $hash,
            ]);
            return $this->responseNotFound(null, 'Không tìm thấy bài đọc');
        }
        return $this->responseSuccess(null, new ReadingRequestResource($reading));
    }

}
