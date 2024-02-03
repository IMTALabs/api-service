<?php

namespace App\Http\Controllers\Api\English;

use App\Enums\English\RequestType;
use App\Enums\English\Skill;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\English\WritingGenInstructionRequest;
use App\Http\Resources\English\WritingRequestResource;
use App\Models\EnglishRequest;
use App\Models\Writing;
use App\Services\English\APIService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                    'topic' => $topic,
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
                    'submission' => $submission,
                ],
            ]);
            $data = APIService::evaluate($instruction, $submission);

            if (Arr::has($data, 'body')) {
                $body = $data['body'];

                $writing->update([
                    'response' => $body,
                    'completed_at' => now(),
                ]);

                Auth::user()->withdraw(8);

                return $this->responseSuccess(null, $body);
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
        try {
            $sampleFile = Storage::get('sample/writing.json');
            $sample = json_decode($sampleFile, true);
            return $this->responseSuccess(null, collect($sample)->random(5));
        } catch (\Throwable $e) {
            Log::channel('server_error')->error($e->getMessage(), $e->getTrace());
            return $this->responseServerError();
        }
    }

    public function writingTest(string $hash)
    {
        $writing = EnglishRequest::where('type', RequestType::GENERATING)
            ->where('skill', Skill::WRITING)
            ->where('hash', $hash)
            ->first();

        if (!$writing) {
            Log::channel('server_error')->error(__('Writing test not found'), [
                'hash' => $hash,
            ]);
            return $this->responseNotFound(null, __('Writing test not found'));
        }

        return $this->responseSuccess(null, $writing);
    }
}
