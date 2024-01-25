<?php

namespace App\Http\Controllers\Api\English;

use App\Http\Controllers\Controller;
use App\Http\Resources\English\HistoryUserEnglishResource;
use App\Http\Resources\English\ReadingRequestResource;
use App\Models\Accounting;
use App\Models\HistoryUserEnglish;
use App\Models\ListenMark;
use App\Models\Reading;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;


class ReadingController extends Controller
{
    use ApiResponse;
    public function reading(Request $request)
    {
        $user_id = 1;
        $mode = $request->input('mode') ?? '';
        $topic = $request->input('topic') ?? '';
        $paragraph = $request->input('paragraph') ?? '';

        $validator = Validator::make($request->all(), [
            'mode' => 'required|in:gen_topic,no_gen_topic',
            'topic' => 'required_without:paragraph',
            'paragraph' => 'required_without:topic',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'message' => \Arr::get(collect($validator->messages()->messages())->flatten(), 0, 'Unknown error'),
                ],
            ], 422);
        } else {
            // $englishConfig = config('english.x_api_key');
            if ($mode == 'gen_topic') {
                try {
                    $response1 = Http::withHeaders([
                        'Accept' => '*/*',
                        'Content-Type' => 'application/json',
                        'x-api-key' => 'ezcS3JyK7NKCV0DkKwG4hqjy65TGnJ64nBB72qnSkNrxaJ3XAf'
                    ])->timeout(120)->post('https://8200.imta-chatbot.online/gen_article', [
                                'id' => (string) $user_id,
                                'topic' => $topic,
                            ]);
                    $statusCode1 = $response1->getStatusCode();
                    $body1 = json_decode($response1->getBody(), true);
                    if ($statusCode1 == 200) {
                        $paragraph = $response1->body();
                    } else {
                        Log::channel('server_error')->error('Lỗi Server', [$response1->body()]);
                        return $this->responseWithCustomError('dịch vụ hiện đang tạm dừng', $body1, $statusCode1);
                    }
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
                //                dd($paragraph);

            }
            try {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'x-api-key' => 'ezcS3JyK7NKCV0DkKwG4hqjy65TGnJ64nBB72qnSkNrxaJ3XAf'
                ])->timeout(120)->post('https://8200.imta-chatbot.online/gen_quizz', [
                            'id' => (string) $user_id,
                            'mode' => 'no_gen_topic',
                            'topic' => $topic,
                            'paragraph' => $paragraph,
                            'num_quizz' => 10
                        ]);


                // Lấy mã trạng thái của response
                $statusCode = $response->getStatusCode();
                if ($topic == '') {
                    $is_topic = false;
                } else {
                    $is_topic = true;
                }
                //                $is_topic=$topic!=''
                // Decode nội dung JSON từ response
                $body = json_decode($response->getBody(), true);

                if ($statusCode == 200) {
                    //Add request Reading
                    $reading = new Reading();
                    $reading->user_id = $user_id;
                    $reading->topic = $topic;
                    $reading->paragraph = $paragraph;
                    $reading->is_topic = $is_topic;
                    $reading->hash = md5($user_id . 'reading' . Str::random(32)); //randome 10 ký tự và unique
                    $reading->response = $response->body();
                    $reading->save();

                    //Add history
                    $history = HistoryUserEnglish::create([
                        'user_id' => $user_id,
                        'skill' => Reading::class,
                        'request_id' => $reading->id,
                    ]);

                    $converter = new CommonMarkConverter([
                        'html_input' => 'strip',
                        'allow_unsafe_links' => false,
                    ]);
                    $paragraph = trim($paragraph, '"');
                    $paragraph = Str::replace('\n', "\n", $paragraph);
                    $convertedParagraph = $converter->convert($paragraph);
                    dd(2);
                    // Hiển thị nội dung để kiểm tra
                    return response()->json([
                        'data' => [
                            'body' => [
                                'id' => $body['id'],
                                'form' => $body['form'],
                                'paragraph' => $convertedParagraph->getContent()
                            ],
                            'hash' => $reading->hash,
                            // 'remaining_accounting_charge' => Auth::user()->getAccountingCharge(),
                            'history' => new HistoryUserEnglishResource($history),
                        ],
                    ]);
                } else {
                    return $this->responseWithCustomError('dịch vụ hiện đang tạm dừng', $body, $statusCode);
                }
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
    public function genTopic(Request $request)
    {
        $topic = $request->input('topic') ?? '';
        $user_id = 1;
        // $englishConfig = config('english.x_api_key');
        $validator = Validator::make($request->all(), [
            'topic' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'message' => \Arr::get(collect($validator->messages()->messages())->flatten(), 0, 'Unknown error'),
                ],
            ], 422);
        } else {
            try {
                $response = Http::withHeaders([
                    'Accept' => '*/*',
                    'Content-Type' => 'application/json',
                    'x-api-key' => 'ezcS3JyK7NKCV0DkKwG4hqjy65TGnJ64nBB72qnSkNrxaJ3XAf'
                ])->timeout(120)->post('https://8200.imta-chatbot.online/gen_article', [
                            'id' => (string) $user_id,
                            'topic' => $topic,
                        ]);
                $statusCode = $response->getStatusCode();
                $body = json_decode($response->getBody(), true);
                if ($statusCode == 200) {
                    $paragraph = $response->body();
                    $converter = new CommonMarkConverter([
                        'html_input' => 'strip',
                        'allow_unsafe_links' => false,
                    ]);
                    $paragraph = trim($paragraph, '"');
                    $paragraph = Str::replace('\n', "\n", $paragraph);
                    $convertedParagraph = $converter->convert($paragraph);
                    return $this->responseSuccess(null, [
                        $convertedParagraph->getContent()
                    ]);
                } else {
                    return $this->responseWithCustomError('dịch vụ hiện đang tạm dừng', $body, $statusCode);
                }
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

    public function submitReading(Request $request)
    {
        $hash = $request->input('hash');
        $submit = $request->input('submit');
        $user_id = 1;
        $validator = Validator::make(\request()->all(), [
            'hash' => 'required',
            'submit' => 'required|array'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages()

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
