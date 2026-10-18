<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('gemini.bot_name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f3f4f6; margin: 0; color: #111827; }
        .wrap { max-width: 720px; margin: 0 auto; padding: 24px 16px; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        h1 { font-size: 20px; margin: 0; }
        .chat { background: #fff; border-radius: 12px; padding: 16px; min-height: 300px; display: flex; flex-direction: column; gap: 12px; }
        .msg { max-width: 80%; padding: 10px 14px; border-radius: 12px; line-height: 1.5; white-space: pre-wrap; word-wrap: break-word; }
        .user { align-self: flex-end; background: #2563eb; color: #fff; }
        .model { align-self: flex-start; background: #e5e7eb; }
        .empty { color: #6b7280; text-align: center; margin: auto; }
        form.send { display: flex; gap: 8px; margin-top: 16px; }
        input[type=text] { flex: 1; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 15px; }
        button { padding: 12px 18px; border: 0; border-radius: 8px; background: #2563eb; color: #fff; font-size: 15px; cursor: pointer; }
        button:disabled { opacity: .6; cursor: wait; }
        button.clear { background: #ef4444; padding: 8px 12px; font-size: 13px; }
        .error { background: #fee2e2; color: #991b1b; padding: 10px 14px; border-radius: 8px; margin-top: 12px; }
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <h1>{{ config('gemini.bot_name') }}</h1>
        @if($messages->isNotEmpty())
            <form method="POST" action="{{ route('chat.clear') }}">
                @csrf
                @method('DELETE')
                <button class="clear" type="submit">Hapus riwayat</button>
            </form>
        @endif
    </header>

    <div class="chat">
        @forelse($messages as $m)
            <div class="msg {{ $m->role }}">{{ $m->content }}</div>
        @empty
            <p class="empty">Belum ada percakapan. Ketik pesan di bawah untuk mulai.</p>
        @endforelse
    </div>

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif
    @error('message')
        <div class="error">{{ $message }}</div>
    @enderror

    <form class="send" method="POST" action="{{ route('chat.send') }}"
          onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').textContent = 'Menunggu...';">
        @csrf
        <input type="text" name="message" value="{{ old('message') }}" placeholder="Tulis pesan untuk {{ config('gemini.bot_name') }}..." maxlength="2000" required autofocus>
        <button type="submit">Kirim</button>
    </form>
</div>
</body>
</html>
