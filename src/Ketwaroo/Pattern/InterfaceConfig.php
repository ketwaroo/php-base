<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\Pattern;

/**
 * Description of InterfaceConfig
 *
 * @author Yaasir Ketwaroo
 */
interface InterfaceConfig
{

    /**
     * Dot notation get.
     * 
     * If key is array, associative array is $key=>$default, otherwise treated as just $key
     * 
     * @param string|array $key if string single set. if array, multiget.
     * @param mixed $default value to returned if not found. ignored if array.
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Dot notation set
     * @param string|array $key if string single set. if array multiset.
     * @param mixed $value value to set. ignored if array.
     * @return static
     */
    public function set($key, $value = null);

    /**
     * Dot noration delete.
     * @param string|array $key
     * @return static
     */
    public function delete($key);
}
