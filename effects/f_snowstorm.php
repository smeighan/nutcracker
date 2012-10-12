<?php

function f_snowstorm($get)
{
$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the targetdelete from s
$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	extract ($get);
	set_time_limit(0);
	ini_set("memory_limit","1024M");
	require_once("../effects/read_file.php");
	//
	$member_id=get_member_id($username);
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	$member_id=get_member_id($username);
	$base = $user_target . "~" . $effect_name;
	$t_dat = $user_target . ".dat";
	$xdat = $user_target ."~".  $effect_name . ".dat";
	$path="../targets/". $member_id;
	$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
	$path="../effects/workspaces/". $member_id;
	if(empty($show_frame)) $show_frame='N';
	if(empty($background_color)) $background_color='#FFFFFF';
	//
	if(!isset($batch)) $batch=0;
	$get['batch']=$batch;
	//
	/*echo "<pre>";
	print_r($get);
	echo "</pre>\n";*/
	$target_path ="../targets/$member_id";
	$arr=read_file($t_dat,$target_path); //  target megatree 32 strands, all 32 being used. read data into an array
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
	$path ="../effects/workspaces/$member_id";
	$directory=$path;
	if (file_exists($directory))
	{
		} else {
		echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$path="../effects/workspaces/". $member_id;
	srand(time());
	$maxFrame=70;
	//$maxTrees=6;	// how many tree to draw at one time
	$seq_number=0;
	$window_array = getWindowArray($minStrand,$maxStrand,$window_degrees);
	//	$maxSnowflakes=40;	// how many snowflakes to start with
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
	/*$model_name_base = basename($model_name,".dat");*/
	//	mode_dtl: username	object_name	strand	pixel	string	user_pixel	created	last_upd
	$counter=0;	// how many total snowflakes we have
	$query0="delete from snowstorm  where username='$username'";
	$result0=mysql_query($query0) or die ("Error on $query0");
	$new_records=$maxSnowflakes;
	for($seed=1;$seed<=$maxSnowflakes;$seed++)
	{
		$counter++;
		$strand=rand(1,$maxStrand);
		$pixel=rand(1,$maxPixel);
		$color_HSV=color_picker($seed,$maxSnowflakes,$maxFrame,$start_color,$end_color);
		$H=$color_HSV['H'];
		$S=$color_HSV['S'];
		$V=$color_HSV['V'];
		$rgb_val=HSV_TO_RGB ($H, $S, $V);
		$state=1;
		/*$snowflake[$counter]['rgb']=$rgb_val;*/
		$query1="replace into snowstorm (username,counter,state,strand,pixel,rgb,parent_rgb) 
		values ('$username',$counter,$state,$strand,$pixel,$rgb_val,$rgb_val)";
		$result1=mysql_query($query1) or die ("Error on $query1");
	}
	//	$trail_length=7;
	if(!isset($trail_length) or $trail_length==null or $trail_length<2)
		$trail_length=2;
	for($frame=1;$frame<=$maxFrame;$frame++)
	{
		if($frame>5000) exit ("Too many frames in sequence");
		$x_dat = $base . "_d_". $frame . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$frame] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$frame];
		$fh_dat [$frame]= fopen($dat_file[$frame], 'w') or die("can't open file");
		fwrite($fh_dat[$frame],"#    " . $dat_file[$frame] . "\n");
		/*$c=count($snowflake);*/
		/*echo "<pre>frame=$frame for($sf=1;$sf<=$counter;$sf++)</pre>\n";*/
		$query2="SELECT * from snowstorm where username='$username'";
		$result2=mysql_query($query2) or die ("Error on $query2");
		/*echo "<table border=1>";
		echo "<tr><td>frame=$frame</td></tr>";*/
		$l=0;
		while ($row = mysql_fetch_assoc($result2))
		{
			extract($row);
			$l++;
			/*	echo "<tr><td bgcolor=lightblue>$l</td><td>$username</td><td>$counter</td><td>$state</td><td>$strand</td><td>$pixel</td><td>$rgb</td><td>$parent_rgb</td></tr>";*/
			if($state>=1) $state++;
			if($state>$trail_length)
			{
				$query3="delete from snowstorm  where username='$username' and counter=$counter";
				$result3=mysql_query($query3) or die ("Error on $query3");
			}
			else
			{
				$rgbnew=dim($parent_rgb,$state,$trail_length);
				//update_row($username,$counter,$rgbnew,$state);
				$query4="update snowstorm set rgb=$rgbnew ,state=$state where username='$username' and counter=$counter"; 
				$result4=mysql_query($query4) or die ("Error on $query4");
				if($state==2)
				{
					//$counter+=$counter + $frame*100;
					$new_records++;
					$xy=advance($strand,$pixel,$frame,$new_records);
					$state=1;
					$strand+=$xy[0];
					$pixel+=$xy[1];
					if($pixel<1) $pixel+=$maxPixel;
					if($pixel>$maxPixel) $pixel=$pixel-$maxPixel;
					if($strand<1) $strand+=$maxStrand;
					if($strand>$maxStrand) $strand=$strand-$maxStrand;
					$query5="replace into snowstorm (username,counter,state,strand,pixel,rgb,parent_rgb) 
					values ('$username',$new_records,$state,$strand,$pixel,$parent_rgb,$parent_rgb)";
					$result5=mysql_query($query5) or die ("Error on $query5");
				}
			}
		}
		/*	echo "</table>";*/
		$query6="SELECT * from snowstorm where username='$username'";
		$result6=mysql_query($query6) or die ("Error on $query6");
		/*	echo "<table border=1>";
		echo "<tr><td>frame=$frame</td></tr>";*/
		$l=0;
		while ($row = mysql_fetch_assoc($result6))
		{
			extract($row);
			//echo "<pre>frame=$frame pass2\n";
			//print_r($row);
			$l++;
			//	echo "<tr><td>$l</td><td>$username</td><td>$counter</td><td>$state</td><td>$strand</td><td>$pixel</td><td>$rgb</td><td>$parent_rgb</td></tr>";
			//echo "</pre>\n";
			$s=$strand;
			$p=$pixel;
		//	if(in_array($s,$window_array)) // Is this strand in our window?, 
			{
				$i = array_search($s,$window_array)+1;
				//	fwrite here	
				//	
				$string=$user_pixel=0;
				$xyz=$tree_xyz[$s][$p];
				$seq_number++;
				$rgb_val=$rgb;
				if($rgb_val <> 0)
					fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number));
			}
		}
		/*echo "</table>";*/
		fclose($fh_dat[$frame]);
		/*echo "<pre>Start Sleeping ..</pre>\n";
		ob_flush();
		sleep (1);
		echo "<pre>Done Sleeping ..</pre>\n";*/
		ob_flush();
	}
	$amperage=array();
	$x_dat_base = $base . ".dat";
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out);
}

