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
require_once ('../conf/auth.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nutcracker: RGB Effects Builder</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="last-modified" content=" 24 Feb 2012 09:57:45 GMT"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
<meta name="robots" content="index,follow"/>
<meta name="googlebot" content="noarchive"/>
<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"/> 
<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php $menu = "effect-form";
require "../conf/menu.php";
?>
<?php
//
echo "<pre>\n";
ini_get('max_execution_time');
set_time_limit(250);
ini_get('max_execution_time');
echo "</pre>\n";
require ("read_file.php");
$array_to_save=$_POST;
$array_to_save['OBJECT_NAME']='gif';
extract ($array_to_save);
$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);
$array_to_save['effect_name']=$effect_name;
$array_to_save['username']=$username;
$frame_delay = $_POST['frame_delay'];
$frame_delay = intval((5+$frame_delay)/10)*10; // frame frame delay to nearest 10ms number_format
$array_to_save['frame_delay']=$frame_delay;
extract ($array_to_save);
save_user_effect($array_to_save);
//show_array($_POST,"_POST");
show_array($array_to_save,"array_to_save");
//show_array($_SESSION,"_SESSION");
//show_array($_SERVER,"_SERVER");
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
$member_id=get_member_id($username);
$path ="workspaces/$member_id";
$gifpath ="gifs/$member_id";
$directory=$path;
if (file_exists($directory))
{
	} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}
$base = $user_target . "+" . $effect_name;
$t_dat = $user_target . ".dat";
$xdat = $user_target ."+".  $effect_name . ".dat";
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
$file = $arr[7];
$min_max = $arr[8];
$strand_pixel = $arr[9];
srand(time());
$maxFrame = 100;
//$maxTrees=6;	// how many tree to draw at one time
$seq_number = 0;
require_once("GIFDecoder.class.php");
$FIC="flag.gif";
$FIC="bells2.gif";
$FIC="usaCa.gif";
$FIC="tree.gif";
$FIC="lights11.gif";
$FIC="wreath06.gif";
$FIC="usaCa.gif";
$FIC=$file1;
$FIC2=$gifpath . "/" . $FIC;
echo "<h1>Processing file $FIC2</h1>";
if(file_exists($FIC2))
{
	$GIF_frame = fread (fopen ($FIC2,'rb'), filesize($FIC2));
	echo "<br/><img src=\"" . $FIC2 . "\"/><br/>\n";
	$decoder = new GIFDecoder ($GIF_frame);
	$frames = $decoder->GIFGetFrames();
	for ( $i = 0; $i < count ( $frames ); $i++ )
	{
		$tokens=explode (".",$FIC2);
		$FIC_new = $tokens[0];
		$fname = ( $i < 10 ) ? $FIC_new."_0$i.giftmp" : $FIC_new."_$i.giftmp";
		$hfic=fopen ( $fname, "wb" );
		$file_array[$i]=$fname;
		fwrite ($hfic , $frames [ $i ] );
		fclose($hfic);
	}
}
$maxFrame=count ( $frames );
//$file="ball-icon.png";
//$image_array=get_image($file);
$path = "workspaces/" . $member_id;
for ($frame = 1; $frame <= $maxFrame; $frame++)
{
	$file = $file_array[$frame-1];
	$image_array=get_image($file,$frame,$maxStrand,$maxPixel,$window_degrees);
	echo "<pre>";
	print_r($image_array);
	echo "</pre>\n";
	$x_dat = $base . "_d_" . $frame . ".dat";
	// for spirals we will use a dat filename starting "S_" and the tree model
	$dat_file[$frame] = $path . "/" . $x_dat;
	$dat_file_array[] = $dat_file[$frame];
	$fh_dat[$frame] = fopen($dat_file[$frame], 'w') or die("can't open file");
	fwrite($fh_dat[$frame], "#    " . $dat_file[$frame] . "\n");
	draw_icon($fh_dat[$frame], $image_array, 0, $frame, $minStrand, $maxStrand, $minPixel, $maxPixel, $tree_xyz, $strand_pixel,$brightness,$window_degrees);
	//	draw_icon(	$fh_dat [$frame],$big_image_array[4],66,$frame,$minStrand ,$maxStrand,$minPixel,$maxPixel,$tree_xyz,$strand_pixel);
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
$amperage = array();
$x_dat_base = $base . ".dat";
echo "make_gp($path, $x_dat_base, $t_dat, $dat_file_array, $min_max, $username, $frame_delay, $script_start, $amperage, $seq_duration, $show_frame);\n";
make_gp($arr,$path, $x_dat_base, $t_dat, $dat_file_array, $min_max, $username, $frame_delay, $script_start, $amperage, $seq_duration, $show_frame);
list($usec, $sec) = explode(' ', microtime());
$script_start = (float)$sec + (float)$usec;
$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out); 

