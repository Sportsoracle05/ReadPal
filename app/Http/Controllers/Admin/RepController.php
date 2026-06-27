<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RepController extends Controller
{
    public function index()
    {
        $reps = User::where('role', 'rep')->get();
        return view('admin.reps.index', compact('reps'));
    }

    public function create()
    {
        return view('admin.reps.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'rep',
        ]);

        return redirect()->route('admin.reps.index')->with('success', 'Rep account created.');
    }

    public function destroy(User $rep)
    {
        $rep->delete();
        return redirect()->route('admin.reps.index')->with('success', 'Rep deleted.');
    }
}
