<?php
/*$command = "ps axfu | grep nignx";
exec($command,$out,$status);
print_r($out);*/
$content = file_get_contents("/etc/passwd");
var_dump($content);