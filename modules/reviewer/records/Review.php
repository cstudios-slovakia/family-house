<?php
/**
 * reviewer module for Craft CMS 3.x
 *
 * It is a quote, reviewer.
 *
 * @link      eugen@juhos.sk
 * @copyright Copyright (c) 2019 Eugen Juhos
 */

namespace modules\reviewermodule\records;

use craft\helpers\UrlHelper;
use modules\reviewermodule\ReviewerModule;

use Craft;
use craft\db\ActiveRecord;

/**
 * Review Record
 *
 * ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 * Active Record implements the [Active Record design pattern](http://en.wikipedia.org/wiki/Active_record).
 * The premise behind Active Record is that an individual [[ActiveRecord]] object is associated with a specific
 * row in a database table. The object's attributes are mapped to the columns of the corresponding table.
 * Referencing an Active Record attribute is equivalent to accessing the corresponding table column for that record.
 *
 * http://www.yiiframework.com/doc-2.0/guide-db-active-record.html
 *
 * @author    Eugen Juhos
 * @package   ReviewerModule
 * @since     1.0.0
 */
class Review extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================
    public $review_id;
     /**
     * Declares the name of the database table associated with this AR class.
     * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]]
     * with prefix [[Connection::tablePrefix]]. For example if [[Connection::tablePrefix]] is `tbl_`,
     * `Customer` becomes `tbl_customer`, and `OrderItem` becomes `tbl_order_item`. You may override this method
     * if the table is not named after this convention.
     *
     * By convention, tables created by modules should be prefixed with the module's
     * name and an underscore.
     *
     * @return string the table name
     */
//    public static function tableName()
////    {
////        return '{{%reviewermodule_questions}}';
////    }
////
////    public function rules()
////    {
////        return [
////            [[
////                'q_10',
////                'q_20',
////                'q_30',
////                'q_40',
////                'q_50',
////                'q_60',
////                'q_70',
////                'q_80',
////            ], 'required'],
////        ];
////
////    }

    public static function tableName()
    {
        return '{{%reviewermodule_reviews}}';
    }

//    public function rules()
//    {
//        return [
//            [[
//                'q_10',
//                'q_20',
//                'q_30',
//                'q_40',
//                'q_50',
//                'q_60',
//                'q_70',
//                'q_80',
//            ], 'required'],
//        ];
//
//    }

    public function getPerson()
    {
        return $this->hasOne(Person::className(), ['id' => 'person_id']);
    }

    public function getQuestion()
    {
        return $this->hasMany(Question::className(), [ 'review_id' => 'id' ]);
    }

    public function getEmailLink(): string
    {
        Craft::$app->urlManager->enablePrettyUrl;
        $tracking   = [
            'utm_source'    => 'dotaznik',
            'utm_medium'    => 'email',
            'utm_campaign'    => $this->uid
        ];

        return UrlHelper::siteUrl('dotaznik/formular', $tracking);

        return Craft::$app->urlManager->createAbsoluteUrl(['reviewing/create'] + $tracking);
    }

}
