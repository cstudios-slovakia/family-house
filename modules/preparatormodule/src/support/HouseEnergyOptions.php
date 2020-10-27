<?php

namespace modules\preparatormodule\support;

class HouseEnergyOptions
{
    /** @var string */
    public $key;

    /** @var int */
    public $index;

    /** @var array */
    public $options;


    public static function getShortEnergyNameForTypeKey(string $io = '', int $typeKey = 0)
    {
        $energyShorts =  [
            'ea' => [
                1 => 'EA1',
                2 => 'EA2',
                3 => 'EA3',
                4 => 'EA4',
            ],
            'ca' => [
                1 => 'CA1',
                2 => 'CA2',
                3 => 'CA3',
                4 => 'CA4',
            ],
         ];
        $keys = [];

        if (!empty($io) && array_key_exists($io, $energyShorts)) {
            $keys = $energyShorts[$io];
        }

        if ( $typeKey > 0  && array_key_exists($typeKey, $keys)) {
            return $keys[$typeKey];
        }

        if (!empty($keys)) {
            return $keys;
        }

        return $energyShorts;
    }

}