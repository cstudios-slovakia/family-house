<?php
/**
 * reviewer module for Craft CMS 3.x
 *
 * It is a quote, reviewer.
 *
 * @link      eugen@juhos.sk
 * @copyright Copyright (c) 2019 Eugen Juhos
 */

namespace modules\reviewermodule\services;

use modules\reviewermodule\ReviewerModule;

use Craft;
use craft\base\Component;
use modules\reviewermodule\records\Review as ReviewRecord;
use modules\reviewermodule\models\Review as ReviewModel;
use modules\reviewermodule\records\Person;
use modules\reviewermodule\models\Question as QuestionModel;
use modules\reviewermodule\records\Question as QuestionRecord;
/**
 * Review Service
 *
 * All of your module’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other modules can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Eugen Juhos
 * @package   ReviewerModule
 * @since     1.0.0
 */
class Question extends Component
{
    // Public Methods
    // =========================================================================

    public function insertAnswers(QuestionModel $questionModel)
    {
        $questionRecord     = new QuestionRecord();

        $questionRecord->load($questionModel->toArray(),'');

        return $questionRecord->save();
    }

    public function all()
    {
        $questionRecord     = new QuestionRecord();

        $questions = $questionRecord->find()->with('review')->with('review.person')->all();

        return $questions;
    }

    public function find(int $id)
    {
        $questionRecord     = new QuestionRecord();

        $question = $questionRecord->find()->where(['id'=>$id])->with('review')->with('review.person')->one();

        return $question;
    }


}
