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

use modules\reviewermodule\records\Question as QuestionRecord;
use modules\reviewermodule\ReviewerModule;

use Craft;
use craft\base\Component;
use modules\reviewermodule\records\Review as ReviewRecord;
use modules\reviewermodule\models\Review as ReviewModel;
use modules\reviewermodule\records\Person;
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
class Review extends Component
{
    // Public Methods
    // =========================================================================

    public function createReview(Person $person) : ReviewRecord
    {
        $review             = new ReviewRecord();
        $review->person_id  = $person->id;
        $saved              = $review->save();
        return $review;
    }

    public function findByUid(string $uid)
    {
        return ReviewRecord::find()->where(['uid' => $uid])->one();
    }

    public function all(): array
    {
        return ReviewRecord::find()
            ->innerJoinWith(['question'])
            ->with('person')
            ->all();

    }

    public function find(int $id)
    {
        return ReviewRecord::find()
            ->with('question')
            ->with('person')
            ->where(['id' => $id])
            ->one();
    }

    public function delete(int $id)
    {
        $review = $this->find($id);
        $questions = collect($review->question);
        $questions->each(function ($question){
            $question->delete();
        });
    }

}
