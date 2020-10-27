<?php namespace modules\housefilter\support\filterables;

use craft\elements\db\EntryQuery;
use modules\housefilter\services\HouseFilter;

class FloorFilter extends BaseFilter
{
    /** @var string */
    protected $filterKey    = HouseFilter::FILTER_FLOOR;


}