<?php

namespace modules\preparatormodule\support;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SheetSettings
{
    public static function getHousePossibilities(): string
    {
        return 'C2:H2';
    }
    public static function getHouseVersionsDataCells(): string
    {
        return 'A3:H6';
    }

     public static function getHousePricesRange(): string
    {
        return 'A81:AB90';
    }

    public static function getHouseTypeDataCells(int $key = 0): string
    {
        $allDataCells = 'A10:AB75';

        if ($key < 1) {
            return $allDataCells;
        }

        switch ($key) {
            case $key === 1:
                $allDataCells = 'B11:AB20';
                break;
            case $key === 2:
                $allDataCells = 'B22:AB31';
                break;
            case $key === 3:
                $allDataCells = 'B33:AB42';
                break;
            case $key === 4:
                $allDataCells = 'B44:AB53';
                break;
            case $key === 5:
                $allDataCells = 'B55:AB64';
                break;
            case $key === 6:
                $allDataCells = 'B66:AB75';
                break;
        }

        return  $allDataCells;
    }

    public static function getHouseEnergyRange(): string
    {
        return 'B93:AB102';
    }


    public static function getValuableColumnIndexes(Worksheet $sheet,string $from = 'D', string $to = 'AB')
    {
        $iterator = $sheet->getColumnIterator($from, $to);
        $iteration = 1;
        $valuableColumnsWithnumericIndex = [];
        foreach ($iterator as $index => $column) {
            $valuableColumnsWithnumericIndex[$iteration]     = $iterator->key();
            $iteration++;
        }

        return $valuableColumnsWithnumericIndex;
    }

    public static function calculationStaticalRanges(): array
    {
        return  [
            'C105:E108',
            'C109:E112',
            'C117:E120',
            'C125:E127',
            'C132:E132',
            'C134:E135',
            'C160:E160',
            'C178:E178',
        ];
        // return  [
        //     'C105:E178'
        // ];
    }

    public static function calculationConstantRanges(): array
    {
        return  [
            'C139:E144',
            'C149:E149',
            'C156:E159',
            'C170:E177',
            'C188:E193'
        ];
        // return  [
        //     'C139:E193'
        // ];
    }

