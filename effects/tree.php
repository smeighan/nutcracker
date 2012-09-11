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

echo "<h2>Nutcracker: RGB Effects Builder for user $username<br/>
	On this page you build an animation of the garland class and crate an animated GIF</h2>"; 
//
require("read_file.php");



show_array($_GET,"_GET");
///*
/*
Array

(
    [username] => f
    [user_target] => MT
    [effect_class] => garlands
    [effect_name] => 44
    [number_garlands] => 4
    [number_rotations] => 2
    [garland_thickness] => 1
    [start_color] => #26FF35
    [end_color] => #2E35FF
    [frame_delay] => 22
    [direction] => 2
    [submit] => Submit Form to create your target model
)



 */ 


extract($_GET);


$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$direction = strtolower($direction);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);

save_user_effect($_GET);

$path="../targets/". $username;

list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5);


$member_id=get_member_id($username);
$path ="workspaces/$member_id";
$directory=$path;
if (file_exists($directory)) {
} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}

$base = $user_target . "~" . $effect_name;

$t_dat = $user_target . ".dat";
$xdat = $user_target ."~".  $effect_name . ".dat";

$path="../targets/". $member_id;


$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array



//	remove old ong and dat files
$mask = $directory . "/*.png";
//array_map( "unlink", glob( $mask ) );

$mask = $directory . "/*.dat";
//array_map( "unlink", glob( $mask ) );


