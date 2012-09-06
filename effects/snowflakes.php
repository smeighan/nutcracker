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
require("read_file.php");
show_array($_POST,"_POST");
///*
/*
Array
(
[username] => f
[user_target] => MT
[effect_class] => garlands
[effect_name] => 44
[number_garlands] => 4
[number_rotations] => 2
[garland_thickness] => 1
[start_color] => #26FF35
[end_color] => #2E35FF
[frame_delay] => 22
[direction] => 2
[submit] => Submit Form to create your target model
)
	*/ 
$array_to_save=$_POST;
$array_to_save['OBJECT_NAME']='snowlakes';
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
show_array($array_to_save,"Effect Settings");
//show_array($_SESSION,"_SESSION");
//show_array($_SERVER,"_SERVER");
$path="../targets/". $username;
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
$member_id=get_member_id($username);
$path ="workspaces/$member_id";
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
//	remove old ong and dat files
$mask = $directory . "/*.png";
//array_map( "unlink", glob( $mask ) );
$mask = $directory . "/*.dat";
//array_map( "unlink", glob( $mask ) );
/*
_POST
username	f
user_target	AA
effect_class	garlands
effect_name	CIRCLE2
window_degrees	180
start_color	#3672FF
end_color	#295BFF
frame_delay	20
sparkles	10
seq_duration	5
submit	Submit Form to create your target model
*/
$path="workspaces/". $member_id;
snowflakes($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$sparkles,$seq_duration,$show_frame,$maxSnowFlakes,$maxPhase);
$target_info=get_info_target($username,$t_dat);
show_array($target_info,'MODEL: ' . $t_dat);
$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out); 
// function garland($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$sparkles,$seq_duration,$garland_gap,$garland,$show_frame)
	
function snowflakes($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$sparkles,$seq_duration,$show_frame,$maxSnowFlakes,$maxPhase)
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
	srand(time());
	$maxFrame=40;
	//$maxSnowFlakes=3;	// how many snowflakes to draw at one time
	$seq_number=0;
	//$maxPhase=5;
	$depth=$maxPhase;	// depth of snowflake trail in pixles
	$isnowflake=0;
	$orig_tree_rgb=$tree_rgb;
	$snowflake_type=2;
	$everyFrame=9;
	// go thru entire tree array. soem random times set a rgb > 0. increment it, each iteration
	for($frame=1;$frame<=$maxFrame;$frame++)
	{
		if($frame>500) exit ("Too many frames in sequence");
		$x_dat = $base . "_d_". $frame . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$frame] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$frame];
		$fh_dat [$frame]= fopen($dat_file[$frame], 'w') or die("can't open file");
		fwrite($fh_dat[$frame],"#    " . $dat_file[$frame] . "\n");
		if(1 == $frame%$everyFrame)
		{
			for($m=1;$m<=$maxSnowFlakes;$m++)	// create a pile of random snowflakes.
			{
				$s= rand($minStrand,$maxStrand);
				$p= rand($minPixel,$maxPixel-$depth);
				if($s<1) $s=1;
				if($p<1) $p=1;
				$isnowflake++;
				if($snowflake_array[$isnowflake]['rgb']==0)
					$snowflake_array[$isnowflake]['strand']=$s;
				$snowflake_array[$isnowflake]['pixel']=$p;
				$snowflake_array[$isnowflake]['frame']=$frame;
				$snowflake_array[$isnowflake]['phase']=0;
				$snowflake_array[$isnowflake]['rgb']=$start_color;
			}
		}
		$tree_rgb=$orig_tree_rgb;
		unset($p_array);
		for($im=1;$im<=$isnowflake;$im++)	//	 loop thru the snowflake heads we already have
		{
			$s=$snowflake_array[$im]['strand'];
			$p=$snowflake_array[$im]['pixel'];
			$phase=$snowflake_array[$im]['phase'];
			$rgb=$snowflake_array[$im]['rgb'];
			switch($snowflake_type)
			{
				case 1:
				//	**
				//	.*
				//
				$p_array[0]['p']=$p;
				$p_array[0]['s']=$s;
				$p_array[1]['p']=$p-1;
				$p_array[1]['s']=$s;
				$p_array[2]['p']=$p-1;
				$p_array[2]['s']=$s+1;
				$p_array[3]['p']=$p;
				$p_array[3]['s']=$s+1;
				break;
				case 2:
				//	***
				//	***
				//	.**
				$p_array[0]['p']=$p;
				$p_array[0]['s']=$s;	
				$p_array[1]['p']=$p-1;
				$p_array[1]['s']=$s;
				$p_array[2]['p']=$p-1;
				$p_array[2]['s']=$s+1;
				$p_array[3]['p']=$p;
				$p_array[3]['s']=$s+1;
				$p_array[4]['p']=$p-2;
				$p_array[4]['s']=$s;
				$p_array[5]['p']=$p-2;
				$p_array[5]['s']=$s+1;
				$p_array[6]['p']=$p-2;
				$p_array[6]['s']=$s+2;
				$p_array[7]['p']=$p-2;
				$p_array[7]['s']=$s+2;
				$p_array[8]['p']=$p-1;
				$p_array[8]['s']=$s+2;
				$p_array[9]['p']=$p;
				$p_array[9]['s']=$s+2;
				break;
			}
			echo "<pre>";
			//print_r($p_array);
			$cnt=count($p_array);
			for($i=0;$i<$cnt;$i++)
			{
				$color_HSV=color_picker($i,$cnt,$MaxFrame,$start_color,$end_color);
				$H=$color_HSV['H'];
				$S=$color_HSV['S'];
				$V=$color_HSV['V'];
				$rgb_val=HSV_TO_RGB ($H, $S, $V);
				$rgb_val=hexdec("#FF0000");
				$p = $p_array[$i]['p'];
				$s = $p_array[$i]['s'];
				if($s>$maxStrand) $s=$s-$maxStrand;
				if($p>=$minPixel and $p<=$maxPixel)
				{
					$tree_rgb[$s][$p]=$rgb_val; // and store all of the snowflake info into the tree_rgb array
				}
			}
			//		$snowflake_array[$im]['pixel']++;	//	 now advance this snowflake head downward
		}
		/*
		for($s=1;$s<=$maxStrand;$s++)
			for($p=1;$p<=$maxPixel;$p++)
		{
			if($tree_rgb[$s][$p]>0)
			{
				echo "$s $p " . ($tree_rgb[$s][$p] . "\n";
			}
		}
		*/
		for($s=1;$s<=$maxStrand;$s++)
			for($p=1;$p<=$maxPixel;$p++)
		{
			$rgb_val=$tree_rgb[$s][$p];
			$string=$user_pixel=0;
			$xyz=$tree_xyz[$s][$p];
			$seq_number++;
			if($rgb_val <> 0)
				fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",
			$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,
			$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number));
		}
		fclose($fh_dat[$frame]);
	}
	$amperage=array();
	$x_dat_base = $base . ".dat";
	make_gp($arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$script_start,$amperage,$seq_duration,$show_frame);
	echo "</body>";
	echo "</html>";
}
?>