    public static function calculationFormulas(string $key = '')
    {
        $formulas   = [
            'CU1'  => '1 / {{CV1}}',
            'CU2'  => '1 / {{CV2}}',
            'CU3'  => '1 / {{CV3}}',
            'CU4'  => '1 / {{CV4}}',
            'CWK1'  => '{{CB2}}*{{CBI1}}*{{CU1}}',
            'CWK2'  => '{{CB1}}*{{CBI2}}*{{CU2}}',
            'CWK3'  => '{{CB2}}*{{CBI3}}*{{CU3}}',
            'CWK4'  => '{{CB4}}*{{CBI4}}*{{CU4}}',
            'CWKA'  => [
                1 => '{{CWK1}} + {{CWK2}} + {{CWK3}} + {{CWK4}} + {{CWK5}} + {{CWK6}}',
                2 => '{{CWK1}} + {{CWK2}} + {{CWK3}} + {{CWK4}} + {{CWK5}} + {{CWK7}}',
            ],
            'CPT'  => '{{CWKA}}*{{CPTK}}',
            'CPTV'  => '{{CPT}} - {{CVTZ}}*{{CFVTZ}}',
            'CSER' => '{{CPTV}} * ( ( {{CFPP}} / {{CUR}} ) - {{CX1}} )',
            'CSEP' => '{{CPTV}} * ( ( {{CFPP}} / {{CUP}} ) - {{CX1}} )',
            'CSES' => '{{CPTV}} * ( ( {{CFPP}} / {{CUS}} ) - {{CX1}} )',
            'CSEE' => '{{CPTV}} * ( ( {{CFPP}} / {{CUE}} ) - {{CX1}} )',
            'CQR' => '{{CPTV}} + {{CSER}}',
            'CQP' => '{{CPTV}} + {{CSEP}}',
            'CQS' => '( {{CPTV}} + {{CSES}} ) * {{CX2}}',
            'CQE' => '{{CPTV}} + {{CSEE}}',
            'CRP EA1+CA1' => '{{CQR}} + {{CZ1}} + {{CTV}} + {{CZ4}} * {{CB3}}',
            'CRP EA1+CA2' => '{{CQP}} + {{CZ1}} + {{CTV}} + {{CZ4}} * {{CB3}}',
            'CRP EA2+CA1' => '{{CQR}} + {{CZ2}} + {{CTV}} + {{CZ4}} * {{CB3}}',
            'CRP EA2+CA2' => '{{CQP}} + {{CZ2}} + {{CTV}} + {{CZ4}} * {{CB3}}',
            'CRP EA3+CA2' => '{{CQP}} + {{CZ3}} + {{CTV}} + {{CZ4}} * {{CB3}}',
            'CRP EA3+CA3' => '{{CQS}} + {{CZ3}} + {{CTV}} + {{CZ4}} * {{CB3}}',
            'CRP EA4+CA4' => '{{CQE}} + {{CTV}}',
            'CPE EA1+CA1' => '{{CRP EA1+CA1}} / {{CB3}} * {{CVFP}} + {{CRPP}} / {{CB3}} * {{CVFE}}',
            'CPE EA1+CA2' => '{{CRP EA1+CA2}} / {{CB3}} * {{CVFP}} + {{CRPP}} / {{CB3}} * {{CVFE}}',
            'CPE EA2+CA1' => '( {{CRP EA2+CA1}} - {{CTV}} ) / {{CB3}} * {{CVFT}} + {{CTV}} / {{CB3}} * {{CDAY}} * {{CVFT}} + {{CTV}} / {{CB3}} * ( 1- {{CDAY}} ) * {{CVFE}} + {{CRPT}} / {{CB3}} * {{CVFE}}',
            'CPE EA2+CA2' => '( {{CRP EA2+CA2}} - {{CTV}} ) / {{CB3}} * {{CVFT}} + {{CTV}} / {{CB3}} * {{CDAY}} * {{CVFT}} + {{CTV}} / {{CB3}} * ( 1- {{CDAY}} ) * {{CVFE}} + {{CRPT}} / {{CB3}} * {{CVFE}}',
            'CPE EA3+CA2' => '( {{CRP EA3+CA2}} + {{CRPC}} ) / {{CB3}} * {{CVFE}} * {{CCOP}}',
            'CPE EA3+CA3' => '( {{CRP EA3+CA3}} + {{CRPC}} ) / {{CB3}} * {{CVFE}} * {{CCOP}}',
            'CPE EA4+CA4' => '( {{CRP EA4+CA4}} + {{CRPE}} ) / {{CB3}} * {{CVFE}}',
            'CMC EA1+CA1' => '{{CRP EA1+CA1}} * {{CP}} / {{CM}} + {{CRPP}} * {{CE1}} / {{CM}}',
            'CMC EA1+CA2' => '{{CRP EA1+CA2}} * {{CP}} / {{CM}} + {{CRPP}} * {{CE1}} / {{CM}}',
            'CMC EA2+CA1' => '( {{CRP EA2+CA1}} - {{CTV}} ) * {{CD}} / {{CPEL}} / {{CM}} + {{CTV}} * {{CD}} * {{CDAY}} / {{CPEL}} / {{CM}} + {{CTV}} * ( 1 - {{CDAY}} ) * {{CE1}} / {{CM}} + {{CRPT}} * {{CE1}} / {{CM}}',
            'CMC EA2+CA2' => '( {{CRP EA2+CA2}} - {{CTV}} ) * {{CD}} / {{CPEL}} / {{CM}} + {{CTV}} * {{CD}} * {{CDAY}} / {{CPEL}} / {{CM}} + {{CTV}} * ( 1 - {{CDAY}} ) * {{CE1}} / {{CM}} + {{CRPT}} * {{CE1}} / {{CM}}',
            'CMC EA3+CA2' => '{{CRP EA3+CA2}} * {{CCOP}} * {{CE1}} / {{CM}} + {{CRPC}} * {{CE1}} / {{CM}}',
            'CMC EA3+CA3' => '{{CRP EA3+CA3}} * {{CCOP}} * {{CE1}} / {{CM}} + {{CRPC}} * {{CE1}} / {{CM}}',
            'CMC EA4+CA4' => '{{CRP EA4+CA4}} * {{CE2}} / {{CM}} + {{CRPE}} * {{CE2}} / {{CM}}',

        ];

        return $formulas;
    }

