<?php
namespace modules\housefilter\services;

use Craft;
use craft\base\Component;
use Illuminate\Support\Collection;
use modules\housefilter\HousefilterModule;

class FilterRequest extends Component
{
    /** @var Collection */
    protected $params;

    /**
     * Get allowed query parameters from query
     *
     * @return Collection
     */
    public function getQueryParams() : Collection
    {
        $params     = Craft::$app->request->getQueryParams();

        $enabledfilterables     = $this->enabledParams()->toArray();

        return $this->params = collect($params)->filter(function ($param, $key) use ($enabledfilterables){

            if (in_array($key, $enabledfilterables, true)) {
                return $param;
            }
        });

    }

    public function checkParam(string $key): bool
    {
        if (!$this->params){
            return  false;
        }

        if (!$this->enabledParams()->has($key)){
            return  false;
        }
        return $this->params->has($key);
    }

    public function getQueryParam(string $key): array
    {
        if (!$this->checkParam($key)) {
            return [];
        }

        return $this->params->get($key);
    }


    protected function enabledParams(): Collection
    {
        return collect(HousefilterModule::$instance->houseFilter->availableFilterableKeys());
    }
}