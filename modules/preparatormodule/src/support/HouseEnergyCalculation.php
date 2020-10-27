<?php

namespace modules\preparatormodule\support;

use modules\preparatormodule\exceptions\SheetNotDefinedHttpException;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;


class HouseEnergyCalculation extends SheetData
{
    const INDEX = 'B';
    const KEY = 'C';

    /** @var array */
    public $selections;

    public function getHouseEnergy($key = '') : HouseEnergyCalculation
    {
        $range  = SheetSettings::getHouseEnergyRange();
        try {

            $sheet = $this->getSheet();

            $cells = $sheet->rangeToArray($range, null, true, true, true);

            $data = collect($cells)->filter(function ($row, $rowIndex){
                if (!is_null($row[self::KEY])) {
                    return $row;
                }
            });

            if (!empty($key)) {
                $data = $data->filter(function ($row, $rowIndex) use ($key) {
                    if ($row[self::KEY] === $key) {
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

    protected function makeOptions(array $row, int $index) : void
    {
        $options    = new HouseEnergyOptions();
        $options->index = $row[self::INDEX];
        $options->key = $key = $row[self::KEY];
        $options->options = $this->buildOptions($row, $index);
        if ($key) {
            $this->selections[$index] = $options;
        }
    }

    protected function buildOptions(array $row, int $rowIndex) : array
    {
        $iterator = new RowCellIterator($this->getSheet(), $rowIndex, 'D','AB');
        $options = [];

        foreach ($iterator as $column) {
            if (($value = (float)$column->getValue()) > 0) {
                $options[$column->getColumn()] = $value;
            }
        }

        return $options;
    }

}