function draw_icon($fh, $image_array, $offset, $frame, $minStrand, $maxStrand, $minPixe, $maxPixel, $tree_xyz, $strand_pixel,$brightness,$window_degrees)
{
	$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	$seq_number = 0;
	for ($x = 0; $x <= 63; $x++)
	{
		for ($y = 0; $y <= 63; $y++)
		{
			if(!isset($image_array[$x][$y]) or $image_array[$x][$y]==null)
			{
				$rgb_val=0;
			}
			else
			{
				$rgb_val = $image_array[$x][$y];
			}
			$s = $x + $offset;
			$p = $y + 4;
			$s = $maxStrand-$s;
			if ($s >= 1 and $s <= $maxStrand and $p >= 1 and $p <= $maxPixel and isset($tree_xyz[$s][$p]))
			{
				$xyz = $tree_xyz[$s][$p];
				$seq_number++;
				$string = $user_pixel = 0;
				//		if($s==10) $rgb_val=hexdec("#FFFF00");
				$BRIGHT=1;
				if(strtoupper($brightness)=='Y')
				{
					$r = ($rgb_val >> 16) & 0xFF;
					$g = ($rgb_val >> 8) & 0xFF;
					$b = $rgb_val & 0xFF;
					$HSV=RGB_TO_HSV($r,$g,$b);
					$H=$HSV['H']; $S=$HSV['S']; $V=$HSV['V'];
					if($V>0.1) $V=$V+.5;
					if($V>1) $V=1;
					$HSV['V']=$V;
					$rgb_val=HSV_TO_RGB($H,$S,$V);
				}
			//	if(in_array($s,$window_array)) // Is this strand in our window?, If yes, then we output lines to the dat file
				{
					fwrite($fh, sprintf("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n", $s, $p, $xyz[0], $xyz[1], $xyz[2], $rgb_val, $string, $user_pixel, $strand_pixel[$s][$p][0], $strand_pixel[$s][$p][1], $frame, $seq_number));
					//					printf ("<pre>t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d</pre>\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number);
				}
			}
		}
	}
}

function get_image($file,$frame,$maxStrand,$maxPixel,$window_degrees)
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
	else if ($image_type == "giftmp")
		$image = imagecreatefromgif($image_path);
	else die("Invalid file type of $image_type");
	
	$size = getimagesize($image_path);
	$img_width = $size[0];
	$img_height = $size[1];
	
/*	include('SimpleImage.php');
$image = new SimpleImage();
$image->load($file);
if($img_width<$img_height)
	$image->resizeToWidth($maxStrand*2);
else
$image->resizeToHeight($maxPixel*2);
$image->save($file2,$img_type_number);
$file=$file2;*/


	//$image=resizeImage($Image,$maxStrand,$maxPixel); 
	//echo "<pre>img width,height = $img_width,$img_height  max strand,pixel=$maxStrand,$maxPixel</pre>\n";
	$s = 0;
	//	If window degrees = 360, then leave maxStrand alone otherwise sclae it.
	$maxStrand = intval( $maxStrand * $window_degrees/360);
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
		echo "<h3>Color palette used in this gif file</h3>\n";
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
/*

function fromRGB($R, $G, $B)
{
	$hex = "#";
	$hex.= str_pad(dechex($R), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($G), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($B), 2, "0", STR_PAD_LEFT);
	return $hex;
}
*/

function setTransparency($new_image,$image_source)
{
	$transparencyIndex = imagecolortransparent($image_source); 
	$transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255); 
	if ($transparencyIndex >= 0)
	{
		$transparencyColor    = imagecolorsforindex($image_source, $transparencyIndex);
	}
	$transparencyIndex    = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']); 
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
?>
