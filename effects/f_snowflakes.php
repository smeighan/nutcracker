<?php

function f_snowflakes($get)
{
	extract ($get);
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	set_time_limit(0);
	ini_set("memory_limit","512M");
	require_once("../effects/read_file.php");
	//
	$member_id=get_member_id($username);
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	$member_id=get_member_id($username);
	$base = $user_target . "~" . $effect_name;
	$t_dat = $user_target . ".dat";
	$xdat = $user_target ."~".  $effect_name . ".dat";
	$path="../targets/". $member_id;
	$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
	$path="../effects/workspaces/". $member_id;
	if(empty($show_frame)) $show_frame='N';
	if(empty($background_color)) $background_color='#FFFFFF';
	//
	if(!isset($batch)) $batch=0;
	$get['batch']=$batch;
	//
	/*echo "<pre>";
	print_r($get);
	echo "</pre>\n";*/
	$target_path ="../targets/$member_id";
	$arr=read_file($t_dat,$target_path); //  target megatree 32 strands, all 32 being used. read data into an array
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
	$path ="../effects/workspaces/$member_id";
	$directory=$path;
	if (file_exists($directory))
	{
		} else {
		echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$path="../effects/workspaces/". $member_id;
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
		if($frame>5000) exit ("Too many frames in sequence");
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
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out); 
	echo "</body>";
	echo "</html>";
}
?>
