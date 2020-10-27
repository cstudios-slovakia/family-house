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
use modules\preparatormodule\records\Energy;
use modules\preparatormodule\records\EnergyPrices;
use modules\preparatormodule\records\VariantPrices;
use modules\preparatormodule\records\Variants;
use modules\preparatormodule\support\CalculationElement;
use modules\preparatormodule\support\CalculationElementsCollection;
use modules\preparatormodule\support\CalculationRequestHelper;
use modules\preparatormodule\support\ExcelReader;
use modules\preparatormodule\support\HouseEnergyCalculation;
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
class EnergyPriceService extends Component implements WorksWithHouseEntriesInterface
{
    // Public Methods
    // =========================================================================
    use UsesSheet;
    use WorksWithHouseEntries;
    use CalculationRequestHelper;

    private $energies;
    private $choosedEnergyTypes = [];
    private $selectorsIndexer = [];
    /** @var Collection */
    private $calculationElements;

    public function save()
    {
        $this->beforeSave();
        $this->bootSheetReader();

        $energyPriceReader = new HouseEnergyCalculation();
        $energyPriceReader->setSheet($sheet = $this->sheet());
        $pricesData  = $energyPriceReader->getHouseEnergy();

        $valuableColumnIndexes = SheetSettings::getValuableColumnIndexes($sheet);

        foreach ($pricesData->selections as $data) {
            /** @var HouseEnergyCalculation $data */

            $energy = new Energy();
            $energy->energy_index     = $data->index;
            $energy->energy_choosed_key   = $data->key;
            $this->addParamsToRecord($this->params, $energy);
            $energy->save();
            foreach ($data->options as $columnIndex =>  $priceOption) {
                $energyPrices = new EnergyPrices();
                $energyPrices->price        = $priceOption;
                $energyPrices->energy_id   = $energy->id;
                $energyPrices->energy_price_index  = (integer) array_keys($valuableColumnIndexes,$columnIndex)[0];
                $energyPrices->siteId = $energy->siteId;

                $energyPrices->save();

            }
        }
    }

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->getRequestHelper();
    }

    /**
     * Defines energy class for an energy value.
     * @param float $energyValue
     * @return string
     */
    public static function getEnergyClass(float $energyValue) : string
    {
        $classTable = SheetSettings::houseEnergyCategory();
        $energyClass = 'G';

        collect($classTable)->reverse()->each(function ($value,$category) use(&$energyClass,$energyValue) {
            if ($energyValue < $value) {
                $energyClass = $category;
            }
        });

        return $energyClass;
    }

    /**
     * Calculated energy values by their keys. Keys are defined in SheetSettings::energyStaticIndexes.
     *
     * @return array
     */
    public function calculateEnergyVariants() : array
    {
        $choosedIndexed = $this->choosedEnergyTypes;
        $selectorIndexer = $this->selectorsIndexer;
        $energyIndexes  = SheetSettings::energyStaticIndexes();
        /** @var EnergyPriceService $energyService */
//        $energyService = PreparatorModule::$instance->energyPriceService;

        $calculatedEnergyDetails = [];
        foreach ($energyIndexes as $energyIndexKey) {
            $energiesForKey  = $this->getEnergiesForChoosedKey($energyIndexKey);

            $calculatedEnergyDetails[$energyIndexKey] = $this->sumEnergyValues($energiesForKey,$choosedIndexed,$selectorIndexer);

        }
        return $calculatedEnergyDetails;
    }

    public function sumEnergyValues(Collection $energiesCollection, array $indexedChoosedTypes, array $selectorIndexer)
    {

        return $energiesCollection->keyBy('energy_index')->map(function (Energy $energy) use ($indexedChoosedTypes, $selectorIndexer) {

            $energyIndex = (int) $energy->energy_index;


            $index = array_search($energyIndex, $selectorIndexer);
            $choosedEnergyOption = $indexedChoosedTypes[$index];


            $energyPrices   = collect($energy->energyPrices)->filter(function (EnergyPrices $energyPrices) use ($choosedEnergyOption,$indexedChoosedTypes ) {
//                var_dump($choosedEnergyOption);
                if ($energyPrices->energy_price_index === $choosedEnergyOption) {
                    return $energyPrices;
                }

            });
//                var_dump($energyPrices);
            return $energyPricesSum = $energyPrices->sum('price');

        })->sum();
    }

    /**
     * @param string $choosedKey
     * @return Collection
     */
    public function getEnergiesForChoosedKey(string $choosedKey): Collection
    {
        $energies = $this->groupEnergiesByChoosedKey($this->loadEnergyPricesTableData());

        if ($energies->has($choosedKey)) {
            return $energies->get($choosedKey);
        }

        return collect([]);
    }

    /**
     * Generetes collection by 'energy_choosed_key' attribute.
     * @param Collection $energiesData
     * @return Collection
     */
    protected function groupEnergiesByChoosedKey(Collection $energiesData) : Collection
    {
        return $energiesData->groupBy('energy_choosed_key');
    }

    /**
     * Energy prices table is defined in excel. It looks like as a raw data for other purposes.
     *
     * @return Collection
     */
    public function loadEnergyPricesTableData() : Collection
    {
        if ($energies = $this->energies) {
            return $energies;
        }

        $houseId = (int) $this->getRequestHelper()->getParameter('houseId');

        $energies = Energy::find()
            ->with('energyPrices')
            ->where(['house_id' => $houseId])
            ->all();

        return $this->energies = collect($energies);
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

    public function clearById(int $id): int
    {
        $deletedRecords = 0;
        $energies   = Energy::findAll(['house_id' => $id]);

        foreach ($energies as $energy) {
            foreach ($energy->energyPrices as $energyPrice) {
                $energyPrice->delete();
            }

            $deletedRecords += (int) $energy->delete();
        }
        return  $deletedRecords;
    }

    public function getCalculatedEnergyOnKey(string $onKey)
    {
        $version = $this->getEnergyVersionNumber();

//        $requestService = PreparatorModule::$instance->calculationRequestService;

        $houseId = $this->houseId = $this->requestHelper->getParameter('houseId');

        /** @var CalculationConstantService */
        $constantsInstance  = PreparatorModule::$instance->calculationConstantService;

        /** @var CalculationDataService $dataInstance */
        $dataInstance       = PreparatorModule::$instance->calculationDataService;

        $dataInstance->setHouseId($houseId);

        if (!isset($this->calculationElements)) {
            $constants  = $constantsInstance->getFormattedCalculationConstants();
            $data       = $dataInstance->getFormattedCalculationDatas();
            $this->calculationElements = $data->merge($constants);
        }

        $formulasCollection = collect(SheetSettings::calculationFormulas());

        $flow   = SheetSettings::calculationFlow();

//        /** @var EnergyPriceService $energyService */
//        $energyService = PreparatorModule::$instance->energyPriceService;
        $calculatedEnergyDetails =  $this->calculateEnergyVariants();
        $parsedEnergyStaticIndexesWithCalculationKeys = SheetSettings::parseEnergyStaticIndexesWithCalculationKeys();
//        dd($parsedEnergyStaticIndexesWithCalculationKeys);
        foreach ($parsedEnergyStaticIndexesWithCalculationKeys as $calcKey => $energyChoosedKey) {

            if ($this->calculationElements->has($calcKey)) {
                $this->calculationElements->get($calcKey)->value = $calculatedEnergyDetails[$energyChoosedKey];
            }
        }

        foreach ($flow as $key) {
            if (! $this->calculationElements->has($key)) {
                $calcElement    = new CalculationElement();

                $formula = $formulasCollection->get($key, '');

                if (is_array($formula)) {
                    $formula = $formula[$version];
                }

                $calcElement->formula = $formula;
                $calcElement->key       = $key;
                $calcElement->elements = $this->calculationElements;

                $this->calculationElements->put($key, $calcElement);
            }
        }

        return $this->calculationElements->get($onKey)->getValue();
    }

    public function getEnergyVersionNumber(): int
    {
        $requestService = PreparatorModule::$instance->calculationRequestService;

        $selectorsIndexer   = $requestService->getParameter('selectorsIndexer');
        if (!empty($selectorsIndexer)) {

            $selectorsOnTypeNumber   = $this->choosedEnergyTypes;

            $index = array_search(6, $selectorsIndexer);

            return $selectorsOnTypeNumber[$index];
        }
        return 1;
    }

    /**
     * @param array $choosedEnergyTypes
     */
    public function setChoosedEnergyTypes(array $choosedEnergyTypes): void
    {
        $this->choosedEnergyTypes = $choosedEnergyTypes;
    }

    /**
     * @return Collection
     */
    public function getCalculationElements(): Collection
    {
        return $this->calculationElements;
    }

    /**
     * @return array
     */
    public function getSelectorsIndexer(): array
    {
        return $this->selectorsIndexer;
    }

    /**
     * @param array $selectorsIndexer
     */
    public function setSelectorsIndexer(array $selectorsIndexer): void
    {
        $this->selectorsIndexer = $selectorsIndexer;
    }


}
