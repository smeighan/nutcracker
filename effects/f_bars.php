<?php

function f_bars($get)
{
	/*echo "<pre>";
	print_r($get);
	echo "</pre>\n";*/
	extract($get);
	require_once("read_file.php");
	$member_id=get_member_id($username);
	set_time_limit(0);
	if(!isset($batch)) $batch=0;
	if($batch==0) // 0 indicates interactive mode
	{
		echo "<h2>Nutcracker: RGB Effects Builder for user $username<br/>
		On this page you build an animation of the spiral class and create an animated GIF</h2>"; 
		show_array($get,"get");
	}
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
	$get['OBJECT_NAME']='bars';
	if(!isset($batch)) $batch=0;
	$get['batch']=$batch;
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
	extract ($get);
	save_user_effect($get);
	//
	if($batch==0) show_array($get,"Effect Settings");
	$path="../targets/". $member_id;
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	$t_dat = $user_target . ".dat";
	$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
	$member_id=get_member_id($username);
	$path ="workspaces/" . $member_id;
	$x_dat = $user_target . "+" . $effect_name . ".dat";
	$base = $user_target . "~" . $effect_name;
	$directory=$path;
	if (file_exists($directory))
	{
		} else {
		if($batch==0) echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$x_dat = $user_target . "~" . $effect_name . ".dat";
	$minStrand =$arr[0];  // lowest strand seen on target
	$minPixel  =$arr[1];  // lowest pixel seen on skeleton
	$maxStrand =$arr[2];  // highest strand seen on target
	$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
	$maxI      =$arr[4];  // maximum number of pixels in target
	$tree_rgb  =$arr[5];
	$tree_xyz  =$arr[6];
	$file      =$arr[7];
	$min_max   =$arr[8];
	$s_pixel   =$arr[9];
	$maxFrame=20;
	$maxFrame=intval(($seq_duration*1000/$frame_delay)/$speed)+1;
	$seq_number=0;
	$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	// $tree_rgb[$s][$p]=$rgb_val;
	//$number_bars=10;
	$bar_width = intval($maxPixel/$number_bars)+1;
	if($bar_width==0) $bar_width=1;
	if($batch==0) echo "<pre>bar_width=$bar_width,  number_bars=$number_bars</pre>\n";
	$direction=strtolower($direction);	// make sure commands are lower case
	for($f=1;$f<=$maxFrame;$f++)
	{
		$x_dat = $base . "_d_". $f . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$f] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$f];
		$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
		fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
		for ($s=1;$s<=$maxStrand;$s++)
		{
			for($p=1;$p<=$maxPixel;$p++)
			{
				if(in_array($s,$window_array)) // Is this strand in our window?, If yes, then we output lines to the dat file
				{
					//$amperage[$f][$s] += $V*0.060; // assume 29ma for pixels tobe full on
					$string=$user_pixel=0;
					$rgb_val=hexdec("FFFFFF");
					$bar_n =intval( $p/$bar_width);
					$bar_n=$bar_n%6;
					if($bar_n==0) $rgb_val=hexdec($color1);
					if($bar_n==1) $rgb_val=hexdec($color2);
					if($bar_n==2) $rgb_val=hexdec($color3);
					if($bar_n==3) $rgb_val=hexdec($color4);
					if($bar_n==4) $rgb_val=hexdec($color5);
					if($bar_n==5) $rgb_val=hexdec($color6);
					/*	if($bar_n==6) $rgb_val=hexdec("FF00FF");
					if($bar_n==7) $rgb_val=hexdec("FFF0FF");
					if($bar_n==8) $rgb_val=hexdec("FF0FFF");
					if($bar_n==9) $rgb_val=hexdec("FF00FF");
					if($bar_n==10) $rgb_val=hexdec("F0000F");*/
					//$rgb_val=hexdec("FFFFFF");
					$HSV=RGBVAL_TO_HSV($rgb_val);
					$H=$HSV['H']; $S=$HSV['S'];  $V=$HSV['V'];
					//$direction='expand';
					$f_offset = intval($f*$speed);
					if($direction=='up')
					{
						$n=$p-$f_offset;
						$pixel_ratio = ($bar_width-($p%$bar_width))/$bar_width; // down
					}
					if($direction=='down')
					{
						$n=$p+$f_offset;
						$pixel_ratio = (($p%$bar_width))/$bar_width; // up
					}
					if($direction=='compress')
					{
						$p2 = intval($maxPixel/2);
						if($p<=$p2)
						{
							$n=$p-$f_offset;
							$pixel_ratio = ($bar_width-($p%$bar_width))/$bar_width; // down	
						}
						else{
							$n=$p+$f_offset;
							$pixel_ratio = (($p%$bar_width))/$bar_width; // up
						}
					}
					if($direction=='expand')
					{
						$p2 = intval($maxPixel/2);
						if($p>$p2)
						{
							$n=$p-$f_offset;
							$pixel_ratio = ($bar_width-($p%$bar_width))/$bar_width; // down	
						}
						else{
							$n=$p+$f_offset;
							$pixel_ratio = (($p%$bar_width))/$bar_width; // up
						}
					}
					if($direction=='down')
					{
						$n=$p+$f_offset;
						$pixel_ratio = (($p%$bar_width))/$bar_width; // up
					}
					$highlight='n';
					if(strtoupper($highlight)=='Y' and $p%$bar_width==0) $S=0.0;
					$pixel_ratio += 0.1;
					if($pixel_ratio>1.0) $pixel_ratio=1.0;
					if($fade_3d=='Y' or $fade_3d=='y') $V=$V*$pixel_ratio;
					//echo "<pre>           H,S,V = $H,$S,$V</pre>\n";
					$rgb_val = HSV_TO_RGB ($H, $S, $V);
					$seq_number++;
					if($n<1) $n+=$maxPixel;
					if($n<1) $n+=$maxPixel;
					if($n<1) $n+=$maxPixel;
					if($n<1) $n+=$maxPixel;
					$m=$n%$maxPixel;
					if($m==0)
					{
						$p_new=$maxPixel;
					}
					else
					{
						$p_new = $m;
					}
					if($p_new<0) $p_new+=$maxPixel;
					$hex=dechex($rgb_val);
					//echo "<pre>f,s,p = $f,$s,$p (p_new=$p_new, n=$n mod=$m, $maxPixel). H,S,V = $H,$S,$V $hex</pre>\n";
					$xyz=$tree_xyz[$s][$p_new]; // get x,y,z location from the model.
					fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",
					$s,$p_new,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel
					,$s_pixel[$s][$p_new][0],$s_pixel[$s][$p_new][1],
					$f,$seq_number));
				}
			}
		}
	}
	$x_dat_base=$base . ".dat";
	$show_frame='n';
	$amperage=array();
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$script_start,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out); 
	$description ="Total Elapsed time for this effect:";
	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
	if($batch==0)
	{
		printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
		echo "<pre>Location: bc.php?batch=$batch</pre>\n";
	}
	return;
}
