<?php
{
	// used to join multiple projects together
	//	http://localhost/nutcracker/effects/mega_project.php?project_id=49&ss=214&pole=235     Wizards in Winter
	//	http://localhost/nutcracker/effects/mega_project.php?project_id=181&ss=215&pole=227    Amazing Grace
	//	http://localhost/nutcracker/effects/mega_project.php?project_id=182&ss=216&pole=228    Carol of the Bells
	//	http://localhost/nutcracker/effects/mega_project.php?project_id=183&ss=222&pole=232    Do you Hear
	//	http://localhost/nutcracker/effects/mega_project.php?project_id=161&ss=217&pole=231    Christmas Canon
	//	http://localhost/nutcracker/effects/mega_project.php?project_id=31&ss=218&pole=230     Christmas Sarajevo
	//	http://localhost/nutcracker/effects/mega_project.php?project_id=184&ss=223&pole=234    Music Box Dancer
	//	http://localhost/nutcracker/effects/mega_project.php?project_id=187&ss=224&pole=233    Linus & Lucy
	//	http://localhost/nutcracker/effects/mega_project.php?project_id=186&ss=225&pole=229    All I want


	//	extract ($get);
	require_once("../conf/setup.php"); // override some apache caching.
	require_once("../effects/read_file.php");
	//
	//
	//	$member_id=get_member_id($username);
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	//
	if(!isset($batch)) $batch=0;
	//if($batch==0) show_array($get,"$effect_class Effect Settings");
	//
	$first_time=0;
	//
	echo "<pre>";
	echo "POST:\n";
	print_r($_POST);
	echo "GET:\n";
	print_r($_GET);
	echo "</pre>\n";
	$get=$_GET;
	extract($_GET);
	//mega_project_form($get);
	$mtree_project = '../project/workarea/f~' . $project_id . '~master.nc';  // !!!!!!!!!!!!!!!!!!!!!!
	if(!isset($ss)) $ss=209;
	$eave_wind_star_project= '../project/workarea/f~' . $ss . '~master.nc';  
	if(!isset($pole)) $pole=227;
	$pole_star_project= '../project/workarea/f~' . $pole . '~master.nc';  
	echo "<pre><h2> Processing Megatree $mtree_project</h2></pre>\n";
	echo "<pre><h2> Processing Eaves, Windows $eave_wind_star_project</h2></pre>\n";
	echo "<pre><h2> Processing Pole, Tree Star $pole_star_project</h2></pre>\n";
	$tok=explode(".nc",$mtree_project);
	$vixen_vir = $tok[0] . ".vir"; // change the .nc to .vir
	$vixen_vix = $tok[0] . ".vix"; // change the .nc to .vir
	if (file_exists($vixen_vir))
	{
		unlink($vixen_vir);
	}
	//
	$max_frames=get_max_frames($mtree_project);
	echo "<pre>max frames = $max_frames</pre>\n";
	$tok=explode("~",$mtree_project);
	$project_id=$tok[1];
	$duration = get_project_duration($project_id);
	$project_data = get_project($project_id);
	extract($project_data);
	$song_data=get_song($song_id);
	extract ($song_data);
	echo "<pre>song=$song_id\n name=$song_name\n";
	echo "frame delay = $frame_delay\n\n\n";
	$mega_project[] = array('ncfile'=>$pole_star_project,'channels'=>564,'rgb'=>'N','desc'=>'Megatree');
	$mega_project[] = array('ncfile'=>'channels','channels'=>292,'rgb'=>'N','desc'=>'Dummy 1');
	$mega_project[] = array('ncfile'=>$mtree_project,'channels'=>7200,'rgb'=>'N','desc'=>'Megatree');
	$mega_project[] = array('ncfile'=>'channels','channels'=>196,'rgb'=>'N','desc'=>'dummy 2');
	$mega_project[] = array('ncfile'=>$eave_wind_star_project,'channels'=>7200,'rgb'=>'N','desc'=>'Megatree');
	//print_r($tok);
	//print_r($project_data);
	//	print_r($song_data);
	/*Array
	(
	[0] => ../project/workarea/f
	[1] => 181
	[2] => master.nc
	)
		Array
	(
	[project_id] => 181
	[song_id] => 414
	[username] => f
	[frame_delay] => 100
	[model_name] => A
	[check_sum] => 
	[last_update_date] => 2012-12-03 17:10:38
	[last_compile_date] => 2012-12-03 18:22:19
	)
		Array
	(
	[song_id] => 414
	[active_set] => N
	[song_name] => Amazing Grace
	[artist] => Yule
	[song_url] => http://www.amazon.com/Amazing-Grace-2008-Party-Version/dp/B0012DOESS/ref=sr_1_1?ie=UTF8&qid=1346370185&s=dmusic&sr=1-1
	[last_updated] => 2012-12-02 21:56:35
	[audacity_aup] => 07 - Amazing Grace (2008 Party Version).txt
	[music_mo_file] => 
	[username] => f
	)*/
	$get=array();
	//	if($batch==0) mega_project_form($get);
	//	$mega_project[] = array('ncfile'=>'channels','channels'=>128,'rgb'=>'N','desc'=>'DMX Channels 1-128');
	//	$mega_project[] = array('ncfile'=>'star.nc','channels'=>180,'rgb'=>'Y','desc'=>'60 RGB node coro star');
	echo "<pre>";
	//	$fh_vixen_vir=fopen($vixen_vir,"w") or die("Unable to open $vixen_vir");
	/*$full_path= "../project/workarea";
	$path_parts = pathinfo($full_path);
	$dirname   = $path_parts['dirname'];
	$basename  = $path_parts['basename'];
	$vixen_vir= $dirname . "/" . $base . ".vir";*/
	$ichannel=0;
	$current_channel=0;
	$channel_array=array();
	foreach ($mega_project as $i=>$mega_array)
	{
		print_r ($mega_array);
		$channels=$mega_array['channels'];
		if($mega_array['ncfile']=='channels')
		{
			//$ichannel++;
			//$channel_array[$ichannel]['start']=$current_channel;
			$current_channel=write_blank_channels($vixen_vir,$channels,$current_channel,$max_frames);
			//$current_channel+=$channels;
			//$channel_array[$ichannel]['end']=$current_channel;
			echo "<pre>";
			print_r($channel_array);
		}
		else 
		{
			$filename_buff = $mega_array['ncfile'];
			$ichannel++;
			$channel_array[$ichannel]['start']=$current_channel+1;
			$current_channel=make_vir($vixen_vir,$filename_buff,$current_channel);
			$channel_array[$ichannel]['end']=$current_channel;
		}
	}
	print_r ($mega_project);
}
echo "Channel ARRAY\n";
print_r($channel_array);
$seq_duration = 30;
$duration = $frame_delay * $max_frames;
$frame_delay=100;
//check_vir($vixen_vir);
make_vix($get,$vixen_vir,$duration,$frame_delay,$channel_array);  // also make a *.vix file. Pass in the *.vir
echo "<table border=1>";
printf ("<tr><td bgcolor=lightgreen><h2>$channels channels have been created for Vixen</h2></td>\n");
echo "<td>Instructions</td></tr>";
printf ("<tr><td bgcolor=#98FF73><h2><a href=\"%s\">Right Click here for Vixen vir file. %s</a></h2></td>\n",$vixen_vir,$vixen_vir);
echo "<td>Save the file to your Vixen/routines directory</td></tr>";
printf ("<tr><td bgcolor=#98FF73><h2><a href=\"%s\">Right Click here for Vixen vix file. %s</a></h2></td>\n",$vixen_vix,$vixen_vix);
echo "<td>Save the file to your Vixen/sequences directory</td></tr>";
echo "</table>";
echo "<h2>Here are Emmanuel Miranda's 3 addons for Nutcracker. I would not even try to use Nutcracker ";
echo "on Vixen without these. <br/>\n";
echo "<a href=\"http://emmanuelmiranda.info/?page_id=10\">http://emmanuelmiranda.info/?page_id=10</a>\n";
echo "<ul>\n";
echo "<li>RGBProfilerAddin.dll : This will create a profile matching the strings and pixels you have and colors it as RGB\n";
echo "<li>RoutineLoaderAddin.dll : This will allow you to load huge profile files in one second\n";
echo "<li>RGBTreePreview3D.dll : This will create a 3D tree in Vixen so you can see your animations\n";
echo "</ul>\n";
echo "<br>\n";
echo "<ul>\n";
echo "<li><a href=\"../tutorials/VixenTutorial1.swf\">Vixen Tutorial #1: Creating a Nutcracker effect and storing as a vir file</a>\n";
echo "<li><a href=\"../tutorials/VixenTutorial2.swf\">Vixen Tutorial #2: Setting up the profiler, routine importer and RGB visualizer into Vixen.</a>\n";
echo "<li><a href=\"../tutorials/VixenTutorial3.swf\">Vixen Tutorial #3: Finish setting up the RGB visualizer</a>\n";
echo "</ul>\n";
echo "</h2>";
echo "<pre>End of program\n</pre>";
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
$description="Elapsed time:";
printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);

