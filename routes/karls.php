<?php

use App\Http\Controllers\KarlsController;
use App\Http\Controllers\PrivateKarlController;
use Illuminate\Support\Facades\Route;

// ================================================================
//  KARLS — Thread & DM platform
//  Add this block to your existing routes/web.php inside
//  the auth middleware group, alongside your existing routes.
// ================================================================

Route::middleware(['auth', 'verified', 'show.ad'])
    ->prefix('karls')
    ->name('karls.')
    ->group(function () {

        // ── Public Threads ───────────────────────────────────────

        // Landing: thread list + general thread
        Route::get('/', [KarlsController::class, 'index'])
            ->name('index');

        // Individual thread view
        Route::get('/thread/{thread:slug}', [KarlsController::class, 'thread'])
            ->name('thread');

        // Post a karl to a thread
        Route::post('/thread/{thread:slug}', [KarlsController::class, 'post'])
            ->name('post');

        // Delete own karl
        Route::delete('/karl/{karl}', [KarlsController::class, 'deleteKarl'])
            ->name('karl.delete');

        // Live poll endpoint (returns new karls as JSON since given ID)
        Route::get('/thread/{thread:slug}/poll', [KarlsController::class, 'poll'])
            ->name('poll');

        // Non-anonymous poster profile peek
        Route::get('/user/{user}', [KarlsController::class, 'userProfile'])
            ->name('user.profile');

        // ── Private Karls (DMs) ──────────────────────────────────

        // Inbox — list of all DM conversations
        Route::get('/inbox', [PrivateKarlController::class, 'inbox'])
            ->name('inbox');

        // Conversation with a specific user
        Route::get('/dm/{user}', [PrivateKarlController::class, 'conversation'])
            ->name('dm');

        // Send a private karl
        Route::post('/dm/{user}', [PrivateKarlController::class, 'send'])
            ->name('dm.send');
    });