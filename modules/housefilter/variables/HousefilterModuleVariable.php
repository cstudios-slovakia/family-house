<?php
/**
 * housefilter module for Craft CMS 3.x
 *
 * Filter for houses
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 lostika86
 */

namespace modules\housefilter\variables;

use craft\elements\db\EntryQuery;
use craft\fields\data\OptionData;
use modules\housefilter\HousefilterModule;
use modules\housefilter\services\HouseFilter;

/**
 * housefilter Variable
 *
 * Craft allows modules to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.housefilterModule }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    lostika86
 * @package   HousefilterModule
 * @since     1.0.0
 */
class HousefilterModuleVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Returns filter option.
     *
     * @param string $key
     * @return array
     * @throws \ReflectionException
     */
    public function getFilterInputData(string $key = '') : array
    {
        $checkBoxes = HousefilterModule::$instance->houseFilter->getFilteringProperties()->buildCheckboxes();

        if (empty($key)){
            return $checkBoxes;
        }

        $handle = $this->getPredefinedHandle($key);
        if (! $handle){
            return $checkBoxes;
        }

        if (array_key_exists($handle, $checkBoxes)) {
            return $checkBoxes[$handle];
        }

        return $checkBoxes;
    }

    /**
     * Use this for common handle definition, you only have to know english handle name (step forward for internalization)
     *
     * @param string $key
     * @return mixed
     * @throws \ReflectionException
     */
    public function getPredefinedHandle(string $key)
    {
        $reflect = new \ReflectionClass(HouseFilter::class);
        return $reflect->getConstant('FILTER_' . strtoupper($key));
    }

    /**
     * Returns url query params and build into string.
     * @param bool $withPrefix
     * @return string
     */
    public function getEnabledFilterQueryString(bool $withPrefix = true): string
    {
        $query = HousefilterModule::$instance->filterRequest->getQueryParams()->toArray();

        $queryString    = http_build_query($query);

        /** sometimes we dont need prefix '?', only the params */
        if (! $withPrefix){
            return $queryString;
        }

        return '?'.$queryString;
    }

    /**
     * The filtered house list.
     *
     * @return EntryQuery
     */
    public function getHousesList() : EntryQuery
    {
        return HousefilterModule::$instance->houseFilter->filteringResult();
    }

    /**
     * Get active filter labels list. Builds from current request, and compared with filter owned data.
     * @return array
     * @throws \ReflectionException
     */
    public function getActiveFilterLabelList(): array
    {
        $filterInputData    = $this->getFilterInputData();

        $activeLabels   = collect($filterInputData)->flatten(1)->filter(function (OptionData $optionData) {
            if ($optionData->selected) {
                return $optionData;
            }
        })->transform(function (OptionData $optionData){
            return $optionData->label;
        });

        return $activeLabels->toArray();
    }
}
