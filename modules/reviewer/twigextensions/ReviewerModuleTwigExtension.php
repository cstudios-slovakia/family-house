<?php
/**
 * reviewer module for Craft CMS 3.x
 *
 * It is a quote, reviewer.
 *
 * @link      eugen@juhos.sk
 * @copyright Copyright (c) 2019 Eugen Juhos
 */

namespace modules\reviewermodule\twigextensions;

use modules\reviewermodule\ReviewerModule;

use Craft;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author    Eugen Juhos
 * @package   ReviewerModule
 * @since     1.0.0
 */
class ReviewerModuleTwigExtension extends \Twig_Extension
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
        return 'ReviewerModule';
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
            new \Twig_SimpleFilter('json_decode', [$this, 'jsonDecode']),
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
    public function jsonDecode($text = null)
    {
        return json_decode($text);
    }
}
