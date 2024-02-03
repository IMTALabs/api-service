<?php

namespace App\Http\Controllers\Api\English;

use App\Enums\English\RequestType;
use App\Enums\English\Skill;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\English\GenReadingTopicRequest;
use App\Http\Requests\Api\English\GradingReadingRequest;
use App\Http\Requests\Api\English\ReadingGenerateRequest;
use App\Http\Resources\English\ReadingRequestResource;
use App\Models\EnglishRequest;
use App\Services\English\APIService;
use App\Traits\APIResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

                Auth::user()->withdraw(1);

                return $this->responseSuccess(null, [
                    $article,
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

                Auth::user()->withdraw(1);

                return $this->responseSuccess(null, [
                    'body' => [
                        'id' => $body['id'],
                        'form' => $body['form'],
                        'paragraph' => $paragraph,
                    ],
                    'hash' => $hash,
                    'remaining_accounting_charge' => Auth::user()->balance,
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

    public function grading(GradingReadingRequest $request)
    {
        $hash = $request->input('hash');
        $submit = $request->input('submit');

        try {
            $dataFind = EnglishRequest::where('hash', $hash)->first();
            $form = $dataFind->response['form'];

            $results = collect($form)->map(function ($ques, $key) use ($submit) {
                return [
                    'question' => $ques['question'],
                    'explanation' => $ques['explanation'],
                    'answer' => $ques['answer'],
                    'user_answer' => $submit[$key],
                    'is_correct' => $submit[$key] == $ques['answer'],
                ];
            });

            $point = $results->pluck('is_correct')->filter(fn($is_correct) => $is_correct)->count();

            EnglishRequest::create([
                'type' => RequestType::GRADING,
                'skill' => Skill::READING,
                'user_id' => Auth::id(),
                'hash' => md5(time() . Str::random(32)),
                'response' => ['results' => $results, 'point' => $point],
                'extra_data' => ['request_hash' => $hash, 'user_submission' => $submit],
                'completed_at' => now(),
            ]);

            return $this->responseSuccess(null, compact('results', 'point'));
        } catch (\Throwable $e) {
            Log::channel('server_error')->error($e->getMessage(), $e->getTrace());
            return $this->responseServerError();
        }
    }

    public function readingTest(string $hash)
    {
        $reading = EnglishRequest::where('type', RequestType::GENERATING)
            ->where('skill', Skill::READING)
            ->where('hash', $hash)
            ->first();

        if (!$reading) {
            Log::channel('server_error')->error(__('Reading test not found'), [
                'hash' => $hash,
            ]);
            return $this->responseNotFound(null, __('Reading test not found'));
        }
        return $this->responseSuccess(null, $reading);
    }

    public function randomReading()
    {
        try {
            $sampleFile = Storage::get('sample/reading.json');
            $sample = json_decode($sampleFile, true);
            return $this->responseSuccess(null, collect($sample)->random(5));
        } catch (\Throwable $e) {
            Log::channel('server_error')->error($e->getMessage(), $e->getTrace());
            return $this->responseServerError();
        }
    }
}
