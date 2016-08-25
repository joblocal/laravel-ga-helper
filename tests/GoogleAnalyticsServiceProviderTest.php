<?php

namespace Tests;

class GoogleAnalyticsServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return ['Joblocal\Laravel\GAHelper\Providers\GoogleAnalyticsServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('googleAnalytics', [
            'app_name' => 'Laravel ga helper',
            'type' => 'service_account',
            'client_id' => '1234567890',
            'client_email' => 'your_email@your_website.com',
            'private_key' => 'your_private_key',
        ]);
    }

    public function testProvider()
    {
        $this->assertInstanceOf(\Google_Service_Analytics::class, app('googleAnalytics'));
    }
}
