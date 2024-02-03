<?php

namespace App\Http\Controllers\Api\English;

use App\Enums\English\RequestType;
use App\Enums\English\Skill;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\English\GenerateListeningRequest;
use App\Http\Requests\Api\English\GradingListeningRequest;
use App\Models\EnglishRequest;
use App\Services\English\APIService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ListeningController extends Controller
{
    use APIResponse;

    public function listening(GenerateListeningRequest $request)
    {
        set_time_limit(120);

        $youtubeUrl = $request->get('listen_link');
        $numQuizz = $request->get('num_quizz', 10);

        try {
            $body = [
                'id' => (string)Auth::id(),
                'url' => $youtubeUrl,
                'num_quizz' => $numQuizz,
            ];
            $hash = md5(json_encode($body) . Str::random(32));

            $listening = EnglishRequest::create([
                'type' => RequestType::GENERATING,
                'skill' => Skill::LISTENING,
                'user_id' => Auth::id(),
                'hash' => $hash,
                'extra_data' => [
                    'youtube_url' => $youtubeUrl,
                    'num_quizz' => $numQuizz,
                ],
            ]);

            $data = APIService::genListeningQuizz($youtubeUrl, $body);

            if (Arr::has($data, 'body')) {
                $response = $data['body'];
                $listening->update([
                    'response' => $response,
                    'completed_at' => now(),
                ]);

                Auth::user()->withdraw(1);

                return $this->responseSuccess(null, [
                    'body' => $response,
                    'link' => $youtubeUrl,
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

    public function grading(GradingListeningRequest $request)
    {
        $hash = $request->get('hash');
        $submit = $request->get('submit');

        try {
            $listeningRequest = EnglishRequest::where('hash', $hash)->first();
            $form = $listeningRequest->response['form'];

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
                'skill' => Skill::LISTENING,
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

    public function listeningTest(string $hash)
    {
        $listeningRequest = EnglishRequest::where('type', RequestType::GENERATING)
            ->where('skill', Skill::LISTENING)
            ->where('hash', $hash)
            ->first();

        if (!$listeningRequest) {
            return $this->responseNotFound(null, __('Listening test not found'));
        }

        return $this->responseSuccess(null, [
            'youtube_link' => $listeningRequest->extra_data['youtube_url'],
            'body' => $listeningRequest->response,
            'hash' => $hash,
        ]);
    }

    public function randomYoutubeVideos(Request $request)
    {
        $number = (int)$request->query('number', 10);

        try {
            $data = APIService::getRandomYoutubeVideos($number);

            if (Arr::has($data, 'body')) {
                return $this->responseSuccess(null, $data['body']);
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
}
