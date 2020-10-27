<?php namespace modules\housefilter\support\filterables;

use craft\elements\db\EntryQuery;
use modules\housefilter\services\HouseFilter;

class EligibleFilter extends BaseFilter
{
    /** @var string */
    protected $filterKey    = HouseFilter::FILTER_ELIGIBLE;

    public function filter(string $key, array $value, EntryQuery &$entryQuery)
    {
        if ($key === $this->getFilterKey()) {
            foreach ($value as $v) {
                $entryQuery->search($key.':'.$v);
            }
        }
    }

}