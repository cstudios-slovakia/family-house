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
use modules\preparatormodule\records\Customs;
use modules\preparatormodule\records\HouseVersion;
use modules\preparatormodule\support\CalculationElement;
use modules\preparatormodule\support\CalculationElementsCollection;
use modules\preparatormodule\support\CalculationRequestHelper;
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
class CalculationCustomsService extends Component implements WorksWithHouseEntriesInterface
{
    use UsesSheet;
    use WorksWithHouseEntries;
    use CalculationRequestHelper;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->getRequestHelper();
    }

    public function save()
    {
        $this->beforeSave();
        $this->bootSheetReader();
        $range = SheetSettings::getHousePossibilities();


        $sheet = $this->sheet();

        $cells = $sheet->rangeToArray($range, null, true, true, false);
        $possibilities = [];
        collect($cells[0])->map(function ($cell, $rowIndex) use (&$possibilities){
            if (!empty($cell)) {
                $possibilities[] = (int) $cell;
            }
        });

        $calculationCustoms = new Customs();
        $calculationCustoms->identification = $calculationCustoms::HOUSE_POSSIBILITIES;
        $calculationCustoms->params = json_encode($possibilities);
        $this->addParamsToRecord($this->params,$calculationCustoms);
        $calculationCustoms->save();

    }

    public function getHousePossibilities(): array
    {

        $custom = collect($this->getCustomDatas())->filter(function (Customs $customs) {
            if ($customs->identification === $customs::HOUSE_POSSIBILITIES) {
                return $customs;
            }
        })->first();

        if (!$custom) {
            return [];
        }

        return json_decode($custom->params);
    }


    public function getCustomDatas() : array
    {
        $houseId = (int) $this->getRequestHelper()->getParameter('houseId');
        return Customs::find()->where(['house_id' => $houseId])->all();
    }
    



    public function clearById(int $id): int
    {
        return Customs::deleteAll(['house_id' => $id]);
    }
}
