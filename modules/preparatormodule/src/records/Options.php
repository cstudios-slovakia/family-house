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

/**
 * Options Record
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
class Options extends ActiveRecord
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
        return '{{%preparatormodule_options}}';
    }

    public function rules()
    {
        return [
            [['value', 'house_type_id', 'is_default', 'value_index'], 'required'],
            [['value', 'house_type_id', 'value_index'], 'integer','min' => 0],
            [['value', 'house_type_id', 'value_index', 'is_default'], 'safe'],


        ];
    }
}