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


require_once('../conf/auth.php');
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
<link href="loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Welcome <?php echo $_SESSION['SESS_FIRST_NAME'];?></h1>
<?php $menu="effect-form"; require "../conf/menu.php"; ?>
<?php

echo "<h2>Nutcracker: RGB Effects Builder for user $username<br/>
	On this page you build an animation of the circle class and crate an animated GIF</h2>"; 
//
require("read_file.php");



show_array($_GET,"_GET");
///*
/*
Array

(
    [username] => f
    [user_target] => MT
    [effect_class] => circles
    [effect_name] => 44
    [number_circles] => 4
    [number_rotations] => 2
    [circle_thickness] => 1
    [start_color] => #26FF35
    [end_color] => #2E35FF
    [frame_delay] => 22
    [direction] => 2
    [submit] => Submit Form to create your target model
)



 */ 


extract($_GET);


$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$direction = strtolower($direction);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);

//save_user_effect($_GET);

$path="../targets/". $username;

list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5);

$t_dat = "AMINITREE.dat";
$path="../targets/2";
echo "=read_file($t_dat,$path)\n";

$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
echo "<pre>";


$path ="workspaces/2";
$directory=$path;
if (file_exists($directory)) {
} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}

$x_dat = "ZZ_ZZ+CIRCLE.dat";
$effect_name="CIRCLE";
$user_target="ZZ_ZZ";


//	remove old ong and dat files
$mask = $directory . "/*.png";
//array_map( "unlink", glob( $mask ) );

$mask = $directory . "/*.dat";
//array_map( "unlink", glob( $mask ) );

$base = $user_target . "~" . $effect_name;
circle($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$use_background,$background_color); // create circles on the target

$target_info=get_info_target($username,$t_dat);
show_array($target_info,'MODEL: ' . $t_dat);

show_elapsed_time($script_start,"Total Elapsed time for this effect:");


function circle($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$use_background,$background_color)
{

	echo " circle($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$use_background,$background_color\n";

	$minStrand =$arr[0];  // lowest strand seen on target
	$minPixel  =$arr[1];  // lowest pixel seen on skeleton
	$maxStrand =$arr[2];  // highest strand seen on target
	$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
	$maxI      =$arr[4];  // maximum number of pixels in target
	$tree_rgb  =$arr[5];
	$tree_xyz  =$arr[6];
	$file      =$arr[7];
	$min_max   =$arr[8];

	show_elapsed_time($script_start,"Creating  Effect, circles class:");
	$line= 0;
	$rgb=255;

	$windowStrandArray = getWindowArray($minStrand,$maxStrand,$degrees);

	$spacing_between_circles=2;
	$maxFrames = $maxPixel*$spacing_between_circles+10;
	echo "maxFrames = maxPixel*spacing_between_circles;\n";
	echo "$maxFrames = $maxPixel*$spacing_between_circles;\n";

	$object_id=0;
	$max=$maxPixel;
	$p=1;
	$base="AA+CIRCLE1";
	$path="workspaces/2";

	for( $f= 1; $f<= $maxFrames; $f++)
	{
		echo "f=$f\n";
		$x_dat = $base . "_d_". $f . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$f] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$f];
		$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
		fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");


		if($f % $spacing_between_circles==1)
		{

			$object_id++;
			echo "creating object $object_id on frame $f\n";
			$circle_object[]['pixel']=1;
			$circle_object[]['rgb']=255-$f*2;
			$circle_object[]['object_id']=$object_id;
		}
		$arr=array($circle_object,$max);
		$arr=advance_all_objects($circle_object,$maxPixel,$max);
		$circle_object=$arr[0];
		$max=$arr[1];
		$cnt=count($circle_object);
		$rgb=255;
		for($i=$cnt;$i>=1;$i--)
		{
			$p=$circle_object[$i]['pixel'];
			$rgb=$circle_object[$i]['rgb'];
			for($s=1;$s<=$maxStrand;$s++)
			{
				$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
				fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb));

				//fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb,$string, $user_pixel,$strand_pixel[$strand][$p][0],$strand_pixel[$strand][$p][1],$frame,$seq_number));

				//		printf ("<pre>%3d-%3d t1 %4d %4d %9.3f %9.3f %9.3f %d</pre>\n",$line,$l,$strand,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val);
			}
		}

		fclose($fh_dat[$f]);
		show_elapsed_time($script_start,"Finished  Effect, circles class:");
	}
	echo "make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration)\n";
	$amperage=array();
	make_gp($batch,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration);

	echo "</body>";
	echo "</html>";
}

function advance_all_objects($circle_object,$maxPixel,$max)
{
	echo " advance_all_objects($circle_object,$maxPixel,$max)\n";
	for($i=$cnt;$i>=1;$i--)
	{
		$p=$circle_object[$i]['pixel'];
		$rgb=$circle_object[$i]['rgb'];
		printf("advance_all_objects: $i   frame %d  p,rgb=%d,%d\n",$f,$p,$rgb);
	}

	$color=0;
	$cnt=count($circle_object);
	$rgb=255;
	echo "for($i=0;$i<$cnt;$i++)\n";
	for($i=0;$i<$cnt;$i++)
	{
		$p=$circle_object[$i]['pixel'];
		echo "i=$i p,rgb=". $circle_object[$i]['pixel'] .",". $circle_object[$i]['rgb'] . "\n";
		if($p<$max)
		{
			$old_rgb=	$circle_object[$i]['rgb'];
			$old_object_id =$circle_object[$i]['object_id'];
			$p++;
			$circle_object[$i]['pixel']=$p;
			//
			//$circle_object[$i]['rgb']=$old_rgb;
			//$circle_object[$i]['object_id']=$old_object_id;
			if($p==$max)
			{
				$max--;
				if($max<1) $max=1;
			}
			echo "p < max. p,max=$p,$max\n";
		}
	}
	$arr=array($circle_object,$max);
	return($arr);
}

?>

