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
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Welcome <?php echo $_SESSION['SESS_FIRST_NAME'];?></h1>
<?php $menu="effect-form"; require "../conf/menu.php"; ?>
<?php
//
require("read_file.php");
$username=$_SESSION['SESS_LOGIN'];
$member_id=$_SESSION['SESS_MEMBER_ID'];
echo "<h2>Nutcracker: RGB Effects Builder for user $username<br/>
On this page you build an animation of the spiral class and create an animated GIF</h2>"; 
ini_get('max_execution_time'); 
set_time_limit(250);
ini_get('max_execution_time'); 
//show_array($_POST,"_POST");
//show_array($_SERVER,"_SERVER");
//show_array($_SESSION,"_SESSION");
///*
/*
SESSION
Array
(
[SESS_MEMBER_ID] => 2
[SESS_FIRST_NAME] => sean
[SESS_LAST_NAME] => MEIGHAN
[SESS_LOGIN] => f
)
	FLY_0_0_TEST?username=f?effect_class=butterfly?user_targets=AA
*/ 
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
//
//
$array_to_save=$_POST;
$array_to_save['OBJECT_NAME']='color_wash';
extract ($array_to_save);
$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);
$array_to_save['effect_name']=$effect_name;
$array_to_save['username']=$username;
if(!isset($show_frame)) $show_frame='N';
$array_to_save['show_frame']=$show_frame;
$frame_delay = $_POST['frame_delay'];
$frame_delay = intval((5+$frame_delay)/10)*10; // frame frame delay to nearest 10ms number_format
extract ($array_to_save);
echo "<pre>";
print_r($array_to_save);
echo "</pre>\n";
save_user_effect($array_to_save);
show_array($array_to_save,"Effect Settings");
$path="../targets/". $member_id;
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
$t_dat = $user_target . ".dat";
$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
$member_id=get_member_id($username);
$path ="workspaces/" . $member_id;
$directory=$path;
if (file_exists($directory))
{
	} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}
$maxFrame=intval(($seq_duration*1000)/$frame_delay);
echo "<pre>maxFrame=$maxFrame</pre>\n";
$window_array = getWindowArray($minStrand,$maxStrand,$window_degrees);
$base = $user_target . "+" . $effect_name;
color_wash($arr,$t_dat,$base,$path,$frame_delay,$seq_duration,$window_degrees,$username,$frame_delay,$maxFrame,$show_frame,$start_color,$end_color,$sparkles,$sparkle_count);
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
//if($description = 'Total Elapsed time for this effect:')
	printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out,$sparkles); 

function color_wash($arr,$t_dat,$base,$path,$frame_delay,$seq_duration,$window_degrees,$username,$frame_delay,$maxFrame,$show_frame,$start_color,$end_color,$sparkles,$sparkle_count)
{
	$minStrand =$arr[0];  // lowest strand seen on target
	$minPixel  =$arr[1];  // lowest pixel seen on skeleton
	$maxStrand =$arr[2];  // highest strand seen on target
	$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
	$maxI      =$arr[4];  // maximum number of pixels in target
	$tree_rgb  =$arr[5];
	$tree_xyz  =$arr[6];
	$file      =$arr[7];
	$min_max   =$arr[8];
	$strand_pixel=$arr[9];
	$window_array = getWindowArray($minStrand,$maxStrand,$window_degrees);
	$sparkles_array = create_sparkles($sparkles,$maxStrand,$maxPixel);
	/*	echo "<pre>";
	print_r($sparkles_array);*/
	/*foreach($sparkles_array as $arr2)
	{
		$s=$arr2['s'];
		$p=$arr2['p'];
		$cnt=$arr2['cnt'];
	}
	*/
	for($frame=1;$frame<=$maxFrame;$frame++)
	{
		if($frame>500) exit ("Too many frames in sequence");
		$x_dat = $base . "_d_". $frame . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$frame] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$frame];
		$fh_dat [$frame]= fopen($dat_file[$frame], 'w') or die("can't open file");
		//
		$color_HSV=color_picker($frame,$maxFrame,0,$start_color,$end_color);
		$H=$color_HSV['H'];
		$S=$color_HSV['S'];
		$V=$color_HSV['V'];
		$rgb_val=HSV_TO_RGB ($H, $S, $V);
		$hex=dechex($rgb_val);
		//printf ("<pre>%4d %7.4f %7.4f %7.4f %s</pre>\n",$frame,$H,$S,$V,$hex);
		//
		fwrite($fh_dat[$frame],"#    " . $dat_file[$frame] . "\n");
		for($s=1;$s<=$maxStrand;$s++)
			for($p=1;$p<=$maxPixel;$p++)
		{
			if(in_array($s,$window_array)) // Is this strand in our window?, 
			{
				$string=$user_pixel=0;
				$xyz=$tree_xyz[$s][$p];
				$seq_number++;
				$rgb_val_orig=$rgb_val;
				if($sparkles_array[$s][$p]>1)
				{
					$sparkles_array[$s][$p]++;
					$rgb_val=calculate_sparkle($s,$p,$sparkles_array[$s][$p],$rgb_val,$sparkle_count);
				}
				fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number));
				$rgb_val=$rgb_val_orig;
				//	printf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number);
			}
		}
		fclose($fh_dat[$frame]);
	}
	$amperage=array();
	$x_dat_base = $base . ".dat";
	make_gp($arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$script_start,$amperage,$seq_duration,$show_frame);
}

function  create_sparkles($sparkles,$maxStrand,$maxPixel)
{
	if($sparkles==0) return array();
	$totalPixels=$maxPixel*$maxStrand;
	$pixels_to_allocate = $totalPixels * ($sparkles/100);
	for($i=1;$i<=$pixels_to_allocate;$i++)
	{
		srand();
		$s=rand(1,$maxStrand);
		$p=rand(1,$maxPixel);
		$sparkles_array[$s][$p]=rand(1,100);
	}
	return $sparkles_array;
}

function calculate_sparkle($s,$p,$cnt,$rgb_val,$sparkle_count)
{
	$orig=$rgb_val;
	$v=intval($cnt%$sparkle_count);
	if($v==1)
	{
		$rgb_val=4473924; // #444444
	}
	if($v==2)
	{
		$rgb_val=8947848; // #888888
	}
	if($v==3)
	{
		$rgb_val=12303291; // #BBBBBB
	}
	if($v==4)
	{
		$rgb_val=16777215; // #FFFFFF
	}
	if($v==5)
	{
		$rgb_val=12303291; // #BBBBBB
	}
	if($v==6)
	{
		$rgb_val=8947848; // #888888
	}
	if($v==7)
	{
		$rgb_val=4473924; // #444444
	}
	$hex=dechex($rgb_val);
	//	echo "<pre>s,p=$s,$p cnt=$cnt v=$v, orig=$orig, rgb_val=$rgb_val, $hex</pre>\n";
	return $rgb_val;
}
