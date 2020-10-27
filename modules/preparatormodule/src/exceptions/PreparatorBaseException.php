<?php

namespace modules\preparatormodule\exceptions;

use yii\base\Exception;


class PreparatorBaseException extends Exception
{

    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        $m  = $this->getMessage();

        if ($message) {
            $m = $message;
        }

        parent::__construct($m, 500, $previous);
    }
}