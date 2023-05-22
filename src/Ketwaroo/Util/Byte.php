<?php

namespace Ketwaroo\Util;

/**
 * stolen from drupal 
 */
class Byte {

    /**
     * The number of bytes in a kilobyte.
     *
     * @see http://wikipedia.org/wiki/Kilobyte
     */
    const KILOBYTE = 1024;

    /**
     * The allowed suffixes of a bytes string in lowercase and kilobyte power
     * 
     * @see http://wikipedia.org/wiki/Kilobyte
     */
    const ALLOWED_SUFFIXES = [
        ''           => 0,
        'b'          => 0,
        'byte'       => 0,
        'bytes'      => 0,
        'k'          => 1,
        'kb'         => 1,
        'kib'        => 1,
        'kilobyte'   => 1,
        'kilobytes'  => 1,
        'm'          => 2,
        'mb'         => 2,
        'mib'        => 2,
        'megabyte'   => 2,
        'megabytes'  => 2,
        'g'          => 3,
        'gb'         => 3,
        'gib'        => 3,
        'gigabyte'   => 3,
        'gigabytes'  => 3,
        't'          => 4,
        'tb'         => 4,
        'tib'        => 4,
        'terabyte'   => 4,
        'terabytes'  => 4,
        'p'          => 5,
        'pb'         => 5,
        'pib'        => 5,
        'petabyte'   => 5,
        'petabytes'  => 5,
        'e'          => 6,
        'eb'         => 6,
        'eib'        => 6,
        'exabyte'    => 6,
        'exabytes'   => 6,
        'z'          => 7,
        'zb'         => 7,
        'zib'        => 7,
        'zettabyte'  => 7,
        'zettabytes' => 7,
        'y'          => 8,
        'yb'         => 8,
        'yib'        => 8,
        'yottabyte'  => 8,
        'yottabytes' => 8,
    ];

    /**
     * Parses a given byte size.
     *
     * @param int|float|string $size
     *   An integer, float, or string size expressed as a number of bytes with
     *   optional SI or IEC binary unit prefix (e.g. 2, 2.4, 3K, 5MB, 10G, 6GiB,
     *   8 bytes, 9mbytes).
     *
     * @return float
     *   The floating point value of the size in bytes.
     */
    public static function toNumber($size, $to = 'b'): float {

        if (false !== ($test = static::getSuffix($size))) {

            [$size, $suffix] = $test;
            $toSize = pow(self::KILOBYTE, self::ALLOWED_SUFFIXES[strtolower($to)]
                        ?? 0);

            return ($size * pow(self::KILOBYTE, self::ALLOWED_SUFFIXES[$suffix])) / $toSize;
        }
    }

    /**
     * Get the suffix for a byte notation. Also validates said notation.
     *
     * @param string $string
     *   The string to validate.
     *
     * @return bool|array
     *   false if the string is invalid valid, tuple of [double size, string suffix] otherwise.
     */
    public static function getSuffix($string): array|bool {

        // Ensure that the string starts with a numeric character.
        if (!preg_match('/^[0-9]/', $string)) {
            return FALSE;
        }
        // Remove the non-numeric characters from the size.
        $size   = preg_replace('/[^0-9\\.]/', '', $string);
        // Remove the numeric characters from the beginning of the value.
        $string = preg_replace('/^[0-9\\.]+/', '', $string);

        // Remove remaining spaces from the value.
        $string = trim(strtolower($string));
        return in_array($string, array_keys(self::ALLOWED_SUFFIXES)) ? [(double) $size, $string]
                    : false;
    }

}
