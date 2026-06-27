<?php

use App\Http\Controllers\Ai\AiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAssignmentController;
use App\Http\Controllers\UserAssignmentController;
use App\Http\Controllers\Admin\AiMonitorController;

Route::middleware(['auth'])->prefix('ai')->name('ai.')->group(function () {

    // ── Chat & History ──────────────────────────────────────────
    Route::get('/',        [AiController::class, 'chatPage'])->name('chat');
    Route::post('/ask',    [AiController::class, 'ask'])->name('ask');
    Route::get('/history', [AiController::class, 'historyPage'])->name('history');

    // ── Knowledge Base management ───────────────────────────────
    Route::middleware(['admin'])->prefix('knowledge-bases')->name('knowledge-bases.')->group(function () {
        Route::get('/',                [AiController::class, 'knowledgeBasesIndex'])->name('index');
        Route::get('/create',          [AiController::class, 'createPage'])->name('create');
        Route::post('/',               [AiController::class, 'storeBase'])->name('store');
        Route::delete('/{baseId}',     [AiController::class, 'deleteBase'])->name('destroy');

        // FIX: Matches 'ai.knowledge-bases.store-content' in your Blade file
        Route::post('/{baseId}/content', [AiController::class, 'storeContent'])->name('store-content');
    });
});


// AI Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::prefix('ai-monitor')->name('ai-monitor.')->group(function () {
        Route::get('/',          [AiMonitorController::class, 'index'])->name('index');
        Route::get('/stats',     [AiMonitorController::class, 'stats'])->name('stats');
        Route::post('/test',     [AiMonitorController::class, 'testProvider'])->name('test');
        Route::post('/test-all', [AiMonitorController::class, 'testAll'])->name('test-all');
        Route::get('/logs',      [AiMonitorController::class, 'logs'])->name('logs');
        Route::post('/prune',    [AiMonitorController::class, 'prune'])->name('prune');
    });

    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/',                    [AdminAssignmentController::class, 'index'])->name('index');
        Route::get('/create',              [AdminAssignmentController::class, 'create'])->name('create');
        Route::post('/',                   [AdminAssignmentController::class, 'store'])->name('store');
        Route::get('/{assignment}/edit',   [AdminAssignmentController::class, 'edit'])->name('edit');
        Route::put('/{assignment}',        [AdminAssignmentController::class, 'update'])->name('update');
        Route::delete('/{assignment}',     [AdminAssignmentController::class, 'destroy'])->name('destroy');
        Route::post('/{assignment}/toggle',[AdminAssignmentController::class, 'togglePublish'])->name('toggle-publish');
        Route::get('/{assignment}/submissions', [AdminAssignmentController::class, 'submissions'])->name('submissions');
    });

});

// ── User Routes ───────────────────────────────────────────────
Route::middleware(['auth'])->prefix('assignments')->name('assignments.')->group(function () {

    Route::get('/',                                 [UserAssignmentController::class, 'index'])->name('index');
    Route::get('/{assignment}/workspace',           [UserAssignmentController::class, 'workspace'])->name('workspace');

    // AJAX endpoints
    Route::post('/save-content',                    [UserAssignmentController::class, 'saveContent'])->name('save-content');
    Route::post('/save-all',                        [UserAssignmentController::class, 'saveAll'])->name('save-all');
    Route::post('/generate-section',               [UserAssignmentController::class, 'generateSection'])->name('generate-section');
    Route::post('/improve-content',                 [UserAssignmentController::class, 'improveContent'])->name('improve-content');
    Route::post('/{userAssignment}/complete',       [UserAssignmentController::class, 'markCompleted'])->name('complete');
    Route::get('/{userAssignment}/pdf',             [UserAssignmentController::class, 'exportPdf'])->name('pdf');
});

