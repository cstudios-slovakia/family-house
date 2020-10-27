<?php

namespace modules\preparatormodule\support;

use craft\events\ExceptionEvent;
use craft\web\ErrorHandler;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use modules\preparatormodule\exceptions\SheetNotDefinedHttpException;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use yii\BaseYii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class HousePrices
{
    const TITLE = 'A';
    const INDEX = 'B';
    const POSSIBILITIES = 'C';

    /** @var Worksheet */
    protected $sheet;
    /** @var array */
    public $selections;

    public function getHousePrices($possibilities = '') : HousePrices
    {
        $range  = SheetSettings::getHousePricesRange();
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
        $options    = new HousePriceOptions();
        $options->index = $row[self::INDEX];
        $options->title = $row[self::TITLE];
        $options->possibility = $row[self::POSSIBILITIES];
        $options->options       = $this->buildOptions($row, $index);
        if ($this->variantRowCheck($row)) {
            $this->selections[$index] = $options;
        }
    }

    protected function buildOptions(array $row, int $rowIndex) : array
    {
        $iterator = new RowCellIterator($this->getSheet(), $rowIndex, 'D','AB');
        $options = [];
        foreach ($iterator as $column) {
            if (($price = (float)$column->getValue()) > 0) {
                $options[$column->getColumn()] = $price;
            }
        }

        return $options;
    }

    public function variantRowCheck(array $row): bool
    {
        if (!empty($row[self::INDEX]) && !empty($row[self::TITLE]) && !empty($row[self::POSSIBILITIES])) {
            return true;
        }
        return false;
    }

    /**
     * @return Worksheet
     * @throws SheetNotDefinedHttpException
     */
    public function getSheet(): Worksheet
    {
        if (is_null($this->sheet)) {
            throw new SheetNotDefinedHttpException(500);
        }
        return $this->sheet;
    }

    /**
     * @param Worksheet $sheet
     * @return HousePrices
     */
    public function setSheet(Worksheet $sheet): HousePrices
    {
        $this->sheet = $sheet;
        return $this;
    }
}