    public static function calculationFlow(): array
    {
        return [
            'CB1',
            'CB2',
            'CB3',
            'CB4',
            'CV1',
            'CV2',
            'CV3',
            'CV4',
            'CU1',
            'CU2',
            'CU3',
            'CU4',
            'CBI1',
            'CBI2',
            'CBI3',
            'CBI4',
            'CWK1',
            'CWK2',
            'CWK3',
            'CWK4',
            'CWK5',
            'CWK6',
            'CWK7',
            'CWKA',
            'CPTK',
            'CPT',
            'CVTZ',
            'CFVTZ',
            'CPTV',
            'CFPP',
            'CX1',
            'CUR',
            'CUP',
            'CUS',
            'CUE',
            'CSER',
            'CSEP',
            'CSES',
            'CSEE',
            'CX2',
            'CQR',
            'CQP',
            'CQS',
            'CQE',
            'CZ1',
            'CZ2',
            'CZ3',
            'CZ4',
            'CTV',
            'CRP EA1+CA1',
            'CRP EA1+CA2',
            'CRP EA2+CA1',
            'CRP EA2+CA2',
            'CRP EA3+CA2',
            'CRP EA3+CA3',
            'CRP EA4+CA4',
            'CRPP',
            'CRPT',
            'CRPC',
            'CRPE',
            'CVFP',
            'CVFT',
            'CVFE',
            'CDAY',
            'CCOP',
            'CPE EA1+CA1',
            'CPE EA1+CA2',
            'CPE EA2+CA1',
            'CPE EA2+CA2',
            'CPE EA3+CA2',
            'CPE EA3+CA3',
            'CPE EA4+CA4',
            'CM',
            'CPEL',
            'CD',
            'CP',
            'CE1',
            'CE2',
            'CMC EA1+CA1',
            'CMC EA1+CA2',
            'CMC EA2+CA1',
            'CMC EA2+CA2',
            'CMC EA3+CA2',
            'CMC EA3+CA3',
            'CMC EA4+CA4',
        ];
    }

    public static function getNumberedColumnIndex(string $columnIndex, array $columns): int
    {
        return (integer) array_keys($columns,$columnIndex)[0];
    }

    /**
     * Defined house energy table for comparisons.
     * @return array
     */
    public static function houseEnergyCategory(): array
    {
        return [
            'A0'    => 55,
            'A1'    => 108,
            'B'     => 216,
            'C'     => 324,
            'D'     => 432,
            'E'     => 540,
            'F'     => 648,
            'G'     => 9999,
        ];
    }

    public static function energyStaticIndexes() : array
    {
        return [
            'PO',
            'ST',
            'SR',
            'O'
        ];
    }

    public static function parseEnergyStaticIndexesWithCalculationKeys(): array
    {
        return [
            'CV1' => 'PO',
            'CV2' => 'ST',
            'CV3' => 'SR',
            'CV4' => 'O',
        ];
    }

