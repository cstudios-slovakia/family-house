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

use craft\errors\ElementNotFoundException;
use modules\reviewermodule\ReviewerModule;

use Craft;
use craft\base\Component;
use modules\reviewermodule\records\Person as PersonRecord;
use modules\reviewermodule\models\Person as PersonModel;

/**
 * Person Service
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
class Person extends Component
{
    // Public Methods
    // =========================================================================

    public function all()
    {
        return PersonRecord::find()->all();
    }

    public function find(int $id) : PersonRecord
    {
        return PersonRecord::findOne(['id'=>$id]);
    }

    public function update(int $id)
    {
        $person = new PersonModel();

        return $person->updatePerson($id);
    }

    public function removeOwnedReviews(int $id)
    {
        $person = $this->find($id);

        if (!$person){
            throw new ElementNotFoundException('Person was not found.');
        }


        if (count($person->reviews) > 0){
            foreach ($person->reviews as $review){
                ReviewerModule::$instance->review->delete($review->id);
            }
        }

        return $person->delete();


    }

}
