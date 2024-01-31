<?php

namespace App\Http\Controllers\Api\English;

use App\Enums\English\RequestType;
use App\Enums\English\Skill;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\English\WritingGenInstructionRequest;
use App\Http\Resources\English\HistoryUserEnglishResource;
use App\Http\Resources\English\WritingRequestResource;
use App\Models\EnglishRequest;
use App\Models\HistoryUserEnglish;
use App\Models\ListenMark;
use App\Models\Writing;
use App\Services\English\APIService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Traits\APIResponse;

class WritingController extends Controller
{
    use APIResponse;

    public function genInstruction(WritingGenInstructionRequest $request)
    {
        $topic = $request->get('topic');
        try {
            $hash = md5($topic . Str::random(32));
            $writing = EnglishRequest::create([
                'type' => RequestType::GENERATING,
                'skill' => Skill::WRITING,
                'user_id' => Auth::id(),
                'hash' => $hash,
                'extra_data' => [
                    'topic' => $topic
                ],
            ]);
            $data = APIService::genInstruction($topic);

            if (Arr::has($data, 'body')) {
                $body = $data['body'];

                $writing->update([
                    'response' => $body,
                    'completed_at' => now(),
                ]);
                return $this->responseSuccess(null, [
                    'body' => $body,
                    'hash' => $writing->hash,
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

    public function evaluate(Request $request)
    {
        $instruction = $request->input('instruction');
        $submission = $request->input('submission');
        $hash = $request->input('hash');

        if ($hash != null) {
            $dataFind = Writing::where('hash', $hash)->first();
            if ($dataFind) {
                $instruction = $dataFind['response'];
            } else {
                return $this->responseNotFound(null, 'không tìm thấy bài viết');
            }

        } else {
            $dataFind = EnglishRequest::create([
                'type' => RequestType::GENERATING,
                'skill' => Skill::WRITING,
                'user_id' => Auth::id(),
                'hash' => md5(time() . Str::random(32)),
                'extra_data' => ['instruction' => $instruction],
                'completed_at' => now(),
            ]);
        }
        try {
            $hashWriting = md5(time() . Str::random(32));
            $writing = EnglishRequest::create([
                'type' => RequestType::GRADING,
                'skill' => Skill::WRITING,
                'user_id' => Auth::id(),
                'hash' => $hashWriting,
                'extra_data' => [
                    'hash' => $hash,
                    'instruction' => $instruction,
                    'submission' => $submission
                ],
            ]);
            $data = APIService::evaluate($instruction, $submission);

            if (Arr::has($data, 'body')) {
                $body = $data['body'];

                $writing->update([
                    'response' => $body,
                    'completed_at' => now(),
                ]);
                return $this->responseSuccess(null,$body);

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