function insert_snowstorm($username,$counter,$strand,$pixel,$rgb_val)
{
	$query="replace into snowstorm (username,counter,state,strand,pixel,rgb) 
	values ('$username',$counter,$strand,$pixel,$rgb_val)";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}

function update_row($username,$counter,$rgbnew,$state)
{
	echo "<pre>update_row($username,$counter,$rgbnew,$state)</pre>\n";
}

function dim($rgb,$trail,$trail_length)
{
	$r = ($rgb >> 16) & 0xFF;
	$g = ($rgb >> 8) & 0xFF;
	$b = $rgb & 0xFF;
	$color_HSV=RGB_TO_HSV ($r, $g, $b);
	$origV=$color_HSV['V'] ;
	$newV=$color_HSV['V'] * (1-(($trail-1)/$trail_length));
	/*echo "<pre>frame,sf =$frame,$sf  trail=$trail origV, newV = $origV, $newV</pre>\n";*/
	$color_HSV['V']=$newV;
	$H=$color_HSV['H'];
	$S=$color_HSV['S'];
	$V=$color_HSV['V'];
	$rgb_val=HSV_TO_RGB ($H, $S, $V);
	return $rgb_val;
}

function advance($x,$y,$frame,$counter)
{
	$forward=array(30,20,10,5,0,5,10,20);
	//$forward=array(40,25,5,0,0,0,5,25);
	$every=array(20,15,10,10,10,10,10,15);
	$backward=array(0,5,10,20,30,20,10,5);
	$r=rand(1,100);
	$mf=($counter%7);
	if($mf>=0 and $mf<=4)
		$xy=getxy($forward,$counter);
	else if($mf>=5 and $mf <=6)
		$xy=getxy($every,$counter);
	else
	$xy=getxy($backward,$counter);
	return ($xy);
}

function getxy($arr,$counter)
{
	$r=rand(1,100);
	$val=0;
	$cnt=count($arr);
	$xy=array(0,0);
	for($i=0;$i<$cnt-1;$i++)
	{
		$val1=$val+$arr[$i+1];
		if($r>=$val and $r <= $val1)
		{
			$xy=vector($i);
			break;
		}
		$xy=vector(7);
		$val=$val1;
	}
	if($r%2==3)
		$xy=array(0,0);
	else if($counter%3==0)
	{
		$xy[0]*=2;
		$xy[1]*=2;
	}
	return $xy;
}

function vector($i)
{
	if ($i==0)
	{
		$x=-1; $y=0;
	}
	if ($i==1)
	{
		$x=-1; $y=-1;
	}
	if ($i==2)
	{
		$x= 0; $y=-1;
	}
	if ($i==3)
	{
		$x= 1; $y=-1;
	}
	if ($i==4)
	{
		$x= 1; $y=0;
	}
	if ($i==5)
	{
		$x= 1; $y=1;
	}
	if ($i==6)
	{
		$x= 0; $y=1;
	}
	if ($i==7)
	{
		$x=-1; $y=1;
	}
	$xy = array($x,$y);
	return ($xy);
}
?>
