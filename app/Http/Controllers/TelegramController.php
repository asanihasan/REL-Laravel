<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $messageText = $request->input('message.text'); 
        $telegramId = $request->input('message.from.id');

        // Check if the message exists and starts with /start 
        if ($messageText && str_starts_with($messageText, '/start ')) {
            
            // Extract the token
            $token = str_replace('/start ', '', $messageText);

            // Find the user with this token
            $user = User::where('telegram_link_token', $token)->first();

            if ($user) {
                // Update the user and clear the token so it can't be reused
                $user->update([
                    'telegram_id' => $telegramId,
                    'telegram_link_token' => null 
                ]);
                
                return response()->json(['status' => 'success']);
            }
        }

        // Always return a 200 OK response to Telegram, even if ignored
        return response()->json(['status' => 'ignored']);
    }
}