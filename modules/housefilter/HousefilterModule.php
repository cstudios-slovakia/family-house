<?php
/**
 * housefilter module for Craft CMS 3.x
 *
 * Filter for houses
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 lostika86
 */

namespace modules\housefilter;

use craft\events\ElementEvent;
use craft\services\Elements;
use modules\housefilter\services\FilterRequest;
use modules\housefilter\services\HouseFilter as HouseFilterService;
use modules\housefilter\services\SearchRequest;
use modules\housefilter\services\SortingRequest;
use modules\housefilter\variables\HousefilterModuleVariable;
use modules\housefilter\twigextensions\HousefilterModuleTwigExtension;

use Craft;
use craft\events\RegisterTemplateRootsEvent;

use craft\web\View;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

use yii\base\Module;

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
 * @author    lostika86
 * @package   HousefilterModule
 * @since     1.0.0
 *
 * @property  HouseFilterService $houseFilter
 */
class HousefilterModule extends Module
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this module class so that it can be accessed via
     * HousefilterModule::$instance
     *
     * @var HousefilterModule
     */
    public static $instance;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        Craft::setAlias('@housefilter', $this->getBasePath());
        Craft::setAlias('@templates', CRAFT_BASE_PATH . '/templates');
        $this->controllerNamespace = 'modules\housefilter\controllers';

        // Translation category
//        $i18n = Craft::$app->getI18n();
//        /** @noinspection UnSafeIsSetOverArrayInspection */
//        if (!isset($i18n->translations[$id]) && !isset($i18n->translations[$id.'*'])) {
//            $i18n->translations[$id] = [
//                'class' => PhpMessageSource::class,
//                'sourceLanguage' => 'en-US',
//                'basePath' => '@modules/housefiltermodule/translations',
//                'forceTranslation' => true,
//                'allowOverrides' => true,
//            ];
//        }



        // Base template directory

        // Set this as the global instance of this module class
        static::setInstance($this);

        parent::__construct($id, $parent, $config);
    }

    /**
     * Set our $instance static property to this class so that it can be accessed via
     * HousefilterModule::$instance
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



        Event::on(View::class, View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $e) {
            $e->roots['houseFilter-front'] =  Craft::getAlias('@templates');
        });



        // Add in our Twig extensions
        Craft::$app->view->registerTwigExtension(new HousefilterModuleTwigExtension());

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['filter/<controller>/<action>'] = 'housefilter/<controller>/<action>';
            }
        );


        $this->setComponents([
            'filterRequest' => FilterRequest::class,
            'houseFilter' => HouseFilterService::class,
            'sortingRequest' => SortingRequest::class,
            'searchRequest' => SearchRequest::class,
        ]);

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('housefilterModule', HousefilterModuleVariable::class);
            }
        );

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
// */
//        Craft::info(
//            Craft::t(
//                'housefilter-module',
//                '{name} module loaded',
//                ['name' => 'housefilter']
//            ),
//            __METHOD__
//        );
    }

    // Protected Methods
    // =========================================================================
}
