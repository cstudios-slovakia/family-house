<?php

namespace modules\preparatormodule\exceptions;


class ReadingIsNotPossibleException extends PreparatorBaseException
{
    protected $message = 'Something is wrong with file input or reader is not correctly defined.';



    /** @inheritDoc */
    public function getName()
    {
        return 'ReadingIsNotPossibleException';
    }
}