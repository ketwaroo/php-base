<?php

/**
 *  @author Yaasir Ketwaroo 
 */

namespace Ketwaroo;

/**
 * Random collection of tools
 * 
 * Should have no dependencies.
 */
class Util
{

    /**
     * merges a param array onto a defaults array
     * @param type $params key=>value associative input
     * @param type $defaults default values.
     * @param bool $strict if true params will be limited to keys present in defaults.
     * @return array
     */
    public static function parseOpt($params, $defaults = [], $strict = true)
    {
        if ($strict)
        {
            $params = array_intersect_key($params, $defaults);
        }
        return array_merge($defaults, $params);
    }

    /**
     * combination of array_merge recursive and array_replace_recursive
     * merges if the values in first are arrays, replaces if not.
     * 
     * @param array|Traversible|ArrayAccess $arrayLike ..
     * @return array|Traversible|ArrayAccess same as imput
     */
    public static function arrayMergeRecursive()
    {

        $args = func_get_args();

        $first = array_shift($args);
        // merge everything else onto first.

        if (!static::isArrayLike($first))
        {
            throw new \InvalidArgumentException('First param is expected to be array-like.');
        }

        foreach ($args as $i => $second)
        {
            if (static::isArrayLike($second))
            {
                foreach ($second as $k => $v)
                {
                    // skip numeric keys.
                    if (is_int($k))
                    {
                        $first[] = $v;
                    }

                    continue;

                    if (static::arrayKeyExists($k, $first))
                    {
                        if (!is_array($v))
                        {

                            $first[$k] = $second[$k];
                        }
                        else
                        {
                            $first[$k] = static::arrayMergeRecursive($first[$k], $v);
                        }
                    }
                    else
                    {
                        $first[$k] = $v;
                    }
                }
            }
            else
            {

                trigger_error('$arg[' . $i . '] is not array like.', E_USER_WARNING);
            }
        }

        return $first;
    }
    
    /**
     * 
     * @param callable $map callable($value,$key), return value.
     * @param array $input
     * @param array $output
     * @return array
     */
    public static function arrayMap(callable $map, array &$input, array &$output = []): array {

        foreach ($input as $k => $v) {

            $v          = $map($v, $k);
            $output[$k] = $v;
        }
        return $output;
    }
    
    /**
     * 
     * @param callable $map callable($key), return altered key, sets existing value to new key.
     * @param array $input
     * @param array $output
     * @return array
     */
    public static function arrayMapKey(callable $map, array &$input, array &$output = []): array {

        foreach ($input as $k => $v) {
            
            $newk =  $map($k);
            $output[$newk] = $v;
        }
        return $output;
    }

    /**
     * Test if is array or implements all array like functionality.
     * 
     * @param mixed $var
     * @return boolean
     */
    public static function isArrayLike(&$var)
    {
        if (is_array($var))
        {
            return true;
        }

        if (!($var instanceof \Countable
            && $var instanceof \Traversable
            && $var instanceof \ArrayAccess))
        {
            return false;
        }
    }

    /**
     * fixes issue with array_key_exists and ArrayAccess objects.
     * 
     * @param string|int $key
     * @param mixed $arrayLike
     * @return boolean 
     */
    public static function arrayKeyExists($key, &$arrayLike)
    {
        if (is_array($arrayLike))
        {
            return array_key_exists($key, $arrayLike);
        }
        elseif ($arrayLike instanceof \ArrayAccess)
        {
            return $arrayLike->offsetExists($key);
        }

        return false;
    }
    
    /**
     * Simple test for an associative array
     *
     * @link http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
     *
     * @param array $array
     * @return bool
     */
    public static function isAssocativeArray(array $array) {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * GUID generator
     * 
     * @stolen http://guid.us/GUID/PHP
     * @see http://guid.us/GUID/PHP
     * @return string
     */
    public static function GUID()
    {
        if (function_exists('com_create_guid'))
        {
            return com_create_guid();
        }
        else
        {
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid   = chr(123)// "{"
                . substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12)
                . chr(125); // "}"
            return $uuid;
        }
    }

    /**
     * safely test existence of a constant and returns it's value.
     * returns null if constant not defined.
     * Warning: will also return null if existing constant is defined as null.
     * @param string $name
     * @return mixed scalar constant value..
     */
    public static function getConstant($name)
    {
        if (defined($name)) 
        {
            return constant($name);
        }
        return null;
    }

    /**
     * test the truthiness&trade; of a string 
     * @param mixed $var variable to test
     * @return bool truthiness&trade;
     */
    public static function isTruthy($var)
    {
        $truthies = array(
            '1',
            'true',
            'yes',
            'y',
            'on',
        );

        return in_array(strtolower(strval($var)), $truthies, true);
    }

    /**
     * 
     * @param mixed $var may not handle objects very well.
     * @return string base64 encoded
     */
    public static function base64Encode($var)
    {
        return base64_encode(json_encode($var));
    }

    /**
     * 
     * @param string $base64EncodedString base64 encoded string
     * @param boolean $assoc Used by json_decode. returns object instead of assoc array if false.
     * @return mixed
     */
    public static function base64Decode($base64EncodedString, $assoc = true)
    {
        return json_decode(base64_decode($base64EncodedString), $assoc);
    }

