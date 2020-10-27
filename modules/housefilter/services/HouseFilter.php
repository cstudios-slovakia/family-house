<?php
/**
 * housefilter module for Craft CMS 3.x
 *
 * Filter for houses
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 lostika86
 */

namespace modules\housefilter\services;

use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use Illuminate\Support\Collection;
use modules\housefilter\HousefilterModule;

use Craft;
use craft\base\Component;
use modules\housefilter\support\filterables\ConstructionFilter;
use modules\housefilter\support\filterables\EligibleFilter;
use modules\housefilter\support\filterables\FloorFilter;
use modules\housefilter\support\filterables\GarageFilter;
use modules\housefilter\support\filterables\RoofFilter;
use modules\housefilter\support\filterables\RoomFilter;
use modules\housefilter\support\filterables\SquareFilter;
use modules\housefilter\support\filterables\TypeFilter;
use modules\housefilter\support\FilteringProperties;

/**
 * HouseFilter Service
 *
 * All of your module’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other modules can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    lostika86
 * @package   HousefilterModule
 * @since     1.0.0
 */
class HouseFilter extends Component
{
    // Public Methods
    // =========================================================================
    CONST SECTION_ENTRY_HANDLE = 'dom';
    CONST SECTION_SECTION_HANDLE = 'nastavenia';

    CONST FILTER_TYPE       = 'typ';
    CONST FILTER_FLOOR      = 'podlaznost';
    CONST FILTER_ELIGIBLE   = 'volitelne';
    CONST FILTER_GARAGE     = 'garaz';
    CONST FILTER_ROOM       = 'izby';
    CONST FILTER_SQUARE     = 'metraz';
    CONST FILTER_CONSTRUCTION     = 'konstrukcia';
    CONST FILTER_ROOF       = 'strecha';


    /** @var Collection|null */
    protected $filterables = null;

    /** @var null|array */
    protected $filteringProperties = null;

    public function filteringResult() : EntryQuery
    {
        $this->filterables = $requestedFilterableParameters = HousefilterModule::$instance->filterRequest->getQueryParams();

        $sectionQuery    = $this->sectionQuery();

        $this->searchQuery($sectionQuery);

        $mappedFilters  = $this->filterMap();

        foreach ($this->filterables as $filterableKey => $filterableValue) {
            $this->loadFilter($filterableKey, $filterableValue, $mappedFilters, $sectionQuery);
        }

        $this->resultSorting($sectionQuery);

        return $sectionQuery->limit($this->filterLimit());
    }

    protected function filterLimit(): int
    {
        return (int) Craft::$app->globals->getSetByHandle('settings')->getFieldValue('houseListCountListView');
    }

    protected function resultSorting(EntryQuery &$query)
    {
        if ($sortBy = HousefilterModule::$instance->sortingRequest->sortParam()){
            $query->orderBy($sortBy);
        }
    }

    protected function searchQuery(EntryQuery &$query)
    {
        if ($q = HousefilterModule::$instance->searchRequest->searchValue()){
            $query->search($q);
            $query->orderBy('score');
        }
    }

    public function getAll() : array
    {
        return $this->sectionQuery()->all();
    }

    protected function sectionQuery() : EntryQuery
    {
        return Entry::find()->section(self::SECTION_ENTRY_HANDLE);
    }

    public function getFilteringProperties(): FilteringProperties
    {
        // there are selected fields defined in request
        $requestedFilterableParameters = HousefilterModule::$instance->filterRequest->getQueryParams();

        return $this->filteringProperties = new FilteringProperties($requestedFilterableParameters, $this->filterFields());
    }

    protected function availableFilterableFields(): array
    {
        $settingsEntry    = Entry::find()->section(self::SECTION_SECTION_HANDLE)->one();

        if (! $settingsEntry){
            return [];
        }
        return $settingsEntry->fieldValues;
    }

    public function availableFilterableKeys(): array
    {
        return $this->filterFields()->keys()->toArray();
    }

    /**
     * Returns all filter fields from 'settings' section
     *
     * @return Collection
     */
    protected function filterFields(): Collection
    {
        return collect($this->availableFilterableFields());
    }

    protected function loadFilter(string $filterableKey,array $filterableValue,array $mappedFilters,EntryQuery &$queryBuilder)
    {
        if (array_key_exists($filterableKey, $mappedFilters)) {
            $filterable = Craft::createObject($mappedFilters[$filterableKey]);
            $filterable->filter($filterableKey, $filterableValue, $queryBuilder);
        }
    }

    /**
     * Maps filter keys to filter logic
     * @return array
     */
    protected function filterMap(): array
    {
        $map    = [
            self::FILTER_TYPE               => TypeFilter::class,
            self::FILTER_FLOOR              => FloorFilter::class,
            self::FILTER_SQUARE             => SquareFilter::class,
            self::FILTER_ROOM               => RoomFilter::class,
            self::FILTER_ROOF               => RoofFilter::class,
            self::FILTER_GARAGE             => GarageFilter::class,
            self::FILTER_ELIGIBLE           => EligibleFilter::class,
            self::FILTER_CONSTRUCTION       => ConstructionFilter::class,
        ];

        return $map;
    }

    /**
     * @return Collection|null
     */
    public function getFilterables(): ?Collection
    {
        return $this->filterables;
    }


}
