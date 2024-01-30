<?php

namespace App\Services\English;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class APIService
{
    public static function getClient($headers = []): PendingRequest
    {
        $apiKey = config('english.x_api_key');

        return Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-api-key' => $apiKey,
            ...$headers,
        ]);
    }

    public static function genListeningQuizz(string $youtubeUrl, array $body)
    {
        return Cache::remember(md5($youtubeUrl), 60 * 60 * 24, function () use ($body) {
            $response = APIService::getClient()
                ->timeout(60)
                ->post(config('english.listening.gen_quizz_endpoint') . '/gen_quizz', $body);

            if ($response->ok()) {
                return ['body' => $response->json()];
            } else {
                return ['error' => $response->json()];
            }
        });
    }

    public static function getRandomYoutubeVideos(int $number): array
    {
        $response = APIService::getClient()
            ->timeout(60)
            ->get(config('english.listening.random_endpoint') . '/random', [
                'num' => $number,
            ]);

        if ($response->ok()) {
            return ['body' => $response->json()];
        } else {
            return ['error' => $response->json()];
        }
    }

    public static function genReadingArticle(mixed $topic): array
    {
        return Cache::remember(md5($topic), 60 * 60 * 24, function () use ($topic) {
            $response = APIService::getClient()
                ->timeout(60)
                ->post(config('english.reading.gen_topic_endpoint') . '/gen_article', [
                    'id' => (string)Auth::id(),
                    'topic' => $topic,
                ]);

            if ($response->ok()) {
                return ['body' => $response->json()];
            } else {
                return ['error' => $response->json()];
            }
        });
    }

    public static function genReadingQuizz($topic, $paragraph, $numQuizz = 10)
    {
        return Cache::remember(md5($topic . $paragraph), 60 * 60 * 24, function () use ($topic, $paragraph, $numQuizz) {
            $response = APIService::getClient()
                ->timeout(60)
                ->post(config('english.reading.gen_quizz_endpoint') . '/gen_quizz', [
                    'id' => (string)Auth::id(),
                    'mode' => 'no_gen_topic',
                    'topic' => $topic,
                    'paragraph' => $paragraph,
                    'num_quizz' => $numQuizz,
                ]);

            if ($response->ok()) {
                return ['body' => $response->json()];
            } else {
                return ['error' => $response->json()];
            }
        });
    }
}
