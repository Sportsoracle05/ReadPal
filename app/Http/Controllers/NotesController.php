<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // List all notes for the current user
    public function index($firstname)
    {
        // Validate user firstname to prevent access mismatch
        $this->checkFirstname($firstname);

        // Paginate notes (e.g., 10 per page)
        $notes = \App\Models\Note::where('user_id', Auth::id())
            ->latest()
            ->paginate(8); // You can change 10 to any number per page

        return view('notes.index', compact('notes', 'firstname'));
    }


    // Show form to create a new note
    public function create($firstname)
    {
        $this->checkFirstname($firstname);

        return view('notes.create', compact('firstname'));
    }

    // Store a new note
    public function store(Request $request, $firstname)
    {
        $this->checkFirstname($firstname);

        $request->validate([
            'content' => 'required|string|max:250',
        ]);

        Note::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->route('notes.index', ['firstname' => $firstname])
                         ->with('success', 'Note added successfully!');
    }

    // Show a single note
    public function show($firstname, Note $note)
    {
        $this->checkFirstname($firstname);
        $this->authorizeNote($note);

        return view('notes.show', compact('note', 'firstname'));
    }

    // Edit a note
    public function edit($firstname, Note $note)
    {
        $this->checkFirstname($firstname);
        $this->authorizeNote($note);

        return view('notes.edit', compact('note', 'firstname'));
    }

    // Update a note
    public function update(Request $request, $firstname, Note $note)
    {
        $this->checkFirstname($firstname);
        $this->authorizeNote($note);

        $request->validate([
            'content' => 'required|string|max:250',
        ]);

        $note->update(['content' => $request->content]);

        return redirect()->route('notes.index', ['firstname' => $firstname])
                         ->with('success', 'Note updated successfully!');
    }

    // Delete a note
    public function destroy($firstname, Note $note)
    {
        $this->checkFirstname($firstname);
        $this->authorizeNote($note);

        $note->delete();

        return redirect()->route('notes.index', ['firstname' => $firstname])
                         ->with('success', 'Note deleted successfully!');
    }

    // Helper: ensure note belongs to the current user
    protected function authorizeNote(Note $note)
    {
        if ((int) $note->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    // Helper: check firstname matches logged-in user
    protected function checkFirstname($firstname)
    {
        if ($firstname !== Auth::user()->firstname) {
            abort(403, 'Unauthorized access.');
        }
    }
}

