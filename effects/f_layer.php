<?php
/*
Nutcracker: RGB Effects Builder
Copyright (C) 2012  Sean Meighan
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function f_layer($get)
{
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	//
	if(isset($get['window_degrees'])) $wind=$get['window_degrees'];
	else $wind=360;
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$wind); // Set window_degrees to match the target
	extract ($get);
	set_time_limit(0);
	ini_set("memory_limit","1024M");
	require_once("../effects/read_file.php");
	//
	$member_id=get_member_id($username);
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
	$tree_xyz_string=read_file2($t_dat,$target_path);
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
	/*echo "<pre>";
	print_r($arr);
	echo "</pre>\n";*/
	$path ="../effects/workspaces/$member_id";
	$directory=$path;
	if (file_exists($directory))
	{
		} else {
		echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$file1 = $path . "/" . $get['file1'];
	$file2 = $path . "/" . $get['file2'];
	if(!file_exists($file1))
	{
		die("File $file1 not found");
	}
	else
	{
		$fh1=fopen($file1,"r");
	}
	if(!file_exists($file2))
	{
		die("File $file2 not found");
	}
	else
	{
		$fh2=fopen($file2,"r");
	}
	$nc_file=$path . "/" . $base . ".nc";
	$fh_nc=fopen($nc_file,"w") or die("Failed opening $nc_file\n");
	$targetpath="../targets/". $member_id;
	$t_dat =  $user_target . ".dat";
	// read_file(AA.dat,../targets/2);
	$val=0;
	$line=0;
	while (!feof($fh1))
	{
		$line1 = fgets($fh1);
		$tok1=preg_split("/ +/", $line1);
		$c1= count($tok1);
		$line2 = fgets($fh2);
		$tok2=preg_split("/ +/", $line2);
		$c2= count($tok2);
		$c=$c1;
		if($c2>$c) $c=$c2;
		if($tok1[0]!="#")
		{
			//echo "c1,c2= $c1, $c2: l1=$line1, l2=$line2\n";
			//echo "\n:";
			if($tok1[0]=="S")
				if($tok2[0]==$tok1[0]  and $tok2[1]==$tok1[1] and $tok2[3]==$tok1[3])
			{
				$line++;
				$cols=0;
				fprintf($fh_nc,sprintf ("S %d P %d ",$tok1[1],$tok1[3]));
				//printf ("S %d P %d ",$tok1[1],$tok1[3]);
				for($i=4;$i<$c;$i++)
				{
					if(!isset($tok1[$i]) or $tok1[$i]==null) $tok1[$i]=0;
					if(!isset($tok2[$i]) or $tok2[$i]==null) $tok2[$i]=0;
					if($line==1) // only open files on first line
					{
						$n=$i-3;
						$dat_file = $path . "/" . $base . "_d_" . $n . ".dat";
						$dat_file_array[]=$dat_file;
						$fh_dat[$i]=fopen($dat_file,"w") or die ("Cannot open $dat_file\n");
						fwrite($fh_dat[$i],"# 	$dat_file\n");
					}
					$val=0;
					if($layer_method=="Pri-1")
					{
						$val=$tok1[$i];
						if($tok1[$i]==0) $val=$tok2[$i];
					}
					if($layer_method=="Pri-2")
					{
						$val=$tok2[$i];
						if($tok2[$i]==0) $val=$tok1[$i];
					}
					if($layer_method=="Mask-1")
					{
						if($tok1[$i]<>0) $val=$tok2[$i];
						else
						$val=0;
					}
					if($layer_method=="Mask-2")
					{
						if($tok2[$i]<>0) $val=$tok1[$i];
						else
						$val=0;
					}
					if($layer_method=="Avg")
					{
						if($tok1[$i]==0 and $tok2[$i]<>0) $val=$tok2[$i];
						else
						if($tok2[$i]==0 and $tok1[$i]<>0) $val=$tok1[$i];
						else
						$val = avg_HSV($tok1[$i],$tok2[$i]);
					}
					//printf ("%d ",$val);
					fprintf($fh_nc,sprintf ("%d ",$val));
					$x=1.1; $y=2.2; $z=3.3;
					$rgb=$val;
					$string=$tok1[1];
					$user_pixel=$tok1[3];
					$s=1; $p=$line;
					$xyz=$tree_xyz_string[$string][$user_pixel]; // get x,y,z location from the model.
					// array($x,$y,$z,$strand,$pixel,$rgb);
					$x=$xyz[0];
					$y=$xyz[1];
					$z=$xyz[2];
					$s=$xyz[3];
					$p=$xyz[4];
					fprintf($fh_dat[$i],sprintf("%s %3d %d %7.3f %7.3f %7.3f %d 00 00 %3d %4d\n",$user_target,$s,$p, $x,$y,$z,$rgb,
					$string,$user_pixel));
					$cols++;
				}
				//printf ("\n");
				fprintf($fh_nc,"\n");
			}
		}
	}
	for($i=4;$i<$c;$i++)
	{
		fclose($fh_dat[$i]);
	}
	fclose($fh_nc);
	$x_dat_base=$base . ".dat";
	$show_frame='n';
	$amperage=array();
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out);
	if($batch==0) elapsed_time($script_start);
}

