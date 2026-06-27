<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Use this alias
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Handle user login.
     */
    public function index(Request $request)
    {
        // 1. Validate credentials
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
        
        // 2. Check for the 'remember-me' checkbox
        // The checkbox name from your form is 'remember-me'
        $remember = $request->filled('remember-me');

        // 3. Attempt to log in, passing the $remember value
        if (Auth::attempt($validated, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        } else {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }
    }

    /**
     * Show the form for creating a new resource (Signup).
     */
    public function create()
    {
        return view('auth.signup');
    }

    /**
     * Store a newly created resource in storage (Process Signup).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'username'  => 'nullable|string|max:255|unique:users,username',
            'email'     => 'required|email|max:255|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Generate username if not provided
        if (empty($validated['username'])) {
            $baseUsername = Str::slug(strtolower($validated['firstname'] . '.' . $validated['lastname']));
            $username = $baseUsername;
            $counter = 1;

            // Ensure unique username
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter++;
            }

            $validated['username'] = $username;
        }

        // Create user
        $user = User::create($validated);

        // Auto-login
        Auth::login($user);


        return redirect()->route('dashboard')->with('success', 'Welcome, ' . $user->firstname . '!');
    
    }

    /**
     * Display the specified resource (Login Form).
     */
    public function show()
    {
        return view('auth.login');
    }
    
    // --- Password Reset Methods ---

    public function forgotPasswordForm()
    {
        return view('auth.forgot');
    }
    
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
    
        $token = Str::random(64);
    
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'email'      => $request->email,
                'token'      => $token,
                'created_at' => Carbon::now()
            ]
        );
    
        // Update URL to match common Laravel convention if you changed it, 
        // e.g., route('password.reset', ['token' => $token])
        $resetLink = url('/reset-password/' . $token); 
    
        // Send email
        Mail::raw("Click the link to reset your password: $resetLink", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Reset Your Password');
        });
    
        return back()->with('success', 'A password reset link has been sent to your email.');
    }
    
    public function resetPasswordForm($token)
    {
        return view('auth.reset', compact('token'));
    }
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'token'    => 'required'
        ]);
    
        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            // Optional: Check token age (e.g., within 60 minutes)
            // ->where('created_at', '>', Carbon::now()->subMinutes(60)) 
            ->first();
    
        if (!$record) {
            return back()->withErrors(['email' => 'Invalid or expired reset link/token.']);
        }
    
        // Update password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
            // Important: Clear the remember_token field after password reset for security
            'remember_token' => null 
        ]);
    
        // Delete token after use
        DB::table('password_resets')->where('email', $request->email)->delete();
    
        return redirect()->route('login')->with('success', 'Your password has been reset successfully. You can now log in.');
    }

    /**
     * Handle user logout.
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    
    // The unused methods 'edit' and 'update' were removed for cleanup.
}