    /**
     * extending the php parse url to handle some unusual cases
     * 
     * <code>
     * moo:///path/to/stuff.file ->     
     * [
     *  'scheme'=>'moo',
     *  'path'=> '/path/to/stuff.file',
     * ]
     * instead of returning false
     * 
     * meow://../relative/path.to -> 
     * [
     *  'scheme'=>'meow',
     *  'path'=> '../relative/path.to',
     * ]
     * instead of returning '..' as the host
     * <code>
     * @param string $url
     * @return array|boolean
     */
    public static function parseUrl($url)
    {
        $tmp = parse_url($url);

        if (empty($tmp)) // fail once could be triple slash
        {
            if (preg_match('~^(([a-z0-9\-]+)://)~', $url, $m))
            {
                $scheme        = $m[2];
                $url           = str_replace($m[1], '', $url);
                $tmp           = parse_url($url);
                // should usually return path
                $tmp['scheme'] = $scheme;
            }
            else
            {
                return $tmp;
            }
        }

        $host = array_get($tmp, 'host', '');

        if (strcmp($host, '..') === 0) // is relative uri
        {
            $tmp['path'] = $host . array_get($tmp, 'path', ''); // it's all really the path.
            unset($tmp['host']);
        }

        return $tmp;
    }

    /**
     * generates a reflection class for an object.
     * @todo add caching possibly.
     * @param object|string $obj
     * @return \Reflector|\ReflectionClass|\ReflectionFunction|null
     */
    public static function getReflectionClass($obj)
    {
        try
        {
            // try first as it can be a class path string.
            return new \ReflectionClass($obj);
        }
        catch (\Exception $exc)
        {
            throw new \InvalidArgumentException('Input is not an loadable object.', __LINE__, $exc);
        }
    }

    /**
     * 
     * @param array|ArrayAccess|object $arrayLike
     * @param string $path
     * @param mixed $default 
     * @return mixed
     */
    public static function dotGet(&$arrayLike, $path, $default = null, $dot='.')
    {
        if (static::arrayKeyExists($path, $arrayLike))
        {
            return $arrayLike[$path];
        }

        $loc = &$arrayLike;
        foreach (explode($dot, $path) as $step)
        {
            if (static::arrayKeyExists($step, $loc))
            {
                $loc = &$loc[$step];
            }
            elseif (is_object($loc)
                && property_exists($loc, $step)
                && (new \ReflectionProperty($loc, $step))->isPublic())
            {
                $loc = &$loc->{$step};
            }
            else
            {
                return $default;
            }
        }
        return $loc;
    }

    public static function dotSet(&$arrayLike, $path, $value, $dot='.')
    {
        $loc = &$arrayLike;
        foreach (explode($dot, $path) as $step)
        {
            if (is_array($loc) || $loc instanceof \ArrayAccess)
            {
                // create missing steps.
                if (!(self::arrayKeyExists($step, $loc)))
                {
                    $loc[$step] = [];
                }

                $loc = &$loc[$step];
            }
            elseif (is_object($loc)
                && property_exists($loc, $step)
                && (new \ReflectionProperty($loc, $step))->isPublic())
            {
                $loc = &$loc->{$step};
            }
            else
            {
                trigger_error("Could not access [{$step}] in [{$path}] for writing", E_USER_WARNING);
                return false;
            }
        }
        $loc = $value;

        return $loc;
    }

    /**
     * 
     * @param type $arrayLike
     * @param type $path
     * @return boolean
     */
    public static function dotUnset(&$arrayLike, $path)
    {
        $loc = &$arrayLike;
        foreach (explode('.', $path) as $step)
        {
            if (is_array($loc) || $loc instanceof \ArrayAccess)
            {
                // create missing steps.
                if (!(self::arrayKeyExists($step, $loc)))
                {
                    $loc[$step] = [];
                }

                $loc = &$loc[$step];
            }
            elseif (is_object($loc)
                && property_exists($loc, $step)
                && (new \ReflectionProperty($loc, $step))->isPublic())
            {
                $loc = &$loc->{$step};
            }
            else
            {
                trigger_error("Could not access [{$step}] in [{$path}] for removal", E_USER_WARNING);
                return false;
            }
        }

        unset($loc);
    }

    /**
     * ISO-8601 date string
     * 
     * @param string $timestring for strtotime()
     * @return string Y-m-d
     */
    public static function dateIso8601($timestring = 'now')
    {
        return date('Y-m-d', strtotime($timestring));
    }

    /**
     * ISO-8601 date time string
     * 
     * @param string $timestring for strtotime()
     * @return string Y-m-d H:i:s
     */
    public static function dateTimeIso8601($timestring = 'now')
    {
        return date('Y-m-d H:i:s', strtotime($timestring));
    }

    /**
     * Export a single php variable to a file. Variable can be imported by `require|include`
     * @param mixed $var
     * @param string $file
     * @return boolean success/fail
     */
    public static function varExportToFile($var, $file)
    {
        return FALSE !== file_put_contents(
                $file
                , '<?php return ' . var_export($var, true) . ';' . PHP_EOL
        );
    }
    
}
