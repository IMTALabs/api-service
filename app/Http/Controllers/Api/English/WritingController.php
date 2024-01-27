<?php

namespace App\Http\Controllers\Api\English;

use App\Http\Controllers\Controller;
use App\Http\Resources\English\HistoryUserEnglishResource;
use App\Http\Resources\English\WritingRequestResource;
use App\Models\HistoryUserEnglish;
use App\Models\ListenMark;
use App\Models\Writing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Essa\APIToolKit\Api\ApiResponse;

class WritingController extends Controller
{
    use ApiResponse;

    public function writing_gen_instruction(Request $request)
    {
        $user_id = 1;
        $topic = $request->input('topic');
        $validator = Validator::make($request->all(), [
            'topic' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages()
            ], 422);
        } else {
            try {
                $englishConfig = config('english.x_api_key');
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'x-api-key' => $englishConfig
                ])->timeout(60)->post('https://8300.imta-chatbot.online/gen_instruction', [
                    'id' => (string)$user_id,
                    'topic' => $topic,
                ]);

                // Lấy mã trạng thái của response
                $statusCode = $response->getStatusCode();

                // Decode nội dung JSON từ response
                $body = json_decode($response->getBody(), true);

                if ($statusCode == 200) {
                    $writing = new Writing();
                    $writing->user_id = $user_id;
                    $writing->topic = $topic;
                    $writing->hash = md5($user_id . 'writing' . Str::random(32)); //randome 10 ký tự và unique
                    $writing->response = $response->getBody();
                    $writing->save();

                    //Add history
                    $history = HistoryUserEnglish::create([
                        'user_id' => $user_id,
                        'skill' => Writing::class,
                        'request_id' => $writing->id,
                    ]);

                    // Hiển thị nội dung để kiểm tra
                    return $this->responseSuccess(null, [

                            'body' => $body,
                            'hash' => $writing->hash,
                            //'remaining_accounting_charge' => Auth::user()->getAccountingCharge(),
                            'history' => new HistoryUserEnglishResource($history),
                    ]);

                } else {
                    return $this->responseWithCustomError('dịch vụ hiện đang tạm dừng', $body, $statusCode);
                    //dd($body);
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
                Log::channel('server_error')->error('Lỗi server', $body);
                return response()->json([
                    'statusCode' => $statusCode,
                    'body' => $body,
                ], 500);
            }
        }
    }

    public function evalue(Request $request)
    {
        $user_id = 1;
        $instruction = $request->input('instruction');
        $submission = $request->input('submission');
        $hash = $request->input('hash');
        $validator = Validator::make($request->all(), [
            'instruction' => 'required',
            'submission' => 'required',
            'hash' => "required_without:instruction"
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages()

            ], 422);
        } else {
            $englishConfig = config('english');
            if ($hash != null) {
                $dataFind = Writing::where('hash', $hash)->first();
                if($dataFind){
                   $instruction = $dataFind['response'];
                }else{
                    return $this->responseNotFound(null,'không tìm thấy bài viết');
                }

            } else {
                $dataFind = new Writing();
                $dataFind->user_id = $user_id;
                $dataFind->hash = md5($user_id . 'writing' . Str::random(32)); //randome 10 ký tự và unique
                $dataFind->response = json_encode($instruction);
                $dataFind->save();
            }
            try {
                $englishConfig = config('english.x_api_key');
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'x-api-key' => $englishConfig
                ])->timeout(60)->post('http://34.133.167.254:8300/evaluate', [
                    'id' => (string)$user_id,
                    'submission' => $submission,
                    'instruction' => $instruction
                ]);

                // Lấy mã trạng thái của response
                $statusCode = $response->getStatusCode();

                // Decode nội dung JSON từ response
                $body = json_decode($response->getBody(), true);
                if ($statusCode == 200) {
                    $results = [
                        'instruction' => $instruction,
                        'submission' => $submission,
                        'evaluate' => $body
                    ];
                    $score = $body['band_score'];
                    $markListening = ListenMark::create([
                        'user_id' => $user_id,
                        'skill' => Writing::class,
                        'request_id' => $dataFind->id,
                        'mark' => json_encode($results, true),
                        'score' => $score,
                    ]);

                    // Hiển thị nội dung để kiểm tra
                    return response()->json([
                        'data' => $body,
                    ], 200);
                } else {
                    return $this->responseWithCustomError('dịch vụ hiện đang tạm dừng', $body, $statusCode);
                    //dd($body);
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
                Log::channel('server_error')->error('Lỗi server', $body);
                return response()->json([
                    'statusCode' => $statusCode,
                    'body' => $body,
                ], 500);
            }
        }
    }

    public function randomWriting()
    {
        $randomTopics = Writing::inRandomOrder()->take(5)->get(['id', 'topic']);

        if ($randomTopics->isEmpty()) {
            return $this->responseServerError(null, "No topics available.");
        } else {
            return $this->responseSuccess("Randomed successfully", ['topics' => $randomTopics]);
        }
    }

    public function writingTest(string $hash)
    {
        $writing = Writing::where('hash', $hash)->first();

        if (!$writing) {
            Log::channel('server_error')->error('Không tìm thấy bài viết', [
                'hash' => $hash,
            ]);
            return $this->responseNotFound(null, 'Không tìm thấy bài viết');
        }

        return $this->responseSuccess(null, new WritingRequestResource($writing));
    }
}