function write_blank_channels($vixen_vir,$channels,$current_channel,$MaxFrame)
{
	echo "<pre>START: write_blank_channels($vixen_vir,$channels)</pre>\n";
	$fh_vixen_vir=fopen($vixen_vir,"a") or die("Unable to open $vixen_vir");
	$val = 0; // set each cell to zero (black)
		$max_channels=$channels;
	for ($loop=1;$loop<=$max_channels;$loop++) // write this many channels of blank data.
	{
		$current_channel++;
		//	fwrite($fh_vixen_vir,sprintf("(%d) ",$current_channel));
		for($f=1;$f<$MaxFrame;$f++)
		{
			fwrite($fh_vixen_vir,sprintf("%d ",$val));
		}
		fwrite($fh_vixen_vir,sprintf("\n"));
	}
	fclose($fh_vixen_vir);
	echo "<pre>DONE: write_blank_channels($fh_vixen_vir,$channels)</pre>\n";
	return $current_channel;
}

function get_project($project_id)
{
	//Include database connection details
	require_once('../conf/config.php');
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
	$query ="select * from project where project_id='$project_id'";
	//echo "<pre>get_effect_user_dtl: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if (!$result)
	{
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	$project_data=array();
	if(!$NO_DATA_FOUND)
	{
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while ($row = mysql_fetch_assoc($result))
		{
			extract($row);
			$project_data=$row;
		}
	}
	return $project_data;
}

function get_project_duration($project_id)
{
	//Include database connection details
	require_once('../conf/config.php');
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
	$query ="SELECT min(start_secs) as min_secs,max(end_secs) as max_secs FROM `project_dtl` WHERE project_id ='$project_id'";
	//echo "<pre>get_effect_user_dtl: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if (!$result)
	{
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	if(!$NO_DATA_FOUND)
	{
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while ($row = mysql_fetch_assoc($result))
		{
			extract($row);
		}
	}
	echo "<pre>max seconds = $max_secs</pre>\n";
	return $max_secs;
}

function get_song($song_id)
{
	//Include database connection details
	require_once('../conf/config.php');
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
	$query ="select * from song where song_id='$song_id'";
	//echo "<pre>get_effect_user_dtl: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if (!$result)
	{
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	$song_data=array();
	if(!$NO_DATA_FOUND)
	{
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while ($row = mysql_fetch_assoc($result))
		{
			extract($row);
			$song_data=$row;
		}
	}
	return $song_data;
}

function get_max_frames($filename)
{
	$fh_buff=fopen($filename,"r") or die("Unable to open $filename");
	$channels=0;
	$max_frames=0;
	$line_counter=0;
	while (!feof($fh_buff))
	{
		$line = fgets($fh_buff);
		$tok=preg_split("/ +/", $line);
		$l=strlen($line);
		$cnt= count($tok);
		if($cnt>$max_frames) $max_frames=$cnt;
		$line_counter++;
		if($line_counter>10) return $max_frames-4;
	}
	return $max_frames-4; // Lines are format S xx P xx rgb rgb rgb 
}

function make_vir($vixen_vir,$filename_buff,$current_channel)
{
	echo "<pre>function make_vir($filename_buff)</pre>\n";
	$fh_vixen_vir=fopen($vixen_vir,"a") or die("Unable to open $vixen_vir");
	$fh_buff=fopen($filename_buff,"r") or die("Unable to open $filename_buff");
	$channels=0;
	while (!feof($fh_buff))
	{
		$line = fgets($fh_buff);
		$tok=preg_split("/ +/", $line);
		$l=strlen($line);
		$cnt= count($tok);
		$MaxFrame=$cnt-4;
		//echo "<pre>cnt=$cnt MaxFrame=$MaxFrame, line=$line</pre>\n";
		if($tok[0]=='S' and $tok[2]=='P')
		{
			$string=$tok[1];
			$pixel=$tok[3];
			//echo "<pre>s,p=$string,$pixel: current_channel=$current_channel $line</pre>\n";
			for($rgbLoop=1;$rgbLoop<=3;$rgbLoop++)
			{
				$current_channel++;
				//			fwrite($fh_vixen_vir,sprintf("(%d) ",$current_channel));
				for($f=1;$f<$MaxFrame;$f++)
				{
					$rgb=$tok[$f+3];
					$r = ($rgb >> 16) & 0xFF;
					$g = ($rgb >> 8) & 0xFF;
					$b = $rgb & 0xFF;
					if($rgbLoop==1)
					{
						$c='R';$color=16711680;
						$val=$r;
					}
					if($rgbLoop==2)
					{
						$c='G';$color=65280;
						$val=$g;
					}
					if($rgbLoop==3)
					{
						$c='B';$color=255;
						$val=$b;
					}
					fwrite($fh_vixen_vir,sprintf("%d ",$val));
					//printf("%d ",$val);
				}
				$channels++;
				fwrite($fh_vixen_vir,sprintf("\n"));
				//printf("\n");
			}
		}
	}
	fclose($fh_vixen_vir);
	fclose($fh_buff);
	return ($current_channel);
}

function mega_project_form($get)
{
	extract($get);
	echo "<h1>Mega Project</h1>";
	$number_segments=12;
	$self=$_SERVER['PHP_SELF'];
	echo "<form action=\"$self\" method=\"GET\">\n";
	?>
	<input type="submit" name="submit" value="Submit Form to create your effects" />
	<?php
	foreach ($get as $item=>$value)
	{
		if (($item !="submit") && (!is_array($value))) 
		echo "<input type=\"hidden\" name=\"".$item ."\" value=\"".$value."\" />\n";
	}
	$columns=array('channel'=>'Channel#', 
	'nc_file'=>'NC Filename',
	'rgb'=>'RGB channels (Y or N)',
	'desc'=>'Description');
	echo "<table border=1>\n";
	echo "<tr>";
	echo "<th>Column</th>";
	foreach($columns as $col=>$col_desc)
	{
		echo "<th>$col_desc</th>";
		// Now set defaults if data is not set
	}
	echo "</tr>\n";
	for($segment=1;$segment<=$number_segments;$segment++)
	{
		echo "<tr>";
		echo "<td >";
		echo "$segment</td>";
		foreach($columns as $col=>$col_desc)
		{
			$val = $col;
			echo "<td >";
			echo "<input type=\"text\"   ";
			$mystring = $col;
			$val=0;
			if(isset($color1[$segment]) and $col=="color1") $val=$color1[$segment];
			if(isset($count1[$segment]) and $col=="count1") $val=$count1[$segment];
			if(isset($color2[$segment]) and $col=="color2") $val=$color2[$segment];
			if(isset($count2[$segment]) and $col=="count2") $val=$count2[$segment];
			if(isset($fade_3d1[$segment]) and $col=="fade_3d1") $val=$fade_3d1[$segment];
			if(isset($fade_3d2[$segment]) and $col=="fade_3d2") $val=$fade_3d2[$segment];
			if(isset($rainbow[$segment]) and $col=="rainbow") $val=$rainbow[$segment];
			if(isset($group[$segment]) and $col=="group") $val=$group[$segment];
			if(isset($group_number[$segment]) and $col=="group_number") $val=$group_number[$segment];
			if(isset($direction_array[$segment]) and $col=="direction") $val=$direction_array[$segment];
			echo " class=\"input\" ";
			echo " name=\"matrix[$col][$segment]\" value=\"$val\" /></td>\n";
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
	/*	
	VIXEN	How much time should be in between timings
	LOR	Event Period. The event period is how long a single on/off event lasts
	LSP	Intervals. How far apart should default intervals be created?
	[matrix] => Array
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
			$YesNoPrompts = array('rainbow','fade3d1','fade3d2','group');
			if (in_array($param_name, $YesNoPrompts)) $param_value=strtoupper($param_value);
			$replace = "replace into effects_user_segment
			(username,effect_name,segment,param_name,param_value) values
			('$username','$effect_name','$segment','$param_name','$param_value')";
			echo "<pre>save_effects_user_segment: query=$replace</pre>\n";
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
		if($param_name=='direction')
			$dir[$segment]=$param_value;
	}
	if(isset($matrix)) $eus_array[0]=$matrix;
	if(isset($dir)) $eus_array[1]=$dir;
	else $eus_array[1]=array();
	return $eus_array;
}

function check_vir($vixen_vir)
{
	$fh_vir=fopen($vixen_vir,"r") or die("Unable to open $vixen_vir");
	$channel=0;
	while (!feof($fh_vir))
	{
		$line = fgets($fh_vir);
		$channel++;
		$tok=preg_split("/ +/", $line);
		$c=count($tok);
		$zero=$non_zero=$invalid=0;
		if($c>1)
		{
			foreach($tok as $i=>$val)
			{
				if($i<=$c-1)
				{
					if($val>=0 and $val<=255)
					{
						if($val<1)
						{
							$zero++;
						}
						else
						{
							$non_zero++;
						}
					}
					else
					$invalid++;
				}
			}
		}
		echo "<pre>channel=$channel. c=$c. zero $zero, non zero $non_zero, invalid $invalid</pre>\n";
	}
	fclose($fh_vir);
}

function make_vix($get,$vixen_vir,$duration,$frame_delay,$channel_array)
{
	extract($get);
	/*$vixen_file = $model_base_name . ".vir";
	$full_path = $path . "/" . $vixen_file;*/
	//
	$path_parts = pathinfo($vixen_vir);
	//$dat_file_array0=$dat_file_array[0];
	$dirname   = $path_parts['dirname']; // workspaces/2
	$basename  = $path_parts['basename']; // AA+CIRCLE1_d_1.dat
	//	$extension =$path_parts['extension']; // .dat
	$filename  = $path_parts['filename']; //  AA+CIRCLE1_d_1
	$file_vix = $dirname . "/" . $filename . ".vix";
	//$file_vire = $dirname . "/" . $filename . ".vire"; //  enhanced vir file()
		$start_channel=1;
	//	echo "<pre>start_channel=get_start_channel(username,model)=$start_channel=get_start_channel($username,$basename);</pre>\n";
	// start_channel=get_start_channel(username,model)=1=get_start_channel(f,AA_SMALL~BARBERPOLE.vir);
	$tok=explode("~",$basename);
	$model=$tok[0];
	//$start_channel=get_start_channel($username,$model);
	echo "<pre>start_channel=$start_channel</pre>\n";
	if(!isset($start_channel)) $start_channel=1;
	//
	$fh_vir=fopen($vixen_vir,"r") or die("Unable to open $vixen_vir");
	//	$fh_vire=fopen($vixen_vire,"w") or die("Unable to open $vixen_vir");
	$fh = fopen($file_vix,"w") or die ("unable to open $file_vix");
	fwrite($fh,sprintf("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"));
	fwrite($fh,sprintf("<Program>\n"));
	fwrite($fh,sprintf("<Time>$duration</Time>\n"));
	fwrite($fh,sprintf("<EventPeriodInMilliseconds>$frame_delay</EventPeriodInMilliseconds>\n"));
	fwrite($fh,sprintf("<MinimumLevel>0</MinimumLevel>\n"));
	fwrite($fh,sprintf("<MaximumLevel>255</MaximumLevel>\n"));
	fwrite($fh,sprintf("<AudioDevice>-1</AudioDevice>\n"));
	fwrite($fh,sprintf("<AudioVolume>0</AudioVolume>\n"));
	$lines=0;
	rewind($fh_vir);
	while (!feof($fh_vir))
	{
		$line = fgets($fh_vir);
		$tok=preg_split("/ +/", $line);
		$c=count($tok);
		if($c>1) $lines++;
	}
	rewind($fh_vir);
	$plugin=0;
	if($plugin==1)
	{
		fwrite($fh,sprintf("<PlugInData>\n"));
		/*fwrite($fh,sprintf("<Channels />\n"));*/
		/*fwrite($fh,sprintf("<PlugInData>\n"));
		fwrite($fh,sprintf("<PlugIn name=\"Adjustable preview\" key=\"-1193625963\" id=\"0\" enabled=\"True\" type=\"Output\" from=\"1\" to=\"698\">\n"));*/
		fwrite($fh,sprintf("<PlugIn name=\"Adjustable preview\" key=\"-1193625963\" id=\"0\" enabled=\"True\" type=\"Output\" from=\"1\" to=\"$lines\">\n"));
		fwrite($fh,sprintf("<RedirectOutputs>False</RedirectOutputs>\n"));
		fwrite($fh,sprintf("<Display>\n"));
		fwrite($fh,sprintf("<Height>211</Height>\n"));
		fwrite($fh,sprintf("<Width>459</Width>\n"));
		fwrite($fh,sprintf("<PixelSize>3</PixelSize>\n"));
		fwrite($fh,sprintf("<Brightness>5</Brightness>\n"));
		fwrite($fh,sprintf("</Display>\n"));
	}
	else
	{
		fwrite($fh,sprintf("<PlugInData />\n"));
	}
	fwrite($fh,sprintf("<Channels>\n"));
	/*	<Channel color="-65536" output="1" id="634715461096095731" enabled="True">STR_1_PIX_1_RED</Channel>
	<Channel color="-16744448" output="2" id="634715461096095731" enabled="True">STR_1_PIX_1_GREEN</Channel>
	<Channel color="-16776961" output="3" id="634715461096095731" enabled="True">STR_1_PIX_1_BLUE</Channel>*/
	$channel=0;
	while (!feof($fh_vir))
	{
		$line = fgets($fh_vir);
		$tok=preg_split("/ +/", $line);
		$c=count($tok);
		if($c>1)
		{
			$channel++;
			$start_channel_rgb = get_start_end($channel,$channel_array);
			if($start_channel_rgb>0) // start and end channels we found from reading nc files
			{
				$channel_mod = ($channel-$start_channel_rgb+1)%3; // we want to star R,G,B wherever start_channel is
				if($channel_mod==1)
				{
					$color=-65536;    $rgb="R";
					//	$color=-hexdec("#FF7777");
				}
				if($channel_mod==2)
				{
					$color=-16744448; $rgb="G";
					//	$color=-hexdec("#77FF77");
				}
				if($channel_mod==0)
				{
					$color=-16776961; $rgb="B";
					//	$color = -hexdec("#7777FF");
				}
			}
			else
			{
				$color=hexdec("#FFFF00");
				$rgb="DMX";
			}
			$new_channel=$channel+$start_channel-1;
			$output=$new_channel-1;
			$base_channel = $new_channel-497+1;
			$pixel = intval((($base_channel%360)-1)/3)+1;
			$string = intval(($base_channel-1)/360)+1;
			if($start_channel_rgb>0)
				$channel_name = "Ch $new_channel $rgb (S$string P$pixel)";
			else
			{
				$channel_name = "Ch $new_channel $rgb";
			}
			fwrite($fh,sprintf("<Channel color=\"$color\" output=\"$output\" id=\"0\" enabled=\"True\">$channel_name</Channel>\n"));
		}
	}
	fwrite($fh,sprintf("</Channels>\n"));
	if($plugin==1)
	{
		fwrite($fh,sprintf("</PlugIn>\n"));
		fwrite($fh,sprintf("</PlugInData>\n"));
	}
	fwrite($fh,sprintf("<SortOrders lastSort=\"-1\" />\n"));
	fwrite($fh,sprintf("<EventValues>"));
	$eventdata = '';
	$channel=0;
	rewind($fh_vir);
	while (!feof($fh_vir))
	{
		$line = fgets($fh_vir);
		$channel++;
		$tok=preg_split("/ +/", $line);
		$c=count($tok);
		//	if($channel >496 and $channel<505) echo "<pre>make_vix chhanel=$channel c=$c, $line</pre>\n";
		if($c>1)
		{
			foreach($tok as $i=>$val)
			{
				if($i<=$c-1)
				{
					if(!isset($val)) $val=0;
					//if($channel >490 and $channel<510) echo "<pre>c=$c, i=$i,val=$val</pre>";
					if($val>=0 and $val<=255)
					{
						$eventdata .= chr($val);
						//	printf ("<pre>line %d. i=%d val=[%d:%c]</pre>\n",$channel,$i,$val,$val);
					}
					else
					echo "<pre>ERROR! line $channel. i=$i val=[$val]</pre>\n";
				}
			}
		}
	}
	fwrite($fh,base64_encode($eventdata));
	fwrite($fh,sprintf("</EventValues>\n"));
	/*fwrite($fh,sprintf("<LoadableData>\n"));
	fwrite($fh,sprintf("<IAddInData>\n"));
	fwrite($fh,sprintf("<IAddIn name=\"RGB Tree profiler\" />\n"));
	fwrite($fh,sprintf("<IAddIn name=\"Vixen Routine Loader\" />\n"));
	fwrite($fh,sprintf("</IAddInData>\n"));
	fwrite($fh,sprintf("</LoadableData>\n"));
	fwrite($fh,sprintf("<EngineType>Standard</EngineType>\n"));
	fwrite($fh,sprintf("<Extensions>\n"));
	fwrite($fh,sprintf("<Extension type=\".vix\" />\n"));
	fwrite($fh,sprintf("</Extensions>\n"));
	fwrite($fh,sprintf("<WindowSize>808,604</WindowSize>\n"));
	fwrite($fh,sprintf("<ChannelWidth>149</ChannelWidth>\n"));*/
	fwrite($fh,sprintf("</Program>\n"));
	fclose($fh);
	fclose($fh_vir);
}

function get_start_end($channel,$channel_array)
{
	$start_end=array();
	$rgb_channel=0;
	{
		foreach ($channel_array as $i => $start_end)
		{
			$start_channel = $start_end['start'];
			$end_channel   = $start_end['end'];
			if($channel>=$start_channel and $channel<=$end_channel ) $rgb_channel=$start_channel;
			/*if($channel> 490 and $channel < 500)
			{
				echo "<pre>";
				echo "[$i] = $start_channel,$end_channel. rgb_channel = $rgb_channel\n";
				echo "</pre>\n";
			}
			*/
		}
	}
	return $rgb_channel;
}
