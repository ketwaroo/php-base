<?php


namespace Ketwaroo\Pattern;

/**
 * Description of TraitImplementsArray
 *
 * @author Yaasir Ketwaroo
 */
trait TraitImplementsArray
{

    /**
     * @return \Iterator|\ArrayAccess|\Countable
     */
    abstract public function getIterator();

    public function offsetExists($offset)
    {
        return call_user_func_array([$this->getIterator(), __FUNCTION__], func_get_args());
    }

    public function offsetGet($offset)
    {
        return call_user_func_array([$this->getIterator(), __FUNCTION__], func_get_args());
    }

    public function offsetSet($offset, $value)
    {
        return call_user_func_array([$this->getIterator(), __FUNCTION__], func_get_args());
    }

    public function offsetUnset($offset)
    {
        return call_user_func_array([$this->getIterator(), __FUNCTION__], func_get_args());
    }

    public function count()
    {
        return call_user_func_array([$this->getIterator(), __FUNCTION__], func_get_args());
    }

    public function current()
    {
        return call_user_func_array([$this->getIterator(), __FUNCTION__], func_get_args());
    }

    public function key()
    {
        return call_user_func_array([$this->getIterator(), __FUNCTION__], func_get_args());
    }

    public function next()
    {
        return call_user_func_array([$this->getIterator(), __FUNCTION__], func_get_args());
    }

    public function rewind()
    {
        return call_user_func_array([$this->getIterator(), __FUNCTION__], func_get_args());
    }

    public function valid()
    {
        return call_user_func_array([$this->getIterator(), __FUNCTION__], func_get_args());
    }

}
