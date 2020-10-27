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

use craft\helpers\UrlHelper;
use modules\reviewermodule\models\Person;
use modules\reviewermodule\ReviewerModule;

use Craft;
use craft\web\Controller;
use yii\filters\VerbFilter;

/**
 * Person Controller
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
class PersonController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = [];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our module's index action URL,
     * e.g.: actions/reviewer-module/person
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $persons = ReviewerModule::$instance->person->all();

        return $this->renderTemplate('reviewermodule/person/index',['persons'=> $persons]);

    }

    public function actionEdit($id)
    {
        $person = ReviewerModule::$instance->person->find($id);

        if (Craft::$app->request->isPost){
            ReviewerModule::$instance->person->update($id);

            $person = ReviewerModule::$instance->person->find($id);
        }

        return $this->renderTemplate('reviewermodule/person/edit', ['person' => $person]);
    }

    public function actionUpdate(int $id)
    {

    }

    /**
     * Handle a request going to our module's actionDoSomething URL,
     * e.g.: actions/reviewer-module/person/do-something
     *
     * @return mixed
     */
    public function actionCreate()
    {

        $personModel    = new Person();
        if (Craft::$app->request->isPost){
            $request    = Craft::$app->request;

            $saved = $personModel->addPerson($request->getBodyParams());

            if ($saved){
                return $this->redirect('actions/reviewer/person/index');
            }
        }

        return $this->renderTemplate('reviewermodule/person/create',['person' => $personModel]);
    }

    public function actionLinkBuilder(int $id)
    {
        $person     = ReviewerModule::$instance->person->find($id);
        $reviews    = $person->reviews;

        if(Craft::$app->request->isPost){

            $review    = ReviewerModule::$instance->review->createReview($person);

            return $this->renderTemplate('reviewermodule/person/link', ['review'    => $review,'reviews' => $reviews,'person' => $person]);
        }

        return $this->renderTemplate('reviewermodule/person/link',['reviews' => $reviews,'person' => $person]);
    }

    public function actionDelete()
    {
        $id = (int) Craft::$app->request->post('id');

        $person = ReviewerModule::$instance->person->removeOwnedReviews($id);


        return $this->redirect('actions/reviewer/person/index');
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
