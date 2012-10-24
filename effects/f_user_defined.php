<?php

function f_user_defined($get)
{
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
	if(!defined('CR')) define('CR', "\r");          // carriage return; Mac
	if(!defined('LF')) define('LF', "\n");          // line feed; Unix
	if(!defined('CRLF')) define('CRLF', "\r\n");      // carriage return and line feed; Windows
	if(!defined('BR')) define('BR', '<br />' . LF); // HTML Break
	//echo "<pre>before: $php_program</pre>\n";
	$php_program = str_replace(CRLF, LF, $php_program);
	$php_program = str_replace(CR, LF, $php_program);
	$php_program = str_replace("\\\"", "\"", $php_program);
	//echo "<pre>after: $php_program</pre>\n";
	$path="../effects/workspaces/". $member_id;
	srand(time());
	$maxFrame=$maxPixel;
	$maxFrame=intval(($seq_duration*1000/$frame_delay))+1;
	//$maxTrees=6;	// how many tree to draw at one time
	$seq_number=0;
	$window_array = getWindowArray($minStrand,$maxStrand,$window_degrees);
	$include_file = $path . "/$effect_name.inc";
	write_user_functon($php_program,$include_file);
	ob_start();
	echo "<pre>include $include_file</pre>\n";
	require_once $include_file;
	ob_get_clean();
	for($frame=1;$frame<=$maxFrame;$frame++)
	{
		if($frame>5000) exit ("Too many frames in sequence");
		$x_dat = $base . "_d_". $frame . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$frame] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$frame];
		$fh_dat [$frame]= fopen($dat_file[$frame], 'w') or die("can't open file");
		fwrite($fh_dat[$frame],"#    " . $dat_file[$frame] . "\n");
		for($s=1;$s<=$maxStrand;$s++)
			for($p=1;$p<=$maxPixel;$p++)
		{
			//		if(in_array($s,$window_array)) // Is this strand in our window?, 
			{
				$i = array_search($s,$window_array)+1;
				srand();
				$rgb_val=user_functon($frame,$i,$p,$maxFrame,$maxStrand,$maxPixel,$param1,$param2,$start_color,$end_color);
				//echo "<pre>user_functon($i,$p,$maxStrand,$maxPixel,$frame,$param1)</pre>\n";
				// user_functon(\$frame,\$s,\$p,\$maxFrame,\$maxStrand,\$maxPixel,\$param1,\$param2,$\$start_color,\$end_color)\n\n"));
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
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out); 
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

function write_user_functon($php_program,$file)
{
	$fh=fopen($file,"w") or die("Unable to open $file for writes\n");
	fwrite($fh,sprintf("<?php\n"));
	fwrite($fh,sprintf("if(!defined('_USER_FUNCTION_'))\n"));
	fwrite($fh,sprintf("{\n"));
		fwrite($fh,sprintf("define('_USER_FUNCTION_', 1);\n"));
		fwrite($fh,sprintf("function user_functon(\$frame,\$s,\$p,\$maxFrame,\$maxStrand,\$maxPixel,\$param1,\$param2,\$start_color,\$end_color)\n\n"));
		fwrite($fh,sprintf("{\n\$rgb=hexdec(\"#FFFFFF\");\n"));
			fwrite($fh,sprintf("%s\n",$php_program));
			fwrite($fh,sprintf("	return \$rgb;\n"));
			fwrite($fh,sprintf("}\n"));
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
