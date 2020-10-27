<?php
/**
 * filterResult module for Craft CMS 3.x
 *
 * Helper for twigs layout filtering
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\filterresultmodule;


use modules\filterresultmodule\variables\FilterResultModuleVariable;


use Craft;

use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

use yii\base\Module;

/**
 * Class FilterResultModule
 *
 * @author    @lostika
 * @package   FilterResultModule
 * @since     1.0.0
 *
 */
class FilterResultModule extends Module
{
    // Static Properties
    // =========================================================================

    /**
     * @var FilterResultModule
     */
    public static $instance;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        Craft::setAlias('@filterresultmodule',__DIR__);


        // Set this as the global instance of this module class
        static::setInstance($this);

        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$instance = $this;

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('filterResultModule', FilterResultModuleVariable::class);
            }
        );

    }

    // Protected Methods
    // =========================================================================
}
