<?php
//
// (23:11:11) oldmanfathertime1000: http://www.youtube.com/watch?v=j​N2fhFSmSP4

function f_countdown($get){
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	//
	if(!isset($get['fade_3d']))   $get['fade_3d']="N";
	if(!isset($get['fade_in']))   $get['fade_in']="0";
	if(!isset($get['fade_out']))  $get['fade_out']="0";
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	extract ($get);
	require_once("../conf/setup.php"); // override some apache caching.
	//   for ($i = 0; $i < ob_get_level(); $i++)
	{
		ob_end_flush();
	}
	//   ob_implicit_flush(1);
	require_once("../effects/read_file.php");
	//
	//
	if(!isset($batch)) $batch=0;
	//	
	audit($username,"f_countdown","$effect_name,$batch,$seq_duration");
	//
	$get['batch']=$batch;
	$member_id=get_member_id($username);
	$get['OBJECT_NAME']='countdown';
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
	$get['frame_delay']=$f_delay;
	if($batch==0) show_array($get,"$effect_class Effect Settings");
	$path="../targets/". $member_id;
	$t_dat = $user_target . ".dat";
	$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
	$member_id=get_member_id($username);
	$path ="../effects/workspaces/" . $member_id;
	$directory=$path;
	if(file_exists($directory)){
	}else{
		if($batch==0) echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$x_dat = $user_target . "~" . $effect_name . ".dat";
	$base = $user_target . "~" . $effect_name;
	$get['arr']=$arr;
	$get['x_dat']=$x_dat;
	$get['t_dat']=$t_dat;
	$get['base']=$base;
	$get['path']=$path;
	$get['f_delay']=$f_delay;
	$dir="workspaces";
	//
	//
	//$dat_file_array=Countdown($get);
	//
	//
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
	if($fade_3d == null or !isset($fade_3d)) $fade_3d="N";
	//
	$fade_3d=strtoupper($fade_3d);
	$get['fade_3d']=$fade_3d;
	extract ($get);
	//show_elapsed_time($script_start,"Creating  Effect, countdown class:");
	if($maxStrand<1)$maxStrand=1;
	$pixelPerStrand=$maxPixel/$maxStrand;
	//
	if(!isset($window_degrees)) $window_degrees=360;
	if($window_degrees<1) $window_degrees=360;
	//$deltaStrands= ($maxStrand/ $number_countdown);
	$line= 0;
	$rgb=255;
	$x_dat_base = $base . ".dat"; // for countdown we will use a dat filename starting "S_" and the tree model
	$dat_file_array=array();
	$r=115;
	$g =115;
	$b = 120; 
	//if($number_rotations<1) $number_rotations=1;
	$seq_number=0;
	$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	//flush();
	//
	$f=1;
	$amperage=array();
	//
	//
	$maxFrames = $seq_duration*1000/$frame_delay;
	echo "<pre>$maxFrames = $seq_duration*1000/$frame_delay;</pre>\n";
	if($batch==0) echo "<pre>maxPixel=$maxPixel,maxStrand=$maxStrand,maxFrames = $maxFrames </pre>\n";
	//
	//	create the Countdown array
	//
	/*if($batch==0)
	{
	echo "<pre>create_sparkles($sparkles,$maxStrand,$maxPixel);\n";
	print_r($sparkles_array);
	echo "</pre>\n";
	}
	*/
	$full_path = "../effects/dotmatrix";
	$fh = fopen($full_path, 'r') or die("can't open file $full_path");
	while(!feof($fh)){
		// load up the dotmatrix file into the $letter array
		$line = fgets($fh);
		//	echo "<pre>$line</pre>\n";
		if(substr($line,0,1) == "="){
			$char = substr($line,1,1);
			$row=0;
			for($i=1;$i<=5;$i++){
				for($j=1;$j<=9;$j++){
					$letter[$j][$i]=0;
				}
			}
		}
		else{
			$row++;
			$strLength = strlen($line);
			for($i=0;$i<=$strLength;$i++){
				$val=0;
				if(substr($line,$i,1)=="x")					$val=1;
				$letter[$row][$i+1]=$val;
				$c[$char]=$letter;
			}
		}
		//$tok=preg_split("/ +/", $line);
	}
	/*echo "<pre>";
	//print_r($letter);
	print_r($c['3']);
	echo "</pre>\n";*/
	//
	$end_seconds=$f=0;
	$frames_per_second = intval(1000/$frame_delay);
	//$frames_per_second=2;
	if($frames_per_second<1) $frames_per_second=1;
	for($seconds=$start_seconds;$seconds>$end_seconds;$seconds--){
		if($batch==0 and $seconds<-1) display_Countdown($Countdown,$maxStrand,$maxPixel);
		for($fps=1;$fps<=$frames_per_second;$fps++){
			$f++;
			$f_ratio = $fps/$frames_per_second;
			$Countdown=create_Countdown($seconds,$f_ratio,$c,$letter,$get,$arr);
			$x_dat = $base . "_d_". $f . ".dat"; // for countdown we will use a dat filename starting "S_" and the tree model
			$dat_file[$f] = $path . "/" .  $x_dat;
			$dat_file_array[]=$dat_file[$f];
			$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
			fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
			//if($batch==0) echo "<pre>f=$f; deltaStrands=$deltaStrands for( ns= minStrand; ns<= number_countdown; ns++) = for( $ns= $minStrand; $ns<= $number_countdown; $ns++)</pre>\n";
			for($s=1;$s<=$maxStrand;$s++){
				for($p=1;$p<=$maxPixel;$p++){
					$rgb_val=$Countdown[$s][$p]; // really rotate
					//	$rgb_val=$Countdown[$s][$p]; // this will make all images static, no rotation
					//if($batch==0) echo "<pre>rgb_val=Countdown[s][p] $rgb_val=Countdown[$s][$p];</pre>\n";
					$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
					$tree_rgb[$s][$p]=$rgb_val;
					//	$xyz=$tree_xyz[$s][$p];
					$seq_number++;
					//	$rgb_val=sparkles($sparkles,$f1_rgb_val); // if sparkles>0, then rgb_val will be changed.
					$hex=dechex($rgb_val);
					$string=$user_pixel=0;
					//	$sparkles_array[$s][$p]=$sparkles_array[$s][$p]+0;
					if($s<=$maxStrand){
						fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$f,$seq_number));
						//	printf ("<pre>f=%d t1 %4d(%4d) %4d %9.3f %9.3f %9.3f %d %d %d %d %d %d %d</pre>\n",$f,$s,$new_s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$new_s][$p][0],$strand_pixel[$new_s][$p][1],$f,$seq_number);
					}
				}
			}
			if(isset($fh_dat[$f]))				fclose($fh_dat[$f]);
		}
	}
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	//
	$target_info=get_info_target($username,$t_dat);
	//show_array($target_info,'MODEL: ' . $t_dat);
	if($batch==0) $description ="Total Elapsed time for this effect:";
	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
	//if($description = 'Total Elapsed time for this effect:')
	$x_dat_base=$base . ".dat";
	$show_frame='n';
	$amperage=array();
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out); 
	if($batch==0) elapsed_time($script_start);
}

