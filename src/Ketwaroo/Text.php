<?php

/**
 *  @author Yaasir Ketwaroo 
 */

namespace Ketwaroo;

/**
 * Collection of text utilities.
 * 
 * Sould have no other dependencies.
 */
class Text {

    const trim_default_chars = " \t\n\r\x00\x0B";
    const trim_left          = "left";
    const trim_right         = "right";
    const trim_both          = "both";

    /**
     * convert "It's alive!!! non-stick" to "its-alive-non-stick"
     * 
     * @param string $str
     * @return string kebab-case-string
     */
    public static function toKebabCase($str) {
        return static::trim(static::toLowerCaseJoined($str, '-'), '-');
    }

    /**
     * converts a bit of text to lower snake case form.
     * 
     * @param string $str
     * @return string snake_case_string
     */
    public static function toSnakeCase($str) {
        return static::trim(static::toLowerCaseJoined($str, '_'), '_');
    }

    /**
     * convert "It's alive!!! non-stick" to "itsAliveNonStick"
     * 
     * @param type $str
     * @return string camelCaseString
     */
    public static function toCamelCase($str) {
        return lcfirst(static::toUpperCaseJoined($str, ''));
    }

    public static function toStudlyCaps($str) {
        return static::toUpperCaseJoined($str, '');
    }

    /**
     * unaccent and reduce string to unpuncuated form. uppercase
     * @param string $str
     * @param string $glu if joined by another string
     * @return string
     */
    public static function toUpperCaseJoined($str, $glu = '') {
        return str_replace(' ', $glu, ucwords(static::toLowerCaseJoined($str, ' ')));
    }

    /**
     * unaccent and reduce string to unpuncuated form. lowercase
     * 
     * @param type $str
     * @param string $glu if joined by another string
     * @return string lower-dash-string
     */
    public static function toLowerCaseJoined($str, $glu = ' ') {
        return strtolower(
            preg_replace(
                [
                    '~([a-z])([A-Z])~', // split camel case
                    '~[^a-z0-9 ]+~i',
                    '~ +~',
                ]
                , [
            '$1 $2',
            ' ',
            $glu
                ]
                , static::unaccent((string) $str)
            )
        );
    }

    /**
     * Attempts to unaccent a string 

     * @param $str input string
     * @return string input string without accent
     */
    public static function unaccent($str) {
        return Text\Unaccent::instance()->unaccent($str);
    }

    public static function trim($string, $charMask = null, $addDefaultMask = false, $direction = null) {
        if ($addDefaultMask || null === $charMask) {
            $charMask = strval($charMask) . self:: trim_default_chars;
        }

        switch ($direction) {
            default:
            case null:
            case self::trim_both:
                return trim((string) $string, $charMask);
                break;
            case self::trim_left:
                return ltrim((string) $string, $charMask);
                break;
            case self::trim_right:
                return rtrim((string) $string, $charMask);
                break;
        }
    }

    public static function ltrim($string, $charMask = '', $addDefaultMask = false) {
        return static::trim($string, $charMask, $addDefaultMask, static::trim_left);
    }

    public static function rtrim($string, $charMask = '', $addDefaultMask = false) {
        return static::trim($string, $charMask, $addDefaultMask, static::trim_right);
    }

}
