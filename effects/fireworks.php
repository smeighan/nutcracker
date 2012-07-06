<?php
$H=$S=$V=0;
$burst_height=30;
$burst_radius=3;
$modulo=30;

if($s%$modulo==0)
{
	if($p==($maxPixel-$frame) and $p>($maxPixel-$burst_height))
	{
		$H=.99;
		$S=$V=1;
	}
}
if( $frame==$burst_height and $p<=($maxPixel-$frame+1) and  $p>=($maxPixel-$frame-1))
	if($s%$modulo==0 or $s%$modulo==1 or $s%$modulo==6)
	{
	$H=rand(80,99)/100;
	$S=$V=1;
}

if(( $frame==($burst_height+1) ) and ($p==($maxPixel-$frame+2) or  $p==($maxPixel-$frame-2)))
	if($s%$modulo==0 or $s%$modulo==1 or $s%$modulo==6 or $s%$modulo==2 or $s%$modulo==5 )
{
	$H=.66;
	$S=$V=1;
}


if(( $frame==($burst_height+2) ) and ($p==($maxPixel-$frame+2) or  $p==($maxPixel-$frame-2)
	 or $p==($maxPixel-$frame+3) or  $p==($maxPixel-$frame-3)  ))
	if($s%$modulo==0 or $s%$modulo==1 or $s%$modulo==6 or $s%$modulo==2 or $s%$modulo==5  or $s%$modulo==3 or $s%$modulo==4)
{
	$H=.66;
	$S=$V=1;
}

if($H>1) $H = $H-intval($H);

$rgb=HSV_TO_RGB ($H, $S, $V);

