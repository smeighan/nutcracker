<?php
//

function f_meteors($get)
{
	if(!isset($get['fade_in']))  $get['fade_in']="0";
	if(!isset($get['fade_out']))  $get['fade_out']="0";
	extract ($get);
	set_time_limit(0);
	ini_set("memory_limit","512M");
	require_once("../effects/read_file.php");
	//
	//
	//show_array($_GET,"_GET");
	if($batch==0) show_array($get,"array_to_save");
	$member_id=get_member_id($username);
	$get['member_id']=$member_id;
	$path ="../effects/workspaces/$member_id";
	$gifpath ="gifs/$member_id";
	$directory=$path;
	if (!file_exists($directory))
	{
		if($batch==0) echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$base = $user_target . "~" . $effect_name;
	$t_dat = $user_target . ".dat";
	$xdat = $user_target ."~".  $effect_name . ".dat";
	$path="../targets/". $member_id;
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
	$path="../effects/workspaces/". $member_id;
	meteors($get);
	$target_info=get_info_target($username,$t_dat);
	if($batch==0) show_array($target_info,'MODEL: ' . $t_dat);
	// function garland($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$sparkles,$seq_duration,$garland_gap,$garland,$show_frame)
		$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out);
}

function meteors($get)
{
	extract($get);
	$base = $user_target . "~" . $effect_name;
	$t_dat = $user_target . ".dat";
	$xdat = $user_target ."~".  $effect_name . ".dat";
	$path="../targets/". $member_id;
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
	$arr_orig=$arr;
	srand(time());
	$maxFrame=120;
	//$maxMeteors=6;	// how many meteors to draw at one time
	$seq_number=0;
	$window_array = getWindowArray($minStrand,$maxStrand,$window_degrees);
	//$maxPhase=5;
	$depth=$maxPhase;	// depth of meteor trail in pixles
	$imeteor=0;
	$orig_tree_rgb=$tree_rgb;
	// go thru entire tree array. soem random times set a rgb > 0. increment it, each iteration
	//
	// if($batch==0) echo "<pre>
	srand();
	for($frame=1;$frame<=$maxFrame;$frame++)
	{
		if($frame>500) exit ("Too many frames in sequence");
		$x_dat = $base . "_d_". $frame . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$frame] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$frame];
		$fh_dat [$frame]= fopen($dat_file[$frame], 'w') or die("can't open file");
		fwrite($fh_dat[$frame],"#    " . $dat_file[$frame] . "\n");
		$meteor_style=1;	
		if($meteor_style==1)
		{
			for($m=1;$m<=$maxMeteors;$m++)	// create a pile of random meteors.
			{
				$s= rand($minStrand,$maxStrand);
				$p= rand($minPixel,$maxPixel-$depth);
				if($s<1) $s=1;
				if($p<1) $p=1;
				$imeteor++;
				//if(!empty($meteor_array[$imeteor]['rgb']) and $meteor_array[$imeteor]['rgb']==0)
					$meteor_array[$imeteor]['strand']=$s;
				$meteor_array[$imeteor]['pixel']=$p;
				$meteor_array[$imeteor]['frame']=$frame;
				$meteor_array[$imeteor]['phase']=0;
				$meteor_array[$imeteor]['rgb']=$start_color;
				$meteor_array[$imeteor]['rgb']=rand(hexdec($start_color),hexdec($end_color));
				$meteor_array[$imeteor]['sparkle']=0;
			}
		}
		else if ($meteor_style==2)
		{
			if($maxPhase<0) $maxPhase=$maxPixel/2;
			$maxBranches=intval($maxPixel/$maxPhase);
			//if($batch==0) echo "<pre>frane=$frame maxBranches=$maxBranches\n";
			for($m=1;$m<=$maxBranches;$m++)	// create a pile of random meteors.
			{
				$p=$maxPhase + ($m-1)*$maxPhase;
				for($s=1;$s<=$maxStrand;$s)
				{
					if($s<1) $s=1;
					if($p<1) $p=1;
					if($p<=$maxPixel)
					{
						$imeteor++;
						//	if(!empty($meteor_array[$imeteor]['rgb']) and $meteor_array[$imeteor]['rgb']==0)
							$meteor_array[$imeteor]['strand']=$s;
						$meteor_array[$imeteor]['pixel']=$p;
						$meteor_array[$imeteor]['frame']=$frame;
						$meteor_array[$imeteor]['phase']=0;
						$meteor_array[$imeteor]['rgb']=$start_color;
						$meteor_array[$imeteor]['sparkle']=0;
					}
				}
			}
		}
		//if($batch==0) echo "<pre>";
		//print_r($meteor_array);
		//if($batch==0) echo "</pre>\n";
		$tree_rgb=$orig_tree_rgb;
		for($im=1;$im<=$imeteor;$im++)	//	 loop thru the meteor heads we already have
		{
			$s=$meteor_array[$im]['strand'];
			$p=$meteor_array[$im]['pixel'];
			$phase=$meteor_array[$im]['phase'];
			$rgb=$meteor_array[$im]['rgb'];
			$random_color_HSV=color_picker($im,$imeteor,$maxFrame,$start_color,$end_color);
			for($ph=0;$ph<=$maxPhase;$ph++)
			{
				if($meteor_type==1)
				{
					$H_array[$ph] = rand(0,1000)/1000;
					$S= 1.0; 
					$V=  1.0;
					$V=$V * (1-($ph/$maxPhase));
					$color_HSV=array('H'=>$H_array[$ph],'S'=>$S,'V'=>$V);
				}
				else if($meteor_type==2)
				{
					$r = ($rgb >> 16) & 0xFF;
					$g = ($rgb >> 8) & 0xFF;
					$b = $rgb & 0xFF;
					$color_HSV=RGB_TO_HSV ($r, $g, $b);
					$color_HSV['V']=$color_HSV['V'] * (1-($ph/$maxPhase));
				}
				else if($meteor_type==3)
				{
					$color_HSV=$random_color_HSV;
					$color_HSV['V']=$color_HSV['V'] * (1-($ph/$maxPhase));
				}
				/*else
				{
					if($meteor_array[$imeteor]['sparkle']>0)
					{
						$start_color2=$meteor_array[$imeteor]['sparkle'];
						$rgb = $start_color;
						$r = ($rgb >> 16) & 0xFF;
						$g = ($rgb >> 8) & 0xFF;
						$b = $rgb & 0xFF;
						$HSL=RGB_TO_HSV ($r, $g, $b);
						$end_color2;
						$color_HSV=color_picker($ph,$maxPhase,$maxFrame,$start_color,$end_color);
					}
					else
					{
						$color_HSV=array('H'=>1,'S'=>1,'V'=>1);
					}
					}*/
				$H=$color_HSV['H'];
				$S=$color_HSV['S'];
				$V=$color_HSV['V'];
				$rgb_val=HSV_TO_RGB ($H, $S, $V);
				if($sparkles>0)
				{
					$frame1_rgb_val=$rgb_val;
					if($ph==0)
					{
						$rgb_val=sparkles($sparkles,$frame1_rgb_val); // if sparkles>0, then rgb_val will be changed.
					}
				}
				$new_p = $p-$ph; // backup the meteor trail
				if($new_p>=$minPixel and $new_p<=$maxPixel)
				{
					$tree_rgb[$s][$new_p]=$rgb_val; // and store all of the meteor info into the tree_rgb array
				}
			}
			$meteor_array[$im]['pixel']++;	//	 now advance this meteor head downward
		}
		/*
		for($s=1;$s<=$maxStrand;$s++)
			for($p=1;$p<=$maxPixel;$p++)
		{
			if($tree_rgb[$s][$p]>0)
			{
				if($batch==0) echo "$s $p " . ($tree_rgb[$s][$p] . "\n";
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
			if(in_array($s,$window_array)) // Is this strand in our window?, 
			{
				if($rgb_val <> 0)
					fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number));
			}
		}
		fclose($fh_dat[$frame]);
	}
	$amperage=array();
	$x_dat_base = $base . ".dat";
	if(!isset($show_frame)) $show_frame='N';
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame);
	if($batch==0) echo "</body>";
	if($batch==0) echo "</html>";
}
?>
