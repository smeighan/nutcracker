<?php
//

function f_pinwheel($get)
{
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	//
	if(!isset($get['fade_in']))   $get['fade_in']="0";
	if(!isset($get['fade_out']))  $get['fade_out']="0";
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	extract ($get);
	require_once("../conf/setup.php"); // override some apache caching.
	//   for ($i = 0; $i < ob_get_level(); $i++)
	{
		ob_end_flush();
	}
	//   ob_implicit_flush(1);
	require_once("../effects/read_file.php");
	//
	//
	if(!isset($batch)) $batch=0;
	//	
	audit($username,"f_pinwheel","$effect_name,$batch,$seq_duration");
	//
	$get['batch']=$batch;
	$member_id=get_member_id($username);
	$get['OBJECT_NAME']='pinwheel';
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
	//$dat_file_array=pinwheel($get);
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
	//
	extract ($get);
	//show_elapsed_time($script_start,"Creating  Effect, pinwheel class:");
	if($maxStrand<1)$maxStrand=1;
	$pixelPerStrand=$maxPixel/$maxStrand;
	//
	if(!isset($window_degrees)) $window_degrees=360;
	if($window_degrees<1) $window_degrees=360;
	//$deltaStrands= ($maxStrand/ $number_pinwheel);
	$line= 0;
	$rgb=255;
	$x_dat_base = $base . ".dat"; // for pinwheel we will use a dat filename starting "S_" and the tree model
	$dat_file_array=array();
	$seq_number=0;
	$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	//flush();
	//
	$f=1;
	$amperage=array();
	//
	//
	$maxFrames = $seq_duration*1000/$frame_delay;
	//	$maxFrames = $maxPixel;
	echo "<pre>$maxFrames = $seq_duration*1000/$frame_delay;</pre>\n";
	if($batch==0) echo "<pre>maxPixel=$maxPixel,maxStrand=$maxStrand,maxFrames = $maxFrames </pre>\n";
	//
	//	create the pinwheel array
	//
	/*if($batch==0)
	{
		echo "<pre>create_sparkles($sparkles,$maxStrand,$maxPixel);\n";
		print_r($sparkles_array);
		echo "</pre>\n";
	}
	*/
	//
	$degrees=0;
	$number_steps=18;
	$degrees_delta = 360/$number_steps;
	$k=3; // if odd then k= number of petals on rose, if even then k*2 = number of petals
	for ($f=1;$f<=$maxFrames;$f++)
	{
		$x_dat = $base . "_d_". $f . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$f] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$f];
		$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
		fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
		$pinwheel=create_pinwheel($f,$k,$degrees,$get,$arr); // for each frame rotate pinwheel display
		//if($f<=3) display_pinwheel($pinwheel,$maxStrand,$maxPixel);
		$degrees += $degrees_delta;
		for($s=1;$s<=$maxStrand;$s++)
		{
			for($p=1;$p<=$maxPixel;$p++)
			{
				$rgb_val=$pinwheel[$s][$p]; // really rotate
				//	$rgb_val=$pinwheel[$s][$p]; // this will make all images static, no rotation
				//if($batch==0) echo "<pre>rgb_val=pinwheel[s][p] $rgb_val=pinwheel[$s][$p];</pre>\n";
				$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
				$tree_rgb[$s][$p]=$rgb_val;
				//	$xyz=$tree_xyz[$s][$p];
				$seq_number++;
				//	$rgb_val=sparkles($sparkles,$f1_rgb_val); // if sparkles>0, then rgb_val will be changed.
				$hex=dechex($rgb_val);
				$string=$user_pixel=0;
				//	$sparkles_array[$s][$p]=$sparkles_array[$s][$p]+0;
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
	if($batch==0) elapsed_time($script_start);
}

function create_pinwheel($f,$k,$degrees,$get,$arr)
{
	/*
	Polar Rose
	The variable a represents the length of the petals of the rose.
	A polar rose is a famous mathematical curve that looks like a petaled flower, and that can be expressed as a simple polar equation,
	r(theta) = a * cos(k*theta + ))
		for any constant φ0 (including 0). If k is an integer, these equations will produce a k-petaled rose if k is odd, or a 2k-petaled rose if k is even. If k is rational but not an integer, a rose-like shape may form but with overlapping petals. Note that these equations never define a rose with 2, 6, 10, 14, etc. petals. The variable a represents the length of the petals of the rose.
	Archimedan Spiral
	Changing the parameter a will turn the spiral, while b controls the distance between the arms, which for a given spiral is always constant. The Archimedean spiral has two arms, one for theta > 0 and one for theta < 0.
	The two arms are smoothly connected at the pole. Taking the mirror image of one arm across the 90°/270° line will yield the other arm. 
	r(theta) = a + b * theta
	)
		*/
	extract ($get);
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
	//
	$get['maxStrand']=$maxStrand;
	$get['maxPixel']=$maxPixel;
	//echo "<pre>function create_pinwheel($f,$k,$degrees,$get,$arr). color1=$color1</pre>\n";
	//
	for($s=-$maxStrand;$s<=$maxStrand;$s++)
	{
		for($p=-$maxPixel;$p<=$maxPixel;$p++)
		{
			$pinwheel[$s][$p]=0;
		}
	}
	// rotating vert line
	$mode='polar';
	if ($mode=='vert_line')
	{
		$on_s = $f%$maxStrand;
		if($on_s==0) $on_s=$maxStrand;
		for($p=1;$p<=$maxPixel;$p++)
		{
			$pinwheel[$on_s][$p]=hexdec($color1);
		}
		return $pinwheel;
	}
	// sine wave
	if ($mode=='sine_wave')
	{
		$s = intval($maxStrand/2);
		$p= intval($maxPixel/2);
		$height=9;
		for($s=1;$s<=$maxStrand;$s++)
		{
			for($p=1;$p<=$maxPixel;$p+=30)
			{
				$degrees = ($s/$maxStrand*720);
				$r=deg2rad($degrees);
				$new_p = intval(sin($r)*$height +$f);
				for($l=1;$l<=5;$l++)
				{
					$p_loop = $new_p +$l -3; // 3 is halfway between 5
					$HSV=RGBVAL_TO_HSV(hexdec($color1));
					$H=$HSV['H'];
					$S=$HSV['S'];
					$V=$HSV['V'];
					if($l==1 or $l==5) $V=$V*.8;
					if($l==2 or $l==4) $V=$V*.3;
					$S=$s/$maxStrand;
					$rgb_val=HSV_TO_RGB ($H, $S, $V);
					if($p_loop>=1 and $p_loop<=$maxPixel) $pinwheel[$s][$p_loop]=$rgb_val;
				}
			}
		}
		return $pinwheel;
	}
	// circles
	if ($mode=='searchlight')
	{
		$max_radius=8;
		$half_maxStrand =intval($maxStrand/2);
		$falloff_ratio=1; // how fast edges fall offf. 1 = 100 fall off by radius edge, 0.5 = 50% by radius edge
		$falloff = intval($max_radius * $falloff_ratio);
		for($radius=1;$radius<=$max_radius;$radius++)
		{
			for($degrees=1;$degrees<=360;$degrees+=1)
			{
				$r=deg2rad($degrees);
				$H0=$S0=$V0=0;
				$new_s = intval(cos($r)*$radius )+$half_maxStrand;
				$new_p = intval(sin($r)*$radius) +$f;
				$rgb_val=hexdec($color1);
				if($rgb_val>0)
				{
					$HSV=RGBVAL_TO_HSV($rgb_val);
					$H=$HSV['H'];
					$S=$HSV['S'];
					$V=$HSV['V'];
					/*if($l==1 or $l==5) $V=$V*.8;
					if($l==2 or $l==4) $V=$V*.3;
					$S=$s/$maxStrand;*/
					$V= $V*($max_radius-$radius-1)/$falloff;
					//	if($V<.05) $V=.05;
					//$H=$radius/10;
					$rgb_val=HSV_TO_RGB ($H, $S, $V);
					if($new_p>=1 and $new_p<=$maxPixel
					and $new_s>=1 and $new_s<=$maxStrand)
					{
						$pinwheel[$new_s][$new_p]=$rgb_val;
					}
					$V0=$V;
					$S0=$S;
					$H0=$H;
				}
				//
				//	2nd circle
				$new_s = intval(cos($r)*$radius )+$f;
				$new_p = intval(sin($r)*$radius) +$max_radius ;
				$HSV=RGBVAL_TO_HSV(hexdec("#FFFF00"));
				$H=$HSV['H'];
				$S=$HSV['S'];
				$V=$HSV['V'];
				//if($H0>0) $H=($H+$H0)/2;
				/*if($l==1 or $l==5) $V=$V*.8;
				if($l==2 or $l==4) $V=$V*.3;
				$S=$s/$maxStrand;*/
				$V= $V*($max_radius-$radius-1)/$falloff;
				if($V<.05) $V=.05;
				//$H=$radius/10;
				$rgb_val=HSV_TO_RGB ($H, $S, $V);
				if($new_p>=1 and $new_p<=$maxPixel
				and $new_s>=1 and $new_s<=$maxStrand
				and  $H0<=0.01 and $S0<=0.01 and $V0<=0.01)
				{
					$pinwheel[$new_s][$new_p]=$rgb_val;
				}
				else
				{
					echo "<pre>H0,S0,Vo = $H0,$S0,$V0</pre>\n";
				}
			}
		}
		return $pinwheel;
	}
	//
	//
	if ($mode=='polar')
	{
		$k=1;
		$xc= intval($maxStrand/2);
		$yc= intval($maxPixel/2);
		if($xc>$yc)
			$MAX_RADIUS=$xc; // use whatever dimension is longer to make sure we fill whole RGB target
		else
		$MAX_RADIUS=$yc;
		$theta_offset=($f-1)*10;
		$radius=0;
		$steps=360;
		$radius_delta =2.5*$MAX_RADIUS/$steps;
		$theta_delta = 360/$steps;
		$theta_offset=($f-1)*18;
		$arms=$number_arms;
		$sign_direction =1;
		if($direction=='ccw') $sign_direction =-1;
		for ($t=1;$t<=$steps;$t++)
		{
			if($straight=='y' or $straight=='Y')
			{
				$theta =  $theta_offset*$sign_direction;
			}
			else 
			{	
				$theta = ($t-1)*$theta_delta + $theta_offset*$sign_direction;
			}
			$theta *= $speed;
			for($a=1;$a<=$arms;$a++)
			{
				$degree_offset=intval(($a-1)*(360/$arms));
				if($a==1) $color=$color1;
				if($a==2) $color=$color2;
				if($a==3) $color=$color3;
				if($a==4) $color=$color4;
				if($a==5) $color=$color5;
				if($a==6)$color=$color6;
				//
				/*if($a%2==1)
				{
					$color=hexdec("#00FFFF");
				}
				else 
				{
					$color=hexdec("#0000FF");
				}
				*/
				$t_ratio = $t/$steps;
				if($lines>=1) $pinwheel=set_spiragraph($pinwheel,$get,$theta+$degree_offset,$radius+0,
				$fade1,$k,$xc,$yc,$color,$t_ratio);
				if($lines>=2) $pinwheel=set_spiragraph($pinwheel,$get,$theta+$degree_offset,$radius-1,
				$fade23,$k,$xc,$yc,$color,$t_ratio);
				if($lines>=3) $pinwheel=set_spiragraph($pinwheel,$get,$theta+$degree_offset,$radius+1,
				$fade23,$k,$xc,$yc,$color,$t_ratio);
				if($lines>=4) $pinwheel=set_spiragraph($pinwheel,$get,$theta+$degree_offset,$radius-2,
				$fade45,$k,$xc,$yc,$color,$t_ratio);
				if($lines>=5) $pinwheel=set_spiragraph($pinwheel,$get,$theta+$degree_offset,$radius+2,
				$fade45,$k,$xc,$yc,$color,$t_ratio);
			}
			//
			$radius+=$radius_delta*$arm_spacing;
		}
		if($f<=3)	echo "</table>\n";
		return $pinwheel;
	}
}

function set_spiragraph($pinwheel,$get,$theta,$radius,$fade_percentage,$k,$xc,$yc,$color,$t_ratio)
{
	//
	extract ($get);
	$minStrand =$arr[0];  // lowest strand seen on target
	$minPixel  =$arr[1];  // lowest pixel seen on skeleton
	$maxStrand =$arr[2];  // highest strand seen on target
	$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
	$maxI      =$arr[4];  // maximum number of pixels in target
	$rgb_val=hexdec($color);
	//	if($fade_percentage<100)
	{
		// lets fade code down by changing the brightness value V
		$HSV=RGBVAL_TO_HSV($rgb_val);
		$H=$HSV['H'];
		if($rainbow_hue=='y' or $rainbow_hue=='Y')	$H = $H + $t_ratio;
		if($H>1) $H-=1;
		$S=$HSV['S'];
		$V=$HSV['V'];
		$V= $V*($fade_percentage/100);
		//	if($V<.05) $V=.05;
		//$H=$radius/10;
		$rgb_val=HSV_TO_RGB ($H, $S, $V);
	}
	$radian = deg2rad($k*$theta);
	//	$val=$radius * cos($k*$theta_radian); // Polar Rose
	//  $val=$a * $b*$theta;  // Archimedan Spiral
	$x = $radius * cos($radian);	
	$y = $radius * sin($radian);
	$s = intval($x + $xc);			
	$p = intval($y + $yc);
	if($s>=1 and $s<=$maxStrand 
	and $p>= 1 and $p <= $maxPixel) // only set values if we fell within the valid ranges
	{
		//	get current value of HSV in this cell
		$HSV=RGBVAL_TO_HSV($pinwheel[$s][$p]);
		$H0=$HSV['H'];
		$S0=$HSV['S'];
		$V0=$HSV['V'];
		if($V>=$V0) // if we are brighter, then store value
		{
			$pinwheel[$s][$p]=$rgb_val;
		}
	}
	return $pinwheel;
	//
}

function 	display_pinwheel($pinwheel,$maxStrand,$maxPixel)
{
	/*if($batch==0) echo "<pre>";
	print_r($pinwheel);
	echo "</pre>\n";*/
	echo "<h3>Image of pinwheel before it gets replicated and rotated</h3>";
	echo "<table border=1>";
	for($p=1;$p<=$maxPixel;$p++)
	{
		echo "<tr><td>P$p</td>";
		for($s=1;$s<=$maxStrand;$s++)
		{
			$rgb_val=$pinwheel[$s][$p];
			if($s==0 and  $p==0) $rgb_val=hexdec("#FFFFFF");
			if($s==10 and  $p==10) $rgb_val=hexdec("#FFFF00");
			$hex= '#'.str_pad(strtoupper(dechex($rgb_val)), 6, '0', STR_PAD_LEFT);
			echo "<td bgcolor=\"$hex\">&nbsp;</td>";
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
}
