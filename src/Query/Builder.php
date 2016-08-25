<?php

namespace Joblocal\Laravel\GAHelper\Query;

use Joblocal\Laravel\GAHelper\Models\Model;

class Builder
{
    /**
     * Analytic api
     * @var mixin
     */
    protected $analytics;

    /**
     * @var Model
     */
    protected $model;

    /**
     * The raw response from ga
     * @var mixin
     */
    protected $results;

    protected $profile_id;
    protected $from = 'yesterday';
    protected $to = 'yesterday';
    protected $metric;
    protected $dimensions = [];
    protected $max_results = 1000;
    protected $offset = null;
    protected $page = null;

    public function __construct(Model $model, $analytics)
    {
        $this->analytics = $analytics;
        $this->model = $model;
    }

    /**
     * Set the profile id for ga
     * @param  int    $profile_id
     * @return Builder
     */
    public function profileID(int $profile_id)
    {
        $this->profile_id = $profile_id;
        return $this;
    }

    /**
     * Set the from date (2016-01-01 / yesterday)
     * @param  string $from
     * @return Builder
     */
    public function from(string $from = 'yesterday')
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Set the to date (2016-01-01 / yesterday)
     * @param  string $to
     * @return Builder
     */
    public function to(string $to = 'yesterday')
    {
        $this->to = $to;
        return $this;
    }

    /**
     * Set the metric
     * @param  string $metric
     * @return Builder
     */
    public function metric(string $metric)
    {
        $this->metric = $metric;
        return $this;
    }

    /**
     * Set the dimensions
     * @param  array  $dimensions
     * @return Builder
     */
    public function dimensions(array $dimensions)
    {
        $this->dimensions = $dimensions;
        return $this;
    }

    /**
     * Set the maximum results of the request (ga max. 10000)
     * @param  integer $max_results
     * @return Builder
     */
    public function maxResults(int $max_results = 1000)
    {
        $this->max_results = $max_results;
        return $this;
    }

    /**
     * Set the offset
     * @param integer $offset
     * @return Builder
     */
    public function offset(int $offset = 1)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Set the page (calculate the offset with the maxResults)
     * @param  integer $page
     * @return Builder
     */
    public function page(int $page = 1)
    {
        $this->page = $page;
        return $this;
    }

    public function getResultRawData()
    {
        if (!$this->results) {
            $this->results = $this->get();
        }
        return $this->results;
    }

    /**
     * Fetch the data from GA
     * @return Builder
     */
    public function get()
    {
        if (!isset($this->profile_id) || !isset($this->metric)) {
            throw new \Exception('ProfileID and Metric must be set.');
        }
        if ($this->page) {
            $this->offset = ($this->page * $this->max_results) + 1;
        }

        $requestData = array_filter([
            'start-index' => $this->offset ?? false,
            'max-results' => $this->max_results,
        ]);
        if (count($this->dimensions) > 0) {
            $dimensions = [];
            foreach ($this->dimensions as $value) {
                $dimensions[] = 'ga:' . $value;
            }
            $requestData['dimensions'] = implode(',', $dimensions);
        }
        return $this->analytics->data_ga->get(
            'ga:' . $this->profile_id,
            $this->from,
            $this->to,
            'ga:' . $this->metric,
            $requestData
        );
    }

    /**
     * Returns an array of model objects
     * @param  boolean $refresh
     * @return Model[]
     */
    public function all(bool $refresh = false)
    {
        return $this->model->all($refresh);
    }

    /**
     * Get the total results
     * @return int
     */
    public function getTotalResults()
    {
        $data = $this->getResultRawData();
        return $data->totalResults;
    }

    /**
     * Get the count of pages
     * @return int
     */
    public function getPageCount()
    {
        $data = $this->getResultRawData();
        return ceil($this->getTotalResults() / $data->itemsPerPage);
    }

    /**
     *
     * Returns the data of one row in a key value pair with the dimensions name as key.
     * @param  array  $data
     * @return array
     */
    public function getDataByDimensions(array $data)
    {
        $return = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->dimensions)) {
                $return[$this->dimensions[$key]] = $value;
            } else {
                $return['count'] = $value;
            }
        }
        return $return;
    }
}
