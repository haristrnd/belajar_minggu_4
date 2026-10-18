<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat', [ChatController::class, 'send'])->name('chat.send');
Route::delete('/chat', [ChatController::class, 'clear'])->name('chat.clear');
