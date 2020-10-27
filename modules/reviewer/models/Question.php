<?php
/**
 * reviewer module for Craft CMS 3.x
 *
 * It is a quote, reviewer.
 *
 * @link      eugen@juhos.sk
 * @copyright Copyright (c) 2019 Eugen Juhos
 */

namespace modules\reviewermodule\models;

use modules\reviewermodule\ReviewerModule;

use Craft;
use craft\base\Model;

/**
 * Review Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Eugen Juhos
 * @package   ReviewerModule
 * @since     1.0.0
 */
class Question extends Model
{
    // Public Properties
    // =========================================================================

    public $q_10;
    public $q_10_other;
    public $q_20;
    public $q_20_comment;
    public $q_30;
    public $q_30_comment;
    public $q_40;
    public $q_40_comment;
    public $q_50;
    public $q_60;
    public $q_60_other;
    public $q_70;
    public $q_80;
    public $q_80_other;
    public $q_90;
    public $review_id;
    public $other;

//    public $questions;



    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            [[
                'q_10',
                'q_20',
                'q_30',
                'q_40',
                'q_50',
//                'q_60',
                'q_70',
//                'q_80',
                'review_id',
            ], 'required' , 'message' => 'Polia označené * sú požadované'],
            ['q_20_comment', 'required', 'when' => function($model) {
                return $model->q_20 < 7;
            }, 'message' => 'Polia označené * sú požadované ak hodnota je menšia ako 7'],
            ['q_30_comment', 'required', 'when' => function($model) {
                return $model->q_30 < 7;
            }, 'message' => 'Polia označené * sú požadované ak hodnota je menšia ako 7'],
            ['q_40_comment', 'required', 'when' => function($model) {
                return $model->q_40 < 7;
            }, 'message' => 'Polia označené * sú požadované ak hodnota je menšia ako 7'],
            ['q_60', 'required', 'when' => function($model) {
                return $model->q_50 == 'opt_1';
            }, 'message' => 'Polia označené * sú požadované'],
            ['q_80', 'required', 'when' => function($model) {
                return $model->q_70 == 'opt_3';
            }, 'message' => 'Polia označené * sú požadované'],
            ['q_90','safe']
        ];
    }



    public function insertReview(Review $review)
    {
        return ReviewerModule::$instance->review->addReview($review);
    }
}
