<?php

namespace modules\preparatormodule\exceptions;


class CalculationKeyException extends PreparatorBaseException
{
    protected $message = 'Something went wrong with calculation key. Maybe keys are changed in excel.';



    /** @inheritDoc */
    public function getName()
    {
        return 'CalculationKeyException';
    }
}