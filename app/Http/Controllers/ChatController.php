<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $messages = ChatMessage::where('session_id', session()->getId())
            ->orderBy('id')
            ->get();

        return view('chat', compact('messages'));
    }

    public function send(Request $request, GeminiService $gemini)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $sessionId = session()->getId();

        $userMessage = ChatMessage::create([
            'session_id' => $sessionId,
            'role' => 'user',
            'content' => $data['message'],
        ]);

        // Ambil 20 pesan terakhir sebagai konteks percakapan
        $history = ChatMessage::where('session_id', $sessionId)
            ->latest('id')
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        try {
            $reply = $gemini->chat($history);
        } catch (\Throwable $e) {
            report($e);
            $userMessage->delete(); // batalkan pesan agar riwayat tetap rapi

            return redirect()->route('chat.index')
                ->withInput()
                ->with('error', $e->getMessage());
        }

        ChatMessage::create([
            'session_id' => $sessionId,
            'role' => 'model',
            'content' => $reply,
        ]);

        return redirect()->route('chat.index');
    }

    public function clear()
    {
        ChatMessage::where('session_id', session()->getId())->delete();

        return redirect()->route('chat.index');
    }
}
