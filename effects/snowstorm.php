<?php
require_once('../conf/auth.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nutcracker: RGB Effects Builder</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="last-modified" content=" 24 Feb 2012 09:57:45 GMT"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
<meta name="robots" content="index,follow"/>
<meta name="googlebot" content="noarchive"/>
<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"/> 
<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Welcome <?php echo $_SESSION['SESS_FIRST_NAME'];?></h1>
<?php $menu="effect-form"; require "../conf/menu.php"; ?>
<?php
//
echo "<pre>\n";
ini_get('max_execution_time'); 
set_time_limit(60*60*8);
ini_get('max_execution_time'); 
echo "</pre>\n";
require("read_file.php");
$array_to_save=$_POST;
$array_to_save['OBJECT_NAME']='snowstorm';
extract ($array_to_save);
$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);
$array_to_save['effect_name']=$effect_name;
$array_to_save['username']=$username;
$frame_delay = $_POST['frame_delay'];
$frame_delay = intval((5+$frame_delay)/10)*10; // frame frame delay to nearest 10ms number_format
$array_to_save['frame_delay']=$frame_delay;
extract ($array_to_save);
save_user_effect($array_to_save);
//show_array($_POST,"_POST");
show_array($array_to_save,"Effect Settings");
//show_array($_SESSION,"_SESSION");
//show_array($_SERVER,"_SERVER");
$path="../targets/". $username;
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
$member_id=get_member_id($username);
$path ="workspaces/$member_id";
$directory=$path;
if (file_exists($directory))
{
	} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}
$base = $user_target . "+" . $effect_name;
$t_dat = $user_target . ".dat";
$xdat = $user_target ."+".  $effect_name . ".dat";
$path="../targets/". $member_id;
$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
//	remove old ong and dat files
$mask = $directory . "/*.png";
//array_map( "unlink", glob( $mask ) );
$mask = $directory . "/*.dat";
//array_map( "unlink", glob( $mask ) );
/*
_POST
username	f
user_target	AA
effect_class	garlands
effect_name	CIRCLE2
window_degrees	180
start_color	#3672FF
end_color	#295BFF
frame_delay	20
sparkles	10
seq_duration	5
submit	Submit Form to create your target model
*/
if(!isset($show_frame)) $show_frame='N';
$path="workspaces/". $member_id;
snowstorm($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$maxSnowflakes,$seq_duration,$show_frame,$start_color,$end_color,$username,$trail_length);
$target_info=get_info_target($username,$t_dat);
show_array($target_info,'MODEL: ' . $t_dat);
show_elapsed_time($script_start,"Total Elapsed time for this effect:");
// function garland($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$sparkles,$seq_duration,$garland_gap,$garland,$show_frame)
	$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out); 

function snowstorm($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$maxSnowflakes,$seq_duration,$show_frame,$start_color,$end_color,$username,$trail_length)
{
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
	echo "<pre>$query0</pre>\n";
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
		if($frame>500) exit ("Too many frames in sequence");
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
			if(in_array($s,$window_array)) // Is this strand in our window?, 
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
	make_gp($arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$script_start,$amperage,$seq_duration,$show_frame);
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