function create_Countdown($seconds,$f_ratio,$c,$letter,$get,$arr){
	extract ($get);
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
	if($fade_3d == null or !isset($fade_3d)) $fade_3d="N";
	//
	$get['maxStrand']=$maxStrand;
	$get['maxPixel']=$maxPixel;
	$fade_3d=strtoupper($fade_3d);
	//
	//
	/*$color1=hexdec("#FF0000");
	$color2=hexdec("#00FF00");
	$color3=hexdec("#FFFF00");*/
	for($s=1;$s<=$maxStrand;$s++){
		for($p=1;$p<=$maxPixel;$p++){
			$Countdown[$s][$p]=hexdec($color2);
		}
	}
	$d10 = chr(intval($seconds/10)+48);
	$d1  = chr(intval ($seconds - $d10*10)+48);
	//echo "<pre>  create_Countdown($seconds,$d10,$d1)</pre>\n";
	$start_pixel=$maxPixel/2;
	$end_pixel=$start_pixel+8;
	$start_strand=intval(($maxStrand-12)/2);
	if($start_strand<1) $start_strand=1;
	$end_strand=$start_strand+12;
	for($digits=1;$digits<=2;$digits++){
		if($digits==1){
			$letter = $c[$d1];
			$s1 = $start_strand+6;
			$s2= $start_strand+12;
			$dig=$d1;
		}
		elseif($digits==2){
			$letter = $c[$d10];
			$s1 = $start_strand+0;
			$s2= $start_strand+6;
			$dig=$d10;
		}
		/*echo "<pre>seconds=$seconds, digits=$digits, dig=$dig\n";
		if($seconds<4) print_r($letter);
		echo "</pre>\n";*/
		for($s=$s1;$s<=$s2;$s++){
			for($p=$start_pixel;$p<=$end_pixel;$p++){
				$i=$s-$s1+1;
				$j=$p-$start_pixel+1;
				$V=1.0 - $f_ratio;
				$rgb_val = hexdec($color1);
				$HSV=RGBVAL_TO_HSV($rgb_val);
				$H=$HSV['H'];
				$S=$HSV['S'];
				$V=$HSV['V'];
				if($fade_3d=='y' or $fade_3d=='Y') $V=1.0 - ($f_ratio/1.2);
				$rgb_val=HSV_TO_RGB ($H, $S, $V);
				if($letter[$j][$i]==1) $Countdown[$s][$p]=$rgb_val;
			}
		}
	}
	return $Countdown;
}

if(!function_exists('cr2hex')){
	function cr2hex($cr){
		// the usual HTML format, #rrggbb
		return '#'.str_pad(strtoupper(dechex(bgr2rgb($cr))), 6, '0', STR_PAD_LEFT);
	}
}
function 	display_Countdown($Countdown,$maxStrand,$maxPixel){
	/*if($batch==0) echo "<pre>";
	print_r($Countdown);
	echo "</pre>\n";*/
	echo "<h3>Image of Countdown before it gets replicated and rotated</h3>";
	echo "<table border=1>";
	for($p=1;$p<=$maxPixel;$p++){
		echo "<tr><td>P$p</td>";
		for($s=1;$s<=$maxStrand;$s++){
			$rgb_val=$Countdown[$s][$p];
			$hex= '#'.str_pad(strtoupper(dechex($rgb_val)), 6, '0', STR_PAD_LEFT);
			echo "<td bgcolor=\"$hex\">&nbsp;</td>";
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
}
