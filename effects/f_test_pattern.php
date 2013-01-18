
<?php

require_once("../effects/read_file.php");
list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
$get=$_GET;
extract($get);
set_time_limit(0);
if(!isset($batch)) $batch=0;
if($batch==0) show_array($get,"$effect_class Effect Settings");
$get['OBJECT_NAME']='plasma';
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
extract($get);

echo "<pre>";
$maxFrame=intval(($seq_duration*1000/$frame_delay)/$speed)+1;
$seq_number=0;
echo "maxFrame=$maxFrame\n";
echo "</pre>\n";
plasma_write($f,$get)


$member_id=get_member_id($username);
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
$x_dat_base=$base . ".dat";
$show_frame='n';
$amperage=array();
make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame);
$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out); 
if($batch==0) elapsed_time($script_start);

function plasma_write($f,$get)
{
	//echo "<pre>function plasma_write(f,get,plasma):plasma_write($f,$get,$plasma)</pre>\n";
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	extract ($get);
	set_time_limit(0);
	ini_set("memory_limit","1024M");
	require_once("../effects/read_file.php");
	//
	//
	$member_id=get_member_id($username);
	extract ($get);
	//
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
	echo "<pre>";
	//	print_r($get);
	$max=-99;
	$display=0;
	for ($s=1;$s<=$maxStrand;$s++)
	{
		if($display==1)		printf ("s=%3d  ",$s);
		for($p=1;$p<=$maxPixel;$p++)
		{
			$x=$s-1;
			$y=$p-1;
			$S=$V=1;
			if(isset($plasma[$x][$y]))
			{
				$H=$plasma[$x][$y]/360;
				$H=$plasma[$x][$y];
				if($H>$max) $max=$H;
			}
			else {
				$H=0;
				$V=0;
			}
			if($display==1)		printf ("%4.1f ",$H);
		}
		if($display==1)			printf ("\n");
	}
	echo "</pre>\n";
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
	//$maxFrame=intval(($seq_duration*1000/$frame_delay)/$speed)+1;
	$seq_number=0;
	//	for($f=1;$f<=$maxFrame;$f++)
	{
		$x_dat = $base . "_d_". $f . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$f] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$f];
		$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
		fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
		$string=$user_pixel=0;
		for ($s=1;$s<=$maxStrand;$s++)
		{
			for($p=1;$p<=$maxPixel;$p++)
			{
				$x=$s-1;
				$y=$p-1;
				$S=$V=1;
				if(isset($plasma[$x][$y]))
				{
					$H=.5;
					if($max>0)
						$H=$plasma[$x][$y]/$max;
					if($H<0) $H=0.0;
					if($H>1.0) $H=1.0;
				}
				else {
					$H=0;
					$V=0;
				}
				$rgb_val = HSV_TO_RGB ($H, $S, $V);
				$seq_number++;
				//	echo "<pre>f,s,p = $f,$s,$p  H,S,V = $H,$S,$V </pre>\n";
				$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
				fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",
				$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel
				,$s_pixel[$s][$p][0],$s_pixel[$s][$p][1],
				$f,$seq_number));
			}
		}
	}
	return $dat_file[$f];
}
