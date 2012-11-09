<?php
//
// (23:11:11) oldmanfathertime1000: http://www.youtube.com/watch?v=j​N2fhFSmSP4

function f_snowflakes($get)
{
	if(!isset($get['background_color'])) $get['background_color']="#000000";
	if(!isset($get['snowflake_type']))   $get['snowflake_type']="1";
	if(!isset($get['window_degrees']))   $get['window_degrees']=360;
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
	$get['OBJECT_NAME']='snowflakes';
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
	SpiralgetFilesFromDir($dir,$base);
	/*//spiral($arr,$path,$t_dat,$number_snowflakes,$number_rotations,$snowflake_thickness,$base,
	$color1,$color2,$color3,$color4,$color5,$color6,$rainbow_hue,$fade_3d,$speed,
	$direction,$f_delay,$sparkles,$window_degrees,$use_background,$background_color,$handiness,$username,$seq_duration,$show_frame,$effect_type,$sparkles_count); */
	//
	//
	//$dat_file_array=spiral($get);
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
	//show_elapsed_time($script_start,"Creating  Effect, snowflakes class:");
	if($maxStrand<1)$maxStrand=1;
	$pixelPerStrand=$maxPixel/$maxStrand;
	//
	if(!isset($window_degrees)) $window_degrees=360;
	if($window_degrees<1) $window_degrees=360;
	//$deltaStrands= ($maxStrand/ $number_snowflakes);
	$line= 0;
	$rgb=255;
	$x_dat_base = $base . ".dat"; // for snowflakes we will use a dat filename starting "S_" and the tree model
	$dat_file_array=array();
	$r=115;
	$g =115;
	$b = 120; 
	//if($number_rotations<1) $number_rotations=1;
	//$maxLoop = ($maxStrand*$number_rotations) * ($window_degrees/360);
	$S=$V=1;
	$H=RED;
	$lowRange1 = $minStrand;
	$lowRange2 = $maxStrand/4;
	$highRange1=$maxStrand -  $maxStrand/4;
	$highRange2=$maxStrand;
	$seq_number=0;
	$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	//flush();
	//
	$f=1;
	$amperage=array();
	//
	//
	/*	$speed = $maxStrand/60;
	echo "<pre>speed=$speed</pre>\n";*/
	$maxFrames = $maxStrand;
	if($speed<.001) $speed=1;
	$get['speed']=$speed;
	$maxFrames = intval($maxStrand/$speed)*360/$window_degrees+1;
	$maxFrames=intval(($seq_duration*1000/$frame_delay))+1;
	if($batch==0) echo "<pre>maxPixel=$maxPixel,maxStrand=$maxStrand,maxFrames = $maxFrames </pre>\n";
	//
	//	create the SPIRAL array
	//
	$snowflake=create_snowflakes($get,$arr);
	$sparkles_array = create_sparkles($sparkles,$maxStrand,$maxPixel);
	/*if($batch==0) echo "<pre>strand_pixel\n";
	print_r($sparkles_array);
	echo "</pre>\n";*/
	foreach($sparkles_array as $s=>$sarray)
		foreach($sarray as $p=>$value)
	{
		//if($batch==0) echo "<pre>s,p=$s,$p= $value</pre>\n";
		if($snowflake[$s][$p]>0) $snowflake[$s][$p]= hexdec("#FEFEFE"); // for any non black cell, set it to flag as for sparkles
	}
	if($batch==0) display_snowflakes($snowflake,$maxStrand,$maxPixel);
	for ($f=1;$f<=$maxFrames;$f++)
	{
		$x_dat = $base . "_d_". $f . ".dat"; // for snowflakes we will use a dat filename starting "S_" and the tree model
		$dat_file[$f] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$f];
		$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
		fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
		//if($batch==0) echo "<pre>f=$f; deltaStrands=$deltaStrands for( ns= minStrand; ns<= number_snowflakes; ns++) = for( $ns= $minStrand; $ns<= $number_snowflakes; $ns++)</pre>\n";
		for($s=1;$s<=$maxStrand;$s++)
		{
			$speed2=$speed/2;
			$offset=intval(($f-1)*$speed);;
			$new_s = $s+intval(($f-1)*$speed2); // CW
			$new_s2 = $s-intval(($f-1)*$speed2); // CCW
			for($p=1;$p<=$maxPixel;$p++)
			{
				$new_p = $p-intval(($f-1)*$speed); 
				$new_p2 = $p-intval(($f-1)*$speed)-$maxPixel/2; // offset by half of pixel height
				//	if($batch==0) echo "<pre> f,s,p=$f,$s,$p  ns,thick=$ns,$thick.  new_s=$new_s</pre>\n";
				$check=0;
				while ($new_s>$maxStrand and $check<100)
				{
					$check++;
					if($new_s>$maxStrand) $new_s-=$maxStrand;
				}
				//
				$check=0;
				while ($new_s<1 and $check<100)
				{
					$check++;
					if($new_s<1) $new_s+=$maxStrand;
				}
				while ($new_p>$maxPixel and $check<100)
				{
					$check++;
					if($new_p>$maxPixel) $new_p-=$maxPixel;
				}
				//
				$check=0;
				while ($new_p<1 and $check<100)
				{
					$check++;
					if($new_p<1) $new_p+=$maxPixel;
				}
				//
				while ($new_s2>$maxStrand and $check<100)
				{
					$check++;
					if($new_s2>$maxStrand) $new_s2-=$maxStrand;
				}
				//
				$check=0;
				while ($new_s2<1 and $check<100)
				{
					$check++;
					if($new_s2<1) $new_s2+=$maxStrand;
				}
				while ($new_p2>$maxPixel and $check<100)
				{
					$check++;
					if($new_p2>$maxPixel) $new_p2-=$maxPixel;
				}
				//
				$check=0;
				while ($new_p2<1 and $check<100)
				{
					$check++;
					if($new_p2<1) $new_p2+=$maxPixel;
				}
				//
				//
				//	$new_s = $s;
				//	$s=$new_s;
				$rgb_val=$snowflake[$new_s][$new_p]; // really rotate
				if($rgb_val==0)	$rgb_val=$snowflake[$new_s2][$new_p2]; // really rotate
				if($rgb_val==0) // if no color had been assigned , then set new color
				{
					if($background_color!="#FFFFFF") $rgb_val=hexdec($background_color);
				}
				//	$rgb_val=$snowflake[$s][$p]; // this will make all images static, no rotation
				//if($batch==0) echo "<pre>rgb_val=spiral[s][p] $rgb_val=spiral[$s][$p];</pre>\n";
				$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
				$tree_rgb[$s][$p]=$rgb_val;
				//	$xyz=$tree_xyz[$s][$p];
				$seq_number++;
				//	$rgb_val=sparkles($sparkles,$f1_rgb_val); // if sparkles>0, then rgb_val will be changed.
				$hex=dechex($rgb_val);
				if($rgb_val == hexdec("#FEFEFE"))
				{
					/*$sparkles_array[$s][$p]++;
					$rgb_val=calculate_sparkle($s,$p,
					$sparkles_array[$s][$p],
					$rgb_val,$sparkles_count);*/
					$rval=rand(01,255);
					$r=$g=$b=$rval;
					$rgb_val =hexdec(fromRGB($r,$g,$b));
				}
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
	//if($batch==0) show_elapsed_time($script_start,"Finished  Effect, snowflakes class:");
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
/*snowflakes Effect Settings
username	f
user_target	A
effect_class	snowflakes
effect_name	SNOWFLAKES2
window_degrees	180
color1	#0D15FF
color2	#FFFFFF
frame_delay	100
sparkles	0
speed	1
background_color	#3A7EA6
seq_duration	5
fade_in	0
fade_out	0
submit	Submit Form to create your effect
OBJECT_NAME	snowflakes
batch	0
color3	#FFFFFF
color4	#FFFFFF
color5	#FFFFFF
color6	#FFFFFF
direction	down
$use_background	N
fade_3d	N
rainbow_hue	N
handiness	R
show_frame	N*/

function create_snowflakes($get,$arr)
{
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
	//
	//
	for($s=-$maxStrand;$s<=2*$maxStrand;$s++)
	{
		for($p=-$maxPixel;$p<=2*$maxPixel;$p++)
		{
			$snowflake[$s][$p]=0;
		}
	}
	$f=1;
	$line=$seq_number=0;
	// Pick the color that surrounds the center of your snowflake
	// Pick the color for the center of your snowflake
	//$snowflake_type=1;
	$random_flakes=0;
	if($snowflake_type==0)		$random_flakes=1;
	//$color = find_color($s,$p,$get);
	//$colour_model=2;
	if(!isset($number_snowflakes))	$number_snowflakes=$maxPixel/4;
	if(!isset($colour_model)) $colour_model=1;
	//srand();
	$random= mt_rand(1,1000);
	for($n=1;$n<=$number_snowflakes;$n++)
	{
		switch ($colour_model)
		{
			case 1:
			$color1_rgb=hexdec($color1);
			$color2_rgb=hexdec($color2);
			break;
			//
			case 2:
			$color1_rgb=hexdec($color1);
			$color2_rgb=hexdec($color2);
			$HSV1 = RGBVAL_TO_HSV($color1_rgb);
			$HSV2 = RGBVAL_TO_HSV($color2_rgb);
			$H = $HSV1['H'] + ($HSV1['H'] - $HSV2['H']) * ($n/$number_snowflakes);
			while ($H<0) $H+=1.0;
			$S =  $HSV1['S'] + ($HSV1['S'] - $HSV2['S']) * ($n/$number_snowflakes);
			while ($S<0) $S+=1.0;
			$V = $HSV1['V'] + ($HSV1['V'] - $HSV2['V']) * ($n/$number_snowflakes);
			while ($V<0) $V+=1.0;
			$color1_rgb=$color2_rgb= HSV_TO_RGB ($H, $S, $V);
			break;
			//
			case 3:
			$color1_rgb=hexdec($color1);
			$color2_rgb=hexdec($color2);
			break;
			//
		}
		$check=0;
		$rgb=0;
		$delta_p=intval($maxPixel/4);
		$mod=$n%4;
		if($mod==0) $mod=4;
		switch ($mod)
		{
			case 1:
			$p1=1;
			$p2=$delta_p;
			break;
			//
			case 2:
			$p1=1*$delta_p;
			$p2=2*$delta_p;
			break;
			//
			case 3:
			$p1=2*$delta_p;
			$p2=3*$delta_p;
			break;
			//
			case 4:
			$p1=3*$delta_p;
			$p2=$maxPixel;
			break;
			//
		}
		while ($rgb==0 and $check<20)
		{
			$s=intval(mt_rand(1,$maxStrand));
			$p=intval(mt_rand($p1,$p2));
			$rgb=$snowflake[$s][$p];
			$check++;
		}
		if($random_flakes==1) $snowflake_type=intval(mt_rand(1,6));
		//echo "<pre>n= $n  snowflake_type=$snowflake_type mod=$mod  p1=$p1 p2=$p2 s,p=$s,$p</pre>\n";
		switch($snowflake_type)
		{
			case 1:
			$snowflake[$s][$p]=$color1_rgb;
			break;
			//
			case 2:
			if($s<2) $s+=2;
			if($s>$maxStrand-2) $s-=2;
			if($p<2) $p+=2;
			if($p>$maxPixel-2) $p-=2;
			$snowflake[$s][$p]=$color1_rgb;
			$snowflake[$s][$p-1]=$color2_rgb;
			$snowflake[$s][$p+1]=$color2_rgb;
			$snowflake[$s+1][$p]=$color2_rgb;
			$snowflake[$s-1][$p]=$color2_rgb;
			break;
			//
			case 3:
			if($s<2) $s+=2;
			if($s>$maxStrand-2) $s-=2;
			if($p<2) $p+=2;
			if($p>$maxPixel-2) $p-=2;
			$snowflake[$s][$p]=$color1_rgb;
			if(mt_rand(1,100)>50)
			{
				$snowflake[$s][$p-1]=$color2_rgb;
				$snowflake[$s][$p+1]=$color2_rgb;
			}
			else
			{
				$snowflake[$s+1][$p]=$color2_rgb;
				$snowflake[$s-1][$p]=$color2_rgb;
			}
			break;
			//
			case 4:
			if($s<3) $s+=3;
			if($s>$maxStrand-3) $s-=3;
			if($p<3) $p+=3;
			if($p>$maxPixel-3) $p-=3;
			$snowflake[$s][$p]=$color1_rgb;
			$snowflake[$s][$p-1]=$color2_rgb;
			$snowflake[$s][$p-2]=$color2_rgb;
			$snowflake[$s][$p+1]=$color2_rgb;
			$snowflake[$s][$p+2]=$color2_rgb;
			$snowflake[$s+1][$p]=$color2_rgb;
			$snowflake[$s+2][$p]=$color2_rgb;
			$snowflake[$s-1][$p]=$color2_rgb;
			$snowflake[$s-2][$p]=$color2_rgb;
			break;
			//
			case 5:
			if($s<4) $s+=4;
			if($s>$maxStrand-4) $s-=4;
			if($p<4) $p+=4;
			if($p>$maxPixel-4) $p-=4;
			$snowflake[$s][$p]=$color1_rgb;
			$snowflake[$s][$p-1]=$color2_rgb;
			$snowflake[$s][$p-2]=$color2_rgb;
			$snowflake[$s-1][$p-3]=$color2_rgb;
			$snowflake[$s][$p-3]=$color2_rgb;
			$snowflake[$s+1][$p-3]=$color2_rgb;
			//
			$snowflake[$s][$p+1]=$color2_rgb;
			$snowflake[$s][$p+2]=$color2_rgb;
			$snowflake[$s][$p+3]=$color2_rgb;
			$snowflake[$s-1][$p+3]=$color2_rgb;
			$snowflake[$s+1][$p+3]=$color2_rgb;
			//
			$snowflake[$s+1][$p]=$color2_rgb;
			$snowflake[$s+2][$p]=$color2_rgb;
			$snowflake[$s+3][$p]=$color2_rgb;
			$snowflake[$s+3][$p-1]=$color2_rgb;
			$snowflake[$s+3][$p+1]=$color2_rgb;
			//
			$snowflake[$s-1][$p]=$color2_rgb;
			$snowflake[$s-2][$p]=$color2_rgb;
			$snowflake[$s-3][$p]=$color2_rgb;
			$snowflake[$s-3][$p-1]=$color2_rgb;
			$snowflake[$s-3][$p+1]=$color2_rgb;
			break;
			//
			case 6:
			if($s<6) $s+=6;
			if($s>$maxStrand-6) $s-=6;
			if($p<6) $p+=6;
			if($p>$maxPixel-6) $p-=6;
			$snowflake[$s][$p]=$color1_rgb;
			$snowflake[$s][$p-1]=$color2_rgb;
			$snowflake[$s][$p-2]=$color2_rgb;
			$snowflake[$s-1][$p-3]=$color2_rgb;
			$snowflake[$s][$p-3]=$color2_rgb;
			$snowflake[$s+1][$p-3]=$color2_rgb;
			$snowflake[$s][$p-4]=$color2_rgb;
			$snowflake[$s][$p-5]=$color2_rgb;
			$snowflake[$s-1][$p-5]=$color2_rgb;
			$snowflake[$s+1][$p-5]=$color2_rgb;
			$snowflake[$s-2][$p-5]=$color2_rgb;
			$snowflake[$s+2][$p-5]=$color2_rgb;
			//
			$snowflake[$s][$p+1]=$color2_rgb;
			$snowflake[$s][$p+2]=$color2_rgb;
			$snowflake[$s][$p+3]=$color2_rgb;
			$snowflake[$s-1][$p+3]=$color2_rgb;
			$snowflake[$s+1][$p+3]=$color2_rgb;
			$snowflake[$s][$p+4]=$color2_rgb;
			$snowflake[$s][$p+5]=$color2_rgb;
			$snowflake[$s-1][$p+5]=$color2_rgb;
			$snowflake[$s+1][$p+5]=$color2_rgb;
			$snowflake[$s-2][$p+5]=$color2_rgb;
			$snowflake[$s+2][$p+5]=$color2_rgb;
			//
			$snowflake[$s+1][$p]=$color2_rgb;
			$snowflake[$s+2][$p]=$color2_rgb;
			$snowflake[$s+3][$p]=$color2_rgb;
			$snowflake[$s+3][$p-1]=$color2_rgb;
			$snowflake[$s+3][$p+1]=$color2_rgb;
			$snowflake[$s+4][$p]=$color2_rgb;
			$snowflake[$s+5][$p]=$color2_rgb;
			$snowflake[$s+5][$p-1]=$color2_rgb;
			$snowflake[$s+5][$p+1]=$color2_rgb;
			$snowflake[$s+5][$p-2]=$color2_rgb;
			$snowflake[$s+5][$p+2]=$color2_rgb;
			//
			$snowflake[$s-1][$p]=$color2_rgb;
			$snowflake[$s-2][$p]=$color2_rgb;
			$snowflake[$s-3][$p]=$color2_rgb;
			$snowflake[$s-3][$p-1]=$color2_rgb;
			$snowflake[$s-3][$p+1]=$color2_rgb;
			$snowflake[$s-4][$p]=$color2_rgb;
			$snowflake[$s-5][$p]=$color2_rgb;
			$snowflake[$s-5][$p-1]=$color2_rgb;
			$snowflake[$s-5][$p+1]=$color2_rgb;
			$snowflake[$s-5][$p-2]=$color2_rgb;
			$snowflake[$s-5][$p+2]=$color2_rgb;
			break;
		}
		//	if($batch==0) echo "<pre>spiral[s][p]=rgb_val; = spiral[$s][$p]=$rgb_val;</pre>\n";
	}
	return $snowflake;
}
/*

function bgr2rgb($cr)
{
	// bidirectional
	return (($cr & 0xFF0000) << 16 | ($cr & 0x00FF00) | ($cr & 0x0000FF) >> 16);
}

function hex2cr($hex)
{
	// strips any leading characters, like #
	return bgr2rgb(hexdec($hex));
}

function cr2hex($cr)
{
	// the usual HTML format, #rrggbb
	return '#'.str_pad(strtoupper(dechex(bgr2rgb($cr))), 6, '0', STR_PAD_LEFT);
}
*/

function 	display_snowflakes($snowflake,$maxStrand,$maxPixel)
{
	/*if($batch==0) echo "<pre>";
	print_r($snowflake);
	echo "</pre>\n";*/
	echo "<h3>Image of snowflakes before it gets replicated and rotated</h3>";
	echo "<table border=1>";
	for($p=1;$p<=$maxPixel;$p++)
	{
		echo "<tr><td>P$p</td>";
		for($s=1;$s<=$maxStrand;$s++)
		{
			$rgb_val=$snowflake[$s][$p];
			$hex= '#'.str_pad(strtoupper(dechex($rgb_val)), 6, '0', STR_PAD_LEFT);
			echo "<td bgcolor=\"$hex\">&nbsp;</td>";
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
}

function SpiralgetFilesFromDir($dir,$base)
{
	//if($batch==0) echo "<pre>getFilesFromDir($dir,$base)</pre>\n";
	$files = glob("$dir/2/$base*"); // get all file names
	foreach($files as $file)
	{
		// iterate files
		if(is_file($file))
		{
			$tok=explode(".",$file);
			//if($batch==0) echo "<pre>file=$file $tok[1]</pre>\n";
			if($tok[1]=="dat")
				unlink($file); // delete file if a dat file
		}
	}
}