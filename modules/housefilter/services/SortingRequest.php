<?php
namespace modules\housefilter\services;

use Craft;
use craft\base\Component;

class SortingRequest extends Component
{
    CONST SORT_KEY = 'sort';

    /**
     * Checks sort param value in the request
     *
     * @return string|null
     *
     */
    public function sortParam(): ?string
    {
        $params     = collect(Craft::$app->request->getQueryParams());
        if ($params->has($key = self::SORT_KEY)) {
            return $params->get($key);
        }
        return null;
    }

}