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
	echo "<pre>maxFrame=$maxFrame,  maxPixel=$maxPixel</pre>\n";
		$maxFrame=20;
		$maxPixel=50;
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
			$string=$user_pixel=0;
			$delta=($f*$speed);
			if($direction=='left') $delta=-$delta;
			$new_p=$p-1- $delta;
			if($new_p<1) $new_p+=$maxPixel;
			if($new_p%4==1)
				$rgb_val=hexdec("FF0000");
			else
			$rgb_val=hexdec("0000FF");
			$seq_number++;
			//	echo "<pre>f,s,p = $f,$s,$p (p_new=$p, n=$n mod=$m, $maxPixel). H,S,V = $H,$S,$V $hex</pre>\n";
			$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
		fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",
			$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel
			,$s_pixel[$s][$p][0],$s_pixel[$s][$p][1],
			$f,$seq_number));
		}
	}
	$x_dat_base=$base . ".dat";
	$show_frame='n';
	$amperage=array();
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out);
}
