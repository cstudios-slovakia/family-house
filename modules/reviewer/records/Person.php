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

use craft\helpers\Db;
use craft\helpers\StringHelper;
use modules\reviewermodule\ReviewerModule;

use Craft;
use craft\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Person Record
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
class Person extends ActiveRecord
{

    const SCENARIO_STORE = 'store';
    const SCENARIO_UPDATE = 'update';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_STORE] = ['first_name', 'last_name','email','client_number'];
        $scenarios[self::SCENARIO_UPDATE] = ['first_name', 'last_name','client_number'];
        return $scenarios;
    }

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
        return '{{%reviewermodule_persons}}';
    }

    public function rules()
    {
        return [
            [['first_name', 'last_name','email'], 'safe'],
            [['first_name', 'last_name','email'], 'safe','on' => self::SCENARIO_UPDATE],
            [['first_name', 'last_name','email','client_number'], 'required'],
            [['email'], 'unique'],
            [['client_number'], 'number'],
        ];

    }

    public function getReviews()
    {
        return $this->hasMany(Review::className(), [ 'person_id' => 'id' ]);
    }

    public function getFullName(): string
    {
        return  $this->first_name .' '. $this->last_name;
    }
}
