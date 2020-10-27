<?php

namespace modules\preparatormodule\support;

use Illuminate\Support\Collection;
use MathParser\Interpreting\Evaluator;
use MathParser\StdMathParser;
use modules\preparatormodule\exceptions\CalculationKeyException;
use modules\preparatormodule\exceptions\PreparatorBaseException;
use yii\log\Logger;

class CalculationElement
{
    /** @var string */
    public $title;

    /** @var string */
    public $key;

    /** @var float */
    public $value;

    /** @var string */
    public $formula = '';

    /** @var Collection */
    public $elements;

    public function getValue(): float
    {
        \Craft::getLogger()->log(['elementKey' =>$this->key,'elementValue' =>$this->value,'elementFormula' => $this->formula], Logger::LEVEL_TRACE);

        if ($this->value) {
            return $this->value;
        }

        if (!$this->value || !empty($this->formula)) {
            $keys = [];
            $formulaVariables = $this->regex($this->formula);

            foreach ($formulaVariables as $i => $formulaVariable) {
                $keys[] = $this->removeMarkers($formulaVariable);
            }

            $value = $this->formulaResult($keys);

            return $this->elements->get($this->key)->value = $value;
        }


        return $this->value ;
    }

    protected function removeMarkers($formula) : string
    {
        return str_replace('{{', '', str_replace('}}', '', $formula));
    }

    public function formulaResult($keys) : float
    {
        $parser     = new StdMathParser();
        $evaulator  = new Evaluator();
        $variables  = [];

        // remove markers from formula
//        $this->formula = $this->removeMarkers($this->formula);

        $formula    = $this->formula;
        // substitute formula variables with simpler version
        try {
            $variables = $this->substituteVariables($keys, $variables, $formula);

        } catch (PreparatorBaseException $exception) {
            \Craft::getLogger()->log($exception->getMessage(), LOG_ALERT);
        }

        $ast        = $parser->parse($formula);

        $evaulator->setVariables($variables);
        $value      = $ast->accept($evaulator);
//        if ($this->key == 'CWKA') {
//            $ast        = $parser->parse('f');
//
//            $evaulator->setVariables($variables);
//            $value      = $ast->accept($evaulator);
//
//            var_dump($ast);
//            var_dump($evaulator);
//            var_dump(array_sum($variables));
//            var_dump($value);
//            dd($formula);
//        }
        \Craft::getLogger()->log(['elementKey' =>$this->key,'elementValue' =>$this->value,'elementFormula' => $this->formula,'elementVariables' => $variables], Logger::LEVEL_TRACE);

        return $this->value = $value;
    }

    protected function substituteVariables(array $substitutables, array $result,&$formula) : array
    {
        $vars       = $this->getSubstitutableVariables();

//dd($substitutables);
//dd($formula);

        foreach ($substitutables as $i => $substitutable) {

            \Craft::getLogger()->log(['key' => $substitutable,'formula' => $formula,'hasKey' => $this->elements->has($substitutable)], Logger::LEVEL_TRACE);
            \Craft::getLogger()->log(['$substitutables i' => $i,'key' => $substitutable], Logger::LEVEL_TRACE);

            if (!$this->elements->has($substitutable)) {
                throw new CalculationKeyException('Substitutable key does not exist on elements collection.');
            }

            if (!array_key_exists($i, $vars)) {
                throw new CalculationKeyException('Index: '.$i.' key does not exist on SubstitutableVariables.');
            }

            $result[$vars[$i]]    = $this->elements->get($substitutable)->getValue();

            $formula = str_replace('{{'.$substitutable.'}}', $vars[$i], $formula);
            \Craft::getLogger()->log(['$formula' => $formula,'key' => $substitutable], Logger::LEVEL_TRACE);
        }

        return $result;
    }

    protected function regex(string $formula)
    {
        $re = '/\{\{[a-zA-Z0-9\s\+]+\}\}+/';

        preg_match_all($re, $formula, $output, PREG_PATTERN_ORDER);
        $matches = $output[0];

        return $matches;
    }

    protected function getSubstitutableVariables(): array
    {
        return [
            'a','b','c','d',
//            'e',
            'f','g','h','i','j','k','l','m','n','o','p','q','r','s','t'
        ];
    }
}