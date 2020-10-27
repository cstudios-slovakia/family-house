<?php

namespace modules\preparatormodule\support;
require_once __DIR__ . '/../../../reviewer/vendor/autoload.php';

use modules\preparatormodule\exceptions\ReadingIsNotPossibleException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SheetReader
{
    /** @var ExcelReader */
   protected $excelReader;

   /** @var string */
   protected $fileInput;

   /** @var Worksheet */
   protected $sheet;

   /** @var int */
    protected $sheetIndex = 0;

    public function getSheet() : Worksheet
    {
        if (!isset($this->excelReader) && !empty($this->fileInput)) {
            throw new ReadingIsNotPossibleException();
        }

        $excel = $this->getExcelReader()->getReader()->load($this->fileInput);

        if (isset($this->sheet)) {
            return $this->sheet;
        }

        try {
            return $excel->getSheet($this->sheetIndex);
        } catch (\Exception $exception) {
            $exception->getMessage();
        }

    }

    /**
     * @return ExcelReader
     */
    public function getExcelReader(): ExcelReader
    {
        return $this->excelReader;
    }

    /**
     * @param ExcelReader $excelReader
     */
    public function setExcelReader(ExcelReader $excelReader): void
    {
        $this->excelReader = $excelReader;
    }

    /**
     * @return string
     */
    public function getFileInput(): string
    {
        return $this->fileInput;
    }

    /**
     * @param string $fileInput
     */
    public function setFileInput(string $fileInput): void
    {
        $this->fileInput = $fileInput;
    }


}