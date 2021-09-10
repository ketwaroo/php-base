<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\Util;

use Ketwaroo\Text;

/**
 * Description of Cli
 *
 * @author Yaasir Ketwaroo
 */
class CliArg
{

    use \Ketwaroo\Pattern\TraitSingleton;

    protected $args = [];

    public function __construct()
    {
        
    }

    public function __isset($name)
    {
        return null !== ($this->{$name});
    }

    public function __get($name)
    {

        if (isset($this->args[$name]))
        {
            return $this->args[$name];
        }

        if (1 === strlen($name))
        {
            $test = getopt("{$name}::");
            if (isset($test[$name]))
            {
                return $this->parseGetOptValue($test[$name], $name);
            }
        }

        // altforms
        $tests = [
            $name,
            Text::toKebabCase($name),
            Text::toSnakeCase($name),
            Text::toStudlyCaps($name),
        ];
        foreach ($tests as $n)
        {
            $test = getopt('', ["{$n}::"]);
            if (isset($test[$n]))
            {
                return $this->parseGetOptValue($test[$n], $name);
            }
        }
        return null;
    }

    protected function parseGetOptValue($value, $name)
    {
        if (is_bool($value))
        {
            $value = true;
        }
        $this->args[$name] = $value;
        return $value;
    }

    public function getOption($name, $default = null)
    {
        return isset($this->{$name}) ? $this->{$name} : $default;
    }

    /**
     * read Interactive
     * @param string $message
     * @return string
     */
    public function interactive($message)
    {
        echo $message, PHP_EOL;
        $handle = fopen("php://stdin", "r");
        $line   = fgets($handle);
        fclose($handle);
        return trim($line,"\r\n");
    }

}
