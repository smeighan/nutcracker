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
<? $menu="effect-form";?>
<? require "../conf/menu.php"; ?>
<?php
//
require("read_file.php");
$array_to_save=$_POST;
$array_to_save['OBJECT_NAME']='user_defined';
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
show_array($array_to_save,"Effect Settings");
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
define('CR', "\r");          // carriage return; Mac
define('LF', "\n");          // line feed; Unix
define('CRLF', "\r\n");      // carriage return and line feed; Windows
define('BR', '<br />' . LF); // HTML Break
purge_files();
//echo "<pre>before: $php_program</pre>\n";
$php_program = str_replace(CRLF, LF, $php_program);
$php_program = str_replace(CR, LF, $php_program);
$php_program = str_replace("\\\"", "\"", $php_program);
//echo "<pre>after: $php_program</pre>\n";
$path="workspaces/". $member_id;
ud($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$sparkles,$seq_duration,$param1,$param2,$start_color,$end_color,$php_program,$effect_name,$username);
$target_info=get_info_target($username,$t_dat);
show_array($target_info,'MODEL: ' . $t_dat);
show_elapsed_time($script_start,"Total Elapsed time for this effect:");
$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out); 
// function garland($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$sparkles,$seq_duration,$garland_gap,$garland,$show_frame)
	
function ud($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$sparkles,$seq_duration,$param1,$param2,$start_color,$end_color,$php_program,$effect_name,$username)
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
	$maxFrame=$maxPixel;
	//$maxTrees=6;	// how many tree to draw at one time
	$seq_number=0;
	$window_array = getWindowArray($minStrand,$maxStrand,$window_degrees);
	$include_file = $path . "/$effect_name.inc";
	write_user_defined($php_program,$include_file);
	ob_start();
	include $include_file;
	echo "<pre>include $include_file</pre>\n";
	ob_get_clean();
	for($frame=1;$frame<=$maxFrame;$frame++)
	{
		if($frame>500) exit ("Too many frames in sequence");
		$x_dat = $base . "_d_". $frame . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$frame] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$frame];
		$fh_dat [$frame]= fopen($dat_file[$frame], 'w') or die("can't open file");
		fwrite($fh_dat[$frame],"#    " . $dat_file[$frame] . "\n");
		for($s=1;$s<=$maxStrand;$s++)
			for($p=1;$p<=$maxPixel;$p++)
		{
			if(in_array($s,$window_array)) // Is this strand in our window?, 
			{
				$i = array_search($s,$window_array)+1;
				srand();
				$rgb_val=user_defined($frame,$i,$p,$maxFrame,$maxStrand,$maxPixel,$param1,$param2,$start_color,$end_color);
				//echo "<pre>user_defined($i,$p,$maxStrand,$maxPixel,$frame,$param1)</pre>\n";
				// user_defined(\$frame,\$s,\$p,\$maxFrame,\$maxStrand,\$maxPixel,\$param1,\$param2,$\$start_color,\$end_color)\n\n"));
				$string=$user_pixel=0;
				$xyz=$tree_xyz[$s][$p];
				$seq_number++;
				if($rgb_val <> 0)
					fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number));
			}
		}
		fclose($fh_dat[$frame]);
	}
	$amperage=array();
	$x_dat_base = $base . ".dat";
	$show_frame='N';
	make_gp($arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$script_start,$amperage,$seq_duration,$show_frame);
	echo "</body>";
	echo "</html>";
}
//
//	draws colors similar to a Butterfly Wing
//	
//	Parameters:
//	$x .. Strand number (1 .. $maxStrand). Strand 1 is closest facing street or whatever direction tree is pointed.
//	$y .. Pixel number  (1 .. $maxPixel). Pixel number 1 in Nutcracker normal terms is always the top of the rgb device
//	$maxX .. Max strand seen in rgb device
//	$maxY .. Max Pixel seend in RGB device
//	$offset .. Amount to add to the $radian to cause a color shift. Values like .05 to .1 seem best for each frame
//

function write_user_defined($php_program,$file)
{
	$fh=fopen($file,"w") or die("Unable to open $file for writes\n");
	fwrite($fh,sprintf("<?php\n"));
	fwrite($fh,sprintf("function user_defined(\$frame,\$s,\$p,\$maxFrame,\$maxStrand,\$maxPixel,\$param1,\$param2,\$start_color,\$end_color)\n\n"));
	fwrite($fh,sprintf("{\n\$rgb=hexdec(\"#FFFFFF\");\n"));
		fwrite($fh,sprintf("%s\n",$php_program));
		fwrite($fh,sprintf("	return \$rgb;\n"));
		fwrite($fh,sprintf("}\n"));
	fclose($fh);
	return;
}
/*
* 	$r = ($rgb >> 16) & 0xFF;
$g = ($rgb >> 8) & 0xFF;
$b = $rgb & 0xFF;
$HSL=RGB_TO_HSV ($r, $g, $b);
$rgb_val=HSV_TO_RGB ($H, $S, $V);
*/
?>
