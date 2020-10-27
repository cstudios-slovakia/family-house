<?php
/**
 * preparator module for Craft CMS 3.x
 *
 * Excel reader and preparator
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\preparatormodule;

require_once __DIR__ . '/../../reviewer/vendor/autoload.php';

use craft\base\Element;
use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use craft\elements\Entry;
use craft\events\AssetEvent;
use craft\events\ElementContentEvent;
use craft\events\ElementEvent;
use craft\services\Content;
use craft\services\Elements;
use craft\volumes\Local;
use modules\housefilter\services\FilterRequest;
use modules\housefilter\services\HouseFilter as HouseFilterService;
use modules\housefilter\services\SearchRequest;
use modules\housefilter\services\SortingRequest;
use modules\preparatormodule\assetbundles\preparatormodule\PreparatorModuleAsset;
use modules\preparatormodule\controllers\CalculationDataController;
use modules\preparatormodule\services\CalculationDataService;
use modules\preparatormodule\services\HouseVersionService as HouseTypesService;
use modules\preparatormodule\support\SheetChecker;
use modules\preparatormodule\support\SheetSettings;
use modules\preparatormodule\variables\PreparatorModuleVariable;
use modules\preparatormodule\twigextensions\PreparatorModuleTwigExtension;
use modules\preparatormodule\fields\Reader as ReaderField;

use Craft;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\TemplateEvent;
use craft\i18n\PhpMessageSource;
use craft\web\View;
use craft\web\UrlManager;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;


use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\ModelEvent;
use yii\base\Module;
use yii\validators\NumberValidator;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    @lostika
 * @package   PreparatorModule
 * @since     1.0.0
 *
 * @property  HouseTypesService $houseTypes
 */
class PreparatorModule extends Module
{
    // Static Properties
    // =========================================================================
    public $controllerMap = [
        'calculation-data' => CalculationDataController::class
    ];
    /**
     * Static property that is an instance of this module class so that it can be accessed via
     * PreparatorModule::$instance
     *
     * @var PreparatorModule
     */
    public static $instance;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {

        Craft::setAlias('@preparator-module', $this->getBasePath());
        $this->controllerNamespace = 'modules\preparatormodule\controllers';

        // Translation category
        $i18n = Craft::$app->getI18n();
        /** @noinspection UnSafeIsSetOverArrayInspection */
        if (!isset($i18n->translations[$id]) && !isset($i18n->translations[$id.'*'])) {
            $i18n->translations[$id] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => '@preparator-module/translations',
                'forceTranslation' => true,
                'allowOverrides' => true,
            ];
        }

        // Base template directory
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $e) {
            if (is_dir($baseDir = $this->getBasePath().DIRECTORY_SEPARATOR.'templates')) {
                $e->roots[$this->id] = $baseDir;
                $e->roots[$this->id.'-site'] = Craft::$aliases['@templates'];
                $e->roots['_components'] = Craft::$aliases['@templates'].'/_components';

            }
        });

        Event::on(View::class, View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
            $ident = $this->id;
            $e->roots[$ident] = $this->getBasePath().DIRECTORY_SEPARATOR.'templates';
            $e->roots[$ident.'-site'] = Craft::$aliases['@templates'];

        });

        // Set this as the global instance of this module class
        static::setInstance($this);

        parent::__construct($id, $parent, $config);
    }

    /**
     * Set our $instance static property to this class so that it can be accessed via
     * PreparatorModule::$instance
     *
     * Called after the module class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$instance = $this;

        // Add in our Twig extensions
        Craft::$app->view->registerTwigExtension(new PreparatorModuleTwigExtension());

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'modules/preparator-module/reader';
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'modules/preparator-module/reader/do-something';
                $event->rules['preparator/<controller>/<action>'] = 'preparator-module/<controller>/<action>';
            }
        );

        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ReaderField::class;
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('preparatorModule', PreparatorModuleVariable::class);
            }
        );

        $this->setComponents([
            'calculationDataService' => CalculationDataService::class,

        ]);

//        Event::on(
//            Element::class,
//            Element::EVENT_BEFORE_SAVE,
//            function(ModelEvent $event) {
////                dd($event);
////                Craft::getLogger()->log(['Element::EVENT_BEFORE_SAVE'], LOG_DEBUG);
//            }
//        );

        Event::on(
            Element::class,
            Element::EVENT_AFTER_VALIDATE,
            function ($elementEvent) {
//var_dump($elementEvent);
                /** @var Asset $sender */
                $sender = $elementEvent->sender;
                if ($sender instanceof Asset && $sender->kind === 'excel' && is_null($sender->folderId)) {
                    $asset = $sender;

                    $fileInput = $asset->tempFilePath;
                    $reader = new Xlsx();
                    $reader->setReadDataOnly(true);

                    $excel = $reader->load($fileInput);

                    $sheet = $excel->getSheet(0);

                    $checkRules     = SheetChecker::canNotBeEmpty();
                    $messages = [];
                    foreach ($checkRules as $rule) {
                        $cells = $sheet->rangeToArray($rule, null, true, true, true);
                        collect(array_dot($cells))->filter(function ($value, $position) use (&$messages, $rule,$checkRules){
                            if (is_null($value)) {
                                $messages = array_merge($messages,[$position .': No value']);
                            }
                            if (is_array($value)
                                || is_bool($value)
                                || (is_object($value) && !method_exists($value, '__toString'))
                                || (!is_object($value) && !is_scalar($value) && $value !== null)){
                                $messages = array_merge($messages,[$position .': Not a number']);
                            }

                        });
                    }

                    $checkNumerics = SheetChecker::canBeOnlyNumber();
                    foreach ($checkNumerics as $rule) {
                        $cells = $sheet->rangeToArray($rule, null, true, true, true);
                        collect(array_dot($cells))->filter(function ($value, $position) use (&$messages, $rule,$checkRules){
                            if (!is_numeric($value)) {
                                $messages = array_merge($messages,[$position .': Not a number']);
                            }
                        });
                    }

                    if (!empty($messages)) {
                        $asset->addError('calculationField', 'Not correctly formatted fields on: '.implode(', ', $messages));

                    }
                    Craft::getLogger()->log(['elementEventTest AFTER' => json_encode($elementEvent->sender)], LOG_DEBUG);
                }

            }
        );
