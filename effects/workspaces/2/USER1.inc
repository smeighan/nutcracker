<?php
function user_defined($frame,$s,$p,$maxFrame,$maxStrand,$maxPixel,$param1,$param2,$start_color,$end_color)

{
$rgb=hexdec("#FFFFFF");
if($frame%4==0) $rgb=hexdec("#FFFFFF");
if($frame%4==1) $rgb=hexdec("#FF0000");
if($frame%4==2) $rgb=hexdec("#00FF00");
if($frame%4==3) $rgb=hexdec("#0000FF");  
    
	return $rgb;
}