function read_file2($file,$path)
{
	$max_i=-1;
	$min_user_pixel = $min_string = $min_strand=$min_pixel = 9999999;
	$max_user_pixel = $max_string = $max_strand=$max_pixel = -1;
	$lines=0;
	$full_path = $path . "/" . $file;
	$fh = fopen($full_path, 'r') or die("can't open file $full_path");
	$debug=0;
	$i=0;
	$min_x=99999;
	$min_y=99999;
	$min_z=99999;
	$max_x = -999999;
	$max_y = -999999;
	$max_z = -999999;
	#    ../targets/f/BB.dat
	#    Col 1: Your TARGET_MODEL_NAME
	#    Col 2: Strand number.
	#    Col 3: Nutcracker Pixel#
	#    Col 4: X location in world coordinates
	#    Col 5: Y location in world coordinates
	#    Col 6: Z location in world coordinates
	#    Col 7: User string
	#    Col 8: User pixel
	# 
	//BB   1   1   0.000   0.732 142.091     1    50
	//BB   1   2   0.000   1.465 139.182     1    49
	//BB   1   3   0.000   2.197 136.272     1    48
	//BB   1   4   0.000   2.929 133.363     1    47
	//BB   1   5   0.000   3.662 130.454     1    46
	while (!feof($fh))
	{
		$line = fgets($fh);
		$tok=preg_split("/ +/", $line);
		$l=strlen($line);
		$c= substr($line,0,1);
		if($l>20 and $c !="#")
		{
			$i++;
			$device=$tok[0];	// 0 device name
			$strand=$tok[1];	// 1 strand#
			$pixel=$tok[2];// 2 pixel#	
			$x=(float)$tok[3]; // 3 X value
			$y=(float)$tok[4];	// 4 Y value
			$z=(float)$tok[5];	// 5 Z value
			$rgb=$tok[6];	// 5 Z value
			$string=$tok[7];
			$user_pixel=$tok[8];
			if($x<$min_x) $min_x=$x;
			if($y<$min_y) $min_y=$y;
			if($z<$min_z)
			{
				$min_z=$z;
			}
			if($x>$max_x)
			{
				$max_x=$x;
			}
			if($y>$max_y)
			{
				$max_y=$y;
			}
			if($z>$max_z)
			{
				$max_z=$z;
			}
			if(empty($rgb))
				$rgb=0;
			else
			$rgb=$tok[6];
			//
			$tree_xyz_string[$string][$user_pixel]=array($x,$y,$z,$strand,$pixel,$rgb);
		}
	}
	fclose($fh);
	return $tree_xyz_string;
}

function avg_HSV($rgb1,$rgb2)
{
	$rgb = $rgb1;
	$r = ($rgb >> 16) & 0xFF;
	$g = ($rgb >> 8) & 0xFF;
	$b = $rgb & 0xFF;
	$HSL=RGB_TO_HSV ($r, $g, $b);
	$start_H=$HSL['H']; 
	$start_S=$HSL['S']; 
	$start_V=$HSL['V']; 
	$rgb = $rgb2;
	$r = ($rgb >> 16) & 0xFF;
	$g = ($rgb >> 8) & 0xFF;
	$b = $rgb & 0xFF;
	$HSL=RGB_TO_HSV ($r, $g, $b);
	$end_H=$HSL['H']; 
	$end_S=$HSL['S']; 
	$end_V=$HSL['V']; 
	$H=($start_H+$end_H)/2;
	$S=($start_S+$end_S)/2;
	$V=($start_V+$end_V)/2;
	$rgb = HSV_TO_RGB ($H, $S, $V)  ;
	return $rgb;
}
