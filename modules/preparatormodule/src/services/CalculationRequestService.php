<?php
/**
 * preparator module for Craft CMS 3.x
 *
 * Excel reader and preparator
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\preparatormodule\services;

use Craft;
use craft\base\Component;
use modules\preparatormodule\exceptions\CalculationRequestException;

class CalculationRequestService extends Component
{
    protected $requestBody;



    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->requestBody = Craft::$app->request->getQueryParams();

        if (!empty(Craft::$app->request->rawBody)) {
            $this->requestBody = json_decode(Craft::$app->request->rawBody);
        }

    }

    public function getParameter(string $paramKey)
    {

        try {
            $requestBody = $this->getRequestBody();
            $value  = is_array($requestBody) ? $requestBody[$paramKey] : $requestBody->{$paramKey};

            return $value;

        } catch (CalculationRequestException $exception) {
            Craft::getLogger()->log($exception->getMessage(), LOG_ALERT);
        }
    }

    public function getRequestBody()
    {
        if (!isset($this->requestBody)) {
            throw new CalculationRequestException();
        }

        return $this->requestBody;
    }

}