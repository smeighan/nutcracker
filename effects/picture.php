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
echo "<h2>Nutcracker: RGB Effects Builder for user $myusername<br/>
On this page you build an animation of the spiral class and crate an animated GIF<h2>"; 
//
require("read_file.php");
echo "<pre>";
print_r($_GET);
///*
/*
Array
(
[username] => f
[user_target] => MT
[effect_class] => spirals
[effect_name] => 44
[number_spirals] => 4
[number_rotations] => 2
[spiral_thickness] => 1
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
save_user_effect($_GET);
if($window=="360")
	$halfMegatree=0;
else
$halfMegatree=1;
$path="../targets/". $username;
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5);
if($numberSpirals<1 or $numberSpirals>9) $error_message[]="Number of Spirals must be between 1-9.";
$t_dat = $user_target . ".dat";
$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
$path ="../effects/workspaces/" . $username;
$directory=$path;
if (file_exists($directory))
{
	} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}
$x_dat = $user_target . "~" . $effect_name . ".dat";
$base = $user_target . "~" . $effect_name;
picture($image_file,$path,$t_dat,$arr,$halfTree);
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
echo "<pre>\n\nElapsed time = $elapsed_time seconds</pre>\n";

function picture($image_file,$path,$t_dat,$arr,$halfTree)
{
	$path="models/pictures";
	$minStrand =$arr[0];  // lowest strand seen on target
	$minPixel  =$arr[1];  // lowest pixel seen on skeleton
	$maxStrand =$arr[2];  // highest strand seen on target
	$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
	$maxI      =$arr[4];  // maximum number of pixels in target
	$tree_rgb      =$arr[5];
	$tree_xyz      =$arr[6];
	echo "<pre>";
	$image_path = "images/IMG_7965_head_small.png";
	$image_path = "images/IMG_7965_head_black.png";
	$image_path = "images/14578.jpg";
	$image_path = "images/avatar_3207_1325605309.png";
	$image_path = "images/grinch2.png";
	$image_path = "images/grinch.png";
	$image_path = "images/kezw_small.jpg";
	$image_path = "images/snoopy2.jpg";
	$image_path = "images/snoopy.jpg";
	$image_path = "images/IMG_7965_head.png";
	$image_path = "images/steve_gase.png";
	echo "image file = $image_path\n";
	$tokens=explode(".",$image_path);
	$image_type=$tokens[1];
	if($image_type=="png") $image  = imagecreatefrompng($image_path);
	if($image_type=="jpg") $image  = imagecreatefromjpeg($image_path);
	if($image_type=="gif") $image  = imagecreatefromgif($image_path);
	$size   = getimagesize($image_path);
	$img_width  = $size[0];
	$img_height = $size[1];
	echo "width=$img_width, height=$img_height";
	$s=0;
	$percission=intval(($img_height/$maxStrand)+0.5);
	//	if($width<50) $percission=1;
	echo "percission = $percission";
	//$percission=5;
	$x_dat="picture.dat";
	$dat_file = $path . "/" . $x_dat; // for spirals we will use a dat filename starting "S_" and the tree model
	$dat_file_array[]=$dat_file;
	$fh_dat = fopen($dat_file, 'w') or die("can't open file");
	fwrite($fh_dat,"#    $dat_file\n");
	$lowRange1 = $minStrand;
	$lowRange2 = $maxStrand/4;
	$highRange1=$maxStrand -  $maxStrand/4;
	$highRange2=$maxStrand;
	$halfTree=1;
	$windowStrandArray = getWindowArray($minStrand,$maxStrand);
	$pixel_offset = intval($maxPixel - $img_height/$percission);
	if($pixel_offset<0) $pixel_offset=0;
	for($x = 0; $x < $img_width; $x += $percission)
	{
		$s++; $p_raw=0;
		for($y = 0; $y < $img_height; $y += $percission)
		{
			$p_raw++;
			$p = $p_raw+$pixel_offset;
			$rgb = imagecolorat($image, $x, $y);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;
			$HSL=RGB_TO_HSV ($r, $g, $b);
			$H=$HSL['H']; 
			$S=$HSL['S']; 
			$V=$HSL['V']; 
			$S=$S*1.10;
			if($s>1.0) $S=1.0;
			$rgb_val=HSV_TO_RGB ($H, $S, $V);
			$rgb_val=$rgb;
			//	echo " s,p = $s,$p  x,y = $x,$y rgb = $r,$g,$b  \n";
			if(         ($s>=$minStrand and $s <=$maxStrand)
				and ($p>=$minPixel and $p<=$maxPixel))
			{
				$new_s=$s;
				$key = array_search($s, $windowStrandArray); // $key = 2;
				echo "<pre>picture: s,p,key = $s,$p,$key</pre>";
				//		if($halfTree==0 or is_numeric($key))
				{
					$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
					fwrite($fh_dat,sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val));
					printf ("t1 %4d %4d %9.3f %9.3f %9.3f %di (key=%s)\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$key);
				}
			}
		}
	}
	echo "</pre>";
	fclose($fh_dat);
	$halfTree=0;
	$tag="pic";
	$direction="cw";
	rotate($height,$arr,$x_dat,$t_dat,$tag,$path,$direction,$halfTree);
}
/*
(
[username] => f
[user_target] => MT
[effect_class] => spirals
[effect_name] => 44
[number_spirals] => 4
[number_rotations] => 2
[spiral_thickness] => 1
[start_color] => #26FF35
[end_color] => #2E35FF
[frame_delay] => 22
[direction] => 2
[submit] => Submit Form to create your target model
)
	*/

function save_user_effect($passed_array)
{
	//Include database connection details
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	extract($_GET);
	$effect_name = strtoupper($effect_name);
	$effect_name = rtrim($effect_name);
	$direction = strtolower($direction);
	$username=str_replace("%20"," ",$username);
	$effect_name=str_replace("%20"," ",$effect_name);
	//	insert into the header
	// 	effect_class	username	effect_name	effect_desc	created	last_upd
	//
	$effect_desc="desc";
	$insert = "REPLACE into effects_user_hdr( effect_class,username,effect_name,effect_desc,last_upd)
		values ('$effect_class','$username','$effect_name','$effect_desc',now())";
	mysql_query($insert) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $insert . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	$query = "select param_name from effects_dtl where effect_class = '$effect_class'";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	$param_name_array=array();
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$param_name_array[]=$param_name;
	}
	$skip_these=array('submit');
	foreach($_GET AS $key => $value)
	{
		$key=strtolower($key);
		if(in_array($key,$param_name_array))
		{
			//	login	effect_name	param_name	param_value	created	last_upd
			//
			$insert2 = "REPLACE into effects_user_dtl(login,effect_name,param_name,param_value,last_upd) 
			values ('$username','$effect_name','$key','$value',now())";
			mysql_query($insert2) or die ("Error on $insert2");
		}
	}
	//echo "<pre>Target model saved</pre>";
	$date_field= date('Y-m-d');
	$time_field= date("H:i:s");
	$query="INSERT into audit_log values ('$username','$date_field','$time_field','effect','$OBJECT_NAME')";
	$result=mysql_query($query) or die("Failed to execute $query");
	mysql_close();
	$_SESSION['SESS_LOGIN'] = $username;
	session_write_close();
	//header("location: target-exec.php?model=$OBJECT_NAME?user=$username");
	//exit();
}
?>
