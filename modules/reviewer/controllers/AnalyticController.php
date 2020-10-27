<?php
/**
 * reviewer module for Craft CMS 3.x
 *
 * It is a quote, reviewer.
 *
 * @link      eugen@juhos.sk
 * @copyright Copyright (c) 2019 Eugen Juhos
 */

namespace modules\reviewermodule\controllers;

require_once __DIR__.'/../vendor/autoload.php';

use Carbon\Carbon;
use craft\helpers\UrlHelper;

use \Illuminate\Support\Collection;
use modules\reviewermodule\assetbundles\ReviewerModule\ReviewerModuleChartAsset;
use modules\reviewermodule\records\Person;
use modules\reviewermodule\records\Question;
use modules\reviewermodule\ReviewerModule;

use Craft;
use craft\web\Controller;
use modules\reviewermodule\variables\ReviewerModuleVariable;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii2tech\spreadsheet\Spreadsheet;

/**
 * Person Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your module’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Eugen Juhos
 * @package   ReviewerModule
 * @since     1.0.0
 */
class AnalyticController extends Controller
{

    public function actionShow()
    {
        $form   = collect((new ReviewerModuleVariable())->reviewContent());
//        $form   = Collection::make((new ReviewerModuleVariable())->reviewContent()) ;

        $intoAnalytic = $form->filter(function($content){
            if ($content['attr']['stat']){
                return $content;
            }
        });

        // answers to analyze
        $answers    = ReviewerModule::$instance->question->all();

        $colors     = [
            '#E5B83C',
            '#A7E2E5',
            '#3345E5',
            '#2AE552',
            '#A05137',
            '#07A03E',
            '#458EA0',
            '#A0004A',
        ];
        // question for analytic sort by name
        $mapped     = $intoAnalytic->keyBy(function($content){
            return $content['q_name'];
        })->map(function ($content, $key) use ($answers,$colors){
            // search key against answers and collect answers
            $choosedOptions = collect($answers)->map(function(Question $question) use ($key){
                return $question->{$key};
            });
//            Craft::dd($choosedOptions->toArray());
            $optionsToCount = collect($choosedOptions->toArray())->filter(function($answer){
                if (is_integer($answer) || is_string($answer)){
                    return $answer;
                }
            });
//            Craft::dd($optionsToCount);
            $data = array_count_values($optionsToCount->toArray());

            $labels     = collect(array_keys($data))->map(function($label) use ($content){
                if ($content['q_type'] == 'slider'){
                    return $label;
                }
                return $content['q_options'][$label];
            });

            return [
                'title'     => $content['title'],
                'color'     => $content['attr']['color'],
                'mapped'    => $choosedOptions,
                'data' => $data,
                'chart' => [
                    'data'  => json_encode(array_values($data)),
                    'label' => json_encode($labels->toArray()),
                    'colors'    => json_encode($colors)
                ]
            ];
        });

        $this->view->registerAssetBundle(ReviewerModuleChartAsset::className());

        return $this->renderTemplate('reviewermodule/chart/show',['mapped' => $mapped, 'selectedSubnavItem' => $this->id] );
    }


    /**
     * @param array $params
     * @return array
     */
    protected function renderParams($params = [])
    {
        $params['selectedSubnavItem'] = $this->id;
        return $params;
    }

    public function actionExport()
    {
        // current attributes
        $attributes    = (new \modules\reviewermodule\models\Question())->attributes;
        // remove custom answers and no questions
        unset(
            $attributes['review_id'],
            $attributes['other'],
            $attributes['q_10_other'],
            $attributes['q_60_other'],
            $attributes['q_80_other']
//            $attributes['q_90']
        );

        // build up sheet header
        $columns = [];
        // update: requested review name
        $columns[] = [
            'attribute' => 'name',
            'header'    => 'Meno a prizvisko'
        ];;
        foreach ($attributes as $key => $value){
            $answer   = (new ReviewerModuleVariable())->answer($key);
            if (! is_null($answer)){
                $columns[]  = [
                    'attribute' => $key,
                    'header'    => $answer['title']
                ];
            }
        }

        // collect answers
        $answers = Question::find()->with('review')->all();

        // prepare for spreadsheet
        $sheetData  = collect($answers)->unique('review_id')->transform(function ($answer) use($attributes){
            // we have to add answered option to attributes
             $mappedData = collect($attributes)->map(function ($value,$attribute) use ($answer){

                $key = $answer->{$attribute};
                $content = (new ReviewerModuleVariable())->answer($attribute);

                // slider has numeric value as answer
                if ($content['q_type'] !== 'slider' && !is_null($key) && $content['q_type'] !== 'textarea'){
                    if ($key === 'other'){
                       $otherKey     = $content['q_name'].'_'.$key;
                       return $content['q_options'][$key] .': ' .$otherAnswer = $answer->{$otherKey};
                    };
                    return $content['q_options'][$key];
                } else{
                    if ($content['q_type'] === 'slider' && $key < 7){
                        return $key .': '.$answer->{$attribute.'_comment'};
                    }
                    return $key;
                }
            });

            $customData = collect(['name' => $answer->review->person->getFullName()]);
            $customData = $customData->merge($mappedData->toArray());

            return $customData;
        })->toArray();

        $exporter = (new Spreadsheet([
            'title' => 'Analytics',
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $sheetData,
            ]),
            'columns'   => $columns
        ]))->render();

        return $exporter->send('analytic-'.Carbon::now()->format('Y-m-d').'.xls');
    }

}
