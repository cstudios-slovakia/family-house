<?php
/**
 * preparator module for Craft CMS 3.x
 *
 * Excel reader and preparator
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\preparatormodule\assetbundles\preparatormodule;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * PreparatorModuleAsset AssetBundle
 *
 * AssetBundle represents a collection of asset files, such as CSS, JS, images.
 *
 * Each asset bundle has a unique name that globally identifies it among all asset bundles used in an application.
 * The name is the [fully qualified class name](http://php.net/manual/en/language.namespaces.rules.php)
 * of the class representing it.
 *
 * An asset bundle can depend on other asset bundles. When registering an asset bundle
 * with a view, all its dependent asset bundles will be automatically registered.
 *
 * http://www.yiiframework.com/doc-2.0/guide-structure-assets.html
 *
 * @author    @lostika
 * @package   PreparatorModule
 * @since     1.0.0
 */
class PreparatorModuleAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {

        // define the path that your publishable resources live
        $this->sourcePath = "@preparator-module/assetbundles/preparatormodule/dist";

        // define the dependencies
        $this->depends = [
//            CpAsset::class,

        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            "https://cdn.jsdelivr.net/npm/vue/dist/vue.js",
            'https://unpkg.com/axios/dist/axios.min.js',
            'js/PreparatorModule.js',
            'js/PreparatorModule-new.js',
        ];

        $this->css = [
            'css/preparator_module.css',
        ];

        parent::init();
    }
}
