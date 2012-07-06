<?php
$cwd=getcwd();
$file = "carol.7z";
$shellCommand = "p7zip -d " . realpath($file); 
$output = system($shellCommand . " 2>&1"); 
echo "<pre>cwd=$cwd, shellcommand = $shellCommand\n output=$output</pre>\n";
?>