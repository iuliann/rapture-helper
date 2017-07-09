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

    /**
     * Generate UUID v4
     *
     * @credits http://php.net/manual/en/function.uniqid.php#94959
     *
     * @return string
     */
    public static function generateUUIDv4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
