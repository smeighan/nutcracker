<?php
/*$fl='pictures/2/sandbones.gif ';
$p_new_fl='pictures/2/sandbones.jpg ';
$c['red']=255;
$c['green']=0;
$c['blue']=0;
gif2jpeg($fl, $p_new_fl, $c);*/


function gif2jpeg($p_fl, $p_new_fl, $bgcolor=false)
{
	list($wd, $ht, $tp, $at)=getimagesize($p_fl);
	$img_src=imagecreatefromgif($p_fl);
	$img_dst=imagecreatetruecolor($wd,$ht);
	$clr['red']=255;
	$clr['green']=255;
	$clr['blue']=255;
	if(is_array($bgcolor)) $clr=$bgcolor;
	$kek=imagecolorallocate($img_dst,
	$clr['red'],$clr['green'],$clr['blue']);
	imagefill($img_dst,0,0,$kek);
	imagecopyresampled($img_dst, $img_src, 0, 0, 
	0, 0, $wd, $ht, $wd, $ht);
	$draw=true;
	if(strlen($p_new_fl)>0)
	{
		if($hnd=fopen($p_new_fl,'w'))
		{
			$draw=false;
			fclose($hnd);
		}
	}
	if(true==$draw)
	{
		header("Content-type: image/jpeg");
		imagejpeg($img_dst);
	}
	else imagejpeg($img_dst, $p_new_fl);
	imagedestroy($img_dst);
	imagedestroy($img_src);
}
?>