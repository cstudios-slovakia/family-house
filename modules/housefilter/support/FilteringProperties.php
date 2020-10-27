<?php namespace modules\housefilter\support;

use craft\fields\data\OptionData;
use Illuminate\Support\Collection;

class FilteringProperties
{
    /** @var Collection */
    protected $params;

    /**
     * Properties comes from settings entry.
     * @var Collection
     */
    protected $properties;

    public function __construct(Collection $params, Collection $filterfields)
    {
        $this->params       = $params;
        $this->properties   = $filterfields;
    }

    /**
     * Builds checkboxes with selected states. States are defined from url params.
     *
     * @return array
     */
    public function buildCheckboxes(): array
    {
        $checkBoxes     = [];
        $this->properties->each(function ($property,$key) use (&$checkBoxes){
            $checkBoxes[$key] = $this->buildSelectState($key)->toArray();
        });

        return $checkBoxes;
    }

    /**
     * Defines selected option states on checkbox options
     *
     * @param string $key
     * @return Collection
     */
    protected function buildSelectState(string $key) : Collection
    {
        $paramsForKey = $this->getParam($key);
        $selectTypeOptions = $this->selectOptions($key);

        if (is_null($paramsForKey)) {
            return $selectTypeOptions;
        }


        $selectTypeOptions->map(function (OptionData $option) use ($paramsForKey) {
            if (in_array($option->value, $paramsForKey, true)) {
                $option->selected = true;
            }
        });

        return $selectTypeOptions;
    }

    /**
     * Get params for key.
     *
     * @param string $key
     * @return array|null
     */
    protected function getParam(string $key) : ?array
    {
        return $this->params->get($key);
    }

    /**
     * Returns options for given key.
     *
     * @param string $key
     * @return Collection
     */
    protected function selectOptions(string $key): Collection
    {
        $options = $this->properties->get($key)->getOptions();

        return collect($options)->where('value','!==', 'empty');
    }


}