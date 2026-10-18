<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService
{
    /**
     * Kirim riwayat percakapan ke Gemini dan ambil balasan teksnya.
     *
     * @param  Collection  $messages  koleksi ChatMessage (urut dari terlama)
     */
    public function chat(Collection $messages): string
    {
        $apiKey = config('gemini.api_key');
        if (! $apiKey) {
            throw new RuntimeException('GEMINI_API_KEY belum diisi di file .env');
        }

        $model = config('gemini.model');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $contents = $messages->map(fn ($m) => [
            'role' => $m->role, // "user" atau "model"
            'parts' => [['text' => $m->content]],
        ])->values()->all();

        $response = Http::withHeaders(['x-goog-api-key' => $apiKey])
            ->timeout(60)
            ->post($url, [
                'system_instruction' => [
                    'parts' => [['text' => config('gemini.system_prompt')]],
                ],
                'contents' => $contents,
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Gemini API error: ' . ($response->json('error.message') ?? $response->status())
            );
        }

        $text = $response->json('candidates.0.content.parts.0.text');
        if (! $text) {
            throw new RuntimeException('Gemini tidak mengembalikan jawaban (mungkin diblokir safety filter).');
        }

        return $text;
    }
}
