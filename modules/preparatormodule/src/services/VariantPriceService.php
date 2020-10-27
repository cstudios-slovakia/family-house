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
use modules\preparatormodule\records\VariantPrices;
use modules\preparatormodule\records\Variants;
use modules\preparatormodule\support\CalculationElement;
use modules\preparatormodule\support\CalculationElementsCollection;
use modules\preparatormodule\support\CalculationRequestHelper;
use modules\preparatormodule\support\ExcelReader;
use modules\preparatormodule\support\HousePriceOptions;
use modules\preparatormodule\support\HousePrices;
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
class VariantPriceService extends Component implements WorksWithHouseEntriesInterface
{
    // Public Methods
    // =========================================================================
    use UsesSheet;
    use WorksWithHouseEntries;
    use CalculationRequestHelper;

    public function save()
    {
        $this->beforeSave();
        $this->bootSheetReader();

        $range = SheetSettings::getHousePricesRange();

        $variantPriceReader = new HousePrices();
        $variantPriceReader->setSheet($sheet = $this->sheet());
        $pricesData  = $variantPriceReader->getHousePrices();

        $valuableColumnIndexes = SheetSettings::getValuableColumnIndexes($sheet);

        foreach ($pricesData->selections as $data) {
            /** @var HousePriceOptions $data */
            $variant = new Variants();
            $variant->variant_index     = $data->index;
            $variant->variant_possibility_key   = $data->possibility;
            $this->addParamsToRecord($this->params,$variant);
            $variant->save();
            foreach ($data->options as $columnIndex =>  $priceOption) {
                $variantPrice = new VariantPrices();
                $variantPrice->price        = $priceOption;
                $variantPrice->variant_id   = $variant->id;
                $variantPrice->price_index = SheetSettings::getNumberedColumnIndex($columnIndex, $valuableColumnIndexes);
                $variantPrice->siteId = $variant->siteId;

                $variantPrice->save();

            }
        }
    }

    public function priceVariants(): Collection
    {

        $houseId = $this->houseId = $this->getRequestHelper()->getParameter('houseId');

        $variants = Variants::find()->with('pricesOptions')->where(['house_id' => $houseId])->all();

        return collect($variants);

    }



    public function clearById(int $id): int
    {
        $deletedRecords = 0;
        $variants   = Variants::findAll(['house_id' => $id]);

        foreach ($variants as $variant) {
            foreach ($variant->pricesOptions as $pricesOption) {
                $pricesOption->delete();
            }

            $deletedRecords += (int) $variant->delete();
        }
        return  $deletedRecords;
    }
}
