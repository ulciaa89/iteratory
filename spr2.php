<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
  <head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head>
<body>


<pre>
<?php

$f = new SplFileInfo('a/b/c/lorem.txt');

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

$result = $f->isFile();

var_dump($result);


$result = $f->isDir();

var_dump($result);

?>
</pre>


</body>
</html>
