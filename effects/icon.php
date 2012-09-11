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
$member_id = 2;
$user_target = "AA_MATRIX2";
$effect_name = "ICON";
$base = $user_target . "~" . $effect_name;
$t_dat = $user_target . ".dat";
$xdat = $user_target . "~" . $effect_name . ".dat";
$path = "../targets/" . $member_id;
$arr = read_file($t_dat, $path);
//  target megatree 32 strands, all 32 being used. read data into an array
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
echo "	$maxStrand,$maxPixel\n";
srand(time());
$maxFrame = 100;
//$maxTrees=6;	// how many tree to draw at one time
$seq_number = 0;
$icon_array = array('ball', 'bear-n-wagon', 'bike', 'blocks', 'christmas-tree', 'drum', 'Halloween-1', 'Halloween-3', 'Halloween-7', 'USFlag1', 'USFlag2');
$c = count($icon_array);
echo "<pre>";
for ($i = 0; $i < $c; $i++)
{
	$file = $icon_array[$i] . "-icon.png";
	$big_image_array[$i] = get_image($file);
	echo "file=$file\n";
}
//$file="ball-icon.png";
//$image_array=get_image($file);
$path = "workspaces/" . $member_id;
for ($frame = 1; $frame <= $maxFrame; $frame++)
{
	if ($frame > 500)
		exit("Too many frames in sequence");
	$x_dat = $base . "_d_" . $frame . ".dat";
	// for spirals we will use a dat filename starting "S_" and the tree model
	$dat_file[$frame] = $path . "/" . $x_dat;
	$dat_file_array[] = $dat_file[$frame];
	$fh_dat[$frame] = fopen($dat_file[$frame], 'w') or die("can't open file");
	fwrite($fh_dat[$frame], "#    " . $dat_file[$frame] . "\n");
	draw_icon($fh_dat[$frame], $big_image_array[6], 20, $frame, $minStrand, $maxStrand, $minPixel, $maxPixel, $tree_xyz, $strand_pixel);
	draw_icon($fh_dat[$frame], $big_image_array[7], 54, $frame, $minStrand, $maxStrand, $minPixel, $maxPixel, $tree_xyz, $strand_pixel);
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
make_gp($batch,$arr,$path, $x_dat_base, $t_dat, $dat_file_array, $min_max, $username, $frame_delay, $script_start, $amperage, $seq_duration, $show_frame);
list($usec, $sec) = explode(' ', microtime());
$script_start = (float)$sec + (float)$usec;

function draw_icon($fh, $image_array, $offset, $frame, $minStrand, $maxStrand, $minPixe, $maxPixel, $tree_xyz, $strand_pixel)
{
	$seq_number = 0;
	for ($x = 0; $x <= 31; $x++)
	{
		for ($y = 0; $y <= 31; $y++)
		{
			$rgb_val = $image_array[$x][$y];
			$s = $x + $frame + $offset;
			$p = $y + 4;
			if ($s >= 1 and $s <= $maxStrand and $p >= 1 and $p <= $maxPixel and isset($tree_xyz[$s][$p]))
			{
				$xyz = $tree_xyz[$s][$p];
				$seq_number++;
				$string = $user_pixel = 0;
				if ($rgb_val <> 0)
				{
					fwrite($fh, sprintf("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n", $s, $p, $xyz[0], $xyz[1], $xyz[2], $rgb_val, $string, $user_pixel, $strand_pixel[$s][$p][0], $strand_pixel[$s][$p][1], $frame, $seq_number));
					//	printf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number);
				}
			}
		}
	}
}

function get_image($file)
{
	echo "get_image($file)\n";
	$path = "../icons";
	$directory = $path;
	$image_path = $path . "/" . $file;
	$tokens = explode(".", $file);
	$image_type = $tokens[1];
	echo "image_path=$image_path,  type=$image_type\n";
	if ($image_type == "png")
		$image = imagecreatefrompng($image_path);
	if ($image_type == "jpg")
		$image = imagecreatefromjpeg($image_path);
	if ($image_type == "gif")
		$image = imagecreatefromgif($image_path);
	$maxStrand = 34;
	$size = getimagesize($image_path);
	$img_width = $size[0];
	$img_height = $size[1];
	echo "<pre>";
	echo "width=$img_width, height=$img_height";
	$s = 0;
	$percission = intval(($img_height / $maxStrand) + 0.5);
	if ($img_width < 50)
		$percission = 1;
	echo "percission = $percission";
	for ($x = 0; $x < $img_width; $x += $percission)
	{
		$s++;
		$p_raw = 0;
		for ($y = 0; $y < $img_height; $y += $percission)
		{
			$rgb = imagecolorat($image, $x, $y);
			$cols = imagecolorsforindex($image, $rgb);
			$r = $cols['red'];
			$g = $cols['green'];
			$b = $cols['blue'];
			$rgb_val = $r<<16 + $g<<8 + $b;
			$rgb_val = hexdec(fromRGB($r,$g,$b));
			$image_array[$x][$y] = $rgb_val;
			//			echo "<pre>x,y,rgb= $x,$y,($r,$g,$b), rgbval=$rgb_val</pre>";
		}
	}
	echo "</pre>";
	return $image_array;
}

function fromRGB($R, $G, $B)
{
	$hex = "#";
	$hex.= str_pad(dechex($R), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($G), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($B), 2, "0", STR_PAD_LEFT);
	return $hex;
}
