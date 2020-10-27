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
class CalculationConstantService extends Component
{
    // Public Methods
    // =========================================================================
    use UsesSheet;
    use WorksWithHouseEntries;

    public function save(array $params = [])
    {

        $this->bootSheetReader();
        $ranges = SheetSettings::calculationConstantRanges();

        $calculationConstantElementsCollection = new CalculationElementsCollection($ranges);
        $calculationConstantElementsCollection->setSheet($sheet = $this->sheet());
        $elementCollections     = $calculationConstantElementsCollection->getHouseCalculationData()->elements;

        $saves = [];
        foreach ($elementCollections as $element) {
            $calculationConstant = new CalculationConstant();
            $calculationConstant->identification = $element->key;
            $calculationConstant->value = $element->value;
            $saves[] = $calculationConstant->save();
        }


    }

    public function getCalculationConstant(string $key = '')
    {
        $calculationConstant = CalculationConstant::find();

        if (!empty($key)) {
            $calculationConstant->where(['identification' => $key]);
        }

        return $calculationConstant->all();
    }


    public function getFormattedCalculationConstants(): Collection
    {
        return collect($this->getCalculationConstant())->transform(function (CalculationConstant $calculationData){
            $calculationDataModel = new CalculationElement();
            $calculationDataModel->key = $key =  $calculationData->identification;
            $calculationDataModel->value = (float) $calculationData->value;

            return $calculationDataModel;
        })->keyBy('key');

    }
}
