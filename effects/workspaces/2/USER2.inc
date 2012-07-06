<?php
function user_defined($frame,$s,$p,$maxFrame,$maxStrand,$maxPixel,$param1,$param2,$start_color,$end_color)

{
$rgb=hexdec("#FFFFFF");
$rgb=0;
if($s%2==0)
{
if($p<=$frame)
$rgb=hexdec("FF0000");
}
else if($s%2==1)
{
if($p>=($maxPixel-$frame))
$rgb=hexdec("0000FF");
}
        
	return $rgb;
}
