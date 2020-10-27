<?php
/**
 * preparator module for Craft CMS 3.x
 *
 * Excel reader and preparator
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\preparatormodule\controllers;

use craft\web\Response;
use Illuminate\Support\Collection;
use modules\preparatormodule\exceptions\CalculationKeyException;
use modules\preparatormodule\models\HouseTypeOptionModel;
use modules\preparatormodule\models\HouseTypeSelectModel;
use modules\preparatormodule\PreparatorModule;

use Craft;
use craft\web\Controller;
use modules\preparatormodule\records\Energy;
use modules\preparatormodule\records\VariantPrices;
use modules\preparatormodule\records\Variants;
use modules\preparatormodule\services\EnergyPriceService;
use modules\preparatormodule\services\HouseTypesService;
use modules\preparatormodule\support\CalculationElement;
use modules\preparatormodule\support\CalculationElementsCollection;
use modules\preparatormodule\support\ExcelReader;
use modules\preparatormodule\support\HouseEnergyCalculation;
use modules\preparatormodule\support\HouseEnergyOptions;
use modules\preparatormodule\support\HousePrices;
use modules\preparatormodule\support\HouseTypeSelections;
use modules\preparatormodule\support\HouseVersionSelections;
use modules\preparatormodule\support\SheetReader;
use modules\preparatormodule\support\SheetSettings;
use yii\base\Exception;
use yii\log\Logger;

/**
 * Reader Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your module’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    @lostika
 * @package   PreparatorModule
 * @since     1.0.0
 */
class ReaderController extends Controller
{
    protected $allowAnonymous = true;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionTest()
    {
        if (!Craft::$app->request->isAjax || !Craft::$app->request->isPost){
            Craft::$app->response->statusCode = 403;
            Craft::$app->response->format = \craft\web\Response::FORMAT_JSON;

            return Craft::$app->request;

            return Craft::$app->response->send();
        }
    }


    public function actionCalculate()
    {

        if (!Craft::$app->request->isAjax || !Craft::$app->request->isPost){
            Craft::$app->response->statusCode = 403;
            Craft::$app->response->format = \craft\web\Response::FORMAT_JSON;

            return Craft::$app->request;

            return Craft::$app->response->send();
        }


        $response = [];
        $requestBody = json_decode(Craft::$app->request->rawBody);
        $houseId    = (int) $requestBody->houseId;

        $possibilitiesService = PreparatorModule::$instance->calculationCustomsService;
        $possibilities = $possibilitiesService->getHousePossibilities();
        $hVersionNumber = $requestBody->activeHouseVersionNumber < 1 ? $possibilities[0] : $requestBody->activeHouseVersionNumber;
        $response = $this->getCalculatorData($hVersionNumber,$houseId);

        $response['activeHouseTypeNumber'] = $hVersionNumber;

        $buildedSelectorTypesData = $this->buildSelectorsForType($hVersionNumber, $response['houseTypes']);


        if (!isset($requestBody->selectorsOnTypeNumber) ){
            $response['selectorsOnTypeNumber'] = $buildedSelectorTypesData['typeSelectorModels'];
        } else{
            if (count($buildedSelectorTypesData['typeSelectorModels']) !== count($requestBody->selectorsOnTypeNumber)) {
                $response['selectorsOnTypeNumber'] = $buildedSelectorTypesData['typeSelectorModels'];
            } else{
                $response['selectorsOnTypeNumber'] = $requestBody->selectorsOnTypeNumber;
            }

        }
//dd($response);
        if (empty($requestBody->selectorsIndexer)){
            $selectorTypes  = $this->buildSelectorsForType($hVersionNumber, $response['houseTypes']);
            $response['selectorsIndexer']    = $selectorTypes['selectorsIndexer'];
        } else{
            $response['selectorsIndexer']   = $requestBody->selectorsIndexer;
        }
//dd($response['selectorsIndexer'],$response['selectorsOnTypeNumber']);
        $versionPriceMatrix     = $this->makeVersionPriceMatrix($hVersionNumber, $response['selectorsIndexer'],$response['selectorsOnTypeNumber']);
        $versionPrices = $response['houseVersions'];
        $prices = collect([]);


        $addedPrices = 0;
        foreach ($versionPrices as $possibility => $possibilityPrice) {
            if (!$prices->has($possibility)) {
                $prices->put($possibility,collect([(float)$possibilityPrice]));
            }

            if ($versionPriceMatrix->has($possibility)) {
                $sumOfPossibilityPrices = (float) $versionPriceMatrix->get($possibility)->sum();
                $addedPrices += $sumOfPossibilityPrices;
                $prices->get($possibility)->push($addedPrices);
//                var_dump($sumOfPossibilityPrices);
//                var_dump($addedPrices);
            }
        }

        $response['houseVersions'] = $prices->map(function ($pricesCollection) {
            return number_format($pricesCollection->sum(), 0, '.','.');
        });

        try {
            if (empty($requestBody->ea)) {
                if ($index = array_search(5, $response['selectorsIndexer']) !== false) {
                    $choosedTypeKey = $response['selectorsOnTypeNumber'][$index];
                    $energyKey = HouseEnergyOptions::getShortEnergyNameForTypeKey('ea',$choosedTypeKey);
                    $requestBody->ea = $energyKey;
                }

            }
            if (empty($requestBody->ca)) {
                if ($index = array_search(4, $response['selectorsIndexer']) !== false) {
                    $choosedTypeKey = $response['selectorsOnTypeNumber'][$index];
                    $energyKey = HouseEnergyOptions::getShortEnergyNameForTypeKey('ca',$choosedTypeKey);
                    $requestBody->ca = $energyKey;
                }

            }
     
            $primaryEnergyKey = $this->energyCombinatedKeyBuilder($requestBody, 'CPE');
            $totalYearlyEnergyKey = $this->energyCombinatedKeyBuilder($requestBody, 'CRP');
            $totalMonthlyEnergyPriceKey = $this->energyCombinatedKeyBuilder($requestBody, 'CMC');

//dd($primaryEnergyKey);
            $response['energy'] = [];
            /** @var EnergyPriceService $energyService */
            $energyService = PreparatorModule::$instance->energyPriceService;

            $energyService->setChoosedEnergyTypes($response['selectorsOnTypeNumber']);
            $energyService->setSelectorsIndexer($response['selectorsIndexer']);
            $primaryEnergy = $energyService->getCalculatedEnergyOnKey($primaryEnergyKey);
            $response['energy']['primaryEnergy'] = $primaryEnergy;

            $totalYearlyEnergy = $energyService->getCalculatedEnergyOnKey($totalYearlyEnergyKey);
            $response['energy']['totalYearlyEnergy'] = $totalYearlyEnergy;

            $totalMonthlyEnergyPrice = $energyService->getCalculatedEnergyOnKey($totalMonthlyEnergyPriceKey);
            $response['energy']['totalMonthlyEnergyPrice'] = $totalMonthlyEnergyPrice;

            $response['energy']['energyClass'] = $energyService::getEnergyClass($primaryEnergy);
//dd($response);

        } catch (CalculationKeyException $exception) {
            Craft::getLogger()->log($exception->getMessage(), LOG_ALERT);
        }

        // only for demo
        $response['demo']['calc'] = [
            'calcFlow' => SheetSettings::calculationFlow(),
            'calcElements' => collect(SheetSettings::calculationFlow())->keyBy(function ($calcKey){
                return $calcKey;
            })->map(function ($calcKey) use($energyService) {
                return $energyService->getCalculationElements()->get($calcKey)->getValue();
            })
        ];

 
		if (getenv('ENVIRONMENT') === 'dev') {
			$response['demo']['calc']['flowdebug'] = $this->actionDemoCalculation($houseId,$hVersionNumber);
		}
        ;
        $response['customs']['possibilities'] = $possibilities;
        $response['customs']['calculator_properties'] = collect(SheetSettings::sortedVariantsByPosition())->keyBy('typeKey')->toArray();

        return json_encode($response);
    }


    /**
     * @param $requestBody
     * @return string
     * @throws CalculationKeyException
     */
    protected function energyCombinatedKeyBuilder($requestBody,string $prefix) : string
    {

        if (!isset($requestBody->ea) && !isset($requestBody->ca)) {
            throw new CalculationKeyException();
        }

        $key = $prefix . ' ';
        $key.= $requestBody->ea .'+'.$requestBody->ca;

        $calculationFlow    = SheetSettings::calculationFlow();
        if (!in_array($key, $calculationFlow)) {
            throw new CalculationKeyException();
        }

        return $key;

    }

    protected function makeVersionPriceMatrix(int $typeNumber, array $selectorIndexer, array $choosedSelectors) : Collection
    {
        $priceMatrix = collect([]);


        $priceVariants = PreparatorModule::$instance->variantPriceService->priceVariants();
        $pricesByKey = $priceVariants->keyBy(function (Variants $variant) {
            return $variant->variant_index;
        });
//        dd($pricesByKey->toArray(),$selectorIndexer,$choosedSelectors);
        foreach ($selectorIndexer as $index => $selectorIndex) {
            $choosedSelectorValue = $choosedSelectors[$index];
            if ($pricesByKey->has($selectorIndex)) {

                /** @var Variants $variant */
                $variant = $pricesByKey->get($selectorIndex);
                $possibility = $variant->variant_possibility_key;

                if (!$priceMatrix->has($possibility)){
                    $priceMatrix->put($possibility, collect([]));
                }
                $variantPricesCollection = collect($variant->pricesOptions);
                $variantPrice = $variantPricesCollection->filter(function (VariantPrices $variantPrices) use ($choosedSelectorValue){
                    if ($variantPrices->price_index === $choosedSelectorValue) {
                        return $variantPrices;
                    }
                })->first();

                if ($variantPrice) {
                    $priceMatrix->get($possibility)->push((float)$variantPrice->price) ;

                }

            }
        }

        return $priceMatrix;
    }

    protected function getCalculatorData(int $versionNumber, int $houseId): array
    {
        $response = [];
        $hv = $this->getHouseVersionData($versionNumber,$houseId);
        $ht = $this->getHouseTypeData($versionNumber,$houseId);
        $response = array_merge($response,$hv, $ht);

        return  $response;
    }

