<?php
/**
 * reviewer module for Craft CMS 3.x
 *
 * It is a quote, reviewer.
 *
 * @link      eugen@juhos.sk
 * @copyright Copyright (c) 2019 Eugen Juhos
 */

namespace modules\reviewermodule\variables;

require_once __DIR__.'/../vendor/autoload.php';

use craft\helpers\Html;
use modules\reviewermodule\ReviewerModule;

use Craft;
use yii\base\ViewRenderer;
use yii\web\View;

/**
 * reviewer Variable
 *
 * Craft allows modules to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.reviewerModule }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Eugen Juhos
 * @package   ReviewerModule
 * @since     1.0.0
 */
class ReviewerModuleVariable
{
    // Public Methods
    // =========================================================================

    public function name()
    {
        return 'reviewer block';
    }



    public function formFieldRequires(string $mark = '*') : string
    {
        return Html::tag('span',$mark,['class' => 'field-mark__required']);
    }

    public function reviewPart(string $key)
    {
        $content    = collect($this->reviewContent());

        return $content->filter(function ($part) use ($key){

            if ($part['q_name'] === $key){
                return $part;
            }
        })->first();

    }

    public function renderPart(string $key, $model = null)
    {
        $partData = $this->reviewPart($key);


        if ($partData['q_type'] === 'radio'){

            return Craft::$app->view->renderTemplate('_partials/inputs/radio',['radioData' => $partData, 'requiredMark' => $this->formFieldRequires(),'model'=> $model, 'key' => $key]);
        }

        if ($partData['q_type'] === 'slider'){
            return Craft::$app->view->renderTemplate('_partials/inputs/slider',['sliderData' => $partData, 'requiredMark' => $this->formFieldRequires(),'model'=> $model, 'key' => $key]);
        }
    }

    public function answer(string $key): ?array
    {
        return collect($this->reviewContent())->filter(function($content) use ($key){
            if ($key === $content['q_name']){

                return $content;
            }
        })->first();
    }

    public function reviewContent(): array
    {
        return [
            [
                'title' => 'Ako hodnotíte komunikáciu s našou firmou pri vybavovaní projektu?',
                'q_name' => 'q_10',
                'q_type' => 'radio',
                'q_options' => [
                    'opt_1' => 'Veľmi dobrá komunikácia',
                    'opt_2' => 'Priemerná komunikácia',
                    'opt_3' => 'Podpriemerná komunikácia',
                    'other' => 'Iné',
                ],
                'attr'  => [
                    'color'     => 'red',
                    'stat'      => true,
                ]
            ],
            [
                'title' => 'Ako ste spokojný/á s kvalitou Vami zakúpeného projektu?',
                'q_name' => 'q_20',
                'q_type' => 'slider',
                'q_options' => [
                    'opt_1' => 'Vôbec',
                    'opt_2' => 'Úplne',

                ],
                'attr'  => [
                    'color'     => 'yellow',
                    'stat'      => true,
                    'commentable' => true
                ],

            ],
            [
                'title' => 'Ako hodnotíte rýchlosť vypracovania/odovzdania projektu?',
                'q_name' => 'q_30',
                'q_type' => 'slider',
                'q_options' => [
                    'opt_1' => 'Veľmi pomalé odovzdanie',
                    'opt_2' => 'Rýchle odovzdanie',

                ],
                'attr'  => [
                    'color'     => 'green',
                    'stat'      => true,
                    'commentable' => true
                ]
            ],
            [
                'title' => 'Ste spokojný/á s rozsahom nami vypracovaného projektu?',
                'q_name' => 'q_40',
                'q_type' => 'slider',
                'q_options' => [
                    'opt_1' => 'Nie',
                    'opt_2' => 'Veľmi',

                ],
                'attr'  => [
                    'color'     => 'blue',
                    'stat'      => true,
                    'commentable' => true
                ]
            ],
            [
                'title' => 'Obdržali ste od nás aj cenovú ponuku na stavbu domu?',
                'q_name' => 'q_50',
                'q_type' => 'radio',
                'q_options' => [
                    'opt_1' => 'Áno',
                    'opt_2' => 'Nie',

                ],
                'attr'  => [
                    'color'     => 'magenta',
                    'stat'      => true,
                ],
                'logic' => [
                    'chained'   => [
                        'check' => 'opt_1',
                        'chain' => 'q_60'
                    ]
                ]
            ],
            [
                'title' => 'Ako hodnotíte cenovú ponuku na stavbu domu?',
                'q_name' => 'q_60',
                'q_type' => 'radio',
                'q_options' => [
                    'opt_1' => 'Vyhovujúca',
                    'opt_2' => 'Porovnateľná s konkurenčnými ponukami',
                    'opt_3' => 'Vyššia ako u konkurencie',
                    'other' => 'Iné',

                ],
                'attr'  => [
                    'color'     => 'purple',
                    'stat'      => true,
                ],
                'logic' => [
                    'hidden'   => true
                ]
            ],
            [
                'title' => 'Plánujete stavať s našou firmou?',
                'q_name' => 'q_70',
                'q_type' => 'radio',
                'q_options' => [
                    'opt_1' => 'Jednoznačne áno',
                    'opt_2' => 'Ešte sa rozhodujem',
                    'opt_3' => 'Nie',
                ],
                'attr'  => [
                    'color'     => 'navy',
                    'stat'      => true,
                ],
                'logic' => [
                    'chained'   => [
                        'check' => 'opt_3',
                        'chain' => 'q_80'
                    ]
                ]
            ],
            [
                'title' => 'Ak ste zvolili odpoveď nie, Váš dôvod?',
                'q_name' => 'q_80',
                'q_type' => 'radio',
                'q_options' => [
                    'opt_1' => 'Nie, keďže nemám dostatočné množstvo referencií od Vás',
                    'opt_2' => 'Nie, nakoľko budeme riešiť stavbu svojpomocne (rodina, známi, atď.)',
                    'opt_3' => 'Nie, cena sa mi zdala byť pomerne vysoká u Vašej firmy',
                    'opt_4' => 'Nie, keďže staviate ďaleko od nás',
                    'opt_5' => 'Nie, zaujala nás iná firma',
                    'opt_6' => 'Áno, vybrali sme Vás',
                    'other' => 'Iné',
                ],
                'attr'  => [
                    'color'     => '#adc526',
                    'stat'      => true,
                ],
                'logic' => [
                    'hidden'   => true
                ]
            ],
            [
                'title' => 'Ďalšie Vaše podnety, pripomienky?',
                'q_name' => 'q_90',
                'q_type' => 'textarea',
                'attr'  => [

                    'stat'      => false,
                ]

            ],
        ];
    }

}
