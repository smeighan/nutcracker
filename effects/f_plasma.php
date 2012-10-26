
<?php
/*Algorithms   
Plasma Fractals
http://library.thinkquest.org/26242/full/types/ch10.html
The generation of plasma fractals is discussed in the Types of Fractals section. Basically, the description you can find there is implemented in the algorithm below. The fractal is stored entirely in a 2-dimensional array and then the points from the array are plotted on the screen.
Constants used:
number_of_colors: number of colors available in the computer language you’re using
size: size of the fractal; must be a power of 2
roughness: higher values make the fractal more fragmented
‘ randomly choose the rectangle’s corners
array(0, 0) = RND * number_of_colors
array(size, 0) = RND * number_of_colors
array(0, size) = RND * number_of_colors
array(size, size) = RND * number_of_colors
‘ go through the array, decreasing the interval size every time
FOR p = LOG(size) / LOG(2) TO 0 STEP -1
FOR x = 0 TO size STEP 2 ^ p
FOR y = 0 TO size STEP 2 ^ p
IF x MOD 2 ^ (p + 1) = 0 AND y MOD 2 ^ (p + 1) = 0 GOTO nxt
IF x MOD 2 ^ (p + 1) = 0 THEN
average = (array(x, y + 2 ^ p) + array(x, y - 2 ^ p)) / 2
color = average + roughness * (RND - .5)
	array(x, y) = color: GOTO nxt
END IF
IF y MOD 2 ^ (p + 1) = 0 THEN
average = (array(x + 2 ^ p, y) + array(x - 2 ^ p, y)) / 2
color = average + roughness * (RND - .5)
	array(x, y) = color: GOTO nxt
END IF
IF x MOD 2 ^ (p + 1) > 0 AND y MOD 2 ^ (p + 1) > 0 THEN
v1 = array(x + 2 ^ p, y + 2 ^ p)
	v2 = array(x + 2 ^ p, x - 2 ^ p)
	v3 = array(x - 2 ^ p, x + 2 ^ p)
	v4 = array(x - 2 ^ p, y - 2 ^ p)
	average = (v1 + v2 + v3 + v4) / 4
color = average + roughness * (RND - .5)
	array(x, y) = color: GOTO nxt
END IF
nxt:
NEXT y
NEXT x
NEXT p
‘ go through the array, plotting the points
FOR x = 0 TO size
FOR y = 0 TO size
PSET (x, y), array(x, y)
	NEXT y
NEXT x
*/
require_once("../effects/read_file.php");
$get=$_GET;

