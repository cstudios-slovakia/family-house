<?php namespace modules\housefilter\support\filterables;

use craft\elements\db\EntryQuery;
use modules\housefilter\services\HouseFilter;

class GarageFilter extends BaseFilter
{
    /** @var string */
    protected $filterKey    = HouseFilter::FILTER_GARAGE;
}