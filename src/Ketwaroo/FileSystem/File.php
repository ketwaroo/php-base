<?php

namespace Ketwaroo\FileSystem;

/**
 * Description of File
 *
 * @author Yaasir Ketwaroo<ketwaroo.yaasir@gmail.com>
 */
class File extends \SplFileObject
{

    public function getContents($default = '')
    {
        if ($this->isReadable())
        {
            return $this->fread($this->getSize());
        }

        return $default;
    }

    public function setContents($data, $length = null)
    {
        $this->fwrite($str, $length);
        return $this;
    }
    public function setContentsAppend($data, $length = null)
    {
        $this->fwrite($str, $length);
        return $this;
    }
    

    public function getContentsAsJson($param)
    {
        
    }

}
