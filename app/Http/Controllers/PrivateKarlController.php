<?php

namespace App\Http\Controllers;

use App\Models\PrivateKarl;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PrivateKarlController extends Controller
{
    // ── Inbox: list all unique DM conversations ──────────────────

    public function inbox(): View
    {
        $userId = Auth::id();

        // Unique conversation partners (senders or receivers)
        $partnerIds = PrivateKarl::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get(['sender_id', 'receiver_id'])
            ->flatMap(fn($pk) => [$pk->sender_id, $pk->receiver_id])
            ->unique()
            ->filter(fn($id) => $id !== $userId)
            ->values();

        $conversations = User::whereIn('id', $partnerIds)
            ->get()
            ->map(function (User $partner) use ($userId) {
                $latest = PrivateKarl::conversation($userId, $partner->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $unread = PrivateKarl::where('receiver_id', $userId)
                    ->where('sender_id', $partner->id)
                    ->whereNull('viewed_at')
                    ->count();

                return [
                    'user'    => $partner,
                    'latest'  => $latest,
                    'unread'  => $unread,
                    'preview' => $latest
                        ? \Str::limit($latest->content, 55)
                        : null,
                ];
            })
            ->sortByDesc(fn($c) => $c['latest']?->created_at)
            ->values();

        $threads     = Thread::orderByDesc('is_pinned')->orderBy('name')->get();
        $unreadCount = PrivateKarl::unreadFor($userId)->count();

        return view('karls.private.inbox', compact(
            'conversations', 'threads', 'unreadCount'
        ));
    }

    // ── Conversation: messages between auth user and $user ───────

    public function conversation(User $user): View
    {
        abort_if($user->id === Auth::id(), 403, 'You cannot message yourself.');

        $authId = Auth::id();

        $messages = PrivateKarl::conversation($authId, $user->id)
            ->with(['sender', 'receiver'])
            ->get();

        // Stamp all unread messages for the authenticated user as viewed
        PrivateKarl::where('receiver_id', $authId)
            ->where('sender_id', $user->id)
            ->whereNull('viewed_at')
            ->update(['viewed_at' => now()]);

        $threads     = Thread::orderByDesc('is_pinned')->orderBy('name')->get();
        $unreadCount = PrivateKarl::unreadFor($authId)->count();

        return view('karls.private.conversation', compact(
            'user', 'messages', 'threads', 'unreadCount'
        ));
    }

    // ── Send a private karl ──────────────────────────────────────

    public function send(Request $request, User $user): RedirectResponse
    {
        abort_if($user->id === Auth::id(), 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:800'],
        ]);

        PrivateKarl::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $user->id,
            'content'     => trim($validated['content']),
        ]);

        return redirect()
            ->route('karls.dm', $user->username)
            ->with('sent', true);
    }
}