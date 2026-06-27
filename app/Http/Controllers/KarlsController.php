<?php

namespace App\Http\Controllers;

use App\Models\Karl;
use App\Models\PrivateKarl;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KarlsController extends Controller
{
    // ── Index: list all threads + open general by default ────────

    public function index(): View
    {
        $general = Thread::ensureGeneralExists();
        $threads = Thread::orderByDesc('is_pinned')->orderBy('name')->get();

        // Unread private DM count for badge
        $unreadCount = PrivateKarl::unreadFor(Auth::id())->count();

        // Redirect internally to the general thread's karls
        $karls = Karl::with('author')
            ->where('thread_id', $general->id)
            ->latest()
            ->paginate(40);

        return view('karls.index', compact('threads', 'general', 'karls', 'unreadCount'));
    }

    // ── Show a specific thread ────────────────────────────────────

    public function thread(Thread $thread): View
    {
        $threads     = Thread::orderByDesc('is_pinned')->orderBy('name')->get();
        $unreadCount = PrivateKarl::unreadFor(Auth::id())->count();

        $karls = Karl::with('author')
            ->where('thread_id', $thread->id)
            ->latest()                 // chronological in thread view
            ->paginate(60);

        return view('karls.thread', compact('thread', 'threads', 'karls', 'unreadCount'));
    }

    // ── Post a new karl to a thread ──────────────────────────────

    public function post(Request $request, Thread $thread): RedirectResponse
    {
        $validated = $request->validate([
            'content'      => ['required', 'string', 'max:1000'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        Karl::create([
            'thread_id'    => $thread->id,
            'user_id'      => Auth::id(),
            'content'      => trim($validated['content']),
            'is_anonymous' => $request->boolean('is_anonymous'),
        ]);

        return redirect()
            ->route('karls.thread', $thread->slug)
            ->with('posted', true);
    }

    // ── Delete own karl ──────────────────────────────────────────

    public function deleteKarl(Karl $karl): RedirectResponse
    {
        abort_unless($karl->user_id === Auth::id(), 403);

        $slug = $karl->thread->slug;
        $karl->delete();

        return redirect()
            ->route('karls.thread', $slug)
            ->with('success', 'Your karl was removed.');
    }

    // ── Quick profile peek (for non-anonymous posters) ───────────

    public function userProfile(User $user): View
    {
        abort_if($user->id === Auth::id(), 302, redirect()->route('karls.index'));

        // Recent public karls from this user (non-anon only)
        $publicKarls = Karl::with('thread')
            ->where('user_id', $user->id)
            ->where('is_anonymous', false)
            ->oldest()
            ->take(10)
            ->get();

        $threads     = Thread::orderByDesc('is_pinned')->orderBy('name')->get();
        $unreadCount = PrivateKarl::unreadFor(Auth::id())->count();

        return view('karls.profile', compact('user', 'publicKarls', 'threads', 'unreadCount'));
    }

    // ── Poll endpoint: return new karls since a given karl ID ────
    // Used by the front-end 15-second poller.

    public function poll(Request $request, Thread $thread)
    {
        $since = (int) $request->query('since', 0);

        $karls = Karl::with('author')
            ->where('thread_id', $thread->id)
            ->when($since > 0, fn($q) => $q->where('id', '>', $since))
            ->oldest()
            ->take(50)
            ->get()
            ->map(fn(Karl $k) => [
                'id'           => $k->id,
                'content'      => e($k->content),
                'is_anonymous' => $k->is_anonymous,
                'display_name' => $k->display_name,
                'user_id'      => $k->is_anonymous ? null : $k->user_id,
                'initial'      => $k->is_anonymous ? '?' : strtoupper(substr($k->display_name, 0, 1)),
                'time'         => $k->created_at->diffForHumans(),
                'is_own'       => $k->user_id === Auth::id(),
            ]);

        return response()->json([
            'karls'     => $karls,
            'last_id'   => $karls->max('id') ?? $since,
            'timestamp' => now()->toISOString(),
        ]);
    }
}