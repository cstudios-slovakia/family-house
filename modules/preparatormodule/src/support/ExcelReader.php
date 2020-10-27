<?php

namespace modules\preparatormodule\support;
require_once __DIR__ . '/../../../reviewer/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ExcelReader
{
    /** @var Xlsx */
   protected $reader;

   public function __construct()
   {
       $this->reader    = new Xlsx();
       $this->reader->setReadDataOnly(true);
   }



    /**
     * @return Xlsx
     */
    public function getReader(): Xlsx
    {
        return $this->reader;
    }

    /**
     * @param Xlsx $reader
     */
    public function setReader(Xlsx $reader): void
    {
        $this->reader = $reader;
    }


}