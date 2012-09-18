<?php

function f_single_strand($get)
{
	if(!isset($get['direction'])) $get['direction']="right";
	if(!isset($get['fade_in']))   $get['fade_in']="0";
	if(!isset($get['fade_out']))  $get['fade_out']="0";
	if(!isset($get['speed']))     $get['speed']="1";
	extract ($get);
	echo "<pre>";
	print_r($get);
	echo "</pre>\n";
	set_time_limit(0);
	ini_set("memory_limit","512M");
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
	if(isset($matrix))
	{
		save_effects_user_segment($username,$effect_name,$matrix,$dir);
	}
	else
	{
		$eus_array=get_effects_user_segment($username,$effect_name);
		$matrix=$eus_array[0];
		$dir   =$eus_array[1];
		/*echo "<pre>MATRIX,DIR:";
		print_r($matrix);
		print_r($dir);
		echo "</pre>\n";*/
	}
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
	$number_segments=count($segment_array);
	echo "<pre>maxFrame=$maxFrame,  maxPixel=$maxPixel</pre>\n";
	echo "<pre>number_segments=$number_segments  get_segments($username,$effect_name);\n";
	print_r($segment_array);
	echo "</pre>\n";
	$segment_max=$number_segments+1;
	$segment_array[$segment_max]=$maxPixel+1;
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
	effect_form($get,$pixel_to_segment,$segment_array,$number_segments,$matrix,$dir);
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
			$segment=$pixel_to_segment[$p];
			$string=$user_pixel=0;
			$delta=($f*$speed);
			if($direction=='left') $delta=-$delta;
			//if($segment%2==0) $delta=-$delta;
			$new_p=$p-1- $delta;
			if($new_p<1) $new_p+=$maxPixel;
			$on=0;
			if($new_p%16<=3)
			{
				$on=1;
			}
			if($on==1)
			{
				$rgb_val=hexdec("FF00FF");
				$HSV=RGBVAL_TO_HSV($rgb_val);
			}
			else
			{
				$rgb_val=hexdec("000000");
				$HSV=RGBVAL_TO_HSV($rgb_val);
				$H=$HSV['H'];
				$S=$HSV['S'];
				$V=$HSV['V'];
				$H+=$segment/10;
				if($H>1.0) $H-=1.0;
				$rgb_val=HSV_TO_RGB ($H, $S, $V);
			}
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
	}
	$x_dat_base=$base . ".dat";
	$show_frame='n';
	$amperage=array();
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out);
}

function effect_form($get,$pixel_to_segment,$segment_array,$number_segments,$matrix,$dir)
{
	echo "<h1>Single Strand</h1>";
	$self=$_SERVER['PHP_SELF'];
	echo "<script type=\"text/javascript\" src=\"jscolor.js\"></script>\n";
	echo "<form action=\"$self\" method=\"GET\">\n";
	?>
	<input type="submit" name="submit" value="Submit Form to create your effects" />
	<?php
	foreach ($get as $item=>$value)
	{
		echo "<input type=\"hidden\" name=\"$item\" value=\"$value\" />\n";
	}
	$columns=array('color1'=>'Color #1', 'count1'=>'Number of Color1 Pixels',	'color2'=>'Color #2', 'count2'=>'Number of Color2 Pixels',	'direction'=>'Direction (left/right)');
	echo "<table border=1>\n";
	echo "<tr>";
	echo "<th>Column</th>";
	for($segment=1;$segment<=$number_segments;$segment++)
	{
		echo "<th>Segment $segment</th>";
	}
	echo "</tr>\n";
	foreach($columns as $col=>$col_desc)
	{
		echo "<tr>";
		echo "<td>$col_desc</td>";
		for($segment=1;$segment<=$number_segments;$segment++)
		{
			$val = $segment;
			if($col=="direction")
			{
				echo "<td>";
				echo "<input type=\"radio\" name=\"dir[$segment]\" value=\"left\"  />Left</br/>\n";
				echo "<input type=\"radio\" name=\"dir[$segment]\" value=\"right\"  checked />Right<P>\n";
				echo "</td>";
			}
			else 
			{
				echo "<td>";
				echo "<input type=\"text\"   ";
				$mystring = $col;
				$findme="color";
				$pos = strpos($mystring, $findme);
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

function save_effects_user_segment($username,$effect_name,$matrix,$dir)
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
	[1] => #FFFFFF
	[2] => #FFFFFF
	)
		[count1] => Array
	(
	[1] => count1:1
	[2] => count1:2
	)
		[color2] => Array
	(
	[1] => #FFFFFF
	[2] => #FFFFFF
	)
		[count2] => Array
	(
	[1] => count2:1
	[2] => count2:2
	)
		)
		[dir] => Array
	(
	[1] => right
	[2] => right
	)*/
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
	foreach($dir as $segment =>$param_value)
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
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$effects_user_segment[]=$row;
		$matrix[$param_name][$segment]=$param_value;
		if($param_name=='dir')
		$dir[$segment]=$param_value;
	}
	$eus_array[0]=$matrix;
	if(isset($dir)) $eus_array[1]=$dir;
	else $eus_array[1]=array();
	return $eus_array;
}
