<?php

namespace modules\preparatormodule\support;

use craft\events\ExceptionEvent;
use craft\web\ErrorHandler;
use Illuminate\Support\Facades\Event;
use modules\preparatormodule\exceptions\SheetNotDefinedHttpException;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use yii\BaseYii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class HouseTypeSelections extends SheetData
{

    /** @var int */
    public $selectKey;

    /** @var array */
    public $selections;

    public function makeSelection(string $range)
    {
        try {

            $sheet = $this->getSheet();

            $cells = $sheet->rangeToArray($range, null, true, true, true);

            collect($cells)->each(function ($row, $rowIndex){
                $houseTypeOptions                   = new HouseTypeOptions();

                $houseTypeOptions->options          = $this->makeOptions($rowIndex);

                $houseTypeOptions->selectName       =  $row['B'];
                $houseTypeOptions->selectKey        = (int) $row['C'];

                if ($houseTypeOptions->selectKey > 0) {
                    $this->selections[$rowIndex]    = $houseTypeOptions;
                }

            });
            return $this;
        } catch (SheetNotDefinedHttpException $exception) {
            \Craft::error($exception->getMessage());
            throw $exception;
        }


    }

    protected function makeOptions(int $rowIndex)
    {

        $iterator = new RowCellIterator($this->getSheet(), $rowIndex, 'D','AB');
        $options = [];

        foreach ($iterator as $column) {
            $value = $column->getValue();

            if (is_string($value) || (is_float($value) && $value > 0))
                $options[$column->getColumn()] = $value;
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getSelections(): array
    {
        return $this->selections;
    }

}