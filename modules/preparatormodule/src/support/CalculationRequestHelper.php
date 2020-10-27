<?php
namespace modules\preparatormodule\support;

use modules\preparatormodule\PreparatorModule;
use modules\preparatormodule\services\CalculationRequestService;

trait CalculationRequestHelper
{
    protected $requestHelper;

    public function getRequestHelper() : CalculationRequestService
    {
        if (isset($this->requestHelper)) {
            return $this->requestHelper;
        }
        /** @var CalculationRequestService $calculationRequest */
        $calculationRequest = PreparatorModule::$instance->calculationRequestService;

        return $this->requestHelper = $calculationRequest;
    }
}