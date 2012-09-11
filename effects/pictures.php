<?php
/*
Nutcracker: RGB Effects Builder
Copyright (C) 2012  Sean Meighan
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
require_once('../conf/header.php');
//
echo "<pre>\n";
ini_get('max_execution_time');
set_time_limit(250);
ini_get('max_execution_time');
echo "</pre>\n";
require ("read_file.php");
$array_to_save=$_GET;
$array_to_save['OBJECT_NAME']='picture';
extract ($array_to_save);
$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);
$array_to_save['effect_name']=$effect_name;
$array_to_save['username']=$username;
$frame_delay = $_GET['frame_delay'];
$frame_delay = intval((5+$frame_delay)/10)*10; // frame frame delay to nearest 10ms number_format
$array_to_save['frame_delay']=$frame_delay;
extract ($array_to_save);
save_user_effect($array_to_save);
//show_array($_GET,"_GET");
show_array($array_to_save,"Effect Settings");
//show_array($_SESSION,"_SESSION");
//show_array($_SERVER,"_SERVER");
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
$member_id=get_member_id($username);
$path ="workspaces/$member_id";
$picturedir ="pictures";
$picturepath ="pictures/$member_id";
$directory=$path;
if (file_exists($directory))
{
	} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}
if (file_exists($picturedir))  // create the pictures member_id directory. Any images users uploads goes here
{
	} else {
	echo "The directory $picturedir does not exist, creating it";
	mkdir($picturedir, 0777);
}
if (file_exists($picturepath))  // create the pictures member_id directory. Any images users uploads goes here
{
	} else {
	echo "The directory $picturepath does not exist, creating it";
	mkdir($picturepath, 0777);
}
$base = $user_target . "~" . $effect_name;
$t_dat = $user_target . ".dat";
$xdat = $user_target ."~".  $effect_name . ".dat";
$path="../targets/". $member_id;
$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
$minStrand = $arr[0];
// lowest strand seen on target
$minPixel = $arr[1];
// lowest pixel seen on skeleton
$maxStrand = $arr[2];
// highest strand seen on target
$maxPixel = $arr[3];
// maximum pixel number found when reading the skeleton target
$maxI = $arr[4];
// maximum number of pixels in target
$tree_rgb = $arr[5];
$tree_xyz = $arr[6];
$min_max = $arr[8];
$strand_pixel = $arr[9];
srand(time());
$maxFrame = 80;
//$maxTrees=6;	// how many tree to draw at one time
$seq_number = 0;
$file=$picturepath . "/" . $file1;
//$file = getcwd() . "/" . $file;
$file2=$picturepath . "/a_" . $file1;
$filez=$picturepath . "/z_" . $file1;
echo "<h1>Processing file $file</h1>";
$tokens=explode(".",$file);
$image_type=$tokens[1];
$size   = getimagesize($file);
list($width, $height, $type, $attr) = getimagesize($file);
print "<pre>";
print_r($size);
//echo "list($width, $height, $type, $attr)\n";
echo "</pre>";
$img_width  = $size[0];
$img_height = $size[1];
$img_type_number = $size[2];
// 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(orden de bytes intel), 8 = TIFF(orden de bytes motorola), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF, 15 = WBMP, 16 = XBM. 
/*if($img_type_number==1) $image  = imagecreatefromgif($file);
if($img_type_number==2) $image  = imagecreatefromjpeg($file);
if($img_type_number==3) $image  = imagecreatefrompng($file);*/
echo "<pre>width=$img_width, height=$img_height";
include('SimpleImage.php');
$image = new SimpleImage();
$image->load($file);
/*if($img_width<$img_height)
	$image->resizeToWidth($maxStrand*2);
else
$image->resizeToHeight($maxPixel*2);*/
$old_method=1;
if($old_method==1)
{
	$image->resize(intval($maxStrand*($window_degrees/360)),$maxPixel);
	$image->save($file2,$img_type_number);
}
else{
	$src=$file1;
	$dst=$filez;
	$width=intval($maxStrand*($window_degrees/360));
	image_resize($src, $dst,$width ,$maxPixel,0);
}
//
$file=$file2;
if($image_type=="png") $image  = imagecreatefrompng($file);
if($image_type=="jpg") $image  = imagecreatefromjpeg($file);
if($image_type=="gif") $image  = imagecreatefromgif($file);
$size   = getimagesize($file);
$img_width  = $size[0];
$img_height = $size[1];
echo "<pre>width=$img_width, height=$img_height";
$s=0;
$lowRange1 = $minStrand;
$lowRange2 = $maxStrand/4;
$highRange1=$maxStrand -  $maxStrand/4;
$highRange2=$maxStrand;
$halfTree=1;
$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
$precision = intval((max($img_height,$img_width) / $maxStrand) + 0.5);
$precision = intval($img_width / $maxStrand + 0.5);
//if ($img_width < 50)
	//$precision = 2;
