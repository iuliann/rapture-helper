<?php

namespace Rapture\Helper;

/**
 * Class Arrays
 *
 * @package Rapture\Helper
 * @author  Iulian N. <rapture@iuliann.ro>
 * @license LICENSE MIT
 */
class Arrays
{
    /**
     * Proper range with corresponding keys
     *
     * @param int $start First value
     * @param int $end   Last value
     * @param int $step  Step
     *
     * @return array
     */
    public static function range(int $start, int $end, $step = 1):array
    {
        return array_combine(range($start, $end, $step), range($start, $end, $step));
    }

    /**
     * Example:
     *  [
     *      [firstname => 'John', 'lastname' => 'Doe']
     *  ] => ['John' => 'Doe']
     *
     * @param array  $data  Original data
     * @param string $key   Key name
     * @param string $value Value key name
     *
     * @return array
     */
    public static function toKeyValue($data, $key = '', $value = ''):array
    {
        $result = [];
        $index = 0;

        foreach ($data as $item) {
            if (is_array($item)) {
                $result[$item[$key] ?? $index++] = $item[$value] ?? null;
            }
        }

        return $result;
    }

    /**
     * Example:
     *  ['John' => 'Doe'] => [[first_name => 'John', 'last_name' => 'Doe']]
     *
     * @param array  $data      Original data
     * @param string $keyName   Key name for key
     * @param string $valueName Key name for value
     *
     * @return array
     */
    public static function toValueKey($data, $keyName = 'key', $valueName = 'value')
    {
        $result = [];
        foreach ($data as $keyValue => $valueValue) {
            $result[] = [
                $keyName    =>  $keyValue,
                $valueName  =>  $valueValue
            ];
        }

        return $result;
    }

    /**
     * @param array  $data         Collection array|iterator
     * @param string $key          Key name
     * @param string $value        Value key name
     * @param string $methodPrefix Method prefix
     *
     * @return array
     */
    public static function collectionToKeyValue($data, $key = '', $value = '', $methodPrefix = 'get'):array
    {
        $result = [];
        $keyMethod = $methodPrefix . $key;
        $valueMethod = $methodPrefix . $value;

        foreach ($data as $item) {
            $result[$item->{$keyMethod}()] = $item->{$valueMethod}();
        }

        return $result;
    }

    /**
     * Convert source array values to scalar
     *
     * @param array $data Source array
     *
     * @return array
     */
    public static function toScalarValues(array $data):array
    {
        foreach ($data as $key => $value) {
            switch (gettype($value)) {
                case 'array':
                    $data[$key] = json_encode($value);
                    break;
                case 'object':
                    if ($value instanceof \DateTime) {
                        $data[$key] = $value->format('Y-m-d H:i:s');
                    }
                    else {
                        $data[$key] = json_encode($value);
                    }
                    break;
                case 'resource':
                    $data[$key] = 'resource';
                    break;
            }
        }

        return $data;
    }
}
