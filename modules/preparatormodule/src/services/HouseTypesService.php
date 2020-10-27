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

use craft\base\Component;

use modules\preparatormodule\models\HouseTypeOptionModel;
use modules\preparatormodule\models\HouseTypeSelectModel;
use modules\preparatormodule\records\Options;
use modules\preparatormodule\support\CalculationRequestHelper;
use modules\preparatormodule\support\HouseTypeSelections;
use modules\preparatormodule\support\SheetSettings;
use modules\preparatormodule\support\UsesSheet;
use modules\preparatormodule\records\HouseTypes;
use modules\preparatormodule\support\WorksWithHouseEntries;

class HouseTypesService extends Component implements WorksWithHouseEntriesInterface
{
    use UsesSheet;
    use WorksWithHouseEntries;
    use CalculationRequestHelper;

    public function save()
    {
        $this->beforeSave();

        $this->bootSheetReader();

        $valuableColumnIndexes = SheetSettings::getValuableColumnIndexes($sheet = $this->sheet());

        for ($i = 1; $i <= 6; $i++) {
            $range                          = SheetSettings::getHouseTypeDataCells($i);
            $houseTypeSelections            = new HouseTypeSelections();
            $houseTypeSelections->selectKey = $i;
            $houseTypeSelections->setSheet($sheet);
            $houseTypeSelections->makeSelection($range);
            $selections                     = $houseTypeSelections->getSelections();

            foreach ($selections as $selection) {
                $houseTypeRecord    = new HouseTypes();
                $houseTypeRecord->type_key  = $selection->selectKey;
                $houseTypeRecord->type_name = $selection->selectName;
                $this->addParamsToRecord($this->params, $houseTypeRecord);
                $houseTypeRecord->type_index = $houseTypeSelections->selectKey;

                $houseTypeRecord->save();
                $selectionOptions = $selection->options;

                foreach ($selectionOptions as $columnIndex => $selectionOption) {
                    $option = new Options();

                    $option->is_default = is_string($selectionOption) && $selectionOption === 'D' ? true : false;
                    $option->house_type_id = $houseTypeRecord->id;
                    $option->value = SheetSettings::getNumberedColumnIndex($columnIndex, $valuableColumnIndexes);
                    $option->value_index    = $houseTypeRecord->type_key;
                    $option->siteId = $houseTypeRecord->siteId;
                    $option->save();


                }
            }
        }



    }

    public function getHouseTypeSelections(int $typeNumber = 0)
    {
        $query = HouseTypes::find()->with('options')->where(['house_id' => $this->houseId]);

        if ($typeNumber < 1) {
            $houseTypes     = $query->all();
        } else{
            $houseTypes = $query->andWhere(['type_index' => $typeNumber])->all();
        }

        $housePossibilitiesForTypeIndex = SheetSettings::housePossibilities();
//        $completisation = $this->getRequestHelper()->getParameter('completisation');
        $houseTypeSelectModels = [];
        foreach ($houseTypes as $houseType) {
            $houseTypeSelectModel   = new HouseTypeSelectModel();
            $houseTypeSelectModel->typeKey     = $typeKey =  $houseType->type_key;
            $houseTypeSelectModel->selectLabel     = $houseType->type_name;
            $houseTypeSelectModel->typeIndex     = $houseType->type_index;
            $houseTypeSelectModel->id   = $houseType->id;
            $houseTypeSelectModel->options = [];
            $houseTypeSelectModel->possibility = $housePossibilitiesForTypeIndex[$typeKey];
            foreach ($houseType->options as $option) {

                $houseTypeOptionModel   = new HouseTypeOptionModel();
                $houseTypeOptionModel->selected     = $option->is_default;
                $houseTypeOptionModel->text         = $houseTypeSelectModel->selectLabel;
                $houseTypeOptionModel->value        = $option->value;
                $houseTypeSelectModel->options[]     = $houseTypeOptionModel;

            }
            if (!empty($houseTypeSelectModel->options)) {
                $houseTypeSelectModels[$houseType->type_index][]    = $houseTypeSelectModel;
            }
        }
        $houseTypeSelectModels = collect($houseTypeSelectModels[$typeNumber])->keyBy(function ($houseTypeSelectModel){
            return $houseTypeSelectModel->typeKey;
        });

        $sortedProperties = SheetSettings::sortedVariantsByPosition();

        $keys = $sortedProperties->keys();
        $types = [];
        foreach ($keys as $propertyPositionNumber) {
            if ($houseTypeSelectModels->has($propertyPositionNumber)) {
                $houseTypeSelectModel = $houseTypeSelectModels->get($propertyPositionNumber);

//                if ($houseTypeSelectModel->possibility == $completisation){

                    $types[] = $houseTypeSelectModel;

//                }
            }
        }

//dd($types);
        return $types;

    }

    public function clearById(int $id): int
    {
        return  HouseTypes::deleteAll(['house_id' => $id]);
    }
}