$pixel_offset = intval($maxPixel - $img_height/$precision);
if($pixel_offset<0) $pixel_offset=0;
for($x = 0; $x < $img_width; $x += $precision)
{
	$s++; $p_raw=0;
	for($y = 0; $y < $img_height; $y += $precision)
	{
		$p_raw++;
		$p = $p_raw+$pixel_offset;
		$rgb = imagecolorat($image, $x, $y);
		$hex=dechex($rgb);
		//	echo "<pre>x,y=rgb  $x,$y=$hex</pre>\n";
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		if($image_type=="gif")
		{
			if($r==0 and $g==0)
			{
				$r=$g=$b;
			}
		}
		$HSL=RGB_TO_HSV ($r, $g, $b);
		$H=$HSL['H']; 
		$S=$HSL['S']; 
		$V=$HSL['V']; 		
		if($brightness>0.0)
		{
			if($V>0.1) $V=$V+$brightness;
			if($V>1) $V=1;
			$HSV['V']=$V;
			$rgb_val=HSV_TO_RGB($H,$S,$V);
		}
		$rgb_val=HSV_TO_RGB ($H, $S, $V);
		//$rgb_val=$rgb;
		//	echo " s,p = $s,$p  x,y = $x,$y rgb = $r,$g,$b  \n";
		if(         ($s>=$minStrand and $s <=$maxStrand)
			and ($p>=$minPixel and $p<=$maxPixel))
		{
			$new_s=$s;
			//$key = array_search($s, $windowStrandArray); // $key = 2;
			//echo "<pre>picture: s,p,key = $s,$p,$key</pre>";
			//		if($halfTree==0 or is_numeric($key))
			{
				//
				$tree_rgb[$s][$p]=$rgb_val;
				//	fwrite($fh_dat,sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val));
				//	printf ("t1 %4d %4d %9.3f %9.3f %9.3f %di (key=%s)\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$key);
			}
		}
	}
}
echo "<pre>";
//print_r($tree_rgb);
echo "</pre>";
//$orig_tree_rgb=$tree_rgb;
//$image_array=get_image($file);
$path = "workspaces/" . $member_id;
for ($f = 1; $f <= $maxFrame; $f++)
{
	$x_dat = $base . "_d_". $f . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
	$dat_file[$f] = $path . "/" .  $x_dat;
	$dat_file_array[]=$dat_file[$f];
	$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
	fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
	//$tree_rgb=$orig_tree_rgb; // always start with original loaded image
	$shift=intval(($f-1)*$speed);
	for($s=1;$s<=$maxStrand;$s++)
	{
		for($p=1;$p<=$maxPixel;$p++)
		{
			$s0=$s; $p0=$p;
			switch ($direction)
			{
				case 'down':
				$p0-=$shift;
				break;
				case 'up':
				$p0+=$shift;
				break;
				case 'right':
				$s0+=$shift;
				break;
				case 'left':
				$s0-=$shift;
				break;
			}
			$s1=($s0%$maxStrand);
			if($s1==0) $s1=$maxStrand;
			$p1=($p0%$maxPixel);
			if($p1==0) $p1=$maxPixel;
			if($p1<1) $p1+=$maxPixel;
			if($s1<1) $s1+=$maxStrand;
			$rgb_val=$tree_rgb[$s1][$p1];
			//$rgb_val=transition($rgb_val,$f,$s,$p,$maxStrand,$maxPixel);
			if(in_array($s1,$window_array)) // Is this strand in our window?, If yes, then we output lines to the dat file
			{
				if ($rgb_val <> 0 )
				{
					//	$rgb_val=brighten($rgb_val,30); // brighten by 10%
					$string=$user_pixel=0;
					$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
					fwrite($fh_dat[$f], sprintf("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n", $s, $p, $xyz[0], $xyz[1], $xyz[2], $rgb_val, $string, $user_pixel, $strand_pixel[$s][$p][0], $strand_pixel[$s][$p][1], $f, $seq_number));
				}
			}
		}
		}			
	echo "</pre>\n";
}
if (!isset($show_frame))
	$show_frame = 'N';
