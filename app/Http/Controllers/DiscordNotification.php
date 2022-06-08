<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DiscordNotification extends Controller
{
    public function notification()
    {
        return Http::post('https://discord.com/api/webhooks/935920131169730640/wYjtipK-6lxi60rduhvT_0uSjgR6S7XVp9NF4j_blTfCLR-SmN8OkR9WLPm85dFVPaMY', [
            'content' => "Vừa mua sản phẩm",
            'embeds' => [
                [
                    'title' => "An awesome new notification!",
                    'description' => "Discord Webhooks are great!",
                    'color' => '7506394',
                ]
            ],
        ]);

    }
}