<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo;

use Ketwaroo\Pattern\InterfaceConfig;

/**
 * For getting/setting config
 *
 * @author Yaasir Ketwaroo<ketwaroo.yaasir@gmail.com>
 */
class Config implements InterfaceConfig, \ArrayAccess, \Countable, \Iterator
{

    use Pattern\TraitImplementsArray;

    /**
     *
     * @var array|\ArrayAccess 
     */
    protected $config;

    public function __construct($config)
    {
        if (false === Util::isArrayLike($config))
        {
            throw new \InvalidArgumentException('config must be array or implement \ArrayAccess');
        }
        $this->config = $config;
    }

    public function delete($key)
    {
        
    }

    public function get($key, $default = null)
    {
        if (is_array($key))
        {
            $value = [];
            foreach ($this->sanitiseKeyArray($key) as $k => $v)
            {
                $value[$k] = $this->get($k, $v);
            }
        }
        elseif (is_string($key))
        {
            $value = Util::dotGet($this->config, $key, $default);
        }
        else
        {
            return null;
        }

        return $value;
    }

    /**
     * 
     * {@inheritdoc}
     */
    public function set($key, $value = null)
    {
        if (is_array($key))
        {
            $value = [];
            foreach ($this->sanitiseKeyArray($key, $value) as $k => $v)
            {
                $value[$k] = $this->set($k, $v);
            }
        }
        elseif (is_string($key))
        {
            $value = Util::dotSet($this->config, $key, $value);
        }


        return $this;
    }

    /**
     * 
     * @param array $keys by reference.
     * @return array
     */
    protected function sanitiseKeyArray(array &$keys, $nullValue = null)
    {
        $clean = [];
        foreach ($keys as $k => $v)
        {
            if (is_int($k)
                && !empty(('' . $v)))
            {
                $clean['' . $v] = $nullValue;
            }
            elseif (!empty('' . $k))
            {
                $clean[$k] = $v;
            }
        }
        return $clean;
    }

    public function getIterator()
    {
        return $this->config;
    }

}
