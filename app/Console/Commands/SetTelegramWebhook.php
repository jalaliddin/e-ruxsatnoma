<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class SetTelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook {url? : To\'liq webhook URL manzili}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Telegram bot uchun webhook manzilini sozlaydi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $token = config('services.telegram.bot_token');

        if (! $token) {
            $this->error('TELEGRAM_BOT_TOKEN .env faylida sozlanmagan.');

            return self::FAILURE;
        }

        $url = $this->argument('url') ?? url('/telegram/webhook');
        $secret = config('services.telegram.webhook_secret');

        $client = new Client();
        $response = $client->post("https://api.telegram.org/bot{$token}/setWebhook", [
            'json' => array_filter([
                'url' => $url,
                'secret_token' => $secret,
            ]),
        ]);

        $this->info($response->getBody()->getContents());

        return self::SUCCESS;
    }
}
