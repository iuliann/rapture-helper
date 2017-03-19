<?php

namespace Rapture\Helper;

/**
 * Locale (Gettext)
 *
 * @package Rapture\Helper
 * @author  Iulian N. <rapture@iuliann.ro>
 * @license LICENSE MIT
 */
class Locale
{
    /**
     * @param string $locale  Locale to set. Ex: en_US.utf8
     * @param string $domain  Domain for locale
     * @param string $path    Path to translation files
     * @param string $charset Charset. Default UTF-8
     *
     * @return void
     */
    public static function init(string $locale, string $domain = 'messages', string $path = './translations', string $charset = 'UTF-8')
    {
        self::setLocale($locale);
        self::setDomain($domain, $path, $charset);
    }

    /**
     * @param string $locale Locale. Check 'locale -a' output
     *
     * @return bool
     */
    public static function setLocale(string $locale)
    {
        // LC_NUMERIC = fix floating point numeric issues
        return putenv("LANG={$locale}") && setlocale(LC_ALL, $locale) && setlocale(LC_NUMERIC, 'C');
    }

    /**
     * @param string $domain  Domain for locale
     * @param string $path    Path to translation files
     * @param string $charset Charset - Default to UTF-8
     *
     * @return void
     */
    public static function setDomain(string $domain = 'messages', string $path = './translations', string $charset = 'UTF-8')
    {
        bindtextdomain($domain, $path);
        bind_textdomain_codeset($domain, $charset);
        textdomain($domain);
    }

    /**
     * Get current locale
     *
     * @return string
     */
    public static function getLocale()
    {
        return setlocale(LC_ALL, 0);
    }

    /**
     * Get current domain
     *
     * @return string
     */
    public static function getDomain()
    {
        return textdomain(null);
    }
}
