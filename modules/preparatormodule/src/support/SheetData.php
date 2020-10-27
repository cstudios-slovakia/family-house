<?php
namespace modules\preparatormodule\support;

use modules\preparatormodule\exceptions\SheetNotDefinedHttpException;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class SheetData
{
    /** @var Worksheet */
    protected $sheet;

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
     * @return SheetData
     */
    public function setSheet(Worksheet $sheet): SheetData
    {
        $this->sheet = $sheet;
        return $this;
    }

}