$size=32;
$color1=$color3="#00FF00";
$color2=$color4="#0000FF";
$plasma[0][0]=RGBVAL_TO_HSV($color1);
$plasma[0][$size]=RGBVAL_TO_HSV($color2);
$plasma[$size][0]=RGBVAL_TO_HSV($color3);
$plasma[$size][$size]=RGBVAL_TO_HSV($color4);
$log_size=log($size);
$log2 = log(2);
echo "<pre>";
echo "size=$size\n";
echo "log_size=$log_size\n";
echo "log2=$log2\n";
$result = $log_size/$log2;
echo "result=$result\n";
for($x=0;$x<=$size;$x+=1)
{
	for($y=0;$y<=$size;$y+=1)
	{
		$plasma[$x][$y]=0;
	}
}
//
//
$roughness=.1;
for($p = $result;$p>=0;$p--)
{
	$pow2=	pow(2, $p);
	for($x=0;$x<=$size;$x+=$pow2)
	{
		for($y=0;$y<=$size;$y+=$pow2)
		{
			echo "<pre>p,pow2,size=$p,$pow2,$size</pre>\n";
			echo "<pre>p,x,y=$p,$x,$y</pre>\n";
			/*	 IF x MOD 2 ^ (p + 1) = 0 AND y MOD 2 ^ (p + 1) = 0 GOTO nxt
			IF x MOD 2 ^ (p + 1) = 0 THEN
			average = (array(x, y + 2 ^ p) + array(x, y - 2 ^ p)) / 2
			color = average + roughness * (RND - .5)
				array(x, y) = color: GOTO nxt
			END IF*/
			if($x % pow(2 ,($p + 1)) == 0 and $y % pow(2,($p + 1)) == 0)
			{
				$i=1; // GOTO nxt
			}
			else if($x % pow(2 ,($p + 1)) == 0)
			{
				$average = ($plasma[$x][ $y + $pow2] + $plasma[$x][ $y - $pow2]) / 2;
				$color = $average + $roughness * (rand() - .5);
				$color=.2;
				$plasma[$x][$y] = $color;
				// GOTO nxt
			}
			/*IF y MOD 2 ^ (p + 1) = 0 THEN
			average = (array(x + 2 ^ p, y) + array(x - 2 ^ p, y)) / 2
			color = average + roughness * (RND - .5)
				array(x, y) = color: GOTO nxt
			END IF*/
			else if($y % pow(2 ,($p + 1)) == 0)
			{
				$average = ($plasma[$x + $pow2][ $y] + $plasma[$x - $pow2][ $y]) / 2;
				$color = $average + $roughness * (rand() - .5);
				$color=.4;
				$plasma[$x][$y]  = $color;
				// GOTO nxt
			}
			/*IF x MOD 2 ^ (p + 1) > 0 AND y MOD 2 ^ (p + 1) > 0 THEN
			v1 = array(x + 2 ^ p, y + 2 ^ p)
				v2 = array(x + 2 ^ p, x - 2 ^ p)
				v3 = array(x - 2 ^ p, x + 2 ^ p)
				v4 = array(x - 2 ^ p, y - 2 ^ p)
				average = (v1 + v2 + v3 + v4) / 4
			color = average + roughness * (RND - .5)
				array(x, y) = color: GOTO nxt
			END IF*/
			else if($x % pow(2 ,($p + 1)) > 0 and  $y % pow(2,($p + 1)) > 0 )
			{
				echo "<pre>pow2p=$pow2,   x,y=$x,$y</pre>\n";
				$v1 = $plasma[$x +$pow2][ $y +$pow2];
				$v2 = $plasma[$x +$pow2][ $x -$pow2];
				$v3 = $plasma[$x -$pow2][ $x +$pow2];
				$v4 = $plasma[$x -$pow2][ $y -$pow2];
				$average = ($v1 + $v2 + $v3 + $v4) / 4;
				$color = $average + $roughness * (rand() - .5);
				$color=.6;
				$plasma[$x][$y]  = $color;
				//: GOTO nxt
			}
			//		nxt:
			} //NEXT y
		}// NEXT x
	}// 	NEXT p
echo "<pre>";
print_r($plasma);
plasma_write($get,$plasma);

function plasma_write($get,$plasma)
{
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	extract ($get);
	echo "<pre>";
	print_r($get);
	echo "</pre>\n";
	set_time_limit(0);
	ini_set("memory_limit","1024M");
	require_once("../effects/read_file.php");
	//
	//
	$member_id=get_member_id($username);
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
	$maxFrame=intval(($seq_duration*1000/$frame_delay)/$speed)+1;
	$seq_number=0;
	for($f=1;$f<=$maxFrame;$f++)
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
					$H=$plasma[$x][$y];
				else {
					$H=0;
					$V=0;
				}
				$rgb_val = HSV_TO_RGB ($H, $S, $V);
				$seq_number++;
				//echo "<pre>f,s,p = $f,$s,$p (p_new=$p_new, n=$n mod=$m, $maxPixel). H,S,V = $H,$S,$V $hex</pre>\n";
				$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
				fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",
				$s,$p_new,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel
				,$s_pixel[$s][$p_new][0],$s_pixel[$s][$p_new][1],
				$f,$seq_number));
			}
		}
	}
	$x_dat_base=$base . ".dat";
	$show_frame='n';
	$amperage=array();
	make_gp($batch,$arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out); 
	$description ="Total Elapsed time for this effect:";
	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
	if($batch==0)
	{
		printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
		echo "<pre>Location: bc.php?batch=$batch</pre>\n";
	}
	return;
}
