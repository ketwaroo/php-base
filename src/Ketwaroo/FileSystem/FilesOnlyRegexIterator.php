<?php

namespace Ketwaroo\FileSystem;

/**
 * Description of FilesOnlyRegexIterator
 */
class FilesOnlyRegexIterator extends \RegexIterator {

    protected $depth        = true, $currentDepth = 0;

    public function valid(): bool {

        return parent::valid() && (
            $this->hasChildren() || (
            $this->current() instanceof \SplFileInfo
            && $this->current()->isFile()
            )
            );
    }

    public function getDepth() {
        return $this->depth;
    }

    public function getCurrentDepth() {
        return $this->currentDepth;
    }

    public function setDepth($depth) {
        $this->depth = $depth;
        return $this;
    }

}
