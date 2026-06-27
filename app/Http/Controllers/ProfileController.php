<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;


class ProfileController extends Controller
{
    public function show(User $profile)
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function edit(User $profile)
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request, User $profile)
    {
        $user = Auth::user();

    $validated = $request->validate([
        'firstname' => ['nullable', 'string', 'max:255'],
        'lastname'  => ['nullable', 'string', 'max:255'],
        'username'  => [
            'nullable',
            'string',
            'max:255',
            Rule::unique('users', 'username')->ignore($user->id),
        ],
        'email'     => [
            'nullable',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user->id),
        ],
    ]);

    // Only update changed fields
    foreach ($validated as $key => $value) {
        if (!is_null($value) && $value !== $user->$key) {
            $user->$key = $value;
        }
    }

    $user->save();

    return redirect()
        ->route('profile.show', ['user' => $user->username])
        ->with('success', 'Profile updated successfully!');

    }
}