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
require_once('../conf/auth.php');

function butterfly_main($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$script_start,$sparkles,$seq_duration,$show_frame,$radian_shift,$start_color,$end_color,$background_chunk,$background_skip,$background_color,$formula,$username)
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
	$enable_project=get_enable_project($username);
	echo "<pre> enable_project=$enable_project</pre>\n";
	srand(time());
	$maxFrame=80;
	//$maxTrees=6;	// how many tree to draw at one time
	$seq_number=0;
	$window_array = getWindowArray($minStrand,$maxStrand,$window_degrees);
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
				$pi2 = 2*pi();
				$new_radian_shift = intval(($maxFrame*$radian_shift)/$pi2)*$pi2/$maxFrame;
				//$new_radian_shift = intval(($maxFrame*$radian_shift)/pi())*pi()/$maxFrame;
				//if($new_radian_shift==0) $new_radian_shift=$pi2/$maxFrame;
				if($new_radian_shift==0)
				{
					//	echo "<pre>We had a zero new_radian_shift . maxFrame=$maxFrame </pre>\n";
					$new_radian_shift=pi()/$maxFrame;
					$new_radian_shift=$pi2/$maxFrame;
				}
				//echo "<pre>new_radian_shift = intval(($maxFrame*$radian_shift+0.5)/$pi2)*$pi2/$maxFrame</pre>\n";
				$shift=($frame-1) * $new_radian_shift; // value passed in thru users form
				//echo "<pre>$frame,$s,$p $radian_shift $new_radian_shift</pre>\n";
				$halfs=$maxStrand/2;
				$v=butterfly($i,$p,$maxStrand,$maxPixel,$shift,$frame,$maxFrame,$formula);
				//	echo "<pre>startend color =$start_color, $end_color\n";
				if($start_color=="#FFFFFF" and $end_color=="#FFFFFF")
				{
					$H=$v;
					$S=$V=1.0;
					if($H<0) $H= -$v;
					if($background_chunk>0 and $background_chunk>0)
					{
						//$background_chunk=4;
						//$background_skip=3;
						//$background_color="#FFFFFF";
						$h1=intval($H*$background_chunk); 
						if($h1%$background_skip==0 )
						{
							$rgb_val=hexdec($background_color);
							$r = ($rgb_val >> 16) & 0xFF;
							$g = ($rgb_val >> 8) & 0xFF;
							$b = $rgb_val & 0xFF;
							$HSL=RGB_TO_HSV ($r, $g, $b);
							$H=$HSL['H']; 
							$S=$HSL['S'];  // Saturation. 1.0 = Full on, 0=off
							$V=$HSL['V']; 
							//	 $V=0;  // $S=0 = WHITE, $V=0 = BLACK
						}
					}
				}
				else
				{
					if($v<0) $v= -$v;		
					$color_HSV=color_picker($v,1.0,0,$start_color,$end_color);
					//			echo "using color picker\n";
					$H=$color_HSV['H'];
					$S=$color_HSV['S'];
					$V=$color_HSV['V'];
				}
				$string=$user_pixel=0;
				$xyz=$tree_xyz[$s][$p];
				$seq_number++;
				$rgb_val=HSV_TO_RGB ($H, $S, $V);
				//	if($rgb_val <> 0)
					fwrite($fh_dat[$frame],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number));
			}
		}
		fclose($fh_dat[$frame]);
	}
	$amperage=array();
	$x_dat_base = $base . ".dat";
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

function butterfly($x,$y,$maxX,$maxY,$offset,$frame,$maxFrame,$formula)
{
	$pi2 = 2*pi();
	if($formula==1)
	{
		$a = $pi2 / ($maxX+$maxY);
		$rad=$offset + (($x+$y)*$a);
		$n = ($x*$x - $y*$y) * sin ($rad);
		$d = ($x*$x+$y*$y);
		if($d>0)
			$v=$n/$d;
		else
		$v=0;
	}
	else if ($formula==2)
	{
		$dx = $maxX * ($x/$maxX);
		$dx=$maxX/2;
		$dy=$maxY/2;
		$frame2=intval($maxFrame/2);
		if($frame<=$frame2) $f=$frame;
		else
		$f=$maxFrame-$frame+1;
		$x1 = ($x-$dx)/$f;
		$x1 = ($x)/$f;
		$y1 = ($y-$dy)/$f;
		$v=sqrt($x1*$x1 + $y1*$y1);
	}
	else if ($formula==3)
	{
		$dx = $maxX * ($x/$maxX);
		$dx=$maxX/2;
		$dy=$maxY/2;
		$frame2=intval($maxFrame/2);
		if($frame<=$frame2) 
		$f=$frame;
		else
		$f=$maxFrame-$frame+1;
		if($f==0) $f=1;
		$x1 = ($x-$dx)/$f;
		$y1 = ($y-$dy)/$f;
		$v=sin($x1) * cos($y1);
	}
	if(empty($v))
		$v=1;
	else
	{
		if($v<0) $v=-$v;
		if($v>1) $v=1;
	}
	//echo "<pre>f,s,p=$frame,$x,$y v=$v</pre>\n";
	return $v;
}
?>
