<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TwilioServiceProvider extends ServiceProvider
{
    /**
     * Initializes and registers the application object
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'TwilioApp', function ($app) {
                $twilio = $app->make('Twilio');

                $matchingAppsIter = $twilio
                    ->account
                    ->applications
                    ->getIterator(0, 50, ['FriendlyName' => 'Call tracking app']);

                $matchingApps = iterator_to_array($matchingAppsIter);

                if (empty($matchingApps)) {
                    error_log('App not found, creating one');

                    return $twilio->account->applications->create(
                        ['friendly_name' => 'Call tracking app']
                    );
                } else {
                    error_log('App found, returning');

                    return $matchingApps[0];
                }
            }
        );
    }
}
