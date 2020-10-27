<?php
/**
 * reviewer module for Craft CMS 3.x
 *
 * It is a quote, reviewer.
 *
 * @link      eugen@juhos.sk
 * @copyright Copyright (c) 2019 Eugen Juhos
 */

namespace modules\reviewermodule;

use craft\events\RegisterCpNavItemsEvent;

use craft\web\twig\variables\Cp;
use modules\reviewermodule\assetbundles\reviewermodule\ReviewerModuleAsset;

use modules\reviewermodule\assetbundles\ReviewerModule\ReviewerModuleChartAsset;
use modules\reviewermodule\services\Person as PersonService;
use modules\reviewermodule\services\Person;
use modules\reviewermodule\services\Question;
use modules\reviewermodule\services\Review as ReviewService;
use modules\reviewermodule\services\Review;
use modules\reviewermodule\variables\ReviewerModuleVariable;
use modules\reviewermodule\twigextensions\ReviewerModuleTwigExtension;

use Craft;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\TemplateEvent;
use craft\i18n\PhpMessageSource;
use craft\web\View;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;
use yii\base\InvalidConfigException;
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
 * @author    Eugen Juhos
 * @package   ReviewerModule
 * @since     1.0.0
 *
 * @property  PersonService $person
 * @property  ReviewService $review
 */
class ReviewerModule extends Module
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this module class so that it can be accessed via
     * ReviewerModule::$instance
     *
     * @var ReviewerModule
     */
    public static $instance;

    public $controllerMap = [
        'person' => 'modules\reviewermodule\controllers\PersonController',
        'review' => 'modules\reviewermodule\controllers\ReviewController',
        'analytic' => 'modules\reviewermodule\controllers\AnalyticController',
    ];


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        Craft::setAlias('@modules/reviewermodule', $this->getBasePath());
        $this->controllerNamespace = 'modules\reviewermodule\controllers';

        // Translation category
        $i18n = Craft::$app->getI18n();
        /** @noinspection UnSafeIsSetOverArrayInspection */
        if (!isset($i18n->translations[$id]) && !isset($i18n->translations[$id.'*'])) {
            $i18n->translations[$id] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => '@modules/reviewermodule/translations',
                'forceTranslation' => true,
                'allowOverrides' => true,
            ];
        }

        // Base template directory
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $e) {
            if (is_dir($baseDir = $this->getBasePath().DIRECTORY_SEPARATOR.'templates')) {
                $e->roots[$this->id] = $baseDir;
            }
        });

        // Set this as the global instance of this module class
        static::setInstance($this);

        parent::__construct($id, $parent, $config);
    }

    /**
     * Set our $instance static property to this class so that it can be accessed via
     * ReviewerModule::$instance
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

        // Load our AssetBundle
        if (Craft::$app->getRequest()->getIsSiteRequest()) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                function (TemplateEvent $event) {
                    try {
                        Craft::$app->getView()->registerAssetBundle(ReviewerModuleAsset::class);
                    } catch (InvalidConfigException $e) {
                        Craft::error(
                            'Error registering AssetBundle - '.$e->getMessage(),
                            __METHOD__
                        );
                    }
                }
            );
        }

        if (Craft::$app->getRequest()->getIsCpRequest()) {


        }

        // Add in our Twig extensions
        Craft::$app->view->registerTwigExtension(new ReviewerModuleTwigExtension());

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'modules\reviewermodule\console\controllers';
        }

        // Register a new templates folder within a Module
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
            $e->roots['reviewermodule'] = $this->getBasePath().DIRECTORY_SEPARATOR.'templates';
        });

        Event::on(View::class, View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
            $e->roots['reviewermodule'] = $this->getBasePath().DIRECTORY_SEPARATOR.'templates';
            $e->roots['reviewermodule-site'] = Craft::$aliases['@templates'];
            $e->roots['_partials'] = $this->getBasePath().DIRECTORY_SEPARATOR.'templates/front/_partials';
//            dd($e->roots);

        });


        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['dotaznik/formular'] = 'reviewer/review/create';
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['reviewer'] = 'reviewer/person/index';

                $event->rules['reviewer/<controller>/<action>'] = 'reviewer/<controller>/<action>';
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('reviewerModule', ReviewerModuleVariable::class);
            }
        );



        $this->setComponents([
            'person' => Person::class,
            'review' => Review::class,
            'question'  => Question::class,
        ]);




        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function(RegisterCpNavItemsEvent $event) {

                $event->navItems[] = [
                    'url' => 'reviewer',
                    'label' => 'Reviews',
                    'subnav' => [
                        'person' => ['label' => Craft::t('reviewer','Persons list'), 'url' => 'reviewer/person/index'],
                        'review' => ['label' => Craft::t('reviewer','Reviews list'), 'url' => 'reviewer/review/index'],
                        'analytic' => ['label' => Craft::t('reviewer','Analytic show'), 'url' => 'reviewer/analytic/show'],
                    ],
                ];

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
 */
        Craft::info(
            Craft::t(
                'reviewer',
                '{name} module loaded',
                ['name' => 'reviewer']
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================
}
