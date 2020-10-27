<?php
/**
 * preparator module for Craft CMS 3.x
 *
 * Excel reader and preparator
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\preparatormodule\controllers;

use modules\preparatormodule\models\CalculationDataModel;
use modules\preparatormodule\PreparatorModule;

use Craft;
use craft\web\Controller;

/**
 * Reader Controller
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
 * @author    @lostika
 * @package   PreparatorModule
 * @since     1.0.0
 */
class CalculationDataController extends Controller
{

    protected $allowAnonymous = ['create', 'do-something'];

    // Public Methods
    // =========================================================================

    public function actionCreate()
    {
        $calculationData = PreparatorModule::$instance->calculationDataService->getCalculationData();
        $this->renderTemplate('preparator-module/calculation-data/edit',['calculationData' => $calculationData]);
    }

    /**
     * Handle a request going to our module's index action URL,
     * e.g.: actions/preparator-module/reader
     *
     * @return mixed
     */
    public function actionEdit(int $id)
    {
        $calculationData = PreparatorModule::$instance->calculationDataService->getCalculationDataById($id);
        dd($calculationData);
//        $this->renderTemplate('preparator-module/calculation-data/edit',$calculationData);
    }


    public function actionSave()
    {
        $this->requirePostRequest();

        $input = Craft::$app->request->post();
        $calculationDataRecord = PreparatorModule::$instance->calculationDataService->saveCalculationData($input);

        if (empty($calculationDataRecord->getErrors())) {
            return $this->redirect('preparator/calculation-data/create',['calculationData' => $calculationDataRecord]);
        }

        return $this->redirectToPostedUrl();
    }


}
