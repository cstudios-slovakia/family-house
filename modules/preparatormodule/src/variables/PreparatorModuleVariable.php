<?php
/**
 * preparator module for Craft CMS 3.x
 *
 * Excel reader and preparator
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\preparatormodule\variables;

use modules\preparatormodule\PreparatorModule;

use Craft;

/**
 * preparator Variable
 *
 * Craft allows modules to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.preparatorModule }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    @lostika
 * @package   PreparatorModule
 * @since     1.0.0
 */
class PreparatorModuleVariable
{

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.preparatorModule.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.preparatorModule.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function exampleVariable($optional = null)
    {
//        $result = "And away we go to the Twig template...";
//        if ($optional) {
//            $result = "I'm feeling optional today...";
//        }
//        return $result;
    }
}
