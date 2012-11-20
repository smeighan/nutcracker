<?php
//
// (23:11:11) oldmanfathertime1000: http://www.youtube.com/watch?v=j​N2fhFSmSP4

function f_spirals($get)
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
	$get['OBJECT_NAME']='spirals';
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
	/*//spiral($arr,$path,$t_dat,$number_spirals,$number_rotations,$spiral_thickness,$base,
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
	if($color3 == null or !isset($color3)) $color3="#FFFFFF";
	if($color4 == null or !isset($color4)) $color4="#FFFFFF";
	if($color5 == null or !isset($color5)) $color5="#FFFFFF";
	if($color6 == null or !isset($color6)) $color6="#FFFFFF";
	if($fade_3d == null or !isset($fade_3d)) $fade_3d="N";
	if($rainbow_hue == null or !isset($rainbow_hue)) $rainbow_hue="Y";
	if($speed == null or !isset($speed)) $speed=0.5;
	//
	$direction=strtolower($direction);
	$handiness=strtoupper($handiness);
	$fade_3d=strtoupper($fade_3d);
	$rainbow_hue=strtoupper($rainbow_hue);
	$get['rainbow_hue']=$rainbow_hue;
	$get['handiness']=$handiness;
	$get['direction']=$direction;
	$get['fade_3d']=$fade_3d;
	extract ($get);
	//show_elapsed_time($script_start,"Creating  Effect, spirals class:");
	if($maxStrand<1)$maxStrand=1;
	$pixelPerStrand=$maxPixel/$maxStrand;
	//
	if(!isset($window_degrees)) $window_degrees=360;
	if($window_degrees<1) $window_degrees=360;
	if( $number_spirals<1)  $number_spirals=1;
	$deltaStrands= ($maxStrand* (360/$window_degrees)/ $number_spirals);
	//$deltaStrands= ($maxStrand/ $number_spirals);
	$line= 0;
	$rgb=255;
	$x_dat_base = $base . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
	$dat_file_array=array();
	$r=115;
	$g =115;
	$b = 120; 
	//if($number_rotations<1) $number_rotations=1;
	$maxLoop = ($maxStrand* (360/$window_degrees)*$number_rotations);
	if($maxLoop<1) $maxLoop=1;
	if($batch==0)  echo "<pre>deltaStrands=$deltaStrands,maxLoop=$maxLoop</pre>\n";
	//$maxLoop = ($maxStrand*$number_rotations) * ($window_degrees/360);
	$deltaPixel = $maxPixel/$maxLoop;
	$S=$V=1;
	$deltaH = (RED - ORANGE)/$maxLoop;
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
	$maxFrames = $maxStrand;
	$maxFrames = intval($maxStrand/$speed)*360/$window_degrees+1;
	if($batch==0) echo "<pre>maxPixel=$maxPixel,maxStrand=$maxStrand,maxFrames = $maxFrames </pre>\n";
	//
	//	create the SPIRAL array
	//
	$spiral=create_spiral($get,$arr);
	$sparkles_array = create_sparkles($sparkles,$maxStrand,$maxPixel);
	/*if($batch==0) echo "<pre>strand_pixel\n";
	print_r($sparkles_array);
	echo "</pre>\n";*/
	foreach($sparkles_array as $s=>$sarray)
		foreach($sarray as $p=>$value)
	{
		//if($batch==0) echo "<pre>s,p=$s,$p= $value</pre>\n";
		if($spiral[$s][$p]>0) $spiral[$s][$p]= hexdec("#FEFEFE"); // for any non black cell, set it to flag as for sparkles
	}
	if($batch==0) display_spiral($spiral,$maxStrand,$maxPixel);
	for ($f=1;$f<=$maxFrames;$f++)
	{
		$x_dat = $base . "_d_". $f . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$f] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$f];
		$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
		fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
		//if($batch==0) echo "<pre>f=$f; deltaStrands=$deltaStrands for( ns= minStrand; ns<= number_spirals; ns++) = for( $ns= $minStrand; $ns<= $number_spirals; $ns++)</pre>\n";
		for($s=1;$s<=$maxStrand;$s++)
		{
			if($direction=="ccw")
			{
				$new_s = $s-intval(($f-1)*$speed); // CCW
			}
			else
			{
				$new_s = $s+intval(($f-1)*$speed); // CW
			}
			for($p=1;$p<=$maxPixel;$p++)
			{
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
				//
				//
				//
				//	$new_s = $s;
				//	$s=$new_s;
				$rgb_val=$spiral[$new_s][$p]; // really rotate
				//	$rgb_val=$spiral[$s][$p]; // this will make all images static, no rotation
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
	//if($batch==0) show_elapsed_time($script_start,"Finished  Effect, spirals class:");
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

function create_spiral($get,$arr)
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
	if($color3 == null or !isset($color3)) $color3="#FFFFFF";
	if($color4 == null or !isset($color4)) $color4="#FFFFFF";
	if($color5 == null or !isset($color5)) $color5="#FFFFFF";
	if($color6 == null or !isset($color6)) $color6="#FFFFFF";
	if($fade_3d == null or !isset($fade_3d)) $fade_3d="N";
	if($rainbow_hue == null or !isset($rainbow_hue)) $rainbow_hue="Y";
	if($speed == null or !isset($speed)) $speed=0.5;
	//
	$get['maxStrand']=$maxStrand;
	$get['maxPixel']=$maxPixel;
	$direction=strtolower($direction);
	$fade_3d=strtoupper($fade_3d);
	//
	//
	for($s=1;$s<=$maxStrand;$s++)
	{
		for($p=1;$p<=$maxPixel;$p++)
		{
			$spiral[$s][$p]=0;
		}
	}
	$f=1;
	$line=$seq_number=0;
	$deltaStrands= intval($maxStrand* ($window_degrees/360)/ $number_spirals); // oldway
	$deltaStrands= intval($maxStrand/ $number_spirals);
	//
	$ns1=array(1,2);
	$ns2=array(9,10);
	$color1=hexdec("#FF0000");
	$color2=hexdec("#00FF00");
	$color3=hexdec("#FFFF00");
	$maxStrandLoops = ($maxStrand* (360/$window_degrees)*$number_rotations);
	$maxStrandLoops = ($maxStrand* (360/$window_degrees));
	if($maxStrandLoops<0) $maxStrandLoops=1;
	$maxLoop=$maxStrandLoops/$maxStrand;
	if($maxLoop<1) $maxLoop=1;
	/*echo "<pre>";
	print_r($get);
	echo "</pre>";*/
	$loop=0;
	$ns=intval(($s-1)/($maxStrand/$number_spirals))+1;
	//echo "<pre>ns=intval((s-1)/(maxStrand/number_spirals))+1</pre<\n";
	//echo "<pre>$ns=intval(($s-1)/($maxStrand/$number_spirals))+1</pre>\n"; // figure out which spiral segment we are
	for($l=1;$l<=$maxLoop;$l++)
	{
		for($s=1;$s<=$maxStrand;$s++)
		{
			$loop++;
			if($loop<=$maxStrandLoops)
			{
				for($p=1;$p<=$maxPixel;$p++)
				{
					/*$p_offset = intval(($p-0)*$number_rotations);
					if($direction=="ccw")
					{
						$new_s = $strand+intval(($f-1)*$speed)+$p_offset; // CCW
					}
					else
					{
						$new_s = $strand-intval(($f-1)*$speed)-$p_offset; // CW
						}*/
					$s_offset_L = $s + ($maxStrand*($p-1)/$maxPixel)*$number_rotations;
					$s_offset_R = $s - ($maxStrand*($p-1)/$maxPixel)*$number_rotations;
					$check=0;
					while ($s_offset_L>$maxStrand and $check<100)
					{
						$check++;
						if($s_offset_L>$maxStrand) $s_offset_L-=$maxStrand;
					}
					//
					$check=0;
					while ($s_offset_R>$maxStrand and $check<100)
					{
						$check++;
						if($s_offset_R>$maxStrand) $s_offset_R-=$maxStrand;
					}
					//
					$check=0;
					while ($s_offset_L<1 and $check<100)
					{
						$check++;
						if($s_offset_L<1) $s_offset_L+=$maxStrand;
					}
					//
					$check=0;
					while ($s_offset_R<1 and $check<100)
					{
						$check++;
						if($s_offset_R<1) $s_offset_R+=$maxStrand;
					}
					//
					$color = find_color($loop,$s,$p,$get);
					$ns1 = make_array_segments($s,$p,$get);
					/*if($batch==0)
					{
						echo "<pre>l=$l loop=$loop, s=$s p=$p color=$color\n";
						print_r($ns1);
						echo "</pre>\n";
					}
					*/
					if(in_array($s,$ns1))
					{
						if($number_rotations<0.001)
						{
							$spiral[$s][$p]=$color;
						}
						else
						{
							if($handiness=="L" or $handiness=="B") $spiral[$s_offset_L][$p]=$color;
							if($handiness=="R" or $handiness=="B") $spiral[$s_offset_R][$p]=$color;
						}
					}
					//	if($batch==0) echo "<pre>spiral[s][p]=rgb_val; = spiral[$s][$p]=$rgb_val;</pre>\n";
				}
			}
		}
	}
	return $spiral;
}

function make_array_segments($s,$p,$get)
{
	extract ($get);
	$segment_width =intval( $maxStrand/$number_spirals);
	$ns1=array();
	for($ns=1;$ns<=$number_spirals;$ns++)
	{
		$s1 = ($ns-1)*$segment_width + 1;
		$s2 = $s1 + $spiral_thickness -1;
		if($s>=$s1 and $s<=$s2)
		{
			for ($s=$s1;$s<=$s2;$s++)
			{
				$ns1[]=$s;
			}
		}
	}
	return $ns1;
}

function find_color($loop,$s,$p,$get)
{
	extract ($get);
	$ns=intval(($s-1)/($maxStrand/$number_spirals))+0; // figure out which spiral segment we are
	$y=intval($maxStrand/$number_spirals);
	$ns=1 + intval(($s-1) / $y);
	$thick = intval(($s-1)%($maxStrand/$number_spirals));
	if($ns==0) $ns=1;
	if($rainbow_hue<>'N')
	{
		$color_HSV=color_picker($p,$maxPixel,$number_spirals,$color1,$color2);
		$H=$color_HSV['H'];
		$S=$color_HSV['S'];
		$V=$color_HSV['V'];
		//		if($batch==0) echo "<pre>$strand,$p start,end=$start_color,$end_color  HSV=$H,$S,$V</pre>\n";
	}
	else
	{
		$mod = $ns%6;
		// we want the last color to be next in line from color palete if we are dealing with
		// number of spirals <= 6
		//if($ns==$number_spirals and $mod==0 and $number_spirals<6) $mod=$number_spirals;
		switch ($mod)
		{
			case 1:
			$rgb_val=hexdec($color1);
			break;
			case 2:
			$rgb_val=hexdec($color2);
			break;
			case 3:
			$rgb_val=hexdec($color3);
			break;
			case 4:
			$rgb_val=hexdec($color4);
			break;
			case 5:
			$rgb_val=hexdec($color5);
			break;
			case 0:
			$rgb_val=hexdec($color6);
			break;
		}
		if($mod==0) $mod=6;
		$HSL= RGBVAL_TO_HSV($rgb_val);
		//RGBVAL_TO_HSV($rgb_val);
		$H=$HSL['H']; 
		$S=$HSL['S']; 
		$V=$HSL['V'];
		$hex=dechex($rgb_val);
	}
	if($fade_3d=='Y')
	{
		if($direction=='ccw')
		{
			$mod_ratio=$thick/$spiral_thickness;
		}
		else
		{
			$mod_ratio=($spiral_thickness-($thick-1))/$spiral_thickness;
		}
		$V=$V*$mod_ratio;
	}
	$rgb_val=HSV_TO_RGB ($H, $S, $V);
	$hex=dechex($rgb_val);
	//if($batch==0) echo "<pre>loop=$loop,s=$s,p=$p,color=$hex,ns=$ns,thick=$thick,maxStrand=$maxStrand,/$number_spirals)/$s);</pre>\n";
	return $rgb_val;
}

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

function 	display_spiral($spiral,$maxStrand,$maxPixel)
{
	/*if($batch==0) echo "<pre>";
	print_r($spiral);
	echo "</pre>\n";*/
	echo "<h3>Image of spiral before it gets replicated and rotated</h3>";
	echo "<table border=1>";
	for($p=1;$p<=$maxPixel;$p++)
	{
		echo "<tr><td>P$p</td>";
		for($s=1;$s<=$maxStrand;$s++)
		{
			$rgb_val=$spiral[$s][$p];
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