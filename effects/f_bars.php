<?php

function f_bars($get)
{
list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	//
	if(!isset($get['color3']))    $get['color3']="#FFFFFF";
	if(!isset($get['color4']))    $get['color4']="#FFFFFF";
	if(!isset($get['color5']))    $get['color5']="#FFFFFF";
	if(!isset($get['color6']))    $get['color6']="#FFFFFF";
	if(!isset($get['direction'])) $get['direction']="down";
	if(!isset($get['sparkle_type'])) $get['sparkle_type']="S";
	if(!isset($get['sparkles'])) $get['sparkles']="0";
	if(!isset($get['bar_type'])) $get['bar_type']="H";
	if(!isset($get['fade_3d']))   $get['fade_3d']="N";
	if(!isset($get['fade_in']))   $get['fade_in']="0";
	if(!isset($get['fade_out']))  $get['fade_out']="0";
	if(!isset($get['highlight'])) $get['highlight']="N";
	if(!isset($get['speed']))     $get['speed']="1";
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	extract ($get);
	$bar_type = strtoupper($bar_type);
	$get['bar_type']=$bar_type;
	$sparkle_type = strtoupper($sparkle_type);
	$get['sparkle_type']=$sparkle_type;
	require_once("../conf/setup.php"); // override some apache caching.
	require_once("../effects/read_file.php");
	//
	//
	$member_id=get_member_id($username);
	set_time_limit(0);
	if(!isset($batch)) $batch=0;
	audit($username,"f_bars","$effect_name,$batch,$seq_duration");
	if($batch==0) show_array($get,"$effect_class Effect Settings");
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
	//
	$path="../targets/". $member_id;

	$t_dat = $user_target . ".dat";
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
	$s_pixel   =$arr[9];
	$member_id=get_member_id($username);
	$path ="../effects/workspaces/" . $member_id;
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
	$maxFrame=20;
	$maxFrame=intval(($seq_duration*1000/$frame_delay)/$speed)+1;
	$seq_number=0;
	$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	// $tree_rgb[$s][$p]=$rgb_val;
	//$number_bars=10;
	$bar_width = intval($maxPixel/$number_bars)+1;
	$bar_width_p = intval($maxPixel/$number_bars)+1;
	$bar_width_s = intval($maxStrand/$number_bars)+1;
	if($bar_width==0) $bar_width=1;
	if($bar_width_p==0) $bar_width=1;
	if($bar_width_s==0) $bar_width=1;
	if($batch==0) echo "<pre>bar_width=$bar_width,  number_bars=$number_bars</pre>\n";
	$direction=strtolower($direction);	// make sure commands are lower case
	if($bar_type=='V')
	{
		if($direction<>'right') $direction='left';
	}
	else
	{
	}
	echo "<pre>FFFFFF=" . hexdec("#FFFFFF") . "</pre>\n";
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
				$p_new=$p;
				$s_new=$s;
				//$amperage[$f][$s] += $V*0.060; // assume 29ma for pixels tobe full on
				$string=$user_pixel=0;
				if ($bar_type=='V')
				{
					$bar_n =intval( $s/$bar_width_s);
				}
				else
				{
					$bar_n =intval( $p/$bar_width_p);
				}
				$rgb_val=hexdec("FFFFFF");
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
				if(!isset($direction)) $direction='down';
				if($direction=='up')
				{
					$n=$p-$f_offset;
					$pixel_ratio = ($bar_width-($p%$bar_width_p))/$bar_width; // down
				}
				else if($direction=='down')
				{
					$n=$p+$f_offset;
					$pixel_ratio = (($p%$bar_width))/$bar_width_p; // up
				}
				else if($direction=='left')
				{
					$n=$s-$f_offset;
					$pixel_ratio = ($bar_width_s-($s%$bar_width_s))/$bar_width_s; // down
				}
				else if($direction=='right')
				{
					$n=$s+$f_offset;
					$pixel_ratio = (($s%$bar_width_s))/$bar_width_s; // up
				}
				else if($direction=='compress')
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
				else if($direction=='expand')
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
				$highlight='n';
				if(strtoupper($highlight)=='Y' and $p%$bar_width==0) $S=0.0;
				$pixel_ratio += 0.1;
				if($pixel_ratio>1.0) $pixel_ratio=1.0;
				srand();
				$random_100 = intval(mt_rand(1,100));
				if($sparkle_type=='R') // Rainbow sparkles?
				{ // yes
					$rgb_val = mt_rand(1,16777215); // randomly select any of 16 million colors
					if($sparkles>=$random_100)
						$HSV=RGBVAL_TO_HSV($rgb_val); // replace our previous color wirh rainbow color
				}
				$H=$HSV['H']; $S=$HSV['S'];  $V=$HSV['V'];
				if($fade_3d=='Y' or $fade_3d=='y') $V=$V*$pixel_ratio;
				//echo "<pre>           H,S,V = $H,$S,$V</pre>\n";
				if($sparkle_type=='S')
				{
					//echo "<pre>sparkles,random_100=$sparkles,$random_100</pre>\n";
					if($sparkles>=$random_100)
					{
						$V=mt_rand($V,1.0); // random sprakles using same hue
						//echo "<pre>New V =$V</pre>\n";
					}
				}
				//
				if($sparkle_type=='W')
				{
					if($sparkles>=$random_100)
						if(mt_rand(1,100)>50) $S=0.0; // add white sparkles
				}
				$rgb_val = HSV_TO_RGB ($H, $S, $V);
				//$rgb_val = mt_rand(1,16777215);
				$seq_number++;
				if ($bar_type=='V')
				{
					if($n<1) $n+=$maxStrand;
					if($n<1) $n+=$maxStrand;
					if($n<1) $n+=$maxStrand;
					if($n<1) $n+=$maxStrand;
					$m=$n%$maxStrand;
					if($m==0)
					{
						$s_new=$maxStrand;
					}
					else
					{
						$s_new = $m;
					}
					if($s_new<0) $s_new+=$maxStrand;
					$xyz=$tree_xyz[$s_new][$p]; // get x,y,z location from the model.
					$p_new=$p;
				}
				else
				{
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
					$s_new=$s;
					$xyz=$tree_xyz[$s][$p_new]; // get x,y,z location from the model.
				}
				$hex=dechex($rgb_val);
				//echo "<pre>f,s,p = $f,$s,$p (p_new=$p_new, n=$n mod=$m, $maxPixel). H,S,V = $H,$S,$V $hex</pre>\n";
				fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",
				$s_new,$p_new,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel
				,$s_pixel[$s][$p_new][0],$s_pixel[$s][$p_new][1],
				$f,$seq_number));
			}
		}
		fclose($fh_dat [$f]);
	}
	$x_dat_base=$base . ".dat";
	$show_frame='n';
	$amperage=array();
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out); 
		
	if($batch==0) elapsed_time($script_start);
	return;
}

function get_color($bar_n,$s,$p)
{
	$rgb_val=hexdec("FFFFFF");
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
	$rgb_val = HSV_TO_RGB ($H, $S, $V);
}
