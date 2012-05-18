<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
  <head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head>
<body>


<pre>
<?php

$iterator = new RecursiveDirectoryIterator('dane');

$objects = new RecursiveIteratorIterator(
    $iterator,
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($objects as $f) {

    echo "\n\n=====START=====";
    echo "\necho: " . $f;
    echo "\nvar_dump():";
    var_dump($f);
    echo "\n\n";
    echo "\ngetFilename() = " . $f->getFilename();
    echo "\ngetBasename() = " . $f->getBasename() . '<br />';
//    echo "\ngetExtension() = " . $f->getExtension() . '<br />';
    echo "\ngetPath() = " . $f->getPath() . '<br />';
    echo "\ngetPathname() = " . $f->getPathname() . '<br />';
    echo "\ngetRealPath() = " . $f->getRealPath() . '<br />';

    echo "\n\n=====STOP=====\n\n";
}

?>
</pre>


</body>
</html>
