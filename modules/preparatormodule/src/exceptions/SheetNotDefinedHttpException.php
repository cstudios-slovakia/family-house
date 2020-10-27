<?php

namespace modules\preparatormodule\exceptions;


use yii\web\HttpException;
use yii\web\Response;


class SheetNotDefinedHttpException extends HttpException
{
    protected $message = 'Sheet is not defined, calculation stopped with critical error.';

    public function __construct($status, $message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($status, $this->getMessage(), $code, $previous);
    }

//    /** @inheritDoc */
//    public function getName()
//    {
//        return Response::$httpStatuses[500];
//        return 'SheetNotDefinedException';
//    }
}