<?php

function f_color_wash($get)
{
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	extract ($get);
	set_time_limit(0);
	ini_set("memory_limit","1024M");
	require_once("../effects/read_file.php");
	//
	//
	$member_id=get_member_id($username);
	$get['member_id']=$member_id;
	if($batch==0) show_array($get,"$effect_class Effect Settings");
	$path="../targets/". $member_id;
	$t_dat = $user_target . ".dat";
	$path ="../effects/workspaces/" . $member_id;
	$directory=$path;
	if (!file_exists($directory))
	{
		if($batch==0) echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$base = $user_target . "~" . $effect_name;
	color_wash($get);
	$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out,$sparkles);
}

function color_wash($get)
{
	extract ($get);
	$path="../targets/". $member_id;
	$t_dat = $user_target . ".dat";
	$base = $user_target . "~" . $effect_name;
	$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
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
	echo "<pre>";
	//print_r($arr);
	echo "minStrand,minPixel,maxStrand,maxPixel=$minStrand,$minPixel,$maxStrand,$maxPixel\n";
	echo "</pre>";
	$path="../effects/workspaces/". $member_id;
	$window_array = getWindowArray($minStrand,$maxStrand,$window_degrees);
	$sparkles_array = create_sparkles($sparkles,$maxStrand,$maxPixel);
	$maxFrame=intval(($seq_duration*1000)/$frame_delay);
	/*	if($batch==0) echo "<pre>";
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
		if($frame>5000) exit ("Too many frames in sequence. maxFrame=$maxFrame");
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
		$seq_number=0;
		for($s=1;$s<=$maxStrand;$s++)
			for($p=1;$p<=$maxPixel;$p++)
		{
			//if(in_array($s,$window_array)) // Is this strand in our window?, 
			{
				$string=$user_pixel=0;
				$xyz=$tree_xyz[$s][$p];
				$seq_number++;
				$rgb_val_orig=$rgb_val;
				if(isset($sparkles_array[$s][$p])===false or $sparkles_array[$s][$p]==null )
					$dummy=1;
				else if($sparkles_array[$s][$p]>1)
				{
					$sparkles_array[$s][$p]++;
					$rgb_val=calculate_sparkle($s,$p,$sparkles_array[$s][$p],$rgb_val,$sparkle_count);
				}
				$seq_number++;
				fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number));
				$rgb_val=$rgb_val_orig;
					printf ("<pre>t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number);
			}
		}
		fclose($fh_dat[$frame]);
	}
	$amperage=array();
	$x_dat_base = $base . ".dat";
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,
	$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame);
}
