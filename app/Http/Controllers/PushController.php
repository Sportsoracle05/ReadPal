<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushController extends Controller
{
    // ── Subscribe ──────────────────────────────────────────────

    /**
     * POST /push/subscribe
     * Store or re-activate a Web Push subscription from the browser.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint'         => ['required', 'string', 'max:1000'],
            'keys.p256dh'      => ['required', 'string'],
            'keys.auth'        => ['required', 'string'],
        ]);

        // Upsert: re-activate if exists, create if new
        PushSubscription::updateOrCreate(
            ['endpoint' => $validated['endpoint']],
            [
                'user_id'    => Auth::id(),
                'public_key' => $validated['keys']['p256dh'],
                'auth_token' => $validated['keys']['auth'],
                'user_agent' => $request->userAgent(),
                'is_active'  => true,
            ]
        );

        // Ensure master push toggle is on for this user
        Auth::user()->update(['push_enabled' => true]);

        return response()->json(['status' => 'subscribed'], 201);
    }

    // ── Unsubscribe ────────────────────────────────────────────

    /**
     * POST /push/unsubscribe
     * Deactivate a subscription when the user revokes in browser.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        PushSubscription::where('endpoint', $validated['endpoint'])
            ->where('user_id', Auth::id())
            ->update(['is_active' => false]);

        return response()->json(['status' => 'unsubscribed']);
    }

    // ── Update Preferences (from settings page) ────────────────

    /**
     * POST /push/preferences
     * Update the user's notification preference toggles.
     */


public function updatePreferences(Request $request)
{
    $user = Auth::user();

    // Use ->boolean() which returns false if the key is missing or '0'
    $user->update([
        'push_enabled'           => $request->boolean('push_enabled'),
        'push_lecture_alerts'    => $request->boolean('push_lecture_alerts'),
        'push_lecture_reminders' => $request->boolean('push_lecture_reminders'),
    ]);

    // Build the status message
    $active = [];
    if ($user->push_lecture_alerts) $active[] = "Alerts";
    if ($user->push_lecture_reminders) $active[] = "Reminders";

    $status = $user->push_enabled 
        ? "Enabled (" . (implode(' & ', $active) ?: 'None') . ")" 
        : "Disabled";

    return back()->with('success', "Notification Settings: {$status}");
}

}