<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // <-- Add this to make external API calls

class TelegramController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Log::info('Telegram Webhook Hit!', $request->all());

        $messageText = $request->input('message.text'); 
        $telegramId = $request->input('message.from.id');

        if ($messageText && str_starts_with($messageText, '/start ')) {
            $token = str_replace('/start ', '', $messageText);
            
            $user = User::where('telegram_link_token', $token)->first();

            if ($user) {
                Log::info('User found! Updating ID for: ' . $user->email);
                
                $user->update([
                    'telegram_id' => $telegramId,
                    'telegram_link_token' => null 
                ]);
                
                // --- NEW: Send a welcome/success message ---
                $welcomeText = "✅ Hello {$user->first_name}! Your Telegram account has been successfully linked to the dashboard. You will now receive system alerts here.";
                $this->sendMessage($telegramId, $welcomeText);
                
                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'ignored']);
    }

    // --- NEW: Helper method to send messages ---
    private function sendMessage($chatId, $text)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        
        if (!$botToken) {
            Log::error('Telegram Bot Token is missing from .env');
            return;
        }

        $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text'    => $text,
        ]);

        if (!$response->successful()) {
            Log::error('Failed to send Telegram message: ' . $response->body());
        }
    }

    public function sendInternalMessage(Request $request)
    {
        // 1. Remove the strict 'string' validation on receiver_id
        $request->validate([
            'receiver_id' => 'required', 
            'message' => 'required|string',
        ]);

        $receivers = $request->input('receiver_id');
        $message = $request->input('message');

        // 2. If a single ID is passed as a string, wrap it in an array
        if (!is_array($receivers)) {
            $receivers = [$receivers];
        }

        // 3. Loop through the array and send the message to everyone
        foreach ($receivers as $id) {
            $this->sendMessage($id, $message);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Telegram message dispatched to ' . count($receivers) . ' recipient(s).'
        ]);
    }
}