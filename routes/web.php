<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\QuestionGenerationController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PushController;
use App\Models\Material;
use App\Models\Resource;
use App\Models\User;
use App\Http\Controllers\AdController;



/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/heartbeat', function () {
    return response()->json(['status' => 'alive']);
})->name('heartbeat');


Route::get('/test-push', function () {
    $title = "Test: ReadPal Live";
    $body  = "This is a demo notification from your laptop. Current time: " . now()->format('h:i A');
    $url   = url('/dashboard');

    // Trigger the broadcast
    $successCount = NotificationController::broadcastPush($title, $body, $url);

    if ($successCount > 0) {
        return "Success! Push sent to {$successCount} device(s).";
    }

    return "Failed. No notifications were sent. Check your logs or ensure users have tokens.";
})->middleware(['auth']); // Ensure you are logged in to access this

// Home / Landing Page
Route::get('/', [PageController::class, 'home'])->name('home');

// About
Route::get('/about', [PageController::class, 'about'])->name('about');

// Contact ? show form + handle submission
Route::get('/contact',  [PageController::class, 'contactShow'])->name('contact');
Route::post('/contact', [PageController::class, 'contactStore'])->name('contact.store');

// Feedback ? show form + handle submission
Route::get('/feedback',  [PageController::class, 'feedbackShow'])->name('feedback');
Route::post('/feedback', [PageController::class, 'feedbackStore'])->name('feedback.store');

// Static legal pages
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms',   [PageController::class, 'terms'])->name('terms');



Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');


// Public resources listing
Route::get('/resources', [ResourcesController::class, 'index'])->name('resources.index');

// Push test

Route::get('/send-test-push', function () {
    $messaging = app('firebase.messaging');
    
    // 1. Get all users who actually have a token
    $users = User::whereNotNull('fcm_token')->get();

    if ($users->isEmpty()) {
        return "No users found with valid device tokens.";
    }

    $successCount = 0;
    $errorCount = 0;

    // 2. Loop and send
    foreach ($users as $user) {
        try {
            $message = CloudMessage::fromArray([
                'token' => $user->fcm_token,
                'notification' => [
                    'title' => 'ReadPal Global Alert',
                    'body' => 'Hello ' . $user->firstname . '! This is a broadcast to all readers.',
                ],
                'data' => ['url' => url('/dashboard')],
            ]);

            $messaging->send($message);
            $successCount++;
        } catch (\Exception $e) {
            // If a token is expired/invalid, you might want to null it in your DB
            $errorCount++;
        }
    }

    return "Broadcast complete! Sent to $successCount users. Failed for $errorCount.";
})->middleware('auth');



