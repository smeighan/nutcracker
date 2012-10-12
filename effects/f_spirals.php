<?php
//

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
	save_user_effect($get);
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
	/*//spiral($arr,$path,$t_dat,$number_spirals,$number_rotations,$spiral_thickness,$base,
	$color1,$color2,$color3,$color4,$color5,$color6,$rainbow_hue,$fade_3d,$speed,
	$direction,$f_delay,$sparkles,$window_degrees,$use_background,$background_color,$handiness,$username,$seq_duration,$show_frame,$effect_type,$sparkles_count); */
	spiral($get);
	$target_info=get_info_target($username,$t_dat);
	//show_array($target_info,'MODEL: ' . $t_dat);
	if($batch==0) $description ="Total Elapsed time for this effect:";
	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
	//if($description = 'Total Elapsed time for this effect:')
		if($batch==0) printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
	$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out);
}

function spiral($get)
{
	extract($get);
	echo "<pre>";
	/*$deltaStrands= $maxStrand/ $number_spirals;
	echo "deltaStrands= maxStrand/ number_spirals\n";
	echo "$deltaStrands= $maxStrand/ $number_spirals\n";*/
	//print_r($get);
	echo "</pre>";
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
	$fade_3d=strtoupper($fade_3d);
	$rainbow_hue=strtoupper($rainbow_hue);
	//show_elapsed_time($script_start,"Creating  Effect, spirals class:");
	if($maxStrand<1)$maxStrand=1;
	$pixelPerStrand=$maxPixel/$maxStrand;
	//if( $numberStrands<1)  $numberStrands=1;
	$deltaStrands= ($maxStrand* (360/$window_degrees)/ $number_spirals);
	//$deltaStrands= ($maxStrand/ $number_spirals);
	$line= 0;
	$rgb=255;
	$x_dat_base = $base . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
	$dat_file_array=array();
	$r=115;
	$g =115;
	$b = 120;
	$maxLoop = ($maxStrand* (360/$window_degrees)*$number_rotations);
	echo "<pre>deltaStrands=$deltaStrands,maxLoop=$maxLoop</pre>\n";
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
	$sparkles_array = create_sparkles($sparkles,$maxStrand,$maxPixel);
	//echo "<pre>sparkle_count=$sparkles_count\n";
	//print_r($sparkles_array);
	//echo "</pre>\n";
	//flush();
	//
	$f=1;
	$amperage=array();
	//
	//
	$maxFrames = $maxStrand;
	$maxFrames = intval($maxStrand/$speed)+1;
	for ($f=1;$f<=$maxFrames;$f++)
	{
		$x_dat = $base . "_d_". $f . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$f] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$f];
		$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
		fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
		for( $ns= $minStrand; $ns<= $number_spirals; $ns++)
		{
			$line++;
			$p_to_add=1;
			//	if($effect_type=='v' or $effect_type=='V') $p_to_add=0;
			if(strtoupper($handiness)=="R")
			{
				$strand_base=intval( ($ns-1)*$deltaStrands-$p_to_add);
			}
			else
			{
				$strand_base=intval( ($ns-1)*$deltaStrands+$p_to_add);
			}
			//	echo "<pre>,loop=$l, ns=$ns, strand_base=$strand_base</pre>\n";
			for($thick=1;$thick<=$spiral_thickness;$thick++)
			{
				if(strtoupper($handiness)=="R")
				{
					$strand = ($strand_base%$maxStrand)-$thick;
				}
				else
				{
					$strand = ($strand_base%$maxStrand)+$thick;
				}
				if ($strand < $minStrand) $strand += $maxStrand;
				if ($strand > $maxStrand) $strand -= $maxStrand;
				if($strand<1) $strand=1;
				//
				//
				//
				//
				for($p=1;$p<=$maxPixel;$p++)
				{
					if($rainbow_hue<>'N')
					{
						$color_HSV=color_picker($p,$maxPixel,$number_spirals,$color1,$color2);
						$H=$color_HSV['H'];
						$S=$color_HSV['S'];
						$V=$color_HSV['V'];
						//		echo "<pre>$strand,$p start,end=$start_color,$end_color  HSV=$H,$S,$V</pre>\n";
					}
					else
					{
						$mod = $ns%6;
						// we want the last color to be next in line from color palete if we are dealing with
						// number of spirals <= 6
						//if($ns==$number_spirals and $mod==0 and $number_spirals<6) $mod=$number_spirals;
						if($mod==0) $mod=6;
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
						$HSL= RGBVAL_TO_HSV($rgb_val);
						//RGBVAL_TO_HSV($rgb_val)
							$H=$HSL['H']; 
						$S=$HSL['S']; 
						$V=$HSL['V'];
						$hex=dechex($rgb_val);
						//	echo "<pre> ns=$ns, mod=$mod, rgbval=$rgb_val($hex), HSV=$H,$S,$V.  $mod = ($ns%$number_spirals)%6;</pre>\n";
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
					$f1_rgb_val=$rgb_val;
					//		$rgb_val=sparkles($sparkles,$f1_rgb_val); // if sparkles>0, then rgb_val will be changed.
					$p_offset = intval($p*$number_rotations);
					if($direction=="ccw")
					{
						$new_s = $strand+intval(($f-1)*$speed)+$p_offset; // CCW
					}
					else
					{
						$new_s = $strand-intval(($f-1)*$speed)-$p_offset; // CW
					}
					//	echo "<pre> f,s,p=$f,$strand,$p  ns,thick=$ns,$thick.  new_s=$new_s</pre>\n";
					if($new_s>$maxStrand) $new_s = $new_s%$maxStrand;
					if($new_s<$minStrand) $new_s = $new_s%$maxStrand;
					if($new_s==0) $new_s=$maxStrand;
					if($new_s<0) $new_s+=$maxStrand;
					//	$strand=$new_s;
					$xyz=$tree_xyz[$strand][$p]; // get x,y,z location from the model.
					//echo "<pre> f,s,p=$f,$strand,$p  ns,thick=$ns,$thick</pre>\n";
					$tree_rgb[$strand][$p]=$rgb_val;
					$seq_number++;
					if($rgb_val==0 and $use_background=='Y')
					{
						$rgb_val=hexdec($background_color);
						if($batch==0) echo "<pre>$rgb_val=hexdec($background_color);</pre>\n";
					}
					$xyz=$tree_xyz[$new_s][$p];
					$seq_number++;
					//	$rgb_val=sparkles($sparkles,$f1_rgb_val); // if sparkles>0, then rgb_val will be changed.
					$tree_rgb[$strand][$p]=$rgb_val;
					//if(in_array($new_s,$window_array)) // Is this strand in our window?, If yes, then we output lines to the dat file
					{
						if($rgb_val==0 and $use_background=='Y')
						{
							$rgb_val=hexdec($background_color);
						}
						if(isset($sparkles_array[$strand][$p])===false 
						or $sparkles_array[$strand][$p]==null )
							$x=0;
						else if($sparkles_array[$strand][$p]>1)
						{
							$sparkles_array[$strand][$p]++;
							$rgb_val=calculate_sparkle($strand,$p,
							$sparkles_array[$strand][$p],
							$rgb_val,$sparkles_count);
						}
						$string=$user_pixel=0;
						//	$sparkles_array[$strand][$p]=$sparkles_array[$strand][$p]+0;
						fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$strand,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$new_s][$p][0],$strand_pixel[$new_s][$p][1],$f,$seq_number));
					}
				}
			}
		}
	}
	// for ($f=1;$f<=$maxStrand;$f++) 
	for ($f=1;$f<=$maxStrand;$f++)
	{
		if (isset($fh_dat[$f]))
			fclose($fh_dat[$f]);
	}
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	//if($batch==0) show_elapsed_time($script_start,"Finished  Effect, spirals class:");
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame);
	//echo "<pre>make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame)</pre>\n";
}

function delete_effects($username,$model_name)
{
	//Include database connection details
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	$model_base_name = basename($model_name,".dat");
	$query = "delete from effects where username='$username' and object_name='$model_base_name'";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	mysql_close();
}

function insert_effects($username,$model_name,$strand,$pixel,$x,$y,$z,$rgb_val,$f,$seq_number)
{
	//echo "<pre> insert_effects($username,$model_name,$strand,$pixel,$x,$y,$z,$rgb_val)\n";
	//Include database connection details	
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	$model_base_name = basename($model_name,".dat");
	$x=$x+0.0;
	$y=$y+0.0;
	$z=$z+0.0;
	$query="insert into effects (seq_number,username,object_name,strand,pixel,x,y,z,rgb_val,frame) values
	($seq_number,'$username','$model_base_name',$strand,$pixel,$x,$y,$z,$rgb_val,$f)";
	//echo "<pre>insert_effects: query=$query</pre>\n";
	mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	mysql_close();
}
