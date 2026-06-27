<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\ContactMessage;
use App\Mail\ContactFormSubmitted;
use App\Models\Feedback;

class PageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Home / Landing Page
    |--------------------------------------------------------------------------
    */
    public function home()
    {
        return view('pages.home');
    }

    /*
    |--------------------------------------------------------------------------
    | About Us
    |--------------------------------------------------------------------------
    */
    public function about()
    {
        return view('pages.about');
    }

    /*
    |--------------------------------------------------------------------------
    | Privacy Policy
    |--------------------------------------------------------------------------
    */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /*
    |--------------------------------------------------------------------------
    | Terms of Service
    |--------------------------------------------------------------------------
    */
    public function terms()
    {
        return view('pages.terms');
    }

    /*
    |--------------------------------------------------------------------------
    | Contact Us
    |--------------------------------------------------------------------------
    */
    public function contactShow()
    {
        return view('pages.contact');
    }

    public function contactStore(Request $request)
{
    $validated = $request->validate([
        'name'    => ['required', 'string', 'max:100'],
        'email'   => ['required', 'email', 'max:150'],
        'subject' => ['required', 'string', 'in:general,technical,materials,account,feedback,other'],
        'message' => ['required', 'string', 'min:10', 'max:2000'],
    ]);

    try {
        // ✅ 1. Store in database
        $contact = ContactMessage::create($validated);

        // ✅ 2. Send email using Gmail SMTP (.env)
        Mail::to(config('mail.support_address', env('MAIL_FROM_ADDRESS')))
            ->send(new ContactFormSubmitted($contact));

    } catch (\Exception $e) {
        Log::error('Contact form error', [
            'error' => $e->getMessage(),
            'data'  => $validated
        ]);

        return back()->with('error', 'Something went wrong. Please try again.');
    }

    return redirect()
        ->route('contact')
        ->with('success', 'Your message has been sent successfully!');
}

    /*
    |--------------------------------------------------------------------------
    | Feedback
    |--------------------------------------------------------------------------
    */
    public function feedbackShow()
    {
        return view('pages.feedback');
    }

    public function feedbackStore(Request $request)
{
    $validated = $request->validate([
        'rating'       => ['required', 'integer', 'min:1', 'max:5'],
        'category'     => ['nullable', 'string', 'max:60'],
        'name'         => ['nullable', 'string', 'max:100'],
        'matric_no'    => ['nullable', 'string', 'max:30'],
        'likes'        => ['nullable', 'string', 'max:1000'],
        'improvements' => ['nullable', 'string', 'max:1000'],
        'other'        => ['nullable', 'string', 'max:1000'],
        'recommend'    => ['nullable', 'string', 'max:60'],
    ]);

    try {
        // ✅ Save to database
        Feedback::create($validated);

    } catch (\Exception $e) {
        Log::error('Feedback save error', [
            'error' => $e->getMessage(),
            'data'  => $validated
        ]);

        return back()->with('error', 'Something went wrong. Please try again.');
    }

    return redirect()
        ->route('feedback')
        ->with('success', 'Thank you for your feedback! Your response has been recorded.');
}
}
