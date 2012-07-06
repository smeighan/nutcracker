<?php

require("read_file.php");

$target_model="ZZ_ZZ";
$target_model="SGMEG48";
$t_dat = $target_model . ".dat";
$effect_name="DOTS1";
$username='f';
$username='Steve Gase';


// t1(48,207,60,210);
$path="../targets/". $username;

list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;





$elapsed_time = round($script_end - $script_start, 5);


$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5);
echo "Elapsed time = $elapsed_time\n";

$minStrand =$arr[0];  // lowest strand seen on target
$minPixel  =$arr[1];  // lowest pixel seen on skeleton
$maxStrand =$arr[2];  // highest strand seen on target
$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
$maxI      =$arr[4];  // maximum number of pixels in target
$tree_rgb  =$arr[5];
$tree_xyz  =$arr[6];



$path ="workspaces/" . $username;
$directory=$path;
if (file_exists($directory)) {
} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}

$x_dat = $target_model . "+" . $effect_name . ".dat";

points($arr,$path,$t_dat,$x_dat,$username);
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5);
echo "\n\nElapsed time = $elapsed_time\n";



function points($arr,$path,$t_dat,$x_dat,$username)
{
	require("color_hues.php");

	// $arr = array($min_strand,$min_pixel,$max_strand,$max_pixel,$i,$tree_rgb,$tree_xyz,$file,$min_max);
	$minStrand =$arr[0];  // lowest strand seen on target
	$minPixel  =$arr[1];  // lowest pixel seen on skeleton
	$maxStrand =$arr[2];  // highest strand seen on target
	$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
	$maxI      =$arr[4];  // maximum number of pixels in target
	$tree_rgb  =$arr[5];
	$tree_xyz  =$arr[6];
	$file      =$arr[7];
	$min_max   =$arr[8];
	srand(time());
	$maxLoop=60;
	$deltaH = (RED - ORANGE)/$maxPixel;
	$H=RED;


	// go thru entire tree array. soem random times set a rgb > 0. increment it, each iteration
	for($loop=1;$loop<=$maxLoop;$loop++)
	{
		for($s=1;$s<=$maxStrand;$s++)
		{
			for($p=1;$p<=$maxPixel;$p++)
			{
				$tree_rgb[$s][$p]=0;
				$rnd= rand() % 9;
				if($rnd<3 and 	$tree_rgb[$s][$p]==0) 
					$tree_rgb[$s][$p]++;
	/*
				if($rnd<3 and 	$tree_rgb[$s][$p]==0) 
					$tree_rgb[$s][$p]++;
				else if($tree_rgb[$s][$p]>0 and $tree_rgb[$s][$p]<=9)
					$tree_rgb[$s][$p]++;
				else  if($tree_rgb[$s][$p]>=10)
					$tree_rgb[$s][$p]--;
				else if($tree_rgb[$s][$p]<0)
					$tree_rgb[$s][$p]=0;
	 */
			}
		}
		$tokens=explode(".",$x_dat);
		$base=$tokens[0];
		$dat_file = $path . "/" . $base . "_r_" .  $loop . ".dat";
		$dat_file_array[] = $dat_file;
		//echo "<pre>dat_file = $dat_file</pre>\n";
		$fh_dat = fopen($dat_file, 'w') or die("can't open file");
		fwrite($fh_dat,"# Loop $loop   $dat_file\n");

		for($s=1;$s<=$maxStrand;$s++)
		{
			for($p=1;$p<=$maxPixel;$p++)
			{
				if($tree_rgb[$s][$p]>0) 
				{
					$color_assignment=2;
					if($color_assignment==1)
					{
						$rnd= rand() % 2;
						if($rnd==0)
							$V = 1.0;
						else
							$V = 0.5;

						$H=RED;
						$S=0;  // make color white
					}
					else
					{
						$H =  RED - ($p * $deltaH ) - ($s/100);
						$S = 1.0;
						$rnd= rand() % 2;
						if($rnd==0)
							$V = 1.0;
						else
							$V = 0.7;
					}

					$rgb_val=HSV_TO_RGB ($H, $S, $V);
					write_dat("dot",$fh_dat,$arr,$s,$p,$rgb_val);
				}
			}
		}
		fclose($fh_dat);
	}
	make_gp($arr,$path,$maxLoop,$x_dat,$t_dat,$dat_file_array,$min_max,$username);
}






?>

