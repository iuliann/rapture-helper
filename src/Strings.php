<?php

namespace Rapture\Helper;

/**
 * Class Strings
 *
 * @package Rapture\Helper
 * @author  Iulian N. <rapture@iuliann.ro>
 * @license LICENSE MIT
 */
class Strings extends \DateTime
{
    /**
     * @param int    $length String length
     * @param string $extra  Extra chars besides a-zA-Z0-9
     *
     * @return string
     */
    public static function random($length = 8, $extra = '')
    {
        $list = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' . $extra;

        return substr(str_shuffle($list), 0, $length);
    }

    /**
     * @param string $string String to process
     *
     * @return string
     */
    public static function sluggify(string $string):string
    {
        return preg_replace(
            '/[-]+/',
            '-',
            trim(
                preg_replace(
                    '/[^a-z0-9\-]/',
                    '-',
                    transliterator_transliterate("Any-Latin; Latin-ASCII; Lower()", $string)
                ),
                '-'
            )
        );
    }

    /**
     * @param string $string String to process
     *
     * @return string
     */
    public static function toLatin(string $string):string
    {
        return transliterator_transliterate("Any-Latin; Latin-ASCII", $string);
    }

    /**
     * NOT RECOMMENDED FOR PRODUCTION
     *
     * @param string $string String to encrypt
     * @param string $key    Encryption key
     *
     * @return string
     */
    public static function encrypt(string $string, string $key):string
    {
        $key = substr(sha1($key, true), 0, 16);

        return base64_encode(openssl_encrypt($string, 'AES-128-ECB', $key, false));
    }

    /**
     * NOT RECOMMENDED FOR PRODUCTION
     *
     * @param string $string String to decrypt
     * @param string $key    Decryption key
     *
     * @return string
     */
    public static function decrypt(string $string, string $key):string
    {
        $key = substr(sha1($key, true), 0, 16);

        return openssl_decrypt(base64_decode($string), 'AES-128-ECB', $key, false);
    }
}
