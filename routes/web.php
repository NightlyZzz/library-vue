<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('auth');
    }

    $notes = Auth::user()
        ->notes()
        ->with('book')
        ->orderBy('created_at', 'desc')
        ->get();

    return Inertia::render('Home', [
        'user'  => Auth::user(),
        'notes' => $notes,
    ]);
})->name('home');

Route::middleware('auth')->group(function(){
    Route::post('/notes', [NoteController::class, 'store']);
    Route::put('/notes/{note}', [NoteController::class, 'update']);
});

Route::get('/auth', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }

    return Inertia::render('Auth', [
        'user' => null,
    ]);
})->name('auth');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::fallback(function () {
    return redirect()->route('home');
});
