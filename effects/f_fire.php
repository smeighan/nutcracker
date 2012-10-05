<?php

function f_fire($get)
{
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	extract ($get);
	set_time_limit(0);
	ini_set("memory_limit","512M");
	require_once("../effects/read_file.php");
	//
	//
	$member_id=get_member_id($username);
	if($batch==0) show_array($get,"$effect_class Effect Settings");
	$path="../targets/". $member_id;
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	$t_dat = $user_target . ".dat";
	$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
	$path ="../effects/workspaces/" . $member_id;
	$directory=$path;
	if (file_exists($directory))
	{
		} else {
		if($batch==0) echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$x_dat = $user_target . "~" . $effect_name . ".dat";
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
	$maxFrame=80;
	// $tree_rgb[$strand][$p]=$rgb_val;
	if(empty($seed)) $seed=rand(1,3000);
	srand($seed);
	$window_array = getWindowArray($minStrand,$maxStrand,$window_degrees);
	$base = $user_target . "~" . $effect_name;
	for($s=1;$s<=$maxStrand;$s++)
		for($p=1;$p<=$maxPixel;$p++)
	{
		$buff1[$s][$p]=0;
		$buff2[$s][$p]=0;
	}
	$seq_number=0;
	$s1=5;
	$s2=$maxStrand-5;
	$s1=1;
	$s2=$maxStrand;
	$max_palette=200;
	$i=$max_palette;
	if($batch==0) echo "<pre>Color palette for fire:</pre>\n";
	if($batch==0) echo "<table border=1><tr>";
	for($h=0.1666;$h>=0.0;$h=$h-.001666) // gives 100 hues yellow to red.
	{
		$V=$S=1;
		$rgb_val=HSV_TO_RGB ($h, $S, $V);
		$palette[$i]=$rgb_val;
		$hex=dechex($rgb_val);
		$hex_array[$i]=$hex;
		if($batch==0) echo "<td bgcolor=$hex>$i($hex):$rgb_val</td>";
		if($i%8==0 and $batch==0) echo "</tr><tr>";
		$i--; if($i<0) $i=0;
	}
	for($v=1.0;$v>=0;$v=$v-.01) // gives 100 reds bright to black
	{
		$H=0; $S=1;
		if($v<0) $v=0;
		$rgb_val=HSV_TO_RGB ($H, $S, $v);
		$palette[$i]=$rgb_val;
		$hex=dechex($rgb_val);
		$hex_array[$i]=$hex;
		if($batch==0) echo "<td bgcolor=$hex>$i($hex):$rgb_val</td>";
		if($i%8==0 and $batch==0) echo "</tr><tr>";
		$i--; if($i<0) $i=0;
	}
	if($batch==0) echo "</tr></table>";
	/*print_r($palette);*/
	for($s=$s1;$s<=$s2;$s++)
	{
		$r=rand(100,200);
		$buff1[$s][$maxPixel]=$r;
	}
	/*if($batch==0) echo "Buff1 after seeding\n";*/
	$imax=$i;
	for($frame=1;$frame<=$maxFrame;$frame++)
	{
		$x_dat = $base . "_d_". $frame . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$frame] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$frame];
		//	if($batch==0) echo "<pre>$frame $dat_file[$frame]</pre>\n";
		$fh_dat [$frame]= fopen($dat_file[$frame], 'w') or die("can't open file");
		//$tree_rgb=$zero_tree_rgb;
		$buff2=$buff1;
		$buff1=build_fire($frame,$buff1,$buff2,$maxPixel,$maxStrand);
		/*	if($batch==0) echo "<pre>Frame: $frame\n";	
		print_buff($buff1,$maxPixel,$maxStrand);
		if($batch==0) echo "</pre>\n";*/
		for($p=$maxPixel;$p>=1;$p--)
		{
			for($s=1;$s<=$maxStrand;$s++)
			{
			//	if(in_array($s,$window_array)) // Is this strand in our window?, 
				{
					$xyz=$tree_xyz[$s][$p];
					$index=$buff1[$s][$p]+0;
					/*if($index<1) $index=1;
					if($index>$imax) $index=$imax; */
					if($index==0) $index=1;
					if($index>$max_palette) $index=$max_palette;
					$rgb_val=$palette[$index];
					$tree_rgb[$s][$p]=$rgb_val;
					$string=$user_pixel=0;
					$seq_number++;
					fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",
					$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, 
					$user_pixel,$strand_pixel[$s][$p][0],
					$strand_pixel[$s][$p][1],$frame,$seq_number));
				}
			}
		}
	}
	for ($frame=1;$frame<=$maxFrame;$frame++)
	{
		//	if($batch==0) echo "<pre>closing $fh_dat[$frame]</pre>\n";
		fclose($fh_dat[$frame]);
	}
	$x_dat_base="fire";
	$amperage=array();
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,
	$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame);
	//	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,
	//	$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out);
	// $filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out); 
	// ($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in_secs,$fade_out_secs)
	}

