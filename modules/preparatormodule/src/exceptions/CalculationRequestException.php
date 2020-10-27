<?php

namespace modules\preparatormodule\exceptions;


class CalculationRequestException extends PreparatorBaseException
{
    protected $message = 'Calculation can not started, because of wrongly defined request.';

    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }


    /** @inheritDoc */
    public function getName()
    {
        return 'CalculationRequestException';
    }
}