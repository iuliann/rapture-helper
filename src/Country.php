<?php

namespace Rapture\Helper;

/**
 * Class Country
 *
 * @package Rapture\Helper
 * @author  Iulian N. <rapture@iuliann.ro>
 * @license LICENSE MIT
 */
class Country
{
    /**
     * @return array
     */
    public static function fetch()
    {
        $continents = self::getContinents();
        $names      = self::getNames();
        $iso3       = self::getIso3();
        $capitals   = self::getCapitals();
        $phones     = self::getPhones();
        $currencies = self::getCurrencies();

        ksort($continents);

        $result = [];
        foreach ($continents as $iso2 => $continent) {
            $result[] = [
                'iso2'      =>  $iso2,
                'name'      =>  $names[$iso2],
                'iso3'      =>  $iso3[$iso2],
                'capital'   =>  $capitals[$iso2],
                'phone'     =>  $phones[$iso2],
                'currency'  =>  $currencies[$iso2],
                'continent' =>  $continent,
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function getGeneratedSql()
    {
        $data = self::fetch();

        $sql = [];

        foreach ($data as $row) {
            $sql[] = sprintf(
                'INSERT INTO country (name, iso2, iso3, capital, phone, currency, continent) VALUES ("%s", "%s", "%s", "%s", "%s", "%s", "%s");',
                $row['name'],
                $row['iso2'],
                $row['iso3'],
                $row['capital'],
                str_replace(' and ', ',', $row['phone']),
                $row['currency'],
                $row['continent']
            );
        }

        return $sql;
    }

    /**
     * @return array
     */
    public static function getContinents()
    {
        return json_decode(file_get_contents('http://country.io/continent.json'), true);
    }

    /**
     * @return array
     */
    public static function getNames()
    {
        return json_decode(file_get_contents('http://country.io/names.json'), true);
    }

    /**
     * @return array
     */
    public static function getIso3()
    {
        return json_decode(file_get_contents('http://country.io/iso3.json'), true);
    }

    /**
     * @return array
     */
    public static function getCapitals()
    {
        return json_decode(file_get_contents('http://country.io/capital.json'), true);
    }

    /**
     * @return array
     */
    public static function getPhones()
    {
        return json_decode(file_get_contents('http://country.io/phone.json'), true);
    }

    /**
     * @return array
     */
    public static function getCurrencies()
    {
        return json_decode(file_get_contents('http://country.io/currency.json'), true);
    }
}
