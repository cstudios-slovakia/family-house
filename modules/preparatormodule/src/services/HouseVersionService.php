<?php
/**
 * preparator module for Craft CMS 3.x
 *
 * Excel reader and preparator
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\preparatormodule\services;

use craft\errors\ElementNotFoundException;
use modules\preparatormodule\PreparatorModule;

use Craft;
use craft\base\Component;
use modules\preparatormodule\records\HouseVersion;
use modules\preparatormodule\records\VariantPrices;
use modules\preparatormodule\records\Variants;
use modules\preparatormodule\support\HousePriceOptions;
use modules\preparatormodule\support\HousePrices;
use modules\preparatormodule\support\HouseVersionSelections;
use modules\preparatormodule\support\SheetSettings;
use modules\preparatormodule\support\UsesSheet;
use modules\preparatormodule\support\WorksWithHouseEntries;
use yii\log\Logger;

/**
 * HouseTypes Service
 *
 * All of your module’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other modules can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    @lostika
 * @package   PreparatorModule
 * @since     1.0.0
 */
class HouseVersionService extends Component implements WorksWithHouseEntriesInterface
{
    use UsesSheet;
    use WorksWithHouseEntries;
    // Public Methods
    // =========================================================================

    public function save()
    {
        $this->beforeSave();

        $this->bootSheetReader();

        $range = SheetSettings::getHouseVersionsDataCells();
        $reader = new HouseVersionSelections();
        $reader->setSheet($sheet = $this->sheet());
        $valuableColumnIndexes = SheetSettings::getValuableColumnIndexes($sheet, 'C','H');

        $pricesData  = $reader->getHouseVersion();
        foreach ($pricesData->selections as $houseVersionOptions) {

            foreach ($houseVersionOptions->options as $columnIndex => $price) {
                $houseVersion = new HouseVersion();
                $houseVersion->possibility = $houseVersionOptions->possibility;
                $houseVersion->version_number = array_search($columnIndex, $valuableColumnIndexes, true);

                $houseVersion->price = $price;
                $this->addParamsToRecord($this->params, $houseVersion);
                $houseVersion->save();
            }

        }


    }


    public function clearById(int $id) : int
    {
        return HouseVersion::deleteAll(['house_id' => $id]);
    }

    public function versionPricesForNumber(int $versionNumber) : array
    {
        $versions = HouseVersion::find()->where(['version_number' => $versionNumber])->all();

        return collect($versions)->keyBy('possibility')->map(function ($version) {
           return $version->price;
        })->toArray();
    }
}
