<?php

function f_tree($get)
{
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	//
	if(!isset($get['fade_in']))   $get['fade_in']="0";
	if(!isset($get['fade_out']))  $get['fade_out']="0";
	if(!isset($get['speed']))     $get['speed']="1";
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	extract ($get);
	require_once("../conf/setup.php"); // override some apache caching.
	require_once("../effects/read_file.php");
	//
	//
	echo "<pre>";
	print_r($_SERVER);
	echo "</pre>\n";
	$member_id=get_member_id($username);
	set_time_limit(0);
	if(!isset($batch)) $batch=0;
	//
	audit($username,"f_tree","$effect_name,$batch,$seq_duration");
	//
	if(!isset($number_garlands)) $number_garlands=2;
	$get['number_garlands']=$number_garlands;
	if($batch==0) show_array($get,"$effect_class Effect Settings");
	$get['OBJECT_NAME']='tree';
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
	$get['maxStrand']=$maxStrand;
	$get['maxPixel']=$maxPixel;
	if (file_exists($directory))
	{
		} else {
		if($batch==0) echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$x_dat = $user_target . "~" . $effect_name . ".dat";
	//$number_branches=5; now passed in
	$pixels_per_branch=intval($maxPixel/$number_branches);
	$maxFrame=($number_branches+1) *$maxStrand;
	$seq_number=0;
	$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	// $tree_rgb[$s][$p]=$rgb_val;
	//$number_tree=10;
	$string=$user_pixel=0;
	$number_frames_per_blink =intval($blink_speed / $frame_delay);
	if($number_frames_per_blink<1) $number_frames_per_blink=1;
	$twinkle_array=create_twinkle($get,$arr,$number_frames_per_blink);
	$twinkle=$twinkle_array[0];
	$twinkle_counter=$twinkle_array[1];
	$sparkles_array = create_sparkles($sparkles,$maxStrand,$maxPixel);
	$two_blinks = 2*$number_frames_per_blink;
	//
	$color1_tree_rgb = hexdec($color1_tree);
	$color2_tree_rgb = hexdec($color2_tree);
	//
	echo "<pre>number_branches,pixels_per_branch = $number_branches,$pixels_per_branch</pre>\n";
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
				$HSV=RGBVAL_TO_HSV($color1_tree_rgb);
				$H=$HSV['H'];
				$S=1.0;
				$mod=$p%$pixels_per_branch;
				if($mod==0) $mod=$pixels_per_branch;
				$V=($mod/$pixels_per_branch)*.7;
				$rgb_val = HSV_TO_RGB ($H, $S, $V); // set tree
				//$rgb_val = mt_rand(1,16777215);
				$seq_number++;
				$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
				if(!isset($create_garlands)) $create_garlands='N';
				if($create_garlands=='Y' or $create_garlands=='y')
				{
					$rgb_val = get_garland($rgb_val,$get,$s,$p,$f,$mod,
					$number_branches,$pixels_per_branch,$number_garlands);
				}
					if(!isset($create_wash)) $create_wash='N';
				if(($create_wash=='Y' or $create_wash=='y') and $p<=$f)
				{
					$HSV=RGBVAL_TO_HSV($color2_tree_rgb);
					$H=$HSV['H'];
					$S=1.0;
					$mod=$p%$pixels_per_branch;
					if($mod==0) $mod=$pixels_per_branch;
					$V=($mod/$pixels_per_branch)*.7;
					$rgb_val = HSV_TO_RGB ($H, $S, $V); // set tree
				}
				$twinkle_rgb_val=$twinkle[$s][$p]; // really rotate
				//	$rgb_val=sparkles($sparkles,$f1_rgb_val); // if sparkles>0, then rgb_val will be changed.
				$twinkle_counter[$s][$p]++;
				$n=$twinkle_counter[$s][$p]%$two_blinks;
				if($n>$number_frames_per_blink) $twinkle_rgb_val=0;
				srand();
				$random_100 = intval(mt_rand(1,100));
				$sparkle_type='W';
				if($twinkle_rgb_val>0)
				{
					if($sparkle_type=='W')
					{
						/*$ms200 = 500/$frame_delay;
						$ms200_segment = ($f-1)/$ms200;
						$s_p=($ms200_segment)%2;*/
						if($sparkles>=$random_100 )
						{
							$rval=rand(64,255);
							$r=$g=$b=$rval;
							$rgb_val =hexdec(fromRGB($r,$g,$b));
						}
					}
					else
					{
						$rgb_val=$twinkle_rgb_val;
					}
				}
				fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",
				$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel
				,$s_pixel[$s][$p][0],$s_pixel[$s][$p][1],
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

function get_garland($rgb_val,$get,$s,$p,$f,$mod,$number_branches,$pixels_per_branch,$number_garlands)
{
	extract ($get);
	$color_array = array(hexdec("#FF0000"), // RED
	hexdec("#0000FF"), // BLUE
	hexdec("#FFFF00"), // YELLOW
	hexdec("#FF00FF"), // PURPLE
	hexdec("#00FFFF")); // GREEN
	$color_array = array(hexdec($color1), // RED
	hexdec($color2), // BLUE
	hexdec($color3), // YELLOW
	hexdec($color4), // PURPLE
	hexdec($color5)); // GREEN
	$orig_rgbval=$rgb_val;
	$branch = intval(($p-1)/$pixels_per_branch);
	$row = $pixels_per_branch-$mod; // now row=0 is bottom of branch, row=1 is one above bottom
	// mod = which pixel we are in the branch
	//	mod=1,row=pixels_per_branch-1   top picrl in branch
	//	mod=2, second pixel down into branch
	//	mod=pixels_per_branch,row=0  last pixel in branch
	//
	//	row = 0, the $p is in the bottom row of tree
	//	row =1, the $p is in second row from bottom
	$b = intval (($f-1)/$maxStrand); // what branch we are on based on frame #
	//
	//	b = 0, we are on bottomow row of tree during frames 1 to maxStrand
	//	b = 1, we are on second row from bottom, frames = maxStrand+1 to 2*maxStrand
	//	b = 2, we are on third row from bottome, frames - 2*maxStrand+1 to 3*maxStrand
	$f_mod = $f%$maxStrand;
	if($f_mod==0) $f_mod=$maxStrand;
	//	f_mod is 1 to maxStrrand on each row
	//	f_mod == 1, left strand of this row
	//	f_mod==maxStrand, right strand of this row
	//
	$m=($s%6);
	if($m==0) $m=6;  // use $m to indicate where we are in horizontal pattern
	// m=1, 1sr strand
	// m=2, 2nd strand
	// m=6, last strand in 6 strand pattern
	$r = mt_rand(0,4);
	$r=$branch%5;
	$color = $color_array[$r];
	$odd_even=$b%2;
	$s_odd_row = $maxStrand-$s+1;
	$f_mod_odd = $maxStrand-$f_mod+1;
	if($branch>$b) return $orig_rgbval; // for branches aboce current, dont string a garland.
	if(($row==3 or ($number_garlands==2 and $row==6)) and ($m==1 or $m==6))
	{
		$rgb_val = $color;
		$rgb_val=leading_edge($rgb_val,$f_mod,$s , $branch,$b , $odd_even ,$s_odd_row,$sparkles);
	}
	if(($row==2 or ($number_garlands==2 and $row==5)) and ($m==2 or $m==5))
	{
		$rgb_val = $color;
		$rgb_val=leading_edge($rgb_val,$f_mod,$s , $branch,$b , $odd_even ,$s_odd_row,$sparkles);
	}
	if(($row==1 or ($number_garlands==2 and $row==4)) and ($m==3 or $m==4))
	{
		$rgb_val = $color;
		$rgb_val=leading_edge($rgb_val,$f_mod,$s , $branch,$b , $odd_even ,$s_odd_row,$sparkles);
	}
	if($branch>$b)
	{
		return $rgb_val; // for branches below current, dont dont balnk anything out
	}
	else if($branch==$b)
	{
		if($odd_even ==0 and $s>$f_mod)
		{
			$rgb_val=$orig_rgbval;// we are even row ,counting from bottom as zero
		}
		if($odd_even ==1 and $s_odd_row>$f_mod)
		{
			$rgb_val=$orig_rgbval;// we are even row ,counting from bottom as zero
		}
	}
	//if($branch>$b) $rgb_val=$orig_rgbval; // erase rows above our current row.
	return $rgb_val;
}

function leading_edge($rgb_val,$f_mod,$s , $branch,$b , $odd_even ,$s_odd_row,$sparkles)
{
	if($f_mod==$s and $branch==$b and $odd_even ==0) $rgb_val=hexdec("#FFFFFF");
	if($s_odd_row==$f_mod and $branch==$b and $odd_even ==1) $rgb_val=hexdec("#FFFFFF");
	if($sparkles>0) if(mt_rand(1,100) < $sparkles)  $rgb_val=hexdec("#FFFFFF");
	return $rgb_val;
}
