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
use modules\reviewermodule\records\Person as PersonRecord;

/**
 * Person Model
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
class Person extends Model
{
    // Public Properties
    // =========================================================================

    public $first_name;
    public $last_name;
    public $email;
    public $client_number;

    // Public Methods
    // =========================================================================

    public function addPerson(array $input)
    {

        $person     = new PersonRecord();
        if(! $person->load($input,'')){
            return false;
        }

        $validator = $person->validate();

        return $person->save();

    }

    public function updatePerson(int $id)
    {
        $person     = PersonRecord::findOne($id);
        $person->setScenario(PersonRecord::SCENARIO_UPDATE);

        if ($person && $person->load(Craft::$app->request->bodyParams,'')){

            $person->client_number  = (int) Craft::$app->request->post('client_number');
            return $person->save();
        }

        return false;
    }

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
            [['first_name', 'last_name','email','client_number'], 'required'],
            [['client_number'], 'number'],
        ];

    }
}
