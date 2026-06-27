<?php

use App\Http\Controllers\CgpaController;
use Illuminate\Support\Facades\Route;

// ================================================================
//  Student CGPA Routes
// ================================================================

Route::middleware(['auth', 'verified', 'show.ad'])->group(function () {

    // Dashboard
    Route::get('/cgpa/dashboard', [CgpaController::class, 'dashboard'])
        ->name('cgpa.dashboard');

    // ── Semester Routes ──────────────────────────────────────────
    Route::get('/semesters',            [CgpaController::class, 'semesterIndex'])
        ->name('cgpa.semester.index');

    Route::post('/semesters',           [CgpaController::class, 'semesterStore'])
        ->name('cgpa.semester.store');

    Route::get('/semesters/{semester}', [CgpaController::class, 'semesterShow'])
        ->name('cgpa.semester.show');

    Route::delete('/semesters/{semester}', [CgpaController::class, 'semesterDestroy'])
        ->name('cgpa.semester.destroy');

    // ── Course Routes (flat — avoids prefix double-slash bug) ────
    Route::post('/semesters/{semester}/courses',
        [CgpaController::class, 'courseStore'])
        ->name('cgpa.semester.course.store');

    Route::put('/semesters/{semester}/courses/{course}',
        [CgpaController::class, 'courseUpdate'])
        ->name('cgpa.semester.course.update');

    Route::delete('/semesters/{semester}/courses/{course}',
        [CgpaController::class, 'courseDestroy'])
        ->name('cgpa.semester.course.destroy');
});

