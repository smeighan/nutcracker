<?php
function user_defined($frame,$s,$p,$maxFrame,$maxStrand,$maxPixel,$param1,$param2,$start_color,$end_color)

{
$rgb=hexdec("#FFFFFF");
# function user_defined($frame,$s,$p,$maxFrame,$maxStrand,$maxPixel,$param1,$param2,$start_color,$end_color) 

$rgb=hexdec("#FFFFFF");
$H=$S=$V=1;
$slice=$param1; // use first parameter passed in from user
$slice_size=$maxPixel/$slice;
$H0 = (($frame% $slice) * $slice_size);
$H=($p+$H0)/$maxPixel;
if($H>1) $H = $H-intval($H);

$rgb=HSV_TO_RGB ($H, $S, $V);
//echo "<pre>f,s,p=$frame,$s,$p. slice_size=$slice_size. H0=$H0, H=$H</pre>";
//echo "<pre> maxP=$maxPixel. H,S,V=$H,$S,$V, rgb=$rgb</pre>";

return $rgb;  
      
    
	return $rgb;
}
