<?php

namespace Ketwaroo;

use Ketwaroo\Text;
use Ketwaroo\PackageInfo;

/**
 * Description of FileSystem
 *
 * @author Yaasir Ketwaroo
 */
class FileSystem {

    const FILTER_REGEX = 0;
    const FILTER_REGEX_DEFAULT = '~^.*$~';
    const FILTER_GLOB = 1;
    const FILTER_GLOB_DEFAULT = '*';

    public static function osPath($path) {

        $r = [
            '/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR,
            '?' => '\?',
        ];
        return str_replace(array_keys($r), array_values($r), $path);
    }

    public static function nixPath($path) {

        return str_replace(DIRECTORY_SEPARATOR, '/', static::osPath($path));
    }

    /**
     * Generally safe filename. If it work on windows, it'll work anywhere.
     * 
     * @link https://docs.microsoft.com/en-us/windows/win32/fileio/naming-a-file
     * @param string $filename
     * @param string $invalidCharReplacer
     * @return string
     */
    public static function sanitiseWindowsFileName($filename, $invalidCharReplacer = '-') {
        $rep = [
            '~[' . preg_quote('<>"/\\|?*:') . ']+~' => $invalidCharReplacer,
            '~[[:^print:]]+~' => $invalidCharReplacer,
        ];
        return trim(preg_replace(array_keys($rep), array_values($rep), $filename));
    }

    /**
     * 
     * @link https://docs.microsoft.com/en-us/windows/win32/fileio/naming-a-file
     * @param string $filename
     * @param string $invalidCharReplacer
     * @return string
     */
    public static function sanitiseWindowsDirectoryName($filename, $invalidCharReplacer = '-') {
        $rep = [
            '~[\.,]+$~' => '',
        ];
        return trim(preg_replace(array_keys($rep), array_values($rep), static::sanitiseWindowsFileName($filename, $invalidCharReplacer)));
    }

    /**
     * 
     * @param type $path
     * @param type $filter
     * @param type $r
     * @param type $filterType
     * @return array
     */
    public static function readFiles($path, $filter = NULL, $r = true, $filterType = FileSystem::FILTER_GLOB) {
        switch ($filterType) {
            case static::FILTER_REGEX:
                return static::readFilesInDirectoryRegex($path, $filter, $r);
            case static::FILTER_GLOB:
                return static::readFilesInDirectoryGlob($path, $filter, $r);
            default:
                return array();
        }
    }

    /**
     * read files in directory recursive. regex match.
     * 
     * @param string $path bast path
     * @param string $filter regex
     * @param boolean|int $recursive if true fully recursive, integer is folders deep to go
     * @return array list of files
     */
    public static function readFilesInDirectoryRegex($path, $filter = NULL, $recursive = true) {

//
//        $directory = empty($recursive) ? (new \DirectoryIterator($path)) : ( new \RecursiveDirectoryIterator($path));
//        
//        if(is_int($recursive))
//        {
//            
//        }
//
//
//
//        if (!empty($filter)) {
//            $it = new \RecursiveIteratorIterator($directory);
//            $files = new \RegexIterator($it, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
//        } else {
//            $files = $directory;
//        }
//
//
//
//        $fs = [];
//        foreach ($files as $f) {
//            $fs[] = $f;
//        }
//        sort($fs);
//        return $fs;

        $dls = array();
        $subdir = array();

        if (FALSE !== ($d = opendir($path))) {
            while (false !== ($f = readdir($d))) {
                if ($f != "." && $f != "..") {
                    if (is_dir($path . '/' . $f) && !empty($recursive)) {
                        if ($recursive && is_int($recursive)) --$recursive;

                        $subdir = array_merge(static::readFilesInDirectoryRegex($path . '/' . $f, $filter, $recursive), $subdir);
                    } elseif (NULL === $filter || preg_match($filter, $f))
                            array_push($dls, $path . '/' . $f);
                }
            }

            closedir($d);
        }
        sort($dls);
        sort($subdir);
        return array_merge($dls, $subdir);
    }

    /**
     * read files in directory recursive. glob match.
     * 
     * @param string $path bast path
     * @param string $filter glob
     * @param boolean|int $r if true fully recursive, integer is folders deep to go
     * @return array list of files
     */
    public static function readFilesInDirectoryGlob($path, $filter = NULL, $r = true) {
        $filter = (NULL === $filter) ? static::FILTER_GLOB_DEFAULT : $filter;
        $path = static::escapeGlobPath($path);
        $dirs = glob($path . '/*', GLOB_ONLYDIR);
        $files = array_diff(glob($path . '/' . $filter, GLOB_BRACE), $dirs);

        if (!empty($r)) {
            if ($r && is_int($r)) {
                --$r;
            }

            foreach ($dirs as $d) {
                $files = array_merge($files, static::readFilesInDirectoryGlob($d, $filter, $r));
            }
        }

        return $files;
    }

    /**
     * 
     * @param type $path
     * @param type $filter
     * @param boolean $recurse
     * @return array
     */
    public static function readDirectoriesInDirectoryGlob($path, $filter = '*', $recurse = true) {
        $path = static::escapeGlobPath($path);

        $dirs = glob($path . '/' . $filter, GLOB_BRACE | GLOB_ONLYDIR);

        if (!empty($recurse)) {
            if ($recurse && is_int($recurse)) --$recurse;

            $subdirs = glob($path . '/*', GLOB_ONLYDIR); //*/
            foreach ($subdirs as $d)
                $dirs = array_merge($dirs, static::readDirectoriesInDirectoryGlob($d, $filter, $recurse));
        }

        return $dirs;
    }

    /**
     * 
     * @param type $path
     * @param type $filter
     * @param type $r
     * @return array
     */
   public static function readDirectoriesInDirectoryRegex($path, $filter = '/.*/', $r = true, array &$carry = []) {

        $finalSort = empty($carry);
        $subdir    = array();
        if ($r === 0) return array();
        if (is_numeric($r)) $r         = intval($r) - 1;

        $subdirs = [];
        if ($d = opendir($path)) {
            while (false !== ($f = readdir($d))) {
                
                if ($f != "." && $f != "..") {
                    
                    if (is_dir($path . '/' . $f)) {
                        if (!$filter || preg_match($filter, $f)) {
                            $carry[] = $path . '/' . $f;
                        }

                        if ($r) {
                            $subdirs[]=$path . '/' . $f;
                        }
                    }
                }
            }

            closedir($d);
        }
        foreach ($subdirs as $sd) {
            static::readDirectoriesInDirectoryRegex($sd, $filter, $r, $carry);
        }

        if ($finalSort) {
            sort($carry);
            return $carry;
        }
       return [];
    }
    
    public static function recursiveDirectoryIterator($directory,$filter = '/.*/'){
        
        $r = new \RecursiveDirectoryIterator($directory);
        $filter = new \RecursiveRegexIterator($r, $filter);
        
 
        
    }
    

    /*
      Function: move

      move recursive

      Arguments:

      $from 	- from path
      $to 	- to path
      $filter	- pregex filter

     */

    public static function moveFilesRegex($from, $to, $filter = '/.*/', $recurse = true, $deleteEmpty = false) {
        $list = static::readFilesInDirectoryRegex($from, $filter, $recurse);
        foreach ($list as $tmp) {
            $tmp2 = static::strFromTo($from, $to, $tmp);
            static::moveFile($tmp, $tmp2);
        }

        if ($deleteEmpty) {
            static::deleteEmptySubDirectories($from);
            @unlink($from);
        }
    }

    public function moveFilesGlob($from, $to, $filter = '*', $recurse = true, $deleteEmpty = false) {
        $list = static::readFilesInDirectoryGlob($from, $filter, $recurse);
        foreach ($list as $tmp) {
            $tmp2 = static::strFromTo($from, $to, $tmp);
            static::moveFile($tmp, $tmp2);
        }

        if ($deleteEmpty) {
            static::deleteEmptySubDirectories($from);
        }
    }

    function copy2($from, $to, $filter = '/.*/', $verbose = false) {
        $list = rd($from, $filter);
        foreach ($list as $tmp) {
            $tmp2 = str_fromto($from, $to, $tmp);
            if (copyfile($tmp, $tmp2) && $verbose) echo 'copied ', $tmp, "\n";
        }
    }

    function copyskip($from, $to, $filter = '/.*/') {
        $list = rd($from, $filter);
        foreach ($list as $tmp) {
            $tmp2 = str_fromto($from, $to, $tmp);
            if (!file_exists($tmp2) && copyfile($tmp, $tmp2))
                    echo 'copied ', $tmp, "\n";
        }
    }

    /**
     * 
     * @param string $from from base path. can be stripped regex.
     * @param string $to to pase path
     * @param string $path actual path
     * @return string
     */
    public static function strFromTo($from, $to, $path) {
        return preg_replace('~^' . preg_quote($from, '~') . '~i', $to, $path);
    }

    public static function deleteEmptySubDirectories($from, $verbose = false) {
        $list = static::readDirectoriesInDirectoryRegex($from);
        rsort($list);
        $numDirs = count($list);
        while ($v = array_pop($list)) {

            if (empty(static::readFilesInDirectoryRegex($v)) && is_dir($v) && rmdir($v)) {
                if ($verbose) {
                    echo 'removed ', $v, PHP_EOL;
                }
                $numDirs--;
            }
        }
        return empty($numDirs);
    }

    /**
     * Delete all files and directories including itself
     * @param string $d
     */
    public static function deleteDirectory($d) {
        $c = static::readFilesInDirectoryRegex($d);
        foreach ($c as $i) {
            unlink($i);
        }
        if (static::deleteEmptySubDirectories($d)) {
            rmdir($d);
        } else
                trigger_error("$d seems to contain non empty folders or something", E_USER_WARNING);
    }

    function array2file($a, $f) {
        foreach ($a as $l) {
            $l = trim($l);
            if (!empty($l)) file_put_contents($f, $l . "\n", FILE_APPEND);
        }
    }

    /**
     * 
     * @param type $from
     * @param type $to
     * @return type
     */
    public static function movefile($from, $to) {
        if (!is_file($from)) return;
        static::prepareDirectory(dirname($to));

        return rename($from, $to);
    }

    public static function prepareDirectory($dir) {
        if (!is_dir($dir)) {

            $dir2 = static::osPath($dir);
            $p = [];
            do {
                if (!is_dir($dir2)) {
                    array_unshift($p, basename($dir2));
                } else break;
            }while ($dir2 = dirname($dir2));

            foreach ($p as $d) {
                $dir2 = static::osPath($dir2 . '/' . $d);
                mkdir($dir2);
                static::copyPermissions(dirname($dir2), $dir2);
            }
        }
        return $dir;
    }

    /**
     * Copy permission.
     * @param string|array $src either source file or `stat()` output.
     * @param string $dest
     */
    public static function copyPermissions($src, $dest) {
        if (is_string($src) && file_exists($src)) {
            $srcStat = stat($src);
            chmod($dest, $srcStat['mode']);
            chown($dest, $srcStat['uid']);
            chgrp($dest, $srcStat['gid']);
        }
    }

    /**
     * copies a file
     * 
     * @param string $from
     * @param string $to
     * @return boolean
     */
    function copyfile($from, $to) {
        if (!is_file($from)) return;
        $dir = dirname($to);
        if (!is_dir($dir)) mkdir($dir, '0777', true);
        return copy($from, $to);
    }

    /**
     * cached mime by extension map.
     * @var array 
     */
    protected static $extMimeMap = array();

    /**
     * converts bacslash into forward slash in a path because php does not care.
     * and it makes path handling uniform cross platform
     * @param string $path
     * @return string
     */
    public static function unixifyPath($path) {
        return preg_replace('~[\\\/]+~', '/', $path);
    }

    /**
     * attempts to determine mime for a file.
     * 
     * Note that the fileinfo methods are not that accurate.
     * 
     * @param string $file can be existing or non existing file.
     * @param string $default fallback mime if can't be determined. default: application/octet-stream
     * @return type
     */
    public static function determineMime($file, $default = 'application/octet-stream') {
        if (is_file($file) && function_exists('finfo_open')) { // recommended way.
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type a la mimetype extension
            $mime  = finfo_file($finfo, $file);
            finfo_close($finfo);
        }
//        elseif(is_file($file) && function_exists('mime_content_type')) // deprecated way
//        {
//            $mime = @mime_content_type($file);
//        }
        else {
            // scraping the barrel. Also works if file does not exist. //@todo this method may be more accurate than fileinfo.
            $basePath = PackageInfo::whereAmI(__FILE__)->getPackageBasePath();
            if (empty(self::$extMimeMap)) {
                self::$extMimeMap = require($basePath . '/data/mime-by-extension.php');
            }

            $ext = pathinfo($file, PATHINFO_EXTENSION);

            $mime = isset(self::$extMimeMap[$ext]) ? reset(self::$extMimeMap[$ext]) : null;
        }
        return empty($mime) ? $default : $mime;
    }

    /**
     * updates /data/mime-types with data from http://svn.apache.org/viewvc/httpd/httpd/trunk/docs/conf/mime.types?view=co
     * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
     */
    public static function refreshMimeMap() {
        $basePath = Package::detectPackageBasePath(Package::inWhichPackageAmI(__FILE__));

        $src = 'http://svn.apache.org/viewvc/httpd/httpd/trunk/docs/conf/mime.types?view=co';
        $outfile = $basePath . '/data/mime_by_extension.php';

        $raw = explode("\n", file_get_contents($src));

        $out = array();
        $count = array();

        foreach ($raw as $r) {
            $r = trim($r);
            if (substr($r, 0, 1) === '#' || empty($r)) {
                continue;
            }

            $a = preg_split('~\t+~', $r);

            if (!empty($a[1])) {
                $exts = explode(' ', $a[1]);
                $mime = trim($a[0]);
                foreach ($exts as $e) {
                    if (isset($out[$e])) {
                        $out[$e][] = $mime;
                        $count[$e] ++;
                    } else {
                        $out[$e] = array(
                            $mime,
                        );

                        $count[$e] = 1;
                    }
                }
            }
        }

        ksort($out);

        $outFileContent = file_get_contents($outfile);

        $outFileContent = preg_replace('~#mapStart.*?#mapEnd~is', '#mapStart' . PHP_EOL . 'return ' . var_export($out, true) . ';' . PHP_EOL . '#mapEnd', $outFileContent);

        file_put_contents($outfile, $outFileContent);
    }

    public static function escapeGlobPath($path) {
        return preg_replace('~([\*\?\[\]\-\! ])~', '\\\$1', $path);
    }

}