    public static function calculatorProperties(): array
    {
        return array (
            1 =>
                array (
                    'web_title' => 'Zakladanie',
                    'web_position' => 6,
                    'col1' => 'Zakladanie na základových pásoch',
                    'col2' => 'Zakladanie na penovom skle',
                    'col3' => '',
                    'col4' => '',
                    'col5' => '',
                    'col6' => '',
                    'col7' => '',
                    'col8' => '',
                    'col9' => '',
                    'col10' => '',
                    'col11' => '',
                    'col12' => '',
                    'col13' => '',
                    'col14' => '',
                    'col15' => '',
                    'col16' => '',
                    'col17' => '',
                    'col18' => '',
                    'col19' => '',
                    'col20' => '',
                    'col21' => '',
                    'col22' => '',
                    'col23' => '',
                    'col24' => '',
                    'col25' => '',
                ),
            2 =>
                array (
                    'web_title' => 'Stenový nosný systém',
                    'web_position' => 5,
                    'col1' => 'Montovaný stenový systém hr.120mm',
                    'col2' => 'Murovaný-nebrúsený hr.250mm',
                    'col3' => 'Murovaný-nebrúsený hr.300mm',
                    'col4' => 'Murovaný-nebrúsený hr.380mm',
                    'col5' => 'Murovaný-brúsený hr.250mm',
                    'col6' => 'Murovaný-brúsený hr.300mm',
                    'col7' => 'Murovaný-brúsený hr.380mm',
                    'col8' => 'Murovaný-brúsený hr.440mm',
                    'col9' => 'Murovaný-brúsený hr.500mm',
                    'col10' => 'Murovaný-brúsený s TI hr.250mm',
                    'col11' => 'Murovaný-brúsený s TI hr.300mm',
                    'col12' => 'Murovaný-brúsený s TI hr.380mm',
                    'col13' => 'Murovaný-brúsený s TI hr.440mm',
                    'col14' => 'Murovaný-brúsený s TI hr.500mm',
                    'col15' => 'Murovaný-pórobetónový hr.250mm',
                    'col16' => 'Murovaný-pórobetónový hr.300mm',
                    'col17' => 'Murovaný-pórobetónový hr.375mm',
                    'col18' => 'Murovaný-pórobetónový hr.450mm',
                    'col19' => 'Murovaný-pórobetónový hr.500mm',
                    'col20' => '',
                    'col21' => '',
                    'col22' => '',
                    'col23' => '',
                    'col24' => '',
                    'col25' => '',
                ),
            3 =>
                array (
                    'web_title' => 'Zateplovací systém stien',
                    'web_position' => 4,
                    'col1' => 'z Polystyrénu hr.150mm',
                    'col2' => 'z Polystyrénu hr.200mm',
                    'col3' => 'z Polystyrénu hr.250mm',
                    'col4' => 'z Polystyrénu hr.300mm',
                    'col5' => 'z Minerálnej vlny hr.150mm',
                    'col6' => 'z Minerálnej vlny hr.200mm',
                    'col7' => 'z Minerálnej vlny hr.250mm',
                    'col8' => 'z Minerálnej vlny hr.300mm',
                    'col9' => '',
                    'col10' => '',
                    'col11' => '',
                    'col12' => '',
                    'col13' => '',
                    'col14' => '',
                    'col15' => '',
                    'col16' => '',
                    'col17' => '',
                    'col18' => '',
                    'col19' => '',
                    'col20' => '',
                    'col21' => '',
                    'col22' => '',
                    'col23' => '',
                    'col24' => '',
                    'col25' => '',
                ),
            4 =>
                array (
                    'web_title' => 'Odovzdávací vykurovací systém',
                    'web_position' => 8,
                    'col1' => 'Teplovodné radiátorové vykurovanie',
                    'col2' => 'Teplovodné podlahové vykurovanie',
                    'col3' => 'Teplovodné stropné vykurovanie a chladenie',
                    'col4' => 'Elektrické podlahové vykurovanie',
                    'col5' => '',
                    'col6' => '',
                    'col7' => '',
                    'col8' => '',
                    'col9' => '',
                    'col10' => '',
                    'col11' => '',
                    'col12' => '',
                    'col13' => '',
                    'col14' => '',
                    'col15' => '',
                    'col16' => '',
                    'col17' => '',
                    'col18' => '',
                    'col19' => '',
                    'col20' => '',
                    'col21' => '',
                    'col22' => '',
                    'col23' => '',
                    'col24' => '',
                    'col25' => '',
                ),
            5 =>
                array (
                    'web_title' => 'Zdroj tepla',
                    'web_position' => 7,
                    'col1' => 'Plynový kondenzačný kotoľ',
                    'col2' => 'Splyňovací kotoľ na tuhé palivo',
                    'col3' => 'Tepelné čerpadlo',
                    'col4' => 'Elektrické vykurovanie',
                    'col5' => '',
                    'col6' => '',
                    'col7' => '',
                    'col8' => '',
                    'col9' => '',
                    'col10' => '',
                    'col11' => '',
                    'col12' => '',
                    'col13' => '',
                    'col14' => '',
                    'col15' => '',
                    'col16' => '',
                    'col17' => '',
                    'col18' => '',
                    'col19' => '',
                    'col20' => '',
                    'col21' => '',
                    'col22' => '',
                    'col23' => '',
                    'col24' => '',
                    'col25' => '',
                ),
            6 =>
                array (
                    'web_title' => 'Vetranie',
                    'web_position' => 9,
                    'col1' => 'Prirodzené vetranie',
                    'col2' => 'S rekuperačným vetracím systémom',
                    'col3' => '',
                    'col4' => '',
                    'col5' => '',
                    'col6' => '',
                    'col7' => '',
                    'col8' => '',
                    'col9' => '',
                    'col10' => '',
                    'col11' => '',
                    'col12' => '',
                    'col13' => '',
                    'col14' => '',
                    'col15' => '',
                    'col16' => '',
                    'col17' => '',
                    'col18' => '',
                    'col19' => '',
                    'col20' => '',
                    'col21' => '',
                    'col22' => '',
                    'col23' => '',
                    'col24' => '',
                    'col25' => '',
                ),
            7 =>
                array (
                    'web_title' => 'Zateplenie stropu',
                    'web_position' => 3,
                    'col1' => 'z Kamennej vlny hr.240mm',
                    'col2' => 'z Kamennej vlny hr.350mm',
                    'col3' => 'Fúkaná TI z PUR peny hr.240mm',
                    'col4' => 'Fúkaná TI z PUR peny hr.350mm',
                    'col5' => '',
                    'col6' => '',
                    'col7' => '',
                    'col8' => '',
                    'col9' => '',
                    'col10' => '',
                    'col11' => '',
                    'col12' => '',
                    'col13' => '',
                    'col14' => '',
                    'col15' => '',
                    'col16' => '',
                    'col17' => '',
                    'col18' => '',
                    'col19' => '',
                    'col20' => '',
                    'col21' => '',
                    'col22' => '',
                    'col23' => '',
                    'col24' => '',
                    'col25' => '',
                ),
            8 =>
                array (
                    'web_title' => 'Strešná krytina',
                    'web_position' => 1,
                    'col1' => 'Terran Danubia Colorsystem',
                    'col2' => 'Bramac Renova / Moravská',
                    'col3' => 'Tondach',
                    'col4' => 'San Marco',
                    'col5' => '',
                    'col6' => '',
                    'col7' => '',
                    'col8' => '',
                    'col9' => '',
                    'col10' => '',
                    'col11' => '',
                    'col12' => '',
                    'col13' => '',
                    'col14' => '',
                    'col15' => '',
                    'col16' => '',
                    'col17' => '',
                    'col18' => '',
                    'col19' => '',
                    'col20' => '',
                    'col21' => '',
                    'col22' => '',
                    'col23' => '',
                    'col24' => '',
                    'col25' => '',
                ),
            9 =>
                array (
                    'web_title' => 'Výplne otvorov',
                    'web_position' => 2,
                    'col1' => 'Plastové biele okná',
                    'col2' => 'Plastové farebné okná',
                    'col3' => 'Hliníkové okná',
                    'col4' => 'Drevené okná',
                    'col5' => 'Drevené okná Exclusive',
                    'col6' => '',
                    'col7' => '',
                    'col8' => '',
                    'col9' => '',
                    'col10' => '',
                    'col11' => '',
                    'col12' => '',
                    'col13' => '',
                    'col14' => '',
                    'col15' => '',
                    'col16' => '',
                    'col17' => '',
                    'col18' => '',
                    'col19' => '',
                    'col20' => '',
                    'col21' => '',
                    'col22' => '',
                    'col23' => '',
                    'col24' => '',
                    'col25' => '',
                ),
            10 =>
                array (
                    'web_title' => 'Štýl budovy',
                    'web_position' => 10,
                    'col1' => 'Base',
                    'col2' => 'Minimal',
                    'col3' => 'Scandinav',
                    'col4' => 'Loft',
                    'col5' => 'Romantic',
                    'col6' => 'Provence',
                    'col7' => 'Neoclassic',
                    'col8' => '',
                    'col9' => '',
                    'col10' => '',
                    'col11' => '',
                    'col12' => '',
                    'col13' => '',
                    'col14' => '',
                    'col15' => '',
                    'col16' => '',
                    'col17' => '',
                    'col18' => '',
                    'col19' => '',
                    'col20' => '',
                    'col21' => '',
                    'col22' => '',
                    'col23' => '',
                    'col24' => '',
                    'col25' => '',
                ),
        );
    }

    public static function sortedVariantsByPosition()
    {
        return collect(self::calculatorProperties())->sortBy('web_position')->map(function ($properties, $typeKey){

            $propertiesOptionsWithValue = collect($properties)->filter(function ($property,$key){
                // only options to check and return
                if (!empty($property) && strpos($key,'col') !== false) {
                    return $property;
                }
            });
            // build a simple needed response for radio input population
            return [
                'web_title' => $properties['web_title'],
                'web_position' => $properties['web_position'],
                'typeKey' => $typeKey,
                'web_options' => $propertiesOptionsWithValue->toArray()
            ];
        });
    }


    public static function housePossibilities(): array
    {
        return [
            1=>'A',
            2=>'A',
            3=>'B',
            4=>'B',
            5=>'C',
            6=>'C',
            7=>'B',
            8=>'A',
            9=>'B',
            10=>'D',
        ];
    }

}