    protected function buildSelectorsIndexer(array $types, int $versionNumber): array
    {
        $data = $types[$versionNumber];
        $selectorsIndexes = [];
        foreach ($data as $index => $selector) {
            $selectorsIndexes[$index] = (int) $selector->typeKey;
        }

        return $selectorsIndexes;
    }

    protected function buildSelectorsForType(int $number, array $data)
    {
        $typeSelectorModels = [];
        $selectorsIndexer = [];

        collect($data)->each(function (HouseTypeSelectModel $houseTypeSelectModel, int $index) use (&$typeSelectorModels, &$selectorsIndexer){

            $selectedOptionModel     = collect($houseTypeSelectModel->options)
                ->filter(function (HouseTypeOptionModel $houseTypeOptionModel) {
                    if ($houseTypeOptionModel->selected) {
                        return $houseTypeOptionModel;
                    }
                })->first();
            $defaultValue = $selectedOptionModel ? $selectedOptionModel->value : 0;
            $typeSelectorModels[] = $defaultValue;
            $selectorsIndexer[] = (int )$houseTypeSelectModel->typeKey;
        });

        return [
            'typeSelectorModels' => $typeSelectorModels,
            'selectorsIndexer' => $selectorsIndexer,
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex(int $houseId)
    {
        return $this->renderTemplate('preparator-module/reader/index',
                [
                    'settings' => Craft::$app->globals->getSetByHandle('settings'),
                    'contact' => Craft::$app->globals->getSetByHandle('contact'),
                    'currentHouseId' => PreparatorModule::$instance->calculationRequestService->getParameter('houseId')
                ]);
    }

    public function actionDemoCalculation(int $houseId, int $version = 1)
    {

        $constantsInstance  = PreparatorModule::$instance->calculationConstantService;
        $dataInstance       = PreparatorModule::$instance->calculationDataService;

        $dataInstance->setHouseId($houseId);

        $constants  = $constantsInstance->getFormattedCalculationConstants();
        $data       = $dataInstance->getFormattedCalculationDatas();

        /** @var Collection $calculationElements */
        $calculationElements = $data->merge($constants);
		return ['calculationElements' => $calculationElements->pluck('value','key')->toArray()];
        $formulasCollection = collect(SheetSettings::calculationFormulas());

        $flow   = SheetSettings::calculationFlow();
//dd($constants->pluck('key','value')->toArray(),$data->pluck('key','value')->toArray());
        foreach ($flow as $key) {
            if (! $calculationElements->has($key)) {

                $calcElement    = new CalculationElement();

                $formula = $formulasCollection->get($key, '');

                if (is_array($formula)) {
//                    dd($formula);
                    $formula = $formula[$version];
                }

                $calcElement->formula = $formula;
                $calcElement->key       = $key;
                $calcElement->elements = $calculationElements;

                $calculationElements->put($key, $calcElement);

            }
        }

        $calcFlow   = SheetSettings::calculationFlow();
		return ['calcFlow' => $calcFlow, 'calculationElements' => $calculationElements];
        return $this->renderTemplate('preparator-module/reader/demo_calculation', ['calcFlow' => $calcFlow, 'calculationElements' => $calculationElements]);

    }

    protected function getHouseTypeData(int $houseTypeNumber = 0,int $houseId = 0): array
    {
        /** @var HouseTypesService $instance */
        $instance = PreparatorModule::$instance->houseTypesService;
        $instance->setHouseId($houseId);
        return [
            'houseTypes' => $instance->getHouseTypeSelections($houseTypeNumber)
        ];
    }

    protected function getHouseVersionData(int $versionNumber = 0,int $houseId = 0) : array
    {
        $instance = PreparatorModule::$instance->houseVersionService;
        $instance->setHouseId($houseId);
        return [
            'houseVersions' => $instance->versionPricesForNumber($versionNumber)
        ];
    }

    public function actionSeedingData()
    {
        $service    = PreparatorModule::$instance->calculationConstantService;

        $service->setFileInput(Craft::getAlias('@preparator-module').'/../fh-calc-v10.xlsx');
        $service->save();
//        PreparatorModule::$instance->calculationDataService->save();
//        PreparatorModule::$instance->houseVersionService->save();
//        PreparatorModule::$instance->houseTypesService->save();
//        PreparatorModule::$instance->variantPriceService->save();
//        PreparatorModule::$instance->energyPriceService->save();
    }

    public function actionFillData(int $id)
    {
         $services  = [
//             'calculationConstantService',
             'calculationDataService',
             'houseVersionService',
             'houseTypesService',
             'variantPriceService',
             'energyPriceService',
             'calculationCustomsService'
         ];

        foreach ($services as $service) {
            $instance = PreparatorModule::$instance->{$service};
            $instance->setParams([
                'house_id'  => $id,
                'siteId' => 1
            ]);
            $instance->setFileInput(Craft::getAlias('@preparator-module').'/../fh-calc-v10.xlsx');

            $instance->save();
        }
    }
}
