<?php namespace modules\preparatormodule\support;

use Craft;
use craft\db\ActiveRecord;
use craft\errors\ElementNotFoundException;
use yii\log\Logger;

trait WorksWithHouseEntries
{
    /** @var int */
    public $houseId;

    /**
     * @var array
     */
    public $params = [];

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;

        if (array_key_exists('house_id', $this->params)) {
            $this->houseId = $params['house_id'];
        }
    }

    public function addParamsToRecord(array $params, ActiveRecord &$activeRecord)
    {
        foreach ($params as $param => $value) {
            $activeRecord->{$param} = $value;
        }
    }

    /**
     * @return int
     */
    public function getHouseId(): int
    {
        return $this->houseId;
    }

    /**
     * @param int $houseId
     */
    public function setHouseId(int $houseId): void
    {
        $this->houseId = $houseId;
    }

    public function clearTableForHouseId() : bool
    {
        if (!isset($this->houseId)) {
            throw new ElementNotFoundException('House ID is not set correctly.');
        }
        return $this->clearById($this->houseId) > 0;
    }

    public function beforeSave()
    {
        try {
            $this->clearTableForHouseId();
        } catch (\Exception $exception) {
            Craft::getLogger()->log([
                'HouseVersions for house id can not be deleted',
                'houseID' => $this->houseId
            ],Logger::LEVEL_ERROR);
        }
    }
}