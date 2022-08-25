<?php

require 'ruut.framework.php';
$outfile = \Ketwaroo\PackageInfo::whereAmI(__FILE__)->getPackageBasePath() . '/data/mime-by-extension.php';

$src = json_decode(file_get_contents('https://cdn.jsdelivr.net/gh/jshttp/mime-db@1.52.0/db.json'), 1);

$out = [];

foreach ($src as $mime => $x) {

    foreach ($x['extensions'] ?? [] as $e) {
        $out[$e][] = $mime;
    }
}
file_put_contents($outfile, '<?php return ' . var_export($out, 1) . ';');

