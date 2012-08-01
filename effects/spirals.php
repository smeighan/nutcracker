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
//
require("read_file.php");
$username=$_SESSION['SESS_LOGIN'];
$member_id=$_SESSION['SESS_MEMBER_ID'];
echo "<h2>Nutcracker: RGB Effects Builder for user $username<br/>
On this page you build an animation of the spiral class and create an animated GIF</h2>"; 
echo "<pre>\n";
ini_get('max_execution_time'); 
set_time_limit(250);
ini_get('max_execution_time'); 
echo "</pre>\n";
//show_array($_POST,"_POST");
//show_array($_SERVER,"_SERVER");
//show_array($_SESSION,"_SESSION");
///*
/*
SESSION
Array
(
[SESS_MEMBER_ID] => 2
[SESS_FIRST_NAME] => sean
[SESS_LAST_NAME] => MEIGHAN
[SESS_LOGIN] => f
)
	*/ 
$array_to_save=$_POST;
$array_to_save['OBJECT_NAME']='spirals';
extract ($array_to_save);
$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);
$array_to_save['effect_name']=$effect_name;
$array_to_save['username']=$username;
if(!isset($show_frame)) $show_frame='N';
$array_to_save['show_frame']=$show_frame;
$frame_delay = $_POST['frame_delay'];
$frame_delay = intval((5+$frame_delay)/10)*10; // frame frame delay to nearest 10ms number_format
$array_to_save['frame_delay']=$frame_delay;
extract ($array_to_save);
save_user_effect($array_to_save);
show_array($array_to_save,"Effect Settings");
$path="../targets/". $member_id;
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
$t_dat = $user_target . ".dat";
$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
echo "<pre>arr=read_file($t_dat,$path);</pre>\n";
$member_id=get_member_id($username);
$path ="workspaces/" . $member_id;
$directory=$path;
if (file_exists($directory))
{
	} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}
$x_dat = $user_target . "+" . $effect_name . ".dat";

$base = $user_target . "+" . $effect_name;
spiral($arr,$path,$t_dat,$number_spirals,$number_rotations,$spiral_thickness,$base,$start_color,$end_color,$direction,$frame_delay,$sparkles,$window_degrees,$script_start,$use_background,$background_color,$handiness,$username,$seq_duration,$show_frame,$effect_type); 
$target_info=get_info_target($username,$t_dat);
//show_array($target_info,'MODEL: ' . $t_dat);
$description ="Total Elapsed time for this effect:";
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
//if($description = 'Total Elapsed time for this effect:')
	printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration); 