/*
|--------------------------------------------------------------------------
| Guest-only Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // Auth
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login/auth', [AuthController::class, 'index'])->name('auth.index');
    Route::get('/signup', [AuthController::class, 'create'])->name('signup');
    Route::post('/signup', [AuthController::class, 'store'])->name('signup.store');

    // Password reset
    Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth', 'show.ad')->group(function () {
    // Ads
    Route::get('/ads', [AdController::class, 'show'])->name('ads.show');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Logout
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    // Push notifications
    Route::post('/save-fcm-token', [NotificationController::class, 'updateToken'])->middleware('auth')->name('save-fcm-token');


    // Profile routes
    Route::resource('profile', ProfileController::class)
        ->only(['show', 'edit', 'update', 'destroy'])
        ->names([
            'show' => 'profile.show',
            'edit' => 'profile.edit',
            'update' => 'profile.update',
            'destroy' => 'profile.destroy',
        ])
        ->parameters(['profile' => 'user']);

    // Settings page
    Route::get('/settings', fn() => view('settings'))->name('settings');
     // ── Push Notification Routes ─────────────────────────────────
    Route::prefix('push')->name('push.')->group(function () {
 
        // Subscribe this browser/device
        Route::post('/subscribe', [PushController::class, 'subscribe'])
            ->name('subscribe');
 
        // Unsubscribe this browser/device
        Route::post('/unsubscribe', [PushController::class, 'unsubscribe'])
            ->name('unsubscribe');
 
        // Save notification preference toggles from settings page
        Route::post('/preferences', [PushController::class, 'updatePreferences'])
            ->name('preferences');
    });
    /*
    |--------------------------------------------------------------------------
    | Lessons and Resources Routes (Authenticated Only)
    |--------------------------------------------------------------------------
    */

    // Download PDF
    Route::get('/resources/{resource:slug}/{material:slug}/download', [ResourcesController::class, 'downloadPdf'])
        ->name('material.download');
  
  Route::get('/resources/{resource}/download-all', 
    [ResourcesController::class, 'downloadAllMaterialsPdf']
)->name('resources.downloadAll');
  
    // Resource detail page
    Route::get('/resources/{resource:slug}', [ResourcesController::class, 'show'])->name('resources.show');
    
    Route::get('/resources/{resource}/full', [ResourcesController::class, 'full'])
    ->name('resources.full.show');
    
    Route::get('/materials/{material}/view', [ResourcesController::class, 'view'])
    ->name('materials.view');
    
    // Lessons route (must be BEFORE {firstname})
    Route::get('/{resource:slug}/lessons/{material:slug}', [ResourcesController::class, 'material'])
        ->name('lesson.material');

    // Calendar
    Route::get('/calender', [LectureController::class, 'index'])->name('calender.index');

    // Quiz routes
    Route::get('quiz', [QuizController::class, 'index'])->name('quiz.index');
    Route::get('/materials/{id}/quiz', [QuestionGenerationController::class, 'takeQuiz'])->name('materials.quiz');
    Route::post('/materials/{id}/quiz/submit', [QuestionGenerationController::class, 'submitQuiz'])->name('materials.quiz.submit');
    Route::post('quiz/{materialId}/store-result', [QuizController::class, 'storeQuizResult'])
        ->name('quiz.storeResult');

    // Paginated materials list
    Route::get('/materials', function (Request $request) {
    $query = Material::with('resource');

    // Change 'course_code' to 'course' to match your URL
    if ($request->filled('course') && $request->course !== 'all') {
        $course = trim($request->course);
        $query->whereHas('resource', function($q) use ($course) {
            $q->where('course_code', $course);
        });
    }

    if ($request->filled('search')) {
        $search = trim($request->search);
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('note_text', 'like', "%{$search}%");
        });
    }

    $materials = $query->latest()->paginate(10)->withQueryString();
    $courses = Resource::pluck('course_code')->filter()->unique();

    return view('materials.index', compact('materials', 'courses'));
})->name('materials.index');


    /*
    |--------------------------------------------------------------------------
    | Notes Routes (Prefixed by {firstname})
    |--------------------------------------------------------------------------
    */
    Route::prefix('{firstname}')->group(function () {
        Route::get('notes', [NotesController::class, 'index'])->name('notes.index');
        Route::get('notes/create', [NotesController::class, 'create'])->name('notes.create');
        Route::post('notes', [NotesController::class, 'store'])->name('notes.store');
        Route::get('notes/{note}', [NotesController::class, 'show'])->name('notes.show');
        Route::get('notes/{note}/edit', [NotesController::class, 'edit'])->name('notes.edit');
        Route::put('notes/{note}', [NotesController::class, 'update'])->name('notes.update');
        Route::delete('notes/{note}', [NotesController::class, 'destroy'])->name('notes.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| AI Routes
|--------------------------------------------------------------------------
*/

require base_path('routes/ai.php');

/*
|--------------------------------------------------------------------------
| Karls Routes
|--------------------------------------------------------------------------
*/

require base_path('routes/karls.php');

/*
|--------------------------------------------------------------------------
| CGPA Routes
|--------------------------------------------------------------------------
*/

require base_path('routes/cgpaweb.php');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
require base_path('routes/admin.php');

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/
Route::fallback(fn() => response()->view('404', [], 404));
