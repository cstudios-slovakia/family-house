<?php
/**
 * housefilter module for Craft CMS 3.x
 *
 * Filter for houses
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 lostika86
 */

namespace modules\housefilter\controllers;


use craft\web\Controller;

/**
 * HouseFilter Controller
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
 * @author    lostika86
 * @package   HousefilterModule
 * @since     1.0.0
 */
class HouseFilterController extends Controller
{


    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index'];



    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our module's index action URL,
     * e.g.: actions/housefilter-module/house-filter
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->renderTemplate(
            'houseFilter-front/dom/_house_listing_container'
        );
    }

}
