<?php

namespace modules\preparatormodule\support;

class SheetChecker
{
    /**
     * @var string
     */
    protected $inputPath;

    /** @var array */
    public $messages = [];



    public static function canNotBeEmpty(): array
    {
        return [
            'A3:H6',
            'D10:AB10',
            'B11:B20',

            'D21:AB21',
            'B22:B31',

            'D32:AB32',
            'B33:B42',

            'D43:AB43',
            'B44:B53',

            'D54:AB54',
            'B55:B64',

            'D65:AB65',
            'B66:B75',

            'A81:C87',
            'A90:C90',

            'B93:B102',

//            'D107:E109',
//            'D112:E127',
//            'D131:E136',
//            'D139:E153',
//            'D156:E167',
//            'D170:E185',
//            'D188:E200',

            'D107:D127',
            'D131:D136',
            'D139:D153',
            'D156:D167',
            'D170:D185',
            'D188:D200',


            'D139:E144',
            'D149:E149',
            'D156:E159',
            'D170:E177',
            'D188:E193',

            'D105:E108',
            'D117:E120',
            'D125:E127',
            'D132:E132',
            'D134:E135',
            'D160:E160',
            'D178:E178',

        ];
    }

    public static function canBeOnlyNumber(): array
    {
        return [
            'E139:E144',
            'E149:E149',
            'E156:E159',
            'E170:E177',
            'E188:E193',

            'E105:E108',
            'E117:E120',
            'E125:E127',
            'E132:E132',
            'E134:E135',
            'E160:E160',
            'E178:E178',

        ];
    }

    /**
     * @return string
     */
    public function getInputPath(): string
    {
        return $this->inputPath;
    }

    /**
     * @param string $inputPath
     */
    public function setInputPath(string $inputPath): void
    {
        $this->inputPath = $inputPath;
    }

 
}