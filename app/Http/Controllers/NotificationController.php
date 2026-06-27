<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function updateToken(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
            ]);

            // Update the user's fcm_token in the database
            $user = Auth::user();
            $user->fcm_token = $request->token;
            $user->touch(); // Update timestamps to trigger any observers
            $user->save();

            return response()->json(['success' => true, 'message' => 'Token saved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

     /**
     * Send a high-performance multicast push to all users.
     */
   public static function broadcastPush($title, $body, $url = null)
{
    $messaging = app('firebase.messaging');
    
    // 1. Get all tokens as a simple array
    $tokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

    if (empty($tokens)) {
        return 0;
    }

    // 2. Split tokens into chunks of 500 (Firebase Multicast limit)
    $chunks = array_chunk($tokens, 500);
    $totalSuccesses = 0;

    foreach ($chunks as $chunk) {
        // 3. Create the message template
        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData(['url' => $url ?? url('/dashboard')]);

        try {
            // 4. Send the batch
            $report = $messaging->sendMulticast($message, $chunk);
            $totalSuccesses += $report->successes()->count();

            // 5. Cleanup: Find and remove invalid/expired tokens from DB
            $invalidTokens = array_merge(
                $report->invalidTokens(), 
                $report->unknownTokens()
            );

            if (!empty($invalidTokens)) {
                User::whereIn('fcm_token', $invalidTokens)->update(['fcm_token' => null]);
            }

        } catch (\Exception $e) {
            \Log::error("Multicast Batch Error: " . $e->getMessage());
        }
    }

    return $totalSuccesses;
}

}


   


