<?php

namespace Ketwaroo\FileSystem;

/**
 * Description of DirectoriesOnlyReursiveFilterIterator
 */
class DirectoriesOnlyReursiveFilterIterator extends \RecursiveFilterIterator{
    
    public function __construct(\RecursiveDirectoryIterator $iterator): \RecursiveFilterIterator {
        return parent::__construct($iterator);
    }
    
    public function accept(): bool {
        
        return $this->hasChildren() || ($this->current() && $this->current()->isDir() && !($this->current()->isDot()));
        
    }
}
