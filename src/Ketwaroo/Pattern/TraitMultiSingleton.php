<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\Pattern;

/**
 * Somewhat useless..
 *
 * @author Administrator
 */
trait TraitMultiSingleton
{

    /**
     *
     * @var static[] 
     */
    protected static $instances = [];

    /**
     * @return static|$this
     */
    public static function instance()
    {

        $cls  = get_called_class();
        $args = func_get_args();

        $sig = sha1($cls . json_encode($args));

        if (isset(static::$instances[$sig]))
        {
            return static::$instances[$sig];
        }

        static::$instances[$sig] = (new \ReflectionClass($cls))
            ->newInstanceArgs($args);

        return static::$instances[$sig];
    }

    /**
     * create a named instance.
     * First call should have additional parameters for constructor.
     * 
     * @param string $name
     * @param mixed $initialArgs..
     * @return static
     */
    public static function instanceNamed($name)
    {
        if (!isset(static::$instances[$name]))
        {

            $cls  = get_called_class();
            $args = func_get_args();
            array_shift($args);

            static::$instances[$name] = (new \ReflectionClass($cls))
                ->newInstanceArgs($args);
        }
        return static::$instances[$name];
    }

}
