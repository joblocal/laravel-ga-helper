<?php

namespace Tests;

use Joblocal\Laravel\GAHelper\Models\Model;

class ModelTest extends TestCase
{
    public function testSetGetAttribute()
    {
        $value = 'test123';
        $attrName = 'test';

        $model = new Model();
        $model->setAttribute($attrName, $value);

        $this->assertEquals($value, $model->getAttribute($attrName));
    }

    public function testMagicGetterSetter()
    {
        $value = 'test123';
        $attrName = 'test';

        $model = new Model();
        $model->$attrName = $value;

        $this->assertEquals($value, $model->$attrName);
    }

    public function testFill()
    {
        $data = [
            'test' => 'test123',
        ];

        $model = new Model();
        $model->fill($data);

        $this->assertEquals($data, $model->getAttributes());
    }

    public function testToModel()
    {
        $data = [
            'test' => 'test123',
        ];

        $model = new Model();
        $model->fill($data);

        $test = $model->toModel(Model::class);

        $this->assertInstanceOf(Model::class, $test);
    }

    public function testAllOnException()
    {
        $this->expectException(\Exception::class);
        $model = new Model();
        $model->all();
    }
}