if (!isset($seq_duration))
	$seq_duration = 5;
if (!isset($frame_delay))
	$frame_delay = 100;
if (!isset($username))
	$username = 'f';
$amperage=array();
$x_dat_base = $base . ".dat";
make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$script_start,$amperage,$seq_duration,$show_frame);
list($usec, $sec) = explode(' ', microtime());
$script_start = (float)$sec + (float)$usec;
$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out); 
echo "<End of Program>\n";

function brighten($rgb,$per)
{
	$r = ($rgb >> 16) & 0xFF;
	$g = ($rgb >> 8) & 0xFF;
	$b = $rgb & 0xFF;
	$HSL=RGB_TO_HSV ($r, $g, $b);
	$H=$HSL['H']; 
	$S=$HSL['S']; 
	$V=$HSL['V'];
	$totper = (100+$per)/100; 
	$S=$S*$totper;
	if($S>1.0) $S=1.0;
	$V=1;
	$rgb_val=HSV_TO_RGB ($H, $S, $V);
	return $rgb_val;
}

function transition($rgb_val,$f,$s,$p,$maxStrand,$maxPixel)
{
	$s1=$p1=1;
	$s2=intval($maxStrand/2);
	$s3=$s2+1;
	$s4=$maxStrand;
	$p2=intval($maxPixel/2);
	$p3=$p2+1;
	$p4=$maxPixel;
	if($s<$s2-$f+1) $rgb_val=0;
	if($s>$s3+$f-1) $rgb_val=0;
	return $rgb_val;
}

function get_image($file,$frame,$maxStrand,$maxPixel)
{
	$path = "";
	$directory = $path;
	$image_path = $path . "/" . $file;
	$image_path=$file;
	$tokens = explode(".", $file);
	$image_type = $tokens[1];
	if ($image_type == "png")
		$image = imagecreatefrompng($image_path);
	else if ($image_type == "jpg")
		$image = imagecreatefromjpeg($image_path);
	else if ($image_type == "gif")
		$image = imagecreatefromgif($image_path);
	else die("Invalid file type of $image_type");
	$maxStrand = 34;
	$size = getimagesize($image_path);
	$img_width = $size[0];
	$img_height = $size[1];
	//$image=resizeImage($Image,$maxStrand,$maxPixel); 
	//echo "<pre>img width,height = $img_width,$img_height  max strand,pixel=$maxStrand,$maxPixel</pre>\n";
	$s = 0;
	$precision = intval((max($img_height,$img_width) / $maxStrand) + 0.5);
	$precision = intval($img_width / $maxStrand + 0.5);
	//if ($img_width < 50)
		$precision = 1;
	/*
	$cc = ImageColorsTotal($im2);
	for($n=0; $n<$cc; ++$n)
	{
		$c = ImageColorsForIndex($im2, $n);
		print 
		sprintf('<span style="background:#%02X%02X%02X">&nbsp;</span>',
		$c['red'], $c['green'], $c['blue']);
	}
	*/
	if($frame==1)
	{
		$cc = ImageColorsTotal($image);
		echo "<h3>Color palette used in this picture file</h3>\n";
		echo "<table border=1>";
		echo "<tr><th>Color<br/>Index</th>";
		echo "<th>R</th>";
		echo "<th>R</th>";
		echo "<th>G</th>";
		echo "<th>B</th>";
		echo "<th>Hex</th>";
		echo "</tr>\n";
		for($n=0; $n<$cc; ++$n)
		{
			$c = ImageColorsForIndex($image, $n);
			$hex = fromRGB ($c['red'],$c['green'],$c['blue']);
			echo "<tr><td>$n</td><td> ". $c['red'] . "</td><td> " . $c['green'] . "</td><td> " . $c['blue'] . " </td><td bgcolor=\"$hex\">$hex</td></tr>\n";
			sprintf('<span style="background:#%02X%02X%02X">&nbsp;</span>',	$c['red'], $c['green'], $c['blue']);
		}
		echo "</table>";
		echo "<pre>";
		echo "file=$file, width=$img_width, height=$img_height";
		echo "precision = $precision";
		echo "</pre>\n";
	}
	for ($x = 0; $x < $img_width; $x += $precision)
	{
		$s++;
		$p_raw = 0;
		for ($y = 0; $y < $img_height; $y += $precision)
		{
			$rgb_index = imagecolorat($image, $x, $y);
			$cols = ImageColorsForIndex($image, $rgb_index);
			$r = $cols['red'];
			$g = $cols['green'];
			$b = $cols['blue'];
			$rgbhex = fromRGB ($r,$g,$b);
			$rgb_val = hexdec($rgbhex);
			$image_array[$x][$y] = $rgb_val;
			//			echo "<pre>x,y,rgb= $x,$y,($r,$g,$b), rgbval=$rgb_val</pre>";
		}
	}
	echo "</pre>";
	return $image_array;
}

