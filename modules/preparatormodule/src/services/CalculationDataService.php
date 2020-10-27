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


use Illuminate\Support\Collection;
use modules\preparatormodule\models\CalculationDataModel;
use modules\preparatormodule\PreparatorModule;

use Craft;
use craft\base\Component;
use modules\preparatormodule\records\CalculationConstant;
use modules\preparatormodule\records\CalculationData;
use modules\preparatormodule\records\HouseVersion;
use modules\preparatormodule\support\CalculationElement;
use modules\preparatormodule\support\CalculationElementsCollection;
use modules\preparatormodule\support\ExcelReader;
use modules\preparatormodule\support\SheetReader;
use modules\preparatormodule\support\SheetSettings;
use modules\preparatormodule\support\UsesSheet;
use modules\preparatormodule\support\WorksWithHouseEntries;

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
class CalculationDataService extends Component implements WorksWithHouseEntriesInterface
{
    use UsesSheet;
    use WorksWithHouseEntries;

    public function save()
    {
        $this->beforeSave();
        $this->bootSheetReader();
        $ranges = SheetSettings::calculationStaticalRanges();

        $calculationConstantElementsCollection = new CalculationElementsCollection($ranges);
        $calculationConstantElementsCollection->setSheet($sheet = $this->sheet());
        $elementCollections     = $calculationConstantElementsCollection->getHouseCalculationData()->elements;

        $saves = [];

        foreach ($elementCollections as $element) {
            $calculationElement = $this->getCalculationDataModel();
            $calculationElement->identification = $element->key;
            // TODO remove default values
            $calculationElement->value = is_string($element->value) ? 1 : $element->value;

            $this->addParamsToRecord($this->params, $calculationElement);

            $saves[] = $calculationElement->save();
        }

        // TODO  check false saves
    }

    public function getFormattedCalculationDatas(): Collection
    {
        $sheetFormulas = SheetSettings::calculationFormulas();

        return collect($this->getCalculationDatas())->transform(function (CalculationData $calculationData) use ($sheetFormulas){
            $calculationDataModel = new CalculationElement();
            $calculationDataModel->key = $key =  $calculationData->identification;
            $calculationDataModel->value = (float) $calculationData->value;
            if (array_key_exists($key, $sheetFormulas)) {
                $calculationDataModel->formula = $sheetFormulas[$key];
            }
            return $calculationDataModel;
        })->keyBy('key');
    }


    public function getCalculationDatas() : array
    {
        return CalculationData::find()->where(['house_id' => $this->houseId])->all();
    }
    

    public function getCalculationDataModel()
    {
        return new CalculationData();
    }

    public function clearById(int $id): int
    {
        return CalculationData::deleteAll(['house_id' => $id]);
    }
}
