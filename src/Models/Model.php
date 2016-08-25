<?php

namespace Joblocal\Laravel\GAHelper\Models;

use Joblocal\Laravel\GAHelper\Query\Builder;

class Model
{
    /**
     * attributes
     * @var array
     */
    protected $attributes = [];

    /**
     * Google analytics query builder.
     * @var Builder
     */
    protected $builder;

    /**
     * @var Model[]
     */
    protected $resultData;

    /**
     * @param  $method
     * @param  $parameters
     * @return mixin
     */
    public function __call($method, $parameters)
    {
        if (!isset($this->builder)) {
            $this->builder = new Builder($this, app('googleAnalytics'));
        }

        return call_user_func_array([$this->builder, $method], $parameters);
    }

    /**
     * @param   $method
     * @param   $parameters
     * @return  mixin
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = new static;

        return call_user_func_array([$instance, $method], $parameters);
    }

    /**
     * Dynamically get attributes
     *
     * @param mixed $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically sets attributes
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * set an attribute's value by it's key
     *
     * @param string $key
     * @param mixed $value
     */
    public function setAttribute(string $key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * get the attribute value
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * returns all attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * fills attributes
     *
     * @param array $attributes
     * @return Model
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Return the data in a new model
     * @param  string $modelClass
     * @return mixin
     */
    public function toModel(string $modelClass)
    {
        $model = new $modelClass();
        $model->fill($this->attributes);
        return $model;
    }

    /**
     * Get data array of the result models
     * @param  boolean $refresh Execute the get method again
     * @return Model[]
     */
    public function all(bool $refresh = false)
    {
        if ($refresh || !$this->resultData) {
            $this->resultData = [];

            if (!$this->builder) {
                throw new \Exception("Builder not initialized!");
            }
            $data = $this->builder->getResultRawData();

            foreach ($data->rows as $row) {
                $entry = new static();
                $entry->fill($this->builder->getDataByDimensions($row));

                $this->resultData[] = $entry;
            }
        }

        return $this->resultData;
    }
}
