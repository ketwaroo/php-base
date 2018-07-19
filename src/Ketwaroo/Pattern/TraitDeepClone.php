<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\Pattern;

/**
 * Description of TraitDeepClone
 *
 * @author Yaasir Ketwaroo<ketwaroo.yaasir@gmail.com>
 */
trait TraitDeepClone
{

    protected function __deepClone()
    {

        $vars = array_keys(get_class_vars(get_called_class()));

        foreach ($vars as $k)
        {
            if (is_object($this->{$k}))
            {
                $this->{$k} = clone $this->{$k};
            }
            elseif (is_array($this->{$k}))
            {
                $this->{$k} = $this->__deepCloneArray($this->{$k});
            }
        }
    }

    protected function __deepCloneArray(&$arr)
    {
        $ret = [];
        foreach ($arr as $k => $v)
        {
            if (is_object($v))
            {
                $ret[$k] = clone $v;
            }
            elseif (is_array($v))
            {
                $ret[$k] = $this->__deepCloneArray($v);
            }
            else
            {
                $ret[$k] = $v;
            }
        }

        return $ret;
    }

}
