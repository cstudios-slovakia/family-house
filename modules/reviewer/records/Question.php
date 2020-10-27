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
class Question extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

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
    public static function tableName()
    {
        return '{{%reviewermodule_questions}}';
    }

    public function rules()
    {
        return [
//            [[
//                'review_id',
//                'q_10',
//                'q_20',
//                'q_30',
//                'q_40',
//                'q_50',
//                'q_60',
//                'q_70',
//                'q_80',
//            ], 'required'],
            [[
                'review_id',
                'q_10',
                'q_20',
                'q_30',
                'q_40',
                'q_20_comment',
                'q_30_comment',
                'q_40_comment',
                'q_50',
                'q_60',
                'q_70',
                'q_80',
                'q_10_other',
                'q_60_other',
                'q_80_other',
                'q_90',

            ],'safe'],
        ];

    }



    public function getReview()
    {
        return $this->hasOne(Review::className(), ['id' => 'review_id']);
    }


}