//        Event::on(
//            Elements::class,
//            Elements::EVENT_BEFORE_SAVE_ELEMENT,
//            function ($elementEvent) {
//                Craft::getLogger()->log(['Elements::EVENT_BEFORE_SAVE_ELEMENT'], LOG_DEBUG);
//
////                $asset = $elementEvent->element;
////                $fileInput = $asset->tempFilePath;
////                $reader = new Xlsx();
////                $reader->setReadDataOnly(true);
////                Craft::getLogger()->log(['$fileInput BEFORE' => $fileInput], LOG_DEBUG);
////
////                $excel = $reader->load($fileInput);
////                Craft::getLogger()->log(['elementEventTest BEFORE' => json_encode($elementEvent->sender)], LOG_DEBUG);
////                $sheet = $excel->getSheet(0);
//
//
//            }
//        );
//        dd($this);

        Event::on(
            Entry::class,
            Entry::EVENT_AFTER_SAVE,
            function ($event) {
                /** @var Entry $entry */
                $entry = $event->sender;

                if (is_null($entry->fieldLayout->getFieldByHandle('calculationFile'))) {
                    return ;
                }

                $entryParams = [
                    'house_id' => (int) $entry->id,
                    'siteId' => (int) $entry->siteId,
                ];
                $preparatorModule = PreparatorModule::$instance;
                $services  = [
//             'calculationConstantService',
                    'calculationDataService',
                    'houseVersionService',
                    'houseTypesService',
                    'variantPriceService',
                    'energyPriceService',
                    'calculationCustomsService'
                ];


                /** @var Asset $calculationFile */
                $calculationFile    = $entry->getFieldValue('calculationFile')->one();
                if ($uploadFileExists = (!is_null($calculationFile) && $calculationFile instanceof Asset)) {
                    /** @var Local $volumeLocal */
                    $volumeLocal    = $calculationFile->getVolume();
                    $fileInput      = $volumeLocal->getRootPath().'/'.$calculationFile->getPath();

                    $reader = new Xlsx();
                    $reader->setReadDataOnly(true);

                    $excel = $reader->load($fileInput);
                    $sheet = $excel->getSheet(0);
                }

                foreach ($services as $service) {
                    $moduleService = $preparatorModule->{$service};
                    $moduleService->setParams($entryParams);
                    if ($uploadFileExists) {
                        $moduleService->setSheet($sheet);
                        $moduleService->save();
                    } else{
                        $moduleService->clearById($entryParams['house_id']);
                    }
                }
            }
        );

        // Load our AssetBundle


/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
//        Craft::info(
//            Craft::t(
//                'preparator-module',
//                '{name} module loaded',
//                ['name' => 'preparator']
//            ),
//            __METHOD__
//        );
//        Craft::info(
//            Craft::t(
//                'preparator-module',
//                '{name} module loaded',
//                ['name' => 'preparatormodule']
//            ),
//            __METHOD__
//        );

    }

    // Protected Methods
    // =========================================================================


    protected function createSettingsModel()
    {
        return new \modules\preparatormodule\models\Settings();
    }

}