function setTransparency($new_image,$image_source)
{
	$transparencyIndex = imagecolortransparent($image_source); 
	$transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255); 
	if ($transparencyIndex >= 0)
	{
		$transparencyColor    = imagecolorsforindex($image_source, $transparencyIndex);
	}
	$transparencyIndex    = imagecolorallocate($new_image, $transparencyColor['red'], 
	$transparencyColor['green'], $transparencyColor['blue']); 
	imagefill($new_image, 0, 0, $transparencyIndex); 
	imagecolortransparent($new_image, $transparencyIndex);
}

function resizeImage($originalImage,$toWidth,$toHeight)
{
	list($width, $height) = getimagesize($originalImage); 
	$xscale=$width/$toWidth; 
	$yscale=$height/$toHeight; 
	if($xscale<1) $xscale=1;
	if($yscale<1) $yscale=1;
	if ($yscale>$xscale)
	{
		$new_width = round($width * (1/$yscale)); 
		$new_height = round($height * (1/$yscale));
	}
	else { 
		$new_width = round($width * (1/$xscale)); 
		$new_height = round($height * (1/$xscale));
	}
	$imageResized = imagecreatetruecolor($new_width, $new_height); 
	$imageTmp     = imagecreatefromjpeg ($originalImage); 
	imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
	return $imageResized;
}

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

function image_resize($src, $dst, $width, $height, $crop=0)
{
	if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";
	$type = strtolower(substr(strrchr($src,"."),1));
	if($type == 'jpeg') $type = 'jpg';
	switch($type)
	{
		case 'bmp': $img = imagecreatefromwbmp($src); break;
		case 'gif': $img = imagecreatefromgif($src); break;
		case 'jpg': $img = imagecreatefromjpeg($src); break;
		case 'png': $img = imagecreatefrompng($src); break;
		default : return "Unsupported picture type!";
	}
	// resize
	if($crop)
	{
		if($w < $width or $h < $height) return "Picture is too small!";
		$ratio = max($width/$w, $height/$h);
		$h = $height / $ratio;
		$x = ($w - $width / $ratio) / 2;
		$w = $width / $ratio;
	}
	else{
		if($w < $width and $h < $height) return "Picture is too small!";
		$ratio = min($width/$w, $height/$h);
		$width = $w * $ratio;
		$height = $h * $ratio;
		$x = 0;
	}
	$new = imagecreatetruecolor($width, $height);
	// preserve transparency
	if($type == "gif" or $type == "png")
	{
		imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
		imagealphablending($new, false);
		imagesavealpha($new, true);
	}
	imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);
	switch($type)
	{
		case 'bmp': imagewbmp($new, $dst); break;
		case 'gif': imagegif($new, $dst); break;
		case 'jpg': imagejpeg($new, $dst); break;
		case 'png': imagepng($new, $dst); break;
	}
	return true;
}
