<?php
/**
 * preparator module for Craft CMS 3.x
 *
 * Excel reader and preparator
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\preparatormodule\twigextensions;

use modules\preparatormodule\PreparatorModule;

use Craft;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author    @lostika
 * @package   PreparatorModule
 * @since     1.0.0
 */
class PreparatorModuleTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'PreparatorModule';
    }

    /**
     * Returns an array of Twig filters, used in Twig templates via:
     *
     *      {{ 'something' | someFilter }}
     *
     * @return array
     */
    public function getFilters()
    {
        return [
//            new \Twig_SimpleFilter('someFilter', [$this, 'someInternalFunction']),
            new \Twig_SimpleFilter('decimals', [$this, 'decimalCorrector']),
        ];
    }

    /**
     * Returns an array of Twig functions, used in Twig templates via:
     *
     *      {% set this = someFunction('something') %}
     *
    * @return array
     */
    public function getFunctions()
    {
        return [
//            new \Twig_SimpleFunction('someFunction', [$this, 'someInternalFunction']),
        ];
    }

    public function decimalCorrector($price, int $decimals)
    {
        return number_format($price, $decimals);
    }

    /**
     * Our function called via Twig; it can do anything you want
     *
     * @param null $text
     *
     * @return string
     */
    public function someInternalFunction($text = null)
    {
//        $result = $text . " in the way";
//
//        return $result;
    }
}
