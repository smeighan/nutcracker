<?php

function f_garlands($get)
{
	extract ($get);
	set_time_limit(0);
	ini_set("memory_limit","512M");
	require_once("../effects/read_file.php");
	//
	//
	$path="../targets/". $username;
	$member_id=get_member_id($username);
	$get['member_id']=$member_id;
	if($batch==0) show_array($get,"$effect_class Effect Settings");
	$path ="../effects/workspaces/$member_id";
	$directory=$path;
	if (file_exists($directory))
	{
		} else {
		if($batch==0) echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	//	garland($batch,$arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$sparkles,$seq_duration,$garland_gap,$garland,$show_frame,$username); 
	garland($get);
	$base =  $user_target . "~" . $effect_name;
	$t_dat = $user_target . ".dat";
	$xdat =  $user_target ."~".  $effect_name . ".dat";
	$path="../targets/". $member_id;
	$target_info=get_info_target($username,$t_dat);
	if($batch==0)
	{
		show_array($target_info,'MODEL: ' . $t_dat);
	}
	//
	$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out); 
	//if($batch==0) echo "<pre>$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration); </pre>\n";
}

function garland($get)
{
	extract ($get);
	$base = $user_target . "~" . $effect_name;
	$t_dat = $user_target . ".dat";
	$xdat = $user_target ."~".  $effect_name . ".dat";
	$path="../targets/". $member_id;
	$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
	$path="../effects/workspaces/". $member_id;
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
	$seq_number=0;
	$line= 0;
	$maxFrames = $maxPixel*$garland_gap+10;
	//$maxFrames = $maxPixel*4.0;
	//if($maxFrames>100) $maxFrames=100;
	$object_id=-1;
	$max=$maxPixel;
	$p=1;
	$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	for( $f= 1; $f<= $maxFrames and $max>1; $f++)
	{
		if($f>5000) exit ("Too many frames in sequence");
		$x_dat = $base . "_d_". $f . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$f] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$f];
		$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
		fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
		if($f % $garland_gap==1)
		{
			$object_id++;
			$garland_object[$object_id]['pixel']=1;
			$numberSpirals=4;
			$color_HSV=color_picker($f,$maxFrames,$numberSpirals,$start_color,$end_color);
			$H=$color_HSV['H'];
			$S=$color_HSV['S'];
			$V=$color_HSV['V'];
			$rgb_val=HSV_TO_RGB ($H, $S, $V);
			$garland_object[$object_id]['rgb']=$rgb_val;
			$garland_object[$object_id]['object_id']=$object_id;
		}
		$arr=array($garland_object,$max);
		$arr=advance_all_objects($garland_object,$maxPixel,$max);
		$garland_object=$arr[0];
		$max=$arr[1];
		$cnt=count($garland_object);
		for($i=$cnt-1;$i>=0;$i--)
		{
			$p=$garland_object[$i]['pixel'];
			$rgb=$garland_object[$i]['rgb'];
			for($s=1;$s<=$maxStrand;$s++)
			{
				$p=$garland_object[$i]['pixel'];
				$rgb=$garland_object[$i]['rgb'];
				if($garland==1)
				{
					if($s%5==0 or $s%5==4) 
					;	// leave pixel alone;
					else if($s%5==1 or $s%5==3) 
					$p++;
					else  if($s%5==2)
						$p+=2;
				}
				if($garland==2)
				{
					if($s%5==0 or $s%5==4) 
					;	// leave pixel alone;
					else if($s%5==1 or $s%5==3) 
					$p+=2;
					else  if($s%5==2)
						$p+=4;
				}
				if($garland==3)
				{
					if($s%6==0) 
					;	// leave pixel alone;
					else  if($s%6==1 or $s%6==5)
						$p+=2;
					else  if($s%6==2 or $s%6==4)
						$p+=4;
					else  if($s%6==3)
						$p+=6;
				}
				if($garland==4)
				{
					if($s%5==0 or $s%5==4 or $s%5==2) 
					;	// leave pixel alone;
					else if($s%5==1 or $s%5==3) 
					$p+=2;
				}
				if($p>$maxPixel) $p=$maxPixel;
				$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
				if(in_array($s,$window_array))
				{
					$rgb_val=$rgb;
					$rgb_val=sparkles($sparkles,$rgb); // if sparkles>0, then rgb_val will be changed.	
					$seq_number++;
					//fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val));
					$string=$user_pixel=0;
					// debug, just to mark strand 1 as white. if($s==1) $rgb_val =hexdec("#FFFFFF");
					fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$f,$seq_number));
					/*
					if($garland==4)
					{
						$rgb_white = 16777215;
						$p++;
						if($p>$maxPixel) $p=$maxPixel;
						fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_white));
					}
					*/
				}
				//fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb,$string, $user_pixel,$strand_pixel[$strand][$p][0],$strand_pixel[$strand][$p][1],$frame,$seq_number));
				//		printf ("<pre>%3d-%3d t1 %4d %4d %9.3f %9.3f %9.3f %d</pre>\n",$line,$l,$strand,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val);
			}
		}
		fclose($fh_dat[$f]);
	}
	$amperage=array();
	$x_dat_base = $base . ".dat";
	make_gp($batch,$arr_orig,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame);
	if($batch==0) echo "</body>";
	if($batch==0) echo "</html>";
}

function advance_all_objects($garland_object,$maxPixel,$max)
{
	/*
	for($i=$cnt;$i>=1;$i--)
	{
		$p=$garland_object[$i]['pixel'];
		$rgb=$garland_object[$i]['rgb'];
		printf("advance_all_objects: $i   frame %d  p,rgb=%d,%d\n",$f,$p,$rgb);
	}
	*/
	$color=0;
	$cnt=count($garland_object);
	for($i=0;$i<$cnt;$i++)
	{
		$p=$garland_object[$i]['pixel'];
		$rgb=$garland_object[$i]['rgb'];
		if($p<$max or $rgb==0)
		{
			$old_rgb=	$garland_object[$i]['rgb'];
			$old_object_id =$garland_object[$i]['object_id'];
			$p++;
			$garland_object[$i]['pixel']=$p;
			//
			//$garland_object[$i]['rgb']=$old_rgb;
			//$garland_object[$i]['object_id']=$old_object_id;
			if($p==$max)
			{
				//if($batch==0) echo "<pre>p=$p max=$max p==max</pre>\n";
				$garland_object[$i]['pixel']=$p;
				if($rgb==0) $garland_object[$i]['rgb']=255;
				$max--;
				if($max<1) $max=1;
			}
		}
	}
	$arr=array($garland_object,$max);
	return($arr);
}
?>
