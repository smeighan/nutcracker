<?php
//
// (23:11:11) oldmanfathertime1000: http://www.youtube.com/watch?v=j​N2fhFSmSP4

function f_twinkle($get)
{
	if(!isset($get['color3']))    $get['color3']="#FFFFFF";
	if(!isset($get['color4']))    $get['color4']="#FFFFFF";
	if(!isset($get['color5']))    $get['color5']="#FFFFFF";
	if(!isset($get['color6']))    $get['color6']="#FFFFFF";
	if(!isset($get['direction'])) $get['direction']="down";
	if(!isset($get['$use_background'])) $get['$use_background']="N";
	if(!isset($get['background_color'])) $get['background_color']="#FFFFFF";
	if(!isset($get['fade_3d']))   $get['fade_3d']="N";
	if(!isset($get['rainbow_hue']))   $get['rainbow_hue']="N";
	if(!isset($get['handiness']))   $get['handiness']="R";
	if(!isset($get['fade_in']))   $get['fade_in']="0";
	if(!isset($get['fade_out']))  $get['fade_out']="0";
	if(!isset($get['sparkles']))  $get['sparkles']="0";
	if(!isset($get['speed']))     $get['speed']="1";
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	extract ($get);
	set_time_limit(0);
	ini_set("memory_limit","1024M");
	require_once("../effects/read_file.php");
	//
	//
	if(!isset($batch)) $batch=0;
	$get['batch']=$batch;
	$member_id=get_member_id($username);
	$get['OBJECT_NAME']='twinkle';
	$effect_name = strtoupper($effect_name);
	$effect_name = rtrim($effect_name);
	$username=str_replace("%20"," ",$username);
	$effect_name=str_replace("%20"," ",$effect_name);
	$get['effect_name']=$effect_name;
	$get['username']=$username;
	if(!isset($show_frame)) $show_frame='N';
	$get['show_frame']=$show_frame;
	$f_delay = $get['frame_delay'];
	$f_delay = intval((5+$f_delay)/10)*10; // frame frame delay to nearest 10ms number_format
	$get['frame_delay']=$f_delay;
	if($batch==0) show_array($get,"$effect_class Effect Settings");
	$path="../targets/". $member_id;
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	$t_dat = $user_target . ".dat";
	$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
	$member_id=get_member_id($username);
	$path ="../effects/workspaces/" . $member_id;
	$directory=$path;
	if (file_exists($directory))
	{
		} else {
		if($batch==0) echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$x_dat = $user_target . "~" . $effect_name . ".dat";
	$base = $user_target . "~" . $effect_name;
	$get['arr']=$arr;
	$get['x_dat']=$x_dat;
	$get['t_dat']=$t_dat;
	$get['base']=$base;
	$get['path']=$path;
	$get['f_delay']=$f_delay;
	$dir="workspaces";
	//
	//
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
	if($color3 == null or !isset($color3)) $color3="#FFFFFF";
	if($color4 == null or !isset($color4)) $color4="#FFFFFF";
	if($color5 == null or !isset($color5)) $color5="#FFFFFF";
	if($color6 == null or !isset($color6)) $color6="#FFFFFF";
	if($speed == null or !isset($speed)) $speed=0.5;
	//
	extract ($get);
	//show_elapsed_time($script_start,"Creating  Effect, twinkle class:");
	if($maxStrand<1)$maxStrand=1;
	$pixelPerStrand=$maxPixel/$maxStrand;
	//
	$number_frames_per_blink =intval($blink_speed / $frame_delay);
	if($number_frames_per_blink<1) $number_frames_per_blink=1;
	if(!isset($window_degrees)) $window_degrees=360;
	if($window_degrees<1) $window_degrees=360;
	//$deltaStrands= ($maxStrand/ $number_twinkle);
	$line= 0;
	$rgb=255;
	$x_dat_base = $base . ".dat"; // for twinkle we will use a dat filename starting "S_" and the tree model
	$dat_file_array=array();
	$r=115;
	$g =115;
	$b = 120; 
	//if($number_rotations<1) $number_rotations=1;
	$seq_number=0;
	$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	//flush();
	//
	$f=1;
	$amperage=array();
	//
	//
	$maxFrames = $maxStrand;
	$maxFrames = intval($maxStrand/$speed)*360/$window_degrees+1;
	if($batch==0) echo "<pre>maxPixel=$maxPixel,maxStrand=$maxStrand,maxFrames = $maxFrames </pre>\n";
	//
	//	create the twinkle array
	//
	$twinkle_array=create_twinkle($get,$arr,$number_frames_per_blink);
	$twinkle=$twinkle_array[0];
	$twinkle_counter=$twinkle_array[1];
	$sparkles_array = create_sparkles($sparkles,$maxStrand,$maxPixel);
	$two_blinks = 2*$number_frames_per_blink;
	echo "<pre>number_frames_per_blink=$number_frames_per_blink</pre>\n";
	for ($f=1;$f<=$maxFrames;$f++)
	{
		$x_dat = $base . "_d_". $f . ".dat"; // for twinkle we will use a dat filename starting "S_" and the tree model
		$dat_file[$f] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$f];
		$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
		fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
		//if($batch==0) echo "<pre>f=$f; deltaStrands=$deltaStrands for( ns= minStrand; ns<= number_twinkle; ns++) = for( $ns= $minStrand; $ns<= $number_twinkle; $ns++)</pre>\n";
		for($s=1;$s<=$maxStrand;$s++)
		{
			for($p=1;$p<=$maxPixel;$p++)
			{
				$rgb_val=$twinkle[$s][$p]; // really rotate
				$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
				$seq_number++;
				//	$rgb_val=sparkles($sparkles,$f1_rgb_val); // if sparkles>0, then rgb_val will be changed.
				$twinkle_counter[$s][$p]++;
				
				$n=$twinkle_counter[$s][$p]%$two_blinks;
				if($n>$number_frames_per_blink) $rgb_val=0;
				$string=$user_pixel=0;
				//	$sparkles_array[$s][$p]=$sparkles_array[$s][$p]+0;
				if($fade_3d=='Y')
				{
				}
				if($s<=$maxStrand)
				{
					fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$f,$seq_number));
					//	printf ("<pre>f=%d t1 %4d(%4d) %4d %9.3f %9.3f %9.3f %d %d %d %d %d %d %d</pre>\n",$f,$s,$new_s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$new_s][$p][0],$strand_pixel[$new_s][$p][1],$f,$seq_number);
				}
			}
		}
		if (isset($fh_dat[$f]))
			fclose($fh_dat[$f]);
	}
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	//if($batch==0) show_elapsed_time($script_start,"Finished  Effect, twinkle class:");
	//
	//
	//
	//
	$target_info=get_info_target($username,$t_dat);
	//show_array($target_info,'MODEL: ' . $t_dat);
	if($batch==0) $description ="Total Elapsed time for this effect:";
	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
	//if($description = 'Total Elapsed time for this effect:')
		$x_dat_base=$base . ".dat";
	$show_frame='n';
	$amperage=array();
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out); 
	$description ="Total Elapsed time for this effect:";
	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
	if($batch==0)
	{
		printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
		if($batch==0) echo "<pre>Location: bc.php?batch=$batch</pre>\n";
	}
	/*$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out);
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame);
	if($batch==0) echo "<pre>make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame)</pre>\n";
	if($batch==0) echo "<pre>$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out);</pre>\n";
	if($batch==0) printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);*/
}




