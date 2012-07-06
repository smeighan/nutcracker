<?php
function user_defined($frame,$s,$p,$maxFrame,$maxStrand,$maxPixel,$param1,$param2,$start_color,$end_color)

{
$rgb=hexdec("#FFFFFF");
$s_ratio=$frame*$s/$maxStrand; $p_ration=$frame*$p/$maxPixel;
$H1=sin($s_ratio) * sin($s_ratio) +  cos($p_ratio)* cos($p_ratio);
$H = $H1 * $p/$maxPixel;
if($H>1) $H=$H- intval($H);
$S=$V=1;
$rgb=HSV_TO_RGB ($H, $S, $V);  
	return $rgb;
}
