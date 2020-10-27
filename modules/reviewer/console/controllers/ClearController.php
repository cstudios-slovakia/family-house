<?php
/**
 * reviewer module for Craft CMS 3.x
 *
 * It is a quote, reviewer.
 *
 * @link      eugen@juhos.sk
 * @copyright Copyright (c) 2019 Eugen Juhos
 */

namespace modules\reviewermodule\console\controllers;

use modules\reviewermodule\ReviewerModule;

use Craft;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Default Command
 *
 * The first line of this class docblock is displayed as the description
 * of the Console Command in ./craft help
 *
 * Craft can be invoked via commandline console by using the `./craft` command
 * from the project root.
 *
 * Console Commands are just controllers that are invoked to handle console
 * actions. The segment routing is module-name/controller-name/action-name
 *
 * The actionIndex() method is what is executed if no sub-commands are supplied, e.g.:
 *
 * ./craft reviewer-module/default
 *
 * Actions must be in 'kebab-case' so actionDoSomething() maps to 'do-something',
 * and would be invoked via:
 *
 * ./craft reviewer-module/default/do-something
 *
 * @author    Eugen Juhos
 * @package   ReviewerModule
 * @since     1.0.0
 */
class ClearController extends Controller
{
    // Public Methods
    // =========================================================================


    /**
     * Handle reviewer-module/default/do-something console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionAll()
    {
        $clearedQuestions = Craft::$app->db->createCommand()->truncateTable('{{%reviewermodule_questions}}')->execute();
        $clearedReviews = Craft::$app->db->createCommand()->truncateTable('{{%reviewermodule_reviews}}')->execute();
        Craft::$app->db->createCommand()->checkIntegrity(false)->execute();
        $clearedPersons = Craft::$app->db->createCommand()->truncateTable('{{%reviewermodule_persons}}')->execute();
        Craft::$app->db->createCommand()->checkIntegrity(true)->execute();

        $result = '{{%reviewermodule_questions}} Rows cleared: '.$clearedQuestions."\r";
        $result .= '{{%reviewermodule_reviews}} Rows cleared: '.$clearedReviews."\r";
        $result .= '{{%reviewermodule_persons}} Rows cleared: '.$clearedPersons."\r";

//        echo "Welcome to the console DefaultController actionDoSomething() method\n";

        echo  $result;
    }
}
