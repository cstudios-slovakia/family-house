<?php
namespace modules\housefilter\services;

use Craft;
use craft\base\Component;
use Illuminate\Support\Collection;
use modules\housefilter\HousefilterModule;

class SearchRequest extends Component
{
    CONST SEARCH_KEY = 'q';

    /**
     * Checks sort param value in the request
     *
     * @return string|null
     *
     */
    public function searchValue(): ?string
    {
        $params     = collect(Craft::$app->request->getQueryParams());
        if ($params->has($key = self::SEARCH_KEY)) {
            return $params->get($key);
        }
        return null;
    }

}