<?php namespace modules\housefilter\support\filterables;

use craft\elements\db\EntryQuery;

class BaseFilter
{

    public function filter(string $key, array $value, EntryQuery &$entryQuery)
    {
        if ($key === $this->getFilterKey()) {
            $entryQuery->{$key}($value);
        }
    }

    /**
     * @return string
     */
    public function getFilterKey(): string
    {
        return $this->filterKey;
    }

    /**
     * @param string $filterKey
     */
    public function setFilterKey(string $filterKey)
    {
        $this->filterKey = $filterKey;
    }


}