<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request, TelegramBotHandler $handler): Response
    {
        $expectedSecret = config('services.telegram.webhook_secret');

        if ($expectedSecret && $request->header('X-Telegram-Bot-Api-Secret-Token') !== $expectedSecret) {
            abort(403);
        }

        $handler->handleUpdate($request->all());

        return response('', 200);
    }
}
