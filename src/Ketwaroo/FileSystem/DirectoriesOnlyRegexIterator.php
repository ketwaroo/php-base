<?php

namespace Ketwaroo\FileSystem;

/**
 * Description of DirectoriesOnlyReursiveFilterIterator
 */
class DirectoriesOnlyRegexIterator extends FilesOnlyRegexIterator {

    public function valid(): bool {

        return parent::valid() && ($this->hasChildren() || (
            $this->current() instanceof \SplFileInfo
            && $this->current()->isDir()
            && !($this->current()->isDot())
            ));
    }

}
