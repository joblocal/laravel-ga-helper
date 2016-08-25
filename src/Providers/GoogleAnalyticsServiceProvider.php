<?php

namespace Joblocal\Laravel\GAHelper\Providers;

use Illuminate\Support\ServiceProvider;
use \Google_Client;
use \Google_Service_Analytics;

class GoogleAnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/googleAnalytics.php' => config_path('googleAnalytics.php'),
        ]);
    }

    /**
     * Register the google analytics application service.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/googleAnalytics.php',
            'googleAnalytics'
        );
        $this->app->bind('googleAnalytics', function () {
            $config = config('googleAnalytics');
            $config['private_key'] = str_replace("\\n", "\n", $config['private_key']);

            if ($config['client_id']) {
                $client = new Google_Client();
                $client->setApplicationName($config['app_name']);
                $client->setAuthConfig($config);
                $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);

                return new Google_Service_Analytics($client);
            } else {
                throw new Exeption('Component not configured.');
            }
        });
    }
}