function spiral($arr,$path,$t_dat,$numberSpirals,$numberRotations,$spiralThickness,$base,$start_color,$end_color,$direction,$frame_delay,$sparkles,$window_degrees,$script_start,$use_background,$background_color,$handiness,$username,$seq_duration,$show_frame,$effect_type)
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
	echo "<pre>t_dat=$t_dat</pre>";
	show_elapsed_time($script_start,"Creating  Effect, spirals class:");
	if($maxStrand<1)$maxStrand=1;
	$pixelPerStrand=$maxPixel/$maxStrand;
	//if( $numberStrands<1)  $numberStrands=1;
	$deltaStrands= $maxStrand/ $numberSpirals;
	$line= 0;
	$rgb=255;
	$x_dat_base = $base . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
	$dat_file_array=array();
	for ($frame=1;$frame<=$maxStrand;$frame++)
	{
		$x_dat = $base . "_d_". $frame . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$frame] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$frame];
		$fh_dat [$frame]= fopen($dat_file[$frame], 'w') or die("can't open file");
		fwrite($fh_dat[$frame],"#    " . $dat_file[$frame] . "\n");
	}
	$r=115;
	$g =115;
	$b = 120;
	//for( $p= $minPixel; $p<= $maxPixel; $p++)
		$maxLoop = $maxStrand*$numberRotations;
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
	//show_elapsed_time($script_start,"Start     delete_effects:");
	//delete_effects($username,$t_dat);
	//show_elapsed_time($script_start,"Fini      delete_effects:");
	//flush();
	//
	$frame=1;
	$amperage=array();
	for( $l= 1; $l<= $maxLoop; $l++)
	{
		$p= intval(1+ ($l-1)* $deltaPixel);
		for( $ns= $minStrand; $ns<= $numberSpirals; $ns++)
		{
			$line++;
			$p_to_add=$p;
			if($effect_type=='v' or $effect_type=='V') $p_to_add=0;
			if(strtoupper($handiness)=="R")
			{
				$strand_base=intval( ($ns-1)*$deltaStrands-$p_to_add);
			}
			else
			{
				$strand_base=intval( ($ns-1)*$deltaStrands+$p_to_add);
			}
			//	echo "<pre>,loop=$l, ns=$ns, strand_base=$strand_base</pre>\n";
			for($thick=1;$thick<=$spiralThickness;$thick++)
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
				if ( $p<$minPixel ) $p+=$maxPixel;
				if (  $p>$maxPixel) $p-=$maxPixel;
				if($strand<1) $strand=1;
				if($p<1) $p=1;
				if($p==null)
					echo "<pre>pixel is null, p=[$p]</pre>";
				else if($tree_xyz[$strand][$p]==null)
				{
					echo "<pre>Null entry for [$strand,$p] =$tree_xyz[$strand][$p]</pre>\n";
				}
				$xyz=$tree_xyz[$strand][$p]; // get x,y,z location from the model.
				if($ns==1 and  $numberSpirals<=3)
				{
					$H=RED;  // RED band
					$S=$V=1;
					// new way
					$start_dec = hexdec($start_color);
					$rgb = $start_dec;
					$r = ($rgb >> 16) & 0xFF;
					$g = ($rgb >> 8) & 0xFF;
					$b = $rgb & 0xFF;
					$HSL=RGB_TO_HSV ($r, $g, $b);
					$H=$HSL['H']; 
					$S=$HSL['S']; 
					$V=$HSL['V'];
				}
				else if($ns==2 and $numberSpirals<=3)
				{
					$H=RED; // WHITE
					$S=0; $V=1;// becase $S is zero, the color is white no matter what the hue
					$end_dec = hexdec($end_color);
					$rgb = $end_dec;
					$r = ($rgb >> 16) & 0xFF;
					$g = ($rgb >> 8) & 0xFF;
					$b = $rgb & 0xFF;
					$HSL=RGB_TO_HSV ($r, $g, $b);
					$H=$HSL['H']; 
					$S=$HSL['S']; 
					$V=$HSL['V'];
				}
				else  if($ns==3 and $numberSpirals<=3)  // orig: else  if($ns==3 and $numberSpirals<=3)
				{
					$H=BLUE;
					$S=1; $V=1;// 
				}
				else
				{
					$color_HSV=color_picker($p,$maxPixel,$numberSpirals,$start_color,$end_color);
					$H=$color_HSV['H'];
					$S=$color_HSV['S'];
					$V=$color_HSV['V'];
					//		echo "<pre>$strand,$p start,end=$start_color,$end_color  HSV=$H,$S,$V</pre>\n";
				}
				$MARKER=0;
				if($MARKER==1)
				{
					if($strand==1) // mark strand 1
					{
						$H=GREEN;
						$S=$V=1;
					}
				}
				$rgb_val=HSV_TO_RGB ($H, $S, $V);
				$frame1_rgb_val=$rgb_val;
				$rgb_val=sparkles($sparkles,$frame1_rgb_val); // if sparkles>0, then rgb_val will be changed.
				$tree_rgb[$strand][$p]=$rgb_val;
				$seq_number++;
				$frame=1;
				if(in_array($strand,$window_array)) // Is this strand in our window?, If yes, then we output lines to the dat file
				{
					if($rgb_val==0 and $use_background=='Y')
					{
						$rgb_val=hexdec($background_color);
						echo "<pre>$rgb_val=hexdec($background_color);</pre>\n";
					}
					if($rgb_val<0 or $rgb_val>0)
					{
						//$amperage[$frame][$strand] += $V*0.060; // assume 29ma for pixels tobe full on
						$string=$user_pixel=0;
						fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$strand,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$strand][$p][0],$strand_pixel[$strand][$p][1],$frame,$seq_number));
					}
				}
				//insert_effects($username,$t_dat,$strand,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$frame,$seq_number);
				for($frame=2;$frame<=$maxStrand;$frame++)
				{
					if($direction=="ccw")
					{
						$new_s = $strand+($frame-1); // CCW
					}
					else
					{
						$new_s = $strand-($frame-1); // CW
					}
					if($new_s>$maxStrand) $new_s -= $maxStrand;
					if($new_s<$minStrand) $new_s += $maxStrand;
					$xyz=$tree_xyz[$new_s][$p];
					$seq_number++;
					$rgb_val=sparkles($sparkles,$frame1_rgb_val); // if sparkles>0, then rgb_val will be changed.
					$tree_rgb[$strand][$p]=$rgb_val;
					if(in_array($new_s,$window_array)) // Is this strand in our window?, If yes, then we output lines to the dat file
					{
						if($rgb_val==0 and $use_background=='Y')
						{
							$rgb_val=hexdec($background_color);
						}
						if($rgb_val<0 or $rgb_val>0)
						{
							//$amperage[$frame][$new_s] += $V*0.060; // assume 29ma for pixels tobe full on
							$string=$user_pixel=0;
							fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$strand,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$new_s][$p][0],$strand_pixel[$new_s][$p][1],$frame,$seq_number));
						}
					}
				}
			}
		}
		/*
		for($s=1;$s<=$maxStrand;$s)
			if(in_array($s,$window_array)) // Is this strand in our window?, If yes, then we output lines to the dat file
		{
			for($p=1;$p<=$maxPixel;$p++)
			{
				$rgb_val=$tree_rgb[$s][$p];
				if($rgb_val<1 and strtoupper($use_background)=='Y')
				{
					$rgb_val=hexdec($background_color);
					fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$strand,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$new_s][$p][0],$strand_pixel[$new_s][$p][1],$frame,$seq_number));
				}
			}
		}
		*/
	}
	for ($frame=1;$frame<=$maxStrand;$frame++)
	{
		fclose($fh_dat[$frame]);
	}
	show_elapsed_time($script_start,"Finished  Effect, spirals class:");
	make_gp($arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$script_start,$amperage,$seq_duration,$show_frame);
	echo "<pre>make_gp($arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$script_start,$amperage,$seq_duration,$show_frame)</pre>\n";
	?>
	<br/>
	<br/>
	<ul>
	<li> <a href="../index.html">Home</a> 
	<li><a href="../login/member-index.php">Target Generator</a> 
	<li> <a href="effect-form.php">Effects Generator</a> 
	<li> <a href="../login/logout.php">Logout</a>
	</ul>
	<br/>
	</body>
	</html>
	<?php 
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
	$result=mysql_query($query) or die ("Error on $query");
	mysql_close();
}

function insert_effects($username,$model_name,$strand,$pixel,$x,$y,$z,$rgb_val,$frame,$seq_number)
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
	($seq_number,'$username','$model_base_name',$strand,$pixel,$x,$y,$z,$rgb_val,$frame)";
	//echo "<pre>insert_effects: query=$query</pre>\n";
	mysql_query($query) or die ("Error on $query");
	mysql_close();
}
