<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\Pattern;

/**
 * Description of TraitSingleton
 *
 * @author Administrator
 */
trait TraitSingleton
{

    /**
     *
     * @var static 
     */
    protected static $instance = null;

    /**
     * @return static|$this
     */
    public static function instance()
    {

        if (null === static::$instance)
        {
            static::initialiseSingleton(); //danger?
        }

        return static::$instance;
    }

    public static function initialiseSingleton()
    {
        static::$instance = (new \ReflectionClass(get_called_class()))
            ->newInstanceArgs(func_get_args());
        return static::instance();
    }

}
