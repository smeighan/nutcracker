<?php

include 'Bmp.php';

// you can use this the same way as imagepng, imagepng:

//header('Content-type: image/bmp');
//$im = imagecreatefrompng('test.png');
//imagebmp($im);

//or imagecreatefromjpeg:

//$im = imagecreatefrombmp('test.bmp');
//imagepng($im);

//but you can also use it as a class:

header('Content-type: image/bmp');
$im = imagecreatefrompng('test.png');
Bmp::imagebmp($im, 'out.bmp');

/*
header('Content-type: image/png');
$im = Bmp::imagecreatefrombmp('test.bmp');
imagepng($im);
*/