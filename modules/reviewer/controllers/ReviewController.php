<?php
/**
 * reviewer module for Craft CMS 3.x
 *
 * It is a quote, reviewer.
 *
 * @link      eugen@juhos.sk
 * @copyright Copyright (c) 2019 Eugen Juhos
 */

namespace modules\reviewermodule\controllers;

use craft\elements\actions\View;
use modules\reviewermodule\assetbundles\ReviewerModule\ReviewerModuleAsset;
use modules\reviewermodule\models\Question;
use modules\reviewermodule\models\Review;
use modules\reviewermodule\ReviewerModule;

use Craft;
use craft\web\Controller;
use modules\reviewermodule\variables\ReviewerModuleVariable;
use yii\helpers\Url;

/**
 * Review Controller
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
 * @author    Eugen Juhos
 * @package   ReviewerModule
 * @since     1.0.0
 */
class ReviewController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['create'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our module's index action URL,
     * e.g.: actions/reviewer-module/review
     *
     * @return mixed
     */
    public function actionIndex()
    {
//        $questions  = ReviewerModule::$instance->question->all();

        $reviews    = ReviewerModule::$instance->review->all();
//        dd($reviews);

        return $this->renderTemplate('reviewer/review/index',['reviews' => $reviews,'selectedSubnavItem' => $this->id]);
    }


    public function actionCreate()
    {
        $request =   Craft::$app->request;

        $questionModel  = new Question();

        if ($request->isPost){
            // grab this s..it input
            $input  = $request->post();
//            dd($request->getBodyParams());
            // url should contain "fake" uid as utm_medium parameter
            $uid    = $request->getQueryParam('utm_campaign');

            // check review with that uid
            $review = ReviewerModule::$instance->review->findByUid($uid);

            // build up the model

            $questionModel->load($input['questions'],'');
            $questionModel->review_id = $review ? $review->id : null;
            $questionModel->q_10_other   = $input['questions']['other']['q_10'];
            $questionModel->q_60_other   = $input['questions']['other']['q_60'];
            $questionModel->q_80_other   = $input['questions']['other']['q_80'];
//dd($input);
            $questionModel->q_20_comment = empty($input['questions']['comments']['q_20']) ? null : $input['questions']['comments']['q_20'];
            $questionModel->q_30_comment = empty($input['questions']['comments']['q_30']) ? null : $input['questions']['comments']['q_30'];
            $questionModel->q_40_comment = empty($input['questions']['comments']['q_40']) ? null : $input['questions']['comments']['q_40'];

            if ($questionModel->validate()){

                // if exists should be associated with questions
                ReviewerModule::$instance->question->insertAnswers($questionModel);

                return $this->goHome();
            }

        }

        $this->view->registerAssetBundle(ReviewerModuleAsset::className());

        // define frontend template paths

        return $this->renderTemplate('reviewermodule/front/create',['questionModel' => $questionModel]);
    }

    public function actionShow(int $id)
    {
        $review = ReviewerModule::$instance->review->find($id);

        $question = $review->getQuestion()->orderBy(['id' => SORT_DESC])->one();

        return $this->renderTemplate('reviewer/question/show',['question' => $question]);
    }

    public function actionDelete(int $id)
    {
        ReviewerModule::$instance->review->delete($id);

        return $this->redirect('actions/reviewer/review/index');
    }


    /**
     * @param array $params
     * @return array
     */
    protected function renderParams($params = [])
    {
        $params['selectedSubnavItem'] = $this->id;
        return $params;
    }
}
