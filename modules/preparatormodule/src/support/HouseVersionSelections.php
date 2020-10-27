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

class HouseVersionSelections extends SheetData
{
    const TITLE = 'A';
    const POSSIBILITIES = 'B';

    /** @var array */
    public $selections;

    public function getHouseVersion($possibilities = '') : HouseVersionSelections
    {
        $range  = SheetSettings::getHouseVersionsDataCells();
        try {

            $sheet = $this->getSheet();

            $cells = $sheet->rangeToArray($range, null, true, true, true);

            $data = collect($cells)->filter(function ($row, $rowIndex){
                if (!is_null($row[self::TITLE])) {
                    return $row;
                }
            });

            if (!empty($possibilities)) {
                $data = $data->filter(function ($row, $rowIndex) use ($possibilities) {
                    if ($row[self::POSSIBILITIES] === $possibilities) {
                        return $row;
                    }
                });
            }

            $data->each(function ($row, $rowIndex){
                $this->makeOptions($row, $rowIndex);
            });

        } catch (SheetNotDefinedHttpException $exception) {
            \Craft::error($exception->getMessage());
            throw $exception;
        } finally {
            return $this;
        }
    }

    protected function makeOptions(array $row, int $index)
    {
        $options    = new HouseVersionOptions();
        $options->title = $row[self::TITLE];
        $options->possibility = $row[self::POSSIBILITIES];
        $options->options = $this->buildOptions($row, $index);
        $this->selections[$index] = $options;
    }

    protected function buildOptions(array $row, int $rowIndex) : array
    {
        $iterator = new RowCellIterator($this->getSheet(), $rowIndex, 'C','H');
        $options = [];

        foreach ($iterator as $column) {
            $price = (float)$column->getValue();
            $options[$column->getColumn()] = $price;
        }

        return $options;
    }




}