<!DOCTYPE html>
<html>
  <head>
    <title></title>
    <meta charset="UTF-8" />
  </head>
<body>

<pre>
<?php

error_reporting(E_ALL);

require_once 'Finder/FtpSplFileInfo.php';
require_once 'Finder/Iterator/RecursiveDirectoryFtpIterator.php';



use Finder\Iterator\RecursiveDirectoryFtpIterator;
use Finder\FtpSplFileInfo;

$i = new RecursiveDirectoryFtpIterator('ftp://ftp.mozilla.org');

//$objects = new RecursiveIteratorIterator($i, RecursiveIteratorIterator::SELF_FIRST);
$objects = new RecursiveIteratorIterator($i);

foreach($objects as $k => $v){
    echo "[k===$k] [v===$v]\n";
//    var_dump($k);
//    var_dump($v);
//    echo $v->getFilename();
//    echo $v . "\n";
    ob_flush();
    flush();
}

?>
</pre>
</body>
</html>