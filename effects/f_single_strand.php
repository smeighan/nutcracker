<?php

function f_single_strand($get)
{
	if(!isset($get['direction'])) $get['direction']="right";
	if(!isset($get['fade_in']))   $get['fade_in']="0";
	if(!isset($get['fade_out']))  $get['fade_out']="0";
	if(!isset($get['speed']))     $get['speed']="1";
	extract ($get);
	if(isset($get['window_degrees'])) $wind=$get['window_degrees'];
	else $wind=360;
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$wind); // Set window_degrees to match the target
	/*echo "<pre>";
	print_r($get);
	echo "</pre>\n";*/
	set_time_limit(0);
	ini_set("memory_limit","1024M");
	require_once("../effects/read_file.php");
	//
	//
	$member_id=get_member_id($username);
	set_time_limit(0);
	if(!isset($batch)) $batch=0;
	if($batch==0) show_array($get,"$effect_class Effect Settings");
	$get['OBJECT_NAME']='single_strand';
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
	//
	$first_time=0;
	if(isset($matrix))
	{
		save_effects_user_segment($username,$effect_name,$matrix,$direction_array);
	}
	else
	{
		$first_time==1;
		$eus_array=get_effects_user_segment($username,$effect_name);
		$matrix=$eus_array[0];
		$direction_array   =$eus_array[1];
		/*echo "<pre>MATRIX,DIR:";
		print_r($matrix);
		print_r($direction_array);
		echo "</pre>\n";*/
	}
	if(isset($matrix)) extract ($matrix);
	$path="../targets/". $member_id;
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
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
	//$maxFrame=20;
	$maxFrame=intval(($seq_duration*1000/$frame_delay)/$speed)+1;
	$seq_number=0;
	/*	$maxFrame=20;
	$maxPixel=50;*/
	//
	//	if this is second time then save array of answers
	//
	//
	//
	$segment_array=get_segments($username,$user_target);
	/*echo "<pre>";
	print_r($segment_array);
	echo "</pre>";*/
	$number_segments=count($segment_array);
	for($segment=1;$segment<=$number_segments;$segment++)
	{
		// set defaults if data is missing
		if(!isset($matrix['color1'][$segment])) $matrix['color1'][$segment]="#FF0000";
		if(!isset($matrix['color2'][$segment])) $matrix['color2'][$segment]="#00FF00";
		if(!isset($matrix['count1'][$segment])) $matrix['count1'][$segment]=2;
		if(!isset($matrix['count2'][$segment])) $matrix['count2'][$segment]=2;
		if(!isset($matrix['fade_3d1'][$segment])) $matrix['fade_3d1'][$segment]='N';
		if(!isset($matrix['fade_3d2'][$segment])) $matrix['fade_3d2'][$segment]='N';
		if(!isset($direction_array[$segment]))    $direction_array[$segment]='right';
		if(!isset($matrix['rainbow'][$segment]))  $matrix['rainbow'][$segment]='N';
	}
	/*echo "<pre>maxFrame=$maxFrame,  maxPixel=$maxPixel</pre>\n";
	echo "<pre>number_segments=$number_segments  get_segments($username,$effect_name);\n";
	print_r($segment_array);
	echo "</pre>\n";*/
	$segment_max=$number_segments+1;
	$segment_array[$segment_max]=$maxPixel+1;
	//
	//$sparkles_array = create_sparkles($sparkles,$maxStrand,$maxPixel);
	//
	if($number_segments>0)
	{
		for($segment=1;$segment<=$number_segments;$segment++)
		{
			$segment1=$segment+1;
			$start_p=$segment_array[$segment];
			$end_p=$segment_array[$segment1];
			for($p=$start_p;$p<=$end_p;$p++)
			{
				$pixel_to_segment[$p]=$segment;
			}
		}
	}
	if($batch==0) effect_form($get,$pixel_to_segment,$segment_array,$number_segments,$matrix,$direction_array);
	$rainbow=$fade_3d1=$fade_3d2='N';
	extract($matrix);
	$x=$y=$z=0;
	for($f=1;$f<=$maxFrame;$f++)
	{
		$x_dat = $base . "_d_". $f . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$f] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$f];
		$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
		fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
		$s=1;
		for($p=1;$p<=$maxPixel;$p++)
		{
			if(isset($pixel_to_segment[$p])) $segment=$pixel_to_segment[$p];
			else $segment=1;
			$cycle=1;
			if(isset($count1[$segment])) $cycle = $count1[$segment] + $count2[$segment];
			$string=$user_pixel=0;
			$delta=($f*$speed);
			if(isset($direction_array[$segment]) and $direction_array[$segment]=='left') $delta=-$delta;
			//if($segment%2==0) $delta=-$delta;
			$new_p=$p-1- $delta;
			if($new_p<1) $new_p+=$maxPixel;
			$on=0;
			$cnt1=$cnt2=1;
			if(isset($count1[$segment])) $cnt1 = $count1[$segment];
			if(isset($count2[$segment])) $cnt2 = $count2[$segment];
			$mod=$new_p%$cycle;
			if($mod==0) $mod=$cycle;
			if($mod<=$cnt1)
			{
				$on=1;
			}
			$rgb_val=hexdec("#FFFFFF");
			if($on==1)
			{
				if(isset($color1[$segment])) $rgb_val=hexdec($color1[$segment]);
				$HSV=RGBVAL_TO_HSV($rgb_val);
				$H=$HSV['H'];
				$S=$HSV['S'];
				$V=$HSV['V'];
				$rainbow1=0;
				//$fade_3d1='y';
				if($rainbow1==1)
				{
					$segment=$pixel_to_segment[$p];
					$H=$p/$maxPixel;
					$S=$V=1;
				}
				//$fade_3d1='y';
				if($fade_3d1[$segment]=='y' or $fade_3d1[$segment]=='Y')
				{
					$mod1=$new_p%$cnt1;
					if($mod1==0) $mod1=$cnt1;
					$V=$V * $mod1/$cnt1;
				}
				$rgb_val=HSV_TO_RGB ($H, $S, $V);
			}
			else
			{
				if(isset($color2[$segment])) $rgb_val=hexdec($color2[$segment]);
				$HSV=RGBVAL_TO_HSV($rgb_val);
				$H=$HSV['H'];
				$S=$HSV['S'];
				$V=$HSV['V'];
				$rainbow2=0;
				//$fade_3d2='n';
				if($fade_3d2[$segment]=='y' or $fade_3d2[$segment]=='Y')
				{
					$mod2=$new_p%$cnt2;
					if($mod2==0) $mod2=$cnt2;
					$V=$V * $mod2/$cnt2;
				}
				$rgb_val=HSV_TO_RGB ($H, $S, $V);
			}
			if($rainbow[$segment]=='y' or $rainbow[$segment]=='Y')
			{
				$segment1=$segment+1;
				$start_p=$segment_array[$segment];
				$maxPixelsSegment=$segment_array[$segment1]-$segment_array[$segment];
				$H=$speed*($f+$p-$start_p)/($maxPixelsSegment+$maxFrame);
				if($H>1.0) $H-=intval($H);
				$S=$V=1;
				$rgb_val=HSV_TO_RGB ($H, $S, $V);
			}
			/*if(isset($sparkles_array[$strand][$p])===false 
			or $sparkles_array[$strand][$p]==null )
				$x=0;
			else if($sparkles_array[$strand][$p]>1)
			{
				$sparkles_array[$strand][$p]++;
				$rgb_val=calculate_sparkle($strand,$p,
				$sparkles_array[$strand][$p],$rgb_val,$sparkles_count);
			}*/
			$seq_number++;
			//	echo "<pre>f,s,p = $f,$s,$p (p_new=$p, n=$n mod=$m, $maxPixel). H,S,V = $H,$S,$V $hex</pre>\n";
			$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
			fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",
			$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel
			,$s_pixel[$s][$p][0],$s_pixel[$s][$p][1],
			$f,$seq_number));
			/*printf ("<pre>seg=%3d t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d</pre>\n",$segment,
			$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel
			,$s_pixel[$s][$p][0],$s_pixel[$s][$p][1],
			$f,$seq_number);*/
		}
		fclose($fh_dat [$f]);
	}
	$x_dat_base=$base . ".dat";
	$show_frame='n';
	$amperage=array();
	ob_flush();
	//sleep(3);
	/*echo "<pre>";
	print_r($dat_file_array);
	echo "</pre>";*/
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out);
}

function effect_form($get,$pixel_to_segment,$segment_array,$number_segments,$matrix,$direction_array)
{
	extract($get);
	if(isset($matrix)) extract ($matrix);
	/*echo "<pre>";
	print_r($matrix);
	print_r($fade_3d1);
	echo "</pre>";*/
	/*	[username] => f
	[user_target] => A0_ROOF
	[effect_class] => single_strand
	[effect_name] => SS_ROOF
	[frame_delay] => 100
	[direction] => Array
	[speed] => 1.5
	[seq_duration] => 5
	[fade_in] => 0
	[fade_out] => 0
	[submit] => Submit Form to create your effect
	[OBJECT_NAME] => single_strand
	[batch] => 0
	[show_frame] => N*/
	/*echo "<pre>";
	print_r($get);
	echo "</pre>";*/
	$gif_model=get_gif_model($username,$user_target);
	echo "<h1>Single Strand</h1>";
	/*foreach ($matrix as $param_name=>$array)
	{
		if($param_name=='color1') $color1=$array;
		if($param_name=='color1') $color1=$array;
	}
	*/
	/*echo "<pre>";
	echo "color1\n"; print_r($color1);
	echo "count1\n";print_r($count1);
	echo "color2\n";print_r($color2);
	echo "count2\n";print_r($count2);
	echo "direction\n";print_r($direction_array);
	echo "</pre>";*/
	$self=$_SERVER['PHP_SELF'];
	echo "<script type=\"text/javascript\" src=\"jscolor.js\"></script>\n";
	echo "<form action=\"$self\" method=\"GET\">\n";
	?>
	<input type="submit" name="submit" value="Submit Form to create your effects" />
	<?php
	foreach ($get as $item=>$value)
	{
		if (($item !="submit") && (!is_array($value))) 
		echo "<input type=\"hidden\" name=\"".$item ."\" value=\"".$value."\" />\n";
	}
	$columns=array('color1'=>'Color #1', 'count1'=>'Number of Color1 Pixels','fade_3d1'=>'3D Fade for Color#1',
	'color2'=>'Color #2', 'count2'=>'Number of Color2 Pixels',	'fade_3d2'=>'3D Fade for Color#2',
	'rainbow'=>'Use rainbow colors instead of color#1 and color#2',
	'direction'=>'Direction (left/right)');
	echo "<table border=1>\n";
	echo "<tr>";
	echo "<th>Column</th>";
	for($segment=1;$segment<=$number_segments;$segment++)
	{
		echo "<th>Segment $segment</th>";
		// Now set defaults if data is not set
	}
	echo "</tr>\n";
	foreach($columns as $col=>$col_desc)
	{
		echo "<tr>";
		$color="#FFFFFF";
		if($col=="color1" or $col=="count1" or $col=="fade_3d1") $color="#9BFF94";
		if($col=="color2" or $col=="count2" or $col=="fade_3d2") $color="#96B6FF";
		echo "<td bgcolor=\"$color\">";
		echo "$col_desc</td>";
		for($segment=1;$segment<=$number_segments;$segment++)
		{
			$val = $segment;
			if($col=="direction")
			{
				echo "<td>";
				$left_checked=$right_checked='';
				if(isset($direction_array[$segment]))
				{
					if($direction_array[$segment]=="left") $left_checked="checked";
					if($direction_array[$segment]=="right") $right_checked="checked";
				}
				if($gif_model=='window')
				{
					if($segment==1)
					{
						echo "<input type=\"radio\" class=\"input\"  name=\"direction_array[$segment]\" 
						value=\"left\" $left_checked />Left</br/>\n";
						echo "<input type=\"radio\" class=\"input\" name=\"direction_array[$segment]\" 
						value=\"right\"  $right_checked />Right<p>\n";
					}
					if($segment==2)
					{
						echo "<input type=\"radio\" class=\"input\" name=\"direction_array[$segment]\" 
						value=\"right\"  $right_checked />Up<br/>\n";
						echo "<input type=\"radio\" class=\"input\"  name=\"direction_array[$segment]\" 
						value=\"left\" $left_checked />Down<p>\n";
					}
					if($segment==3)
					{
						echo "<input type=\"radio\" class=\"input\" name=\"direction_array[$segment]\" 
						value=\"right\"  $right_checked />Left<br/>\n";
						echo "<input type=\"radio\" class=\"input\"  name=\"direction_array[$segment]\" 
						value=\"left\" $left_checked />Right<p>\n";
					}
					if($segment==4)
					{
						echo "<input type=\"radio\" class=\"input\"  name=\"direction_array[$segment]\" 
						value=\"left\" $left_checked />Up</br/>\n";
						echo "<input type=\"radio\" class=\"input\" name=\"direction_array[$segment]\" 
						value=\"right\"  $right_checked />Down<P>\n";
					}
				}
				else
				{
					echo "<input type=\"radio\" class=\"input\"  name=\"direction_array[$segment]\" value=\"left\" $left_checked />Left</br/>\n";
					echo "<input type=\"radio\" class=\"input\" name=\"direction_array[$segment]\" value=\"right\"  $right_checked />Right<P>\n";
				}
				echo "</td>";
			}
			else 
			{
				echo "<td >";
				echo "<input type=\"text\"   ";
				$mystring = $col;
				$findme="color";
				$pos = strpos($mystring, $findme);
				$val=0;
				if(isset($color1[$segment]) and $col=="color1") $val=$color1[$segment];
				if(isset($count1[$segment]) and $col=="count1") $val=$count1[$segment];
				if(isset($color2[$segment]) and $col=="color2") $val=$color2[$segment];
				if(isset($count2[$segment]) and $col=="count2") $val=$count2[$segment];
				if(isset($fade_3d1[$segment]) and $col=="fade_3d1") $val=$fade_3d1[$segment];
				if(isset($fade_3d2[$segment]) and $col=="fade_3d2") $val=$fade_3d2[$segment];
				if(isset($rainbow[$segment]) and $col=="rainbow") $val=$rainbow[$segment];
				if(isset($direction_array[$segment]) and $col=="direction") $val=$direction_array[$segment];
				if ($pos === false)
				{
					echo " class=\"input\" ";
				}
				else 
				{
					echo " class=\"color {hash:true} {pickerMode:'HSV'}\" ";
				}
				echo " name=\"matrix[$col][$segment]\" value=\"$val\" /></td>\n";
			}
		}
		echo "</tr>\n";
	}
	?>
	</table>
	<input type="submit" name="submit" value="Submit Form to create your effects" />
	</form>
	<?php
}

function save_effects_user_segment($username,$effect_name,$matrix,$direction_array)
{
	/*CREATE TABLE IF NOT EXISTS `effects_user_segment` (
	`username` varchar(25) NOT NULL,
	`effect_name` varchar(25) NOT NULL,
	`param_name` varchar(32) NOT NULL,
	`param_name` varchar(4000) NOT NULL,
	`segment` int(6) DEFAULT '0',
	`created` datetime DEFAULT NULL,
	`last_upd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE KEY `idx_uesp` (`username`,`effect_name`,segment,`param_name`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;*/
	/*	[matrix] => Array
	(
	[color1] => Array
	(
	[1] => #FF0000
	[2] => #FFEE00
	)
		[count1] => Array
	(
	[1] => 2
	[2] => 4
	)
		[color2] => Array
	(
	[1] => #00FF08
	[2] => #0808FF
	)
		[count2] => Array
	(
	[1] => 5
	[2] => 11
	)
		)
		[direction_array] => Array
	(
	[1] => right
	[2] => right
	)
		*/
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
	$delete = "delete from effects_user_segment where username='$username' and  
	effect_name='$effect_name' ";
	$result=mysql_query($delete) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . 
	$delete . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//echo "<pre>delete=$delete</pre>\n";
	//
	foreach($matrix as $param_name=>$array_values)
	{
		foreach($array_values as $segment =>$param_value)
		{
			$replace = "replace into effects_user_segment
			(username,effect_name,segment,param_name,param_value) values
			('$username','$effect_name','$segment','$param_name','$param_value')";
			//	echo "<pre>save_effects_user_segment: query=$replace</pre>\n";
			$result=mysql_query($replace) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $replace . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
		}
	}
	$param_name='direction';
	foreach($direction_array as $segment =>$param_value)
	{
		$replace = "replace into effects_user_segment
		(username,effect_name,segment,param_name,param_value) values
		('$username','$effect_name','$segment','$param_name','$param_value')";
		//	echo "<pre>save_effects_user_segment: query=$replace</pre>\n";
		$result=mysql_query($replace) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $replace . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	}
	//
	return;
}

function get_effects_user_segment($username,$effect_name)
{
	/*CREATE TABLE IF NOT EXISTS `effects_user_segment` (
	`username` varchar(25) NOT NULL,
	`effect_name` varchar(25) NOT NULL,
	`param_name` varchar(32) NOT NULL,
	`param_value` varchar(4000) NOT NULL,
	`segment` int(6) DEFAULT '0',
	`created` datetime DEFAULT NULL,
	`last_upd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE KEY `idx_uesp` (`username`,`effect_name`,segment,`param_name`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;*/
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
	//
	$query = "select * from  effects_user_segment where username='$username' and  
	effect_name='$effect_name' ";
	//echo "<pre>get_number_segments: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . 
	$query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	$number_segments=-1;
	$eus_array[0]=array();
	$eus_array[1]=array();
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$effects_user_segment[]=$row;
		$matrix[$param_name][$segment]=$param_value;
		if($param_name=='dir')
			$dir[$segment]=$param_value;
	}
	if(isset($matrix)) $eus_array[0]=$matrix;
	if(isset($dir)) $eus_array[1]=$dir;
	else $eus_array[1]=array();
	return $eus_array;
}
