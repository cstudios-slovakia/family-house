<?php
/**
 * housefilter module for Craft CMS 3.x
 *
 * Filter for houses
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 lostika86
 */

namespace modules\housefilter\twigextensions;

use modules\housefilter\HousefilterModule;

use Craft;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author    lostika86
 * @package   HousefilterModule
 * @since     1.0.0
 */
class HousefilterModuleTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    public function isChecked(bool $selected)
    {
        return $selected ? 'checked' : '';
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'HousefilterModule';
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
            new \Twig_SimpleFilter('checked', [$this, 'isChecked']),
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
            new \Twig_SimpleFunction('someFunction', [$this, 'someInternalFunction']),
        ];
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
        $result = $text . " in the way";

        return $result;
    }
}
