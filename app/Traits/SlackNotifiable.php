<?php

namespace App\Traits;

use Spatie\SlackAlerts\Facades\SlackAlert;

trait SlackNotifiable
{
    public function slackNotify(string $message = 'IMTALabs', string $channel = null)
    {
        if (is_null($channel)) {
            return false;
        }

        SlackAlert::to($channel)->blocks([
            [
                "type" => "section",
                "text" => [
                "type" => "mrkdwn",
                    "text" => "# You have a new subscriber to the newsletter!
                    ## abcaaaaaaaaa"
                ]
            ]
        ]);

        return true;
    }
}
