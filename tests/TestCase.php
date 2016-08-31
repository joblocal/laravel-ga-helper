<?php

namespace Joblocal\LaravelGAHelper\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
    * mocks the result of a google api get request
    */
    protected function getMockedAnalytics()
    {
        $dataGaMock = $this->createMock(\Google_Service_Analytics_Resource_DataGa::class);
        $dataGaMock->method('get')
            ->willReturn($this->getAnalyticsStubResponse());

        $analytics = $this->createMock(\Google_Service_Analytics::class);
        $analytics->data_ga = $dataGaMock;
        return $analytics;
    }

    protected function getAnalyticsStubResponse()
    {
        $jsonFilePath = dirname(__FILE__) . '/Stubs/GoogleAnalyticsStub.json';
        $jsonResponse = file_get_contents($jsonFilePath);

        return json_decode($jsonResponse);
    }
}