function print_buff($buff1,$maxPixel,$maxStrand)
{
	for($s=1;$s<=$maxStrand;$s++)
	{
		for($p=1;$p<=$maxPixel;$p++)
		{
			printf("%4d ",$buff1[$s][$p]);
		}
		printf ("\n");
	}
}

function build_fire($frame,$buff1,$buff2,$maxPixel,$maxStrand)
{
	$step=intval(255/$maxPixel);
	for ($s=1;$s<=$maxStrand;$s++)
	{
		$r=rand(100,150);
		if($s%2==0) $r=rand(190,200);
		$buff1[$s][$maxPixel]=$r;
		$buff1[$s][$maxPixel-1]=$r;
	}
	//
	for($p=$maxPixel;$p>=1;$p--)
	{
		for ($s=1;$s<=$maxStrand;$s++)
		{
			$pdx=$maxPixel-$p;
			$pdx=$v1=$v2=$v3=$v4=0;
			if($p<$maxPixel)
			{
				$method=3;
				if($method==1)
				{
					$v1=$buff1 [$s-1][$p+1];
					$v2=$buff1 [$s+1][$p+1];
					$v3=$buff1 [$s]  [$p+1];
					$v4=$buff1 [$s]  [$p]  ;
				}
				//
				if($method==2)
				{
					$v1=$buff1 [$s-1][$p];
					$v2=$buff1 [$s+1][$p];
					$v3=$buff1 [$s]  [$p+1];
					$v4=$buff1 [$s]  [$p-1] ;
				}
				if($method==3)
				{
					if(isset($buff1 [$s-1][$p+1])) $v1=$buff1 [$s-1][$p+1];
					if(isset($buff1 [$s+1][$p+1])) $v2=$buff1 [$s+1][$p+1];
					$v3=$buff1 [$s] [$p+1];
					$v4=$buff1 [$s] [$p+1];
				}
			}
			$n=0;
			if($v1<0) $v1=0;
			if($v2<0) $v2=0;
			if($v3<0) $v3=0;
			if($v4<0) $v4=0;
			if($v1>0) $n++;
			if($v2>0) $n++;
			if($v3>0) $n++;
			if($v4>0) $n++;
			$buff2[$s][$p]=$buff1[$s][$p];
			if($n>0  )
			{
				$r2=rand(1,100);
				if($r2<20)
					$new_index=intval(($v1+$v2+$v3+$v4)/$n)+$step;
				else
				$new_index=intval(($v1+$v2+$v3+$v4)/$n)-$step;
				if($new_index<0) $new_index=0;
				if($new_index>1) $buff2[$s][$p]=$new_index;
			}
		}
		$buff1=$buff2;
	}
	return $buff2;
}
//

function get_rgbval ($index,$palette)
{
	$rgb_array = $palette[$index];
	$rgb_val = $rgb_array[0]<<16 +$rgb_array[1]<<8 + $rgb_array[2];
	/*if($batch==0) echo "<pre>";
	if($batch==0) echo "index=$index, rgb_val=$rgb_val";
	print_r($rgb_array);
	if($batch==0) echo "</pre>\n";*/
	return $rgb_val;
}
