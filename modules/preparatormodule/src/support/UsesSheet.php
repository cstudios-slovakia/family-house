<?php
namespace modules\preparatormodule\support;

use Craft;
use modules\preparatormodule\exceptions\ReadingIsNotPossibleException;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use yii\base\Exception;

/**
 * Trait UsesSheet
 * @package modules\preparatormodule\support
 */
trait UsesSheet
{
    /**
     * @var ExcelReader
     */
    protected $excelReader;
    /**
     * @var SheetReader
     */
    protected $sheetReader;

    protected $fileInput = null;

    /** @var Worksheet */
    protected $sheet;

    /**
     *
     */
    public function bootSheetReader()
    {
        if (!isset($this->sheet)) {
            $this->excelReader =  new ExcelReader();
            $this->sheetReader =  new SheetReader();

            $this->sheetReader->setFileInput($this->getFileInput());
            $this->sheetReader->setExcelReader($this->excelReader);
            return $this;
        }


    }

    /**
     * @return Worksheet
     *
     * @throws ReadingIsNotPossibleException
     *
     */
    public function sheet()
    {
        if (isset($this->sheet)) {
            return $this->sheet;
        }
        return $this->sheetReader->getSheet();
    }

    /**
     * @return mixed
     */
    public function getFileInput()
    {
        if (is_null($this->fileInput)){
            throw new Exception('No calculation file was assigned', 500);
        }
        return $this->fileInput;
    }

    /**
     * @param mixed $fileInput
     * @return UsesSheet
     */
    public function setFileInput($fileInput)
    {
        $this->fileInput = $fileInput;
        return $this;
    }

    /**
     * @param Worksheet $sheet
     */
    public function setSheet(Worksheet $sheet)
    {
        $this->sheet = $sheet;
        return $this;
    }
}