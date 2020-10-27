<?php
/**
 * preparator module for Craft CMS 3.x
 *
 * Excel reader and preparator
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\preparatormodule\records;

use modules\preparatormodule\PreparatorModule;

use Craft;
use craft\db\ActiveRecord;
use modules\reviewermodule\records\Review;

/**
 * HouseTypes Record
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
 * @author    @lostika
 * @package   PreparatorModule
 * @since     1.0.0
 */
class HouseTypes extends ActiveRecord
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
        return '{{%preparatormodule_housetypes}}';
    }

    public function rules()
    {
        return [
            [['type_key', 'type_name', 'house_id', 'type_index'], 'required'],
            [['type_key', 'house_id', 'type_index'], 'number','min' => 0],
            [['type_key', 'type_name', 'house_id', 'type_index'], 'safe'],
            ['type_name', 'string'],
            ['house_id', 'integer','min' => 0],
        ];
    }

    public function getOptions()
    {
        return $this->hasMany(Options::className(), [ 'house_type_id' => 'id' ]);
    }

}