/*
_GET
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

purge_files();


$path="workspaces/". $member_id;

tree($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$sparkles,$seq_duration,$show_frame,$maxTrees,$maxPhase,$random_colours);


$target_info=get_info_target($username,$t_dat);
show_array($target_info,'MODEL: ' . $t_dat);

show_elapsed_time($script_start,"Total Elapsed time for this effect:");


// function garland($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$sparkles,$seq_duration,$garland_gap,$garland,$show_frame)

function tree($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$sparkles,$seq_duration,$show_frame,$maxTrees,$maxPhase,$random_colours)
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
	$maxFrame=80;
	//$maxTrees=6;	// how many tree to draw at one time
	$seq_number=0;
	$window_array = getWindowArray($minStrand,$maxStrand,$window_degrees);


	//$maxPhase=5;
	$depth=$maxPhase;	// depth of tree trail in pixles
	$itree=0;
	$orig_tree_rgb=$tree_rgb;
	// go thru entire tree array. soem random times set a rgb > 0. increment it, each iteration
	for($frame=1;$frame<=$maxFrame;$frame++)
	{
		if($frame>500) exit ("Too many frames in sequence");

		$x_dat = $base . "_d_". $frame . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$frame] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$frame];
		$fh_dat [$frame]= fopen($dat_file[$frame], 'w') or die("can't open file");
		fwrite($fh_dat[$frame],"#    " . $dat_file[$frame] . "\n");

		$tree_type=2;
		if($tree_type==1)
		{
			for($m=1;$m<=$maxTrees;$m++)	// create a pile of random tree.
			{
				$s= rand($minStrand,$maxStrand);
				$p= rand($minPixel,$maxPixel-$depth);
				if($s<1) $s=1;
				if($p<1) $p=1;
				$itree++;
				if($tree_array[$itree]['rgb']==0)
					$tree_array[$itree]['strand']=$s;
				$tree_array[$itree]['pixel']=$p;
				$tree_array[$itree]['frame']=$frame;
				$tree_array[$itree]['phase']=0;
				$tree_array[$itree]['rgb']=$start_color;
				$tree_array[$itree]['sparkle']=0;
			}
		}
		else if ($tree_type==2)
		{
			if($maxPhase<0) $maxPhase=$maxPixel/2;
			$maxBranches=intval($maxPixel/$maxPhase);
			//echo "<pre>frane=$frame maxBranches=$maxBranches\n";
			for($m=1;$m<=$maxBranches;$m++)	// create a pile of random tree.
			{
				$p=$maxPhase + ($m-1)*$maxPhase;
				echo "<pre>$frame m=$m $p\n";

				for($s=1;$s<=$maxStrand;$s)
				{
					if($s<1) $s=1;
					if($p<1) $p=1;
					if($p<=$maxPixel)
					{
						$itree++;
						if($tree_array[$itree]['rgb']==0)
							$tree_array[$itree]['strand']=$s;
						$tree_array[$itree]['pixel']=$p;
						$tree_array[$itree]['frame']=$frame;
						$tree_array[$itree]['phase']=0;
						$tree_array[$itree]['rgb']=$start_color;
						$tree_array[$itree]['sparkle']=0;
					}
				}
			}
		}


		$tree_rgb=$orig_tree_rgb;
		for($im=1;$im<=$itree;$im++)	//	 loop thru the tree heads we already have
		{
			$s=$tree_array[$im]['strand'];
			$p=$tree_array[$im]['pixel'];
			$phase=$tree_array[$im]['phase'];
			$rgb=$tree_array[$im]['rgb'];


			for($ph=0;$ph<=$maxPhase;$ph++)
			{
				if($random_colours==1)
				{
					$H_array[$ph] = rand(0,1000)/1000;
					$S= 1.0; 
					$V=  1.0;
					$V=$V * (1-($ph/$maxPhase));
					$color_HSV=array('H'=>$H_array[$ph],'S'=>$S,'V'=>$V);
				}
				else if($random_colours==2)
				{

					$H_array[$ph] = rand(0,1000)/1000;
					$S= 1.0; 
					$V=  1.0;
					$V=$V * (1-($ph/$maxPhase));
					$color_HSV=array('H'=>$H_array[0],'S'=>$S,'V'=>$V);
				}
				else
				{
					if($tree_array[$itree]['sparkle']>0)
					{
						$start_color2=$tree_array[$itree]['sparkle'];
						$rgb = $start_color;
						$r = ($rgb >> 16) & 0xFF;
						$g = ($rgb >> 8) & 0xFF;
						$b = $rgb & 0xFF;

						$HSL=RGB_TO_HSV ($r, $g, $b);
						$end_color2;

						$color_HSV=color_picker($ph,$maxPhase,$MaxFrame,$start_color,$end_color);
					}
					else
					{
					}
				}
				$H=$color_HSV['H'];
				$S=$color_HSV['S'];
				$V=$color_HSV['V'];

				$rgb_val=HSV_TO_RGB ($H, $S, $V);

				$snow=1;
				if($snow==1)
					if($ph==0 or $ph==1) $rgb_val=hexdec("#FFFFFF");
				if($ph==1 and $sparkles>0 and ($random_colours>=3 and $random_colours<=6))
				{
					//	random_colour = 3: Random color of tree head, different color on every frame redraw
					//	random_colour = 4: White  color of tree head, different color on every frame redraw
					//	random_colour = 5: Random color of tree head, that color kept for every subsequent iframe redraw
					//	random_colour = 6: White color of tree head, that color kept for every subsequent iframe redraw

					//if((100-$sparkles)>= rand(1,100)) // if sparkles threshold is met then
				{
					$H = rand(0,1000)/1000;
					$S= 1.0; 
					$V=  1.0;
					if($random_colours==4 or $random_colours==6) $S=0.0;	//	 force a white
					if(count($tree_array[$itree]['sparkle'])==0) 
						$tree_array[$itree]['sparkle']=array('H'=>$H,'S'=>$S,'V'=>$V);
				}
				}

				if($random_colours>=5 and $random_colours<=6 and $tree_array[$itree]['sparkle']>0) 	
				{
					$color_HSV=$tree_array[$itree]['sparkle'];
					$H=$color_HSV['H'];
					$S=$color_HSV['S'];
					$V=$color_HSV['V'];
				}
				$rgb_val=HSV_TO_RGB ($H, $S, $V);

				$new_p = $p-$ph; // backup the tree trail
				if($new_p>=$minPixel and $new_p<=$maxPixel)
				{
					$tree_rgb[$s][$new_p]=$rgb_val; // and store all of the tree info into the tree_rgb array
				}
			}
			$tree_array[$im]['pixel']++;	//	 now advance this tree head downward
		}

		/*
		for($s=1;$s<=$maxStrand;$s++)
			for($p=1;$p<=$maxPixel;$p++)
			{
				if($tree_rgb[$s][$p]>0)
				{
					echo "$s $p " . ($tree_rgb[$s][$p] . "\n";
				}
			}
		 */

		for($s=1;$s<=$maxStrand;$s++)
			for($p=1;$p<=$maxPixel;$p++)
			{
				$rgb_val=$tree_rgb[$s][$p];
				$string=$user_pixel=0;
				$xyz=$tree_xyz[$s][$p];
				$seq_number++;
				if(in_array($s,$window_array)) // Is this strand in our window?, 
				{
					if($rgb_val <> 0)
						fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number));
				}
			}
		fclose($fh_dat[$frame]);
	}
	$amperage=array();
	$x_dat_base = $base . ".dat";
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$script_start,$amperage,$seq_duration,$show_frame);

	echo "</body>";
	echo "</html>";
}


?>

