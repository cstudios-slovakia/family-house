<?php
/**
 * preparator module for Craft CMS 3.x
 *
 * Excel reader and preparator
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\preparatormodule\models;

use modules\preparatormodule\PreparatorModule;

use Craft;
use craft\base\Model;

/**
 * Reader Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    @lostika
 * @package   PreparatorModule
 * @since     1.0.0
 */
class CalculationDataModel extends Model
{
    public $identification;
    public $value;


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
            [['identification', 'value'], 'required'],
            [['identification', 'value'], 'safe'],
            [['identification'], 'string'],
            [['identification'], 'unique'],
            [['value'], 'number','min' => 0],
            [['value'], 'default', 'value' => 1],
        ];
    }
}