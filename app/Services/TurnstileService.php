<?php

namespace App\Services;

use GuzzleHttp\Client;

class TurnstileService
{
    public function open(string $device): string
    {
        $client = new Client([
            'base_uri' => 'http://10.100.90.5'.$device,
            'auth' => ['admin', '01x994ma', 'digest'], // digest auth uchun
        ]);

        $response = $client->request('GET', '/cgi-bin/accessControl.cgi', [
            'query' => [
                'action' => 'openDoor',
                'channel' => 1,
            ],
        ]);

        return $response->getBody()->getContents();
    }
}
