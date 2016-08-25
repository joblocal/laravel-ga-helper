<?php

namespace Tests;

use Joblocal\Laravel\GAHelper\Models\Model;
use Joblocal\Laravel\GAHelper\Query\Builder;

class BuilderTest extends TestCase
{
    public function testConstructor()
    {
        $this->expectException(\TypeError::class);
        $builder = new Builder(null, null);
    }

    public function testProfileID()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $test = $builder->profileID(12345);

        $this->assertInstanceOf(Builder::class, $test);
    }

    public function testMetric()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $test = $builder->metric('metric1');

        $this->assertInstanceOf(Builder::class, $test);
    }

    public function testFrom()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $test = $builder->from('yesterday');

        $this->assertInstanceOf(Builder::class, $test);
    }

    public function testTo()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $test = $builder->to('yesterday');

        $this->assertInstanceOf(Builder::class, $test);
    }

    public function testDimensions()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $test = $builder->dimensions([
            'pagePath',
            'date',
        ]);

        $this->assertInstanceOf(Builder::class, $test);
    }

    public function testMaxResults()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $test = $builder->maxResults(200);

        $this->assertInstanceOf(Builder::class, $test);
    }

    public function testOffset()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $test = $builder->offset(201);

        $this->assertInstanceOf(Builder::class, $test);
    }

    public function testPage()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $test = $builder->page(2);

        $this->assertInstanceOf(Builder::class, $test);
    }

    public function testGetResultRawData()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $builder->profileID(1234)
            ->metric('metric1');

        $this->assertEquals($builder->getResultRawData(), $this->getAnalyticsStubResponse());
    }

    public function testGet()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $builder->profileID(1234)
            ->metric('metric1');

        $this->assertEquals($builder->get(), $this->getAnalyticsStubResponse());
    }

    public function testGetOnException()
    {
        $this->expectException(\Exception::class);
        $builder = new Builder(new Model(), $this->getMockedAnalytics());

        $builder->get();
    }

    public function testGetDataByDimensions()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $builder->dimensions([
            'pagePath',
            'date',
        ]);

        $dataArr = $builder->getDataByDimensions([
            '/link/to/site/123',
            '2016-02-03',
            8
        ]);

        $this->assertEquals(
            $dataArr,
            [
                'pagePath' => '/link/to/site/123',
                'date' => '2016-02-03',
                'count' => 8,
            ]
        );
    }

    public function testGetPageCount()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $builder->profileID(1234)
            ->metric('metric1');

        $this->assertEquals(692, $builder->getPageCount());
    }

    public function testGetTotalResults()
    {
        $builder = new Builder(new Model(), $this->getMockedAnalytics());
        $builder->profileID(1234)
            ->metric('metric1');

        $this->assertEquals(345664, $builder->getTotalResults());
    }

    public function testAll()
    {
        $model = new Model();
        $builder = new Builder($model, $this->getMockedAnalytics());

        $reflectionModel = new \ReflectionClass($model);
        $reflectionModelProp = $reflectionModel->getProperty('builder');
        $reflectionModelProp->setAccessible(true);
        $reflectionModelProp->setValue($model, $builder);

        $builder->profileID(1234)
            ->metric('metric1');

        $data = $builder->all();

        $this->assertTrue(is_array($data));
        $this->assertInstanceOf(Model::class, $data[0]);
    }
}
