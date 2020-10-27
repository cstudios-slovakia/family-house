<?php

namespace modules\preparatormodule\support;

use modules\preparatormodule\exceptions\SheetNotDefinedHttpException;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CalculationElementsCollection extends SheetData
{
    const CALC_TITLE = 'C';
    const CALC_KEY = 'D';
    const CALC_VALUE = 'E';
    /** @var Worksheet */
    protected $sheet;

    /** @var array */
    public $elements;

    /** @var array */
    public $ranges = [];

    public function __construct(array $ranges)
    {
        $this->ranges = $ranges;
    }

    public function getHouseCalculationData() : CalculationElementsCollection
    {
        $ranges  = $this->ranges;
        try {

            $sheet = $this->getSheet();
            $calculationDatas = [];
            foreach ($ranges as $range) {

                $cells = $sheet->rangeToArray($range, null, true, true, true);
                $cells = collect($cells)->map(function ($cell, $rowIndex){
                    if (is_null($cell[self::CALC_TITLE])) {
                        $cell[self::CALC_TITLE] = $cell[self::CALC_KEY];
                    }
                    $calculationElement         = new CalculationElement();
                    $calculationElement->key    = $cell[self::CALC_KEY];
                    $calculationElement->title  = $cell[self::CALC_TITLE];
                    $calculationElement->value  = $cell[self::CALC_VALUE];
                    $this->elements[$rowIndex]  = $calculationElement;
                });

            }

            return $this;

        } catch (SheetNotDefinedHttpException $exception) {
            \Craft::error($exception->getMessage());
            throw $exception;
        } finally {
            return $this;
        }
    }


}