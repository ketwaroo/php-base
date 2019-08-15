<?php

namespace Ketwaroo\Pattern;

/**
 * Description of TraitDeepSerialize
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir at gmail>
 */
trait TraitDeepSerialize
{
    
    
    public function serialize()
    {
        
    }
    
    protected function deepSerializeItem($var)
    {
        
    }
    public function unserialize($serialized)
    {

    }
    protected function deepUnserializeItem($serialized)
    {
       return unserialize($serialized);
    }
}
