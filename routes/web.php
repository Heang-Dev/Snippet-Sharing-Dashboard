<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SnippetController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Guest routes (not authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Snippet routes
    Route::resource('snippets', SnippetController::class);
    Route::post('/snippets/{snippet}/favorite', [SnippetController::class, 'toggleFavorite'])->name('snippets.favorite');
    Route::post('/snippets/{snippet}/fork', [SnippetController::class, 'fork'])->name('snippets.fork');

    // Team routes
    Route::resource('teams', TeamController::class);
    Route::post('/teams/{team}/invite', [TeamController::class, 'invite'])->name('teams.invite');
    Route::post('/teams/{team}/leave', [TeamController::class, 'leave'])->name('teams.leave');
    Route::delete('/teams/{team}/members/{member}', [TeamController::class, 'removeMember'])->name('teams.members.remove');
    Route::patch('/teams/{team}/members/{member}/role', [TeamController::class, 'updateMemberRole'])->name('teams.members.role');

    // Team invitation routes
    Route::post('/invitations/{invitation}/accept', [TeamController::class, 'acceptInvitation'])->name('invitations.accept');
    Route::post('/invitations/{invitation}/decline', [TeamController::class, 'declineInvitation'])->name('invitations.decline');

    // Profile routes will be added here
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Public snippet viewing (anyone can view public snippets)
// Route::get('/s/{snippet:slug}', [SnippetController::class, 'show'])->name('snippets.public.show');
// Route::get('/@{username}', [ProfileController::class, 'show'])->name('profile.show');
