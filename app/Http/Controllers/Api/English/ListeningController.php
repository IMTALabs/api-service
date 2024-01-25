<?php

namespace App\Http\Controllers\Api\English;
use App\Http\Controllers\Controller;
use App\Http\Resources\English\HistoryUserEnglishResource;
use App\Http\Resources\English\ListeningRequestResource;
use App\Models\Accounting;
use App\Models\HistoryUserEnglish;
use App\Models\Listening;
use App\Models\ListenMark;
use essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DateInterval;
class ListeningController extends Controller
{
use ApiResponse;
    public function listening()
    {
        set_time_limit(60);
        $listeningLink = request()->input('listen_link');
        $user_id = 1;
        $validator = Validator::make(\request()->all(), [
            'listen_link' => 'required|url',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'message' => \Arr::get(collect($validator->messages()->messages())->flatten(), 0, 'Unknown error'),
                ],
            ], 422);
        } else {
            try {
                // $englishConfig = config('english.x_api_key');
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'x-api-key' => 'ezcS3JyK7NKCV0DkKwG4hqjy65TGnJ64nBB72qnSkNrxaJ3XAf'
                ])->timeout(60)->post('https://8150.imta-chatbot.online/gen_quizz', [
                    'id' => (string)$user_id,
                    'url' => $listeningLink,
                    'num_quizz' => 10,
                ]);

                // Lấy mã trạng thái của response
                $statusCode = $response->status();

                // Decode nội dung JSON từ response
                $body = json_decode($response->body(), true);
                if ($statusCode == 200) {
                    $listening = new Listening([
                        'user_id' => $user_id,
                        'youtube_url' => $listeningLink,
                        'hash' => md5($user_id . 'listening' . Str::random(32)), //randome 10 ký tự và unique
                        'response' => $response->getBody(),
                    ]);
                    $listening->save();
                    //Add history
                    $history = HistoryUserEnglish::create([
                        'user_id' => $user_id,
                        'skill' => Listening::class,
                        'request_id' => $listening->id,
                    ]);

                    return response()->json([
                        'data' => [
                            'body' => $body,
                            'link' => $listeningLink,
                            'hash' => $listening->hash,
                            'remaining_accounting_charge' => 100,
                            'history' =>[
                                'id' => $history->id,
                                'skill' => $history->skill::DISPLAY_NAME,
                                'created_at' => $history->created_at->locale('en_US')->diffForHumans(),
                            ],
                        ],
                    ], 200);
                } else {
                    Log::channel('server_error')->error('Lỗi Server', [$response->body()]);
                    return $this->responseWithCustomError('dịch vụ hiện đang tạm dừng', $body, $statusCode);
                }
            } catch (\Exception $e) {
                if ($e instanceof \Illuminate\Http\Client\RequestException && $e->response) {

                    $body = $e->response->json();
                    $statusCode = $e->response->status();
                } else {
                    $body = ['error' => $e->getMessage()];
                    $statusCode = $e->getCode() ?: 500;
                }
                Log::channel('server_error')->error('Lỗi Server', $body);
                return response()->json([
                    'statusCode' => $statusCode,
                    'body' => $body,
                ], 500);
            }
        }
    }

    public function submitListening(Request $request)
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
                'message' => \Arr::get(collect($validator->messages()->messages())->flatten(), 0, 'Unknown error'),

            ], 422);
        } else {
            try {
                $dataFind = Listening::where('hash', $hash)->first();
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

                $point = $results->pluck('is_correct')->filter(fn ($is_correct) => $is_correct)->count();
                $markListening = ListenMark::create([
                    'user_id' => $user_id,
                    'skill' => Listening::class,
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

    public function listeningTest(string $hash): \Illuminate\Http\JsonResponse
    {
        $listening = Listening::where('hash', $hash)->first();

        if (!$listening) {
            Log::channel('server_error')->error('Không tìm thấy bài nghe', [
                'hash' => $hash,
            ]);
            return $this->responseNotFound(null, 'Không tìm thấy bài nghe');
        }

               return $this->responseSuccess(null,  [
                'youtube_url' => $listening->youtube_url,
                'hash' => $listening->hash,
                'response' => json_decode($listening->response, true),
            ]);
    }
    // public function getlistening(string $query, int $maxResults)


    public function getlistening(Request $request)
{
    $query = $request->query('query');
    $maxResults = $request->query('maxResults');
    $apiKey = "AIzaSyBpGs-3YVOo5I1vtbxm0qcpq9b6RdaLsic";
    $type = "video";
    $part = "snippet";
    $maxResults = max(1, min(50, $maxResults));
    $url = "https://www.googleapis.com/youtube/v3/search?key={$apiKey}&q={$query}&part={$part}&type={$type}&maxResults={$maxResults}";

    try {
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            $videos = [];

            foreach ($data['items'] as $item) {
                $videoId = $item['id']['videoId'];
                $videoDetailsUrl = "https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id={$videoId}&key={$apiKey}";
                $videoDetailsResponse = Http::get($videoDetailsUrl);
                $videoDetails = $videoDetailsResponse->json()['items'][0];

                $duration = $this->parseYouTubeDuration($videoDetails['contentDetails']['duration']);

                $video = [
                    'videoLink' => "https://www.youtube.com/watch?v={$videoId}",
                    'title' => $item['snippet']['title'],
                    'description' => $item['snippet']['description'],
                    'thumbnails' => $item['snippet']['thumbnails'],
                    'duration' => sprintf('%02d:%02d:%02d', $duration['hours'], $duration['minutes'], $duration['seconds']),
                ];

                $videos[] = $video;
            }
            return $this->responseSuccess('Get data success', response()->json($videos));
        }
    } catch (\Exception $e) {
        return $this->responseServerError(null, 'Server error: ' . $e->getMessage());
    }
}

  // public function convert duration for api listening
    private function parseYouTubeDuration($duration)
    {
        $interval = new DateInterval($duration);
        $hours = $interval->h;
        $minutes = $interval->i;
        $seconds = $interval->s;
        return [
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
        ];
    }
}
