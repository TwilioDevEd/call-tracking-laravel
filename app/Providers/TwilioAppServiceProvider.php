<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TwilioAppServiceProvider extends ServiceProvider
{
    /**
     * Initializes and registers Twilio SDK's object.
     *
     * @return void
     */
    public function register()
    {
        $token = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];

        $twilio = new \Services_Twilio($accountSid, $token);
        $this->app->instance('Twilio', $twilio);
    }
}