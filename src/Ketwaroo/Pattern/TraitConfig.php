<?php

namespace Ketwaroo\Pattern;
use Ketwaroo\Config;
/**
 * Adds config
 *
 * @author Administrator
 */
trait TraitWithConfig
{

    /**
     *
     * @var Config
     */
    protected $config;
    
    /**
     * 
     * @return Config
     */
    public function getConfig()
    {
        if(!isset($this->config))
        {
            $this->config = new Config([]);
        }
        
        return $this->config;
    }
    
    /**
     * 
     * @param Config $newConfig
     * @return static
     */
    protected function setConfig(Config $newConfig)
    {
        $this->config = $newConfig;
        return $this;
    }
    
    

  

}
