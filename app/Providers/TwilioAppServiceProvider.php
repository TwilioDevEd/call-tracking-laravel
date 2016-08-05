<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client;

class TwilioAppServiceProvider extends ServiceProvider
{
    /**
     * Initializes and registers Twilio SDK's object.
     *
     * @return void
     */
    public function register()
    {

        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID']
        or die("TWILIO_ACCOUNT_SID is not set in the environment");
        $authToken = config('app.twilio')['TWILIO_AUTH_TOKEN']
        or die("TWILIO_AUTH_TOKEN is not set in the environment");

        $twilioClient = new Client($accountSid, $authToken);

        $this->app->instance(Client::class, $twilioClient);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Client::class];
    }
}
