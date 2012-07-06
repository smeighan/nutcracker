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
<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"> 
<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Welcome <?php echo $_SESSION['SESS_FIRST_NAME'];?></h1>
<?php $menu="effect-form"; require "../conf/menu.php"; ?>
<?php
//
require("../effects/read_file.php");
echo "<pre>";
echo "max_execution_time =" . ini_get('max_execution_time') . "\n"; 
set_time_limit(60*60*8);
echo "max_execution_time =" . ini_get('max_execution_time') . "\n"; 
echo "</pre>";
//show_array($_SERVER,"SERVER");
// [QUERY_STRING] => make_lor.php?base=ZZ_ZZ+USER2?full_path=workspaces/2/ZZ_ZZ+USER2_d_1.dat?frame_delay=200?member_id=2?seq_duration=2?sequencer=lors2
// base=ZZ_ZZ+USER2?full_path=workspaces/2/ZZ_ZZ+USER2_d_1.dat?frame_delay=200?member_id=2?seq_duration=2?sequencer=lors2
//
$tokens=explode("?",$_SERVER['QUERY_STRING']);
$c=count($tokens);
$tokens2=explode("base=",$tokens[0]);
$base=$tokens2[1];
$tokens2=explode("full_path=",$tokens[1]);
$full_path=$tokens2[1];
$tokens2=explode("frame_delay=",$tokens[2]);
$frame_delay=$tokens2[1];
$tokens2=explode("member_id=",$tokens[3]);
$member_id=$tokens2[1];
$tokens2=explode("seq_duration=",$tokens[4]);
$seq_duration=$tokens2[1];
$tokens2=explode("sequencer=",$tokens[5]);
$sequencer=$tokens2[1];
$tokens2=explode("pixel_count=",$tokens[6]);
$pixel_count=$tokens2[1];
$sequencer="vixen";
$path_parts = pathinfo($full_path);
$dirname   = $path_parts['dirname']; // workspaces/2
$basename  = $path_parts['basename']; // AA+CIRCLE1_d_1.dat
//$extension =$path_parts['extension']; // .dat
$filename  = $path_parts['filename']; //  AA+CIRCLE1_d_1
$tok=explode("/",$dirname);
$member_id=$tok[1];
//echo "<pre>dirname=$dirname basename=$basename extension=$extension filename=$filename\n";
$path=$dirname;
$tokens2=explode("_d_",$filename);
$base=$tokens2[0];	// AA+CIRCLE1
//echo "<pre>member=$member_id base=$base  \n";
$username=get_username($member_id);
//echo "<pre>REQUEST_URI=$REQUEST_URI</pre>\n";
////echo "<pre>base=$base</pre>\n";
//echo "<pre>full_path=$full_path</pre>\n";
//echo "<pre>frame_delay=$frame_delay</pre>\n";
//echo "<pre>member_id=$member_id</pre>\n";
//echo "<pre>seq_duration=$seq_duration</pre>\n";
//echo "<pre>sequencer=$sequencer</pre>\n";
$supported = array('vixen','hls');
//echo "<pre";
//echo "sequencer=$sequencer\n";
if(!in_array($sequencer,$supported))
{
	echo "<pre>";
	echo "Your sequencer is not yet supported.\n";
	echo "-----------------------------------\n";
	echo "Currently spported sequencers:\n";
	echo "vixen .... Vixen 2.1 vir file\n";
	echo "hls ...... Joe Hinkle's new sequencer, HLS\n";
	echo "\n\n";
	echo "Soon to be spported sequencers:\n";
	echo "lors2 .... LOR S2 (by end of march)\n";
	echo "lsp2 ..... LSP 2.0 (by end of april)\n";
	echo "lors3 .... LOR S3 (by end of may)\n";
	echo "lsp3 ..... LSP 2.0 (by end of june)\n";
	?>
	<a href="../index.html">Home</a> | <a href="../login/member-index.php">Target Generator</a> | 
	<a href="effect-form.php">Effects Generator</a> | <a href="../login/logout.php">Logout</a>
	<?php
	exit();
}
extract($_POST);
$path="../targets/". $member_id;
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
$path_parts = pathinfo($path);
$dirname   = $path_parts['dirname'];
$basename  = $path_parts['basename'];
//$extension =$path_parts['extension'];
$filename  = $path_parts['filename'];
$path=$dirname . "/" . $basename;
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
$description = 'Calling read Target:';
printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
//if($description = 'Total Elapsed time for this effect:')
	$description = 'Finished read Target:';
printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
$maxFrame=0;
//
//	read the target data into an array.
//	dirname   =workspaces/f
//	basename  =SGASE+SEAN33_d_1.dat
//	extension =dat
//	filename  =SGASE+SEAN33_d_1
/*
Array
(
[10] => SGASE+SEAN33_d_10.dat
[57] => SGASE+SEAN33_d_57.dat
[5] => SGASE+SEAN33_d_5.dat
[37] => SGASE+SEAN33_d_37.dat
[53] => SGASE+SEAN33_d_53.dat
[66] => SGASE+SEAN33_d_66.dat
[54] => SGASE+SEAN33_d_54.dat
[55] => SGASE+SEAN33_d_55.dat
[68] => SGASE+SEAN33_d_68.dat
[11] => SGASE+SEAN33_d_11.dat
[59] => SGASE+SEAN33_d_59.dat
*/	
//    base=AA+SEAN3  t_dat=AA.dat  username=f
$full_path= "workspaces/$member_id/$base";
$path_parts = pathinfo($full_path);
$dirname   = $path_parts['dirname'];
$basename  = $path_parts['basename'];
//$extension =$path_parts['extension'];
$dir = opendir ($dirname); 
while (false !== ($file = readdir($dir)))
{
	$dat=strpos($file, '.dat',1);
	$m=strpos($file,$base,0);
	//		echo "file =$file. dat,m=$dat,$m \n"; 
	if (strpos($file, '.dat',1) and strpos($file,$base,0)===0 )
	{
		$basen=basename($file,".dat");
		$tokens=explode("_d_",$basen);
		$c=count($tokens);
		if($c>1)
		{
			$i=$tokens[1];
			$dat_file_array[$i]=$file;
			if($i>$maxFrame) $maxFrame=$i;
		}
		//echo "MATCH file =$file \n";
	}
}
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
//if($description = 'Total Elapsed time for this effect:')
	$description = 'Finished filling dat_file_array:';
printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
/*
#    workspaces/f/AA+SEAN3_18.dat
t1   20    1    -0.225     0.692   113.090 16716932 18 18
t1    5    1     0.692     0.225   113.090 16716932 18 38
t1   15    1    -0.692    -0.225   113.090 16716932 18 78
t1   20    1    -0.225     0.692   113.090 16716932 18 98
t1    5    1     0.692     0.225   113.090 16716932 18 118
t1   15    1    -0.692    -0.225   113.090 16716932 18 158
t1   19    2    -0.855     1.177   110.179 16716942 18 178
t1    4    2     1.177     0.855   110.179 16716942 18 198
t1   18    3    -1.766     1.283   107.269 16716696 18 258
t1    3    3     1.283     1.766   107.269 16716696 18 278
*/
$maxFrame=count($dat_file_array);
//echo "<pre>";
//print_r($dat_file_array);
$seq_file = $dirname . "/" . $base . ".dat";
$seq_srt = $dirname . "/" . $base . ".srt";
$fh_seq=fopen($seq_file,"w") or die ("unable to open seq.dat");
for ($frame=1;$frame<=$maxFrame;$frame++)
{
	$filename=$dat_file_array[$frame];
	$full_path = $dirname . "/" . $filename;
	$fh = fopen($full_path, 'r') or die("can't open file $full_path");
	/*
	#    workspaces/f/AA+SEAN3_d_8.dat
	t1    6    1    -0.589     0.428   113.090 16712526 0 0 9 41
	t1    5    1    -0.692     0.225   113.090 16712526 0 0 9 40
	t1    4    1    -0.728     0.000   113.090 16712526 0 0 8 41
	t1   10    1     0.225     0.692   113.090 16712526 0 0 1 41
	t1    9    1     0.000     0.728   113.090 16712526 0 0 1 40
	t1    8    1    -0.225     0.692   113.090 16712526 0 0 10 41
	t1   13    1     0.692     0.225   113.090 16712526 0 0 3 40
	*/
	$loop=$maxPixel=0;
	while (!feof($fh))
	{
		$line = fgets($fh);
		$tok=preg_split("/ +/", $line);
		$tokens=count($tok);
		$l=strlen($line);
		$c= substr($line,0,1);
		if($l>20 and $c !="#")
		{
			$i++;
			$loop++;
			$s=$tok[1];	// 1 strand#
			$p=$tok[2];// 2 pixel#	
			$rgb=intval($tok[6]);	// 5 Z value
			$string=intval($tok[9]);
			$user_pixel=intval($tok[10]);
			if($user_pixel>$maxPixel) $maxPixel=$user_pixel;
			if(empty($rgb))
				$rgb=0;
			else
			$rgb=$tok[6];
			//
			//	$string_array[$string][$user_pixel][$frame]=$rgb;
			fwrite($fh_seq,sprintf("%6d %6d %6d %9d # $loop  %s\n",$string,$user_pixel,$frame,$rgb,$line));
		}
	}
}
fclose($fh_seq);
//
//	Ok, now that $basename.dat exists (the concatenated big file of all the dat's
//	sort it so we get the strings togeter. We will output this to basename.srt
//
echo "<pre>Sorting $seq_file and storing into $seq_srt</pre>\n";
if($_SERVER['HTTP_HOST']=='localhost') // If this is a windows server, 
{
	$data = file($seq_file); // we will do a memory sort
	natsort($data);
	file_put_contents($seq_srt, implode("\n", $data));
}
else // we are on a linux bos, so we can use the linux sort
{
	$shellCommand = "cat $seq_file | awk '{if(NF>5) print $0;
	}
	' | sort -bn -o $seq_srt"; 
	$output = system($shellCommand . " 2>&1"); 
	//echo "<pre>cmd= $shellCommand </pre>\n";
	//echo "<pre>output= $output </pre>\n";
}
$channel=0;
/*		AA+SEAN.srt file format.
*		col 1: string
*		col 2: pixel
*		col 3: frame
*		col 4: rgb
1      2      9    917261 # 1186  t1   10   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     10    917261 # 1186  t1   11   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     11    917261 # 1184  t1   12   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     24  16777215 # 1158  t1   25   79     0.000    57.481     2.076 16777215 0 0 1 2
1      2     25    917261 # 1157  t1   26   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     26    917261 # 1156  t1   27   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     39    917261 # 1158  t1   40   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     40    917261 # 1157  t1   41   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     41    917261 # 1156  t1   42   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     54    917261 # 1172  t1   55   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     55    917261 # 1181  t1   56   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     56    917261 # 1188  t1   57   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     69    917261 # 1181  t1   70   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     70    917261 # 1182  t1   71   79     0.000    57.481     2.076 917261 0 0 1 2
1      2     71    917261 # 1182  t1   72   79     0.000    57.481     2.076 917261 0 0 1 2
1      3     10    917273 # 1178  t1   11   78     0.000    56.753     4.987 917273 0 0 1 3
1      3     10  16777215 # 1169  t1   11   78     0.000    56.753     4.987 16777215 0 0 1 3
1      3     11    917273 # 1167  t1   12   78     0.000    56.753     4.987 917273 0 0 1 3
1      3     11    917273 # 1176  t1   12   78     0.000    56.753     4.987 917273 0 0 1 3
1      3     12    917273 # 1167  t1   13   78     0.000    56.753     4.987 917273 0 0 1 3
*/
$fh = fopen($seq_srt, 'r') or die("can't open file $seq_srt");
// show_srt_file($seq_srt,$maxFrame,$frame_delay,$maxPixel,$pixel_count);
$loop=$hlsnc_loop=0;
$outBuffer=array();
$old_string=-1;
$vixen_vir= $dirname . "/" . $base . ".vir";
$vixen_vix= $dirname . "/" . $base . ".vix";
$vixen_csv= $dirname . "/" . $base . ".txt";
$hlsnc= $dirname . "/" . $base . ".hlsnc";
$fh_vixen_vir=fopen($vixen_vir,"w") or die("Unable to open $vixen_vir");
$fh_vixen_csv=fopen($vixen_csv,"w") or die("Unable to open $vixen_csv");
$fh_hlsnc=fopen($hlsnc,"w") or die("Unable to open $hlsnc");
//	how many frames should we do?
//
//	seq_duration = 9.5 seconds
//	frame_delay = 50  (ms)
	//
//	TotalFrames = (9.5*1000)/50
//	Totalframes = 190
//
if($frame_delay>0)
	$TotalFrames= ($seq_duration*1000)/$frame_delay;
else
$TotalFrames=$MaxFrame;
if($TotalFrames>1000)
{
	echo "<font color=red><h2>Limiting current sequences to 1000 frames</h2></font>\n";
	$TotalFrames=1000;
}
$old_pixel=0;
$savedIndex=0;
echo "<h3>$seq_duration seconds of animation with a $frame_delay ms frame timing = $TotalFrames frames of animation</h3>\n";
while (!feof($fh))
{
	$line = fgets($fh);
	$tok=preg_split("/ +/", $line);
	$l=strlen($line);
	$c= substr($line,0,1);
	if($l>20 and $c !="#")
	{
		$i++;
		$string=$tok[1];	// string#
		$pixel=$tok[2];	// pixel#
		$frame=$tok[3];	// frame#
		$rgb=$tok[4];	// rgb#
		//echo "<pre>loop=$loop   string,pixel=$string,$pixel,$rgb old_string=$old_string</pre>\n";
		if($string>0 and $old_pixel>=0 and ($string!=$old_string or $pixel!=$old_pixel))
		{
			$rgbChannel_name[$old_string][$old_pixel]=sprintf("S%d-P%d",$old_string,$old_pixel);
			$array_write_buffer=write_buffer($outBuffer,$savedIndex,$fh_vixen_vir,$fh_vixen_csv,$sequencer,$string,$old_string,$pixel,$old_pixel,$maxPixel,$maxFrame,$frame_delay,$loop,$TotalFrames);
			$loop=$array_write_buffer[0];
			for($f=1;$f<=$maxFrame;$f++)
			{
				$outBuffer[$f]=0;
			}
		}
		$old_string=$string;
		$old_pixel=$pixel;
		$outBuffer[$frame]=$rgb;
	}
}
fclose($fh_vixen_vir);
fclose($fh_vixen_csv);
fclose($fh_hlsnc);
/*$TotalFrames= ($seq_duration*1000)/$frame_delay;*/
$duration = $seq_duration*1000;
// make_vix($vixen_vir,$duration,$frame_delay);  // also make a *.vix file. Pass in the *.vir
if($sequencer=="vixen")
{
	printf ("<h2>$loop channels have been created for Vixen</h2>\n");
	printf ("<h2><a href=\"%s\">Click here for Vixen vir file. %s</a></h2>\n",$vixen_vir,$vixen_vir);
	printf ("<h2><a href=\"%s\">Click here for Vixen vix file. %s</a></h2>\n",$vixen_vix,$vixen_vix);
	printf ("<h2><a href=\"%s\">Click here for Vixen csv file. %s</a></h2>\n",$vixen_csv,$vixen_csv);
	echo "<h2>Here are Emmanuel Miranda's 3 addons for Nutcracker. I would not even try to use Nutcracker ";
	echo "on Vixen without these. <br/>\n";
	echo "<a href=\"http://emmanuelmiranda.info/?page_id=10\">http://emmanuelmiranda.info/?page_id=10</a>\n";
	echo "<ul>\n";
	echo "<li>RGBProfilerAddin.dll : This will create a profile matching the strings and pixels you have and colors it as RGB\n";
	echo "<li>RoutineLoaderAddin.dll : This will allow you to load huge profile files in one second\n";
	echo "<li>RGBTreePreview3D.dll : This will create a 3D tree in Vixen so you can see your animations\n";
	echo "</ul>\n";
	echo "<br>\n";
	echo "<ul>\n";
	echo "<li><a href=\"../tutorials/VixenTutorial1.swf\">Vixen Tutorial #1: Creating a Nutcracker effect and storing as a vir file</a>\n";
	echo "<li><a href=\"../tutorials/VixenTutorial2.swf\">Vixen Tutorial #2: Setting up the profiler, routine importer and RGB visualizer into Vixen.</a>\n";
	echo "<li><a href=\"../tutorials/VixenTutorial3.swf\">Vixen Tutorial #3: Finish setting up the RGB visualizer</a>\n";
	echo "</ul>\n";
	echo "</h2>";
}
if($sequencer=="hls")
{
	printf ("<br/><h2>$hlsnc_loop channels have been created for HLS</h2>\n");
	printf ("<h2><a href=\"%s\">Click here for HLS csv file. %s</a></h2>\n",$hlsnc,$hlsnc);
}
$description ="Total Elapsed time for this effect:";
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
//if($description = 'Total Elapsed time for this effect:')
	printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
?>
<a href="../index.html">Home</a> | <a href="../login/member-index.php">Target Generator</a> | 
<a href="effect-form.php">Effects Generator</a> | <a href="../login/logout.php">Logout</a>
<?php

function read_target_file($file,$path)
{
	$max_i=-1;
	$min_string = $min_strand = $min_pixel = $min_user_pixel = 9999999;
	$max_string = $max_strand = $max_pixel = $max_user_pixel = -1;
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
		if($debug==1) echo $line;
		//	$i=$tok[1]; // strand#
		$l=strlen($line);
		$c= substr($line,0,1);
		//	echo "<pre>strlen=$l, $c, buff=$line </pre>\n";
		if($l>20 and $c !="#")
		{
			$i++;
			echo "<pre>";
			print_r($tokens);
			$device=$tok[0];	// 0 device name
			$strand=$tok[1];	// 1 strand#
			$pixel=$tok[2];// 2 pixel#	
			$x=(float)$tok[3]; // 3 X value
			$y=(float)$tok[4];	// 4 Y value
			$z=(float)$tok[5];	// 5 Z value
			$rgb=intval($tok[6]);	// 5 Z value
			$string=intval($tok[7]);
			$user_pixel=intval($tok[8]);
			if($x<$min_x) $min_x=$x;
			if($y<$min_y) $min_y=$y;
			if($z<$min_z) $min_z=$z;
			if($x>$max_x) $max_x=$x;
			if($y>$max_y) $max_y=$y;
			if($z>$max_z) $max_z=$z;
			if(empty($rgb))
				$rgb=0;
			else
			$rgb=$tok[6];
			//
			if($strand<$min_strand) $min_strand=$strand;
			if($pixel<$min_pixel) $min_pixel=$pixel;
			if($strand>$max_strand) $max_strand=$strand;
			if($pixel>$max_pixel) $max_pixel=$pixel;
			if($string<$min_string) $min_string=$string;
			if($string>$max_string) $max_string=$string;
			if($user_pixel<$min_user_pixel) $min_user_pixel=$user_pixel;
			if($user_pixel>$max_user_pixel) $max_user_pixel=$user_pixel;
			//$tree_rgb[$strand][$pixel]=$rgb;
			$tree_user_string_pixel[$string][$user_pixel]['strand']=$strand;
			$tree_user_string_pixel[$string][$user_pixel]['pixel']=$pixel;
			//$tree_xyz[$strand][$pixel]=array($x,$y,$z,$rgb);
		}
	}
	fclose($fh);
	$min_max = array($min_x,$max_x,$min_y,$max_y,$min_z,$max_z);
	$arr = array('min_strand'=>$min_strand,'min_pixel'=>$min_pixel,'max_strand'=>$max_strand,'max_pixel'=>$max_pixel,'min_string'=>$min_string,'max_string'=>$max_string,'min_user_pixel'=>$min_user_pixel,'max_user_pixel'=>$max_user_pixel,'tree_user_string_pixel'=>$tree_user_string_pixel);
	return $arr;
}

function write_buffer($outBuffer,$savedIndex,$fh_vixen_vir,$fh_vixen_csv,$sequencer,$string,$old_string,$pixel,$old_pixel,$maxPixel,$maxFrame,$frame_delay,$loop,$TotalFrames)
{
	//echo "<pre>write_buffer($outBuffer,$savedIndex,$file_type,$fh_vixen_vir,$fh_vixen_csv,$sequencer,$string,$old_string,$pixel,$old_pixel,$maxFrame,$frame_delay,$loop)</pre>\n";
	
	$hlsnc_loop=0;
	if($old_pixel<$maxPixel and $old_string==$string) //	96,97 (assume maxPixel=100))
	{
		fill_missing($fh_vixen_vir,$fh_vixen_csv,$sequencer,$savedIndex,$string,$old_pixel+1,$maxPixel,$maxPixel,$TotalFrames);
	}
	if($pixel>1 and $old_string!=$string) // 5,6,7,8
	{
		fill_missing($fh_vixen_vir,$fh_vixen_csv,$sequencer,$savedIndex,$string,1,$pixel-1,$maxPixel,$TotalFrames);
	}
	if(($pixel-$old_pixel)>1 and $old_string==$string)  //46,47,48,52,53
	{
		fill_missing($fh_vixen_vir,$fh_vixen_csv,$sequencer,$savedIndex,$string,$old_pixel+1,$pixel-1,$maxPixel,$TotalFrames);
	}
	// DEBUG fwrite($fh_vixen_vir,sprintf("(s%d-p%d) ",$string,$pixel));
	for($rgbLoop=1;$rgbLoop<=3;$rgbLoop++)
	{
		if($rgbLoop==1) $c='R';
		if($rgbLoop==2) $c='G';
		if($rgbLoop==3) $c='B';
		//printf("%3d-%3d-%s ",$old_string,$old_pixel,$c);
		for($f=1;$f<=$TotalFrames;$f++)
		{
			$frame = 1 + $f % $maxFrame;
			if (!isset($outBuffer[$frame]) || $outBuffer[$frame] == null)
				$rgb=0;
			else
			$rgb = $outBuffer[$frame];
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;
			//echo "<pre>f=$f frame=$frame maxFrame=$maxFrame</pre>\n";
			if($rgbLoop==1) $val=$r;
			if($rgbLoop==2) $val=$g;
			if($rgbLoop==3) $val=$b;
			if($sequencer=="vixen")
			{
				if($frame<$TotalFrames)
				{
					fwrite($fh_vixen_vir,sprintf("%d ",$val));
					fwrite($fh_vixen_csv,sprintf("%d,",$val));
				}
				else
				{
					fwrite($fh_vixen_vir,sprintf("%d",$val));
					fwrite($fh_vixen_csv,sprintf("%d ",$val));
				}
			}
			if($rgbLoop==1 and $sequencer=="hls")
			{
				fwrite($fh_hlsnc,sprintf("%d ",$rgb));
			}
		}
		if($sequencer=="vixen")
		{
			fwrite($fh_vixen_vir,sprintf("\n"));
			fwrite($fh_vixen_csv,sprintf("\n"));
		}
		$loop++;
		if($rgbLoop==1)
		{
			$hlsnc_loop++;
			if($sequencer=="hls")	fwrite($fh_hlsnc,sprintf("\n",$rgb));
		}
	}
	for($f=1;$f<=$TotalFrames;$f++)
		$outBuffer[$f]=0;
	$arr[0]=$loop;
	return $arr;
}

function fill_missing($fh_vixen_vir,$fh_vixen_csv,$sequencer,$savedIndex,$string,$start_pixel,$end_pixel,$maxPixel,$TotalFrames)
{
	// $fh_vixen_vir,$fh_vixen_csv,$sequencer
	//echo "<pre>fill_missing($fh_vixen_vir,$fh_vixen_csv,$sequencer,$file_type,$channel_savedIndex,$savedIndex,$string,$start_pixel,$end_pixel,$maxPixel)</pre>\n";
	for($p=$start_pixel;$p<=$end_pixel;$p++)
	{
		for($rgbLoop=1;$rgbLoop<=3;$rgbLoop++)
		{
			//
			// echo "<pre>Filling in missing pixel $p, rgbloop=$rgbLoop</pre>\n";
			// DEBUG		fwrite($fh_vixen_vir,sprintf("(s%d-p%d**) ",$string,$p));
			for($f=1;$f<=$TotalFrames;$f++)
			{
				$val=0;	
				if($sequencer=="vixen")
				{
					fwrite($fh_vixen_vir,sprintf("%d ",$val));
					if($f<$TotalFrames)
						fwrite($fh_vixen_csv,sprintf("%d,",$val));
					else
					fwrite($fh_vixen_csv,sprintf("%d ",$val));
				}
				if($rgbLoop==1 and $sequencer=="hls")
				{
					fwrite($fh_hlsnc,sprintf("%d ",$rgb));
				}
			}
			if($sequencer=="vixen") fwrite($fh_vixen_vir,sprintf("\n"));
			if($sequencer=="vixen") fwrite($fh_vixen_csv,sprintf("\n"));
		}
	}
}

function make_vix($vixen_vir,$duration,$frame_delay)
{
	/*$vixen_file = $model_base_name . ".vir";
	$full_path = $path . "/" . $vixen_file;*/
	//
	$path_parts = pathinfo($vixen_vir);
	//$dat_file_array0=$dat_file_array[0];
	$dirname   = $path_parts['dirname']; // workspaces/2
	$basename  = $path_parts['basename']; // AA+CIRCLE1_d_1.dat
	$extension =$path_parts['extension']; // .dat
	$filename  = $path_parts['filename']; //  AA+CIRCLE1_d_1
	$file_vix = $dirname . "/" . $filename . ".vix";
	echo "<pre>$dirname|$basename |$filename |$file_vix</pre>\n";
	$fh_vir=fopen($vixen_vir,"r") or die("Unable to open $vixen_vir");
	$fh = fopen($file_vix,"w") or die ("unable to open $file_vix");
	fwrite($fh,sprintf("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"));
	fwrite($fh,sprintf("<Program>\n"));
	fwrite($fh,sprintf("<Time>$duration</Time>\n"));
	fwrite($fh,sprintf("<EventPeriodInMilliseconds>$frame_delay</EventPeriodInMilliseconds>\n"));
	fwrite($fh,sprintf("<MinimumLevel>0</MinimumLevel>\n"));
	fwrite($fh,sprintf("<MaximumLevel>255</MaximumLevel>\n"));
	fwrite($fh,sprintf("<AudioDevice>-1</AudioDevice>\n"));
	fwrite($fh,sprintf("<AudioVolume>0</AudioVolume>\n"));
	$lines=0;
	rewind($fh_vir);
	while (!feof($fh_vir))
	{
		$line = fgets($fh_vir);
		$tok=preg_split("/ +/", $line);
		$c=count($tok);
		if($c>1) $lines++;
	}
	rewind($fh_vir);
	$plugin=0;
	if($plugin==1)
	{
		fwrite($fh,sprintf("<PlugInData>\n"));
		/*fwrite($fh,sprintf("<Channels />\n"));*/
		/*fwrite($fh,sprintf("<PlugInData>\n"));
		fwrite($fh,sprintf("<PlugIn name=\"Adjustable preview\" key=\"-1193625963\" id=\"0\" enabled=\"True\" type=\"Output\" from=\"1\" to=\"698\">\n"));*/
		fwrite($fh,sprintf("<PlugIn name=\"Adjustable preview\" key=\"-1193625963\" id=\"0\" enabled=\"True\" type=\"Output\" from=\"1\" to=\"$lines\">\n"));
		fwrite($fh,sprintf("<RedirectOutputs>False</RedirectOutputs>\n"));
		fwrite($fh,sprintf("<Display>\n"));
		fwrite($fh,sprintf("<Height>211</Height>\n"));
		fwrite($fh,sprintf("<Width>459</Width>\n"));
		fwrite($fh,sprintf("<PixelSize>3</PixelSize>\n"));
		fwrite($fh,sprintf("<Brightness>5</Brightness>\n"));
		fwrite($fh,sprintf("</Display>\n"));
	}
	else
	{
		fwrite($fh,sprintf("<PlugInData />\n"));
	}
	fwrite($fh,sprintf("<Channels>\n"));
	/*	<Channel color="-65536" output="1" id="634715461096095731" enabled="True">STR_1_PIX_1_RED</Channel>
	<Channel color="-16744448" output="2" id="634715461096095731" enabled="True">STR_1_PIX_1_GREEN</Channel>
	<Channel color="-16776961" output="3" id="634715461096095731" enabled="True">STR_1_PIX_1_BLUE</Channel>*/
	$channel=0;
	while (!feof($fh_vir))
	{
		$line = fgets($fh_vir);
		$tok=preg_split("/ +/", $line);
		$c=count($tok);
		if($c>1)
		{
			$channel++;
			if($channel%3==1)
			{
				$color=-65536; $rgb="R";
			}
			if($channel%3==2)
			{
				$color=-16744448; $rgb="G";
			}
			if($channel%3==0)
			{
				$color=-16776961; $rgb="B";
			}
			$channel_name = "Channel $channel $rgb";
			$output=$channel-1;
			fwrite($fh,sprintf("<Channel color=\"$color\" output=\"$output\" id=\"0\" enabled=\"True\">$channel_name</Channel>\n"));
		}
	}
	fwrite($fh,sprintf("</Channels>\n"));
	if($plugin==1)
	{
		fwrite($fh,sprintf("</PlugIn>\n"));
		fwrite($fh,sprintf("</PlugInData>\n"));
	}
	fwrite($fh,sprintf("<SortOrders lastSort=\"-1\" />\n"));
	fwrite($fh,sprintf("<EventValues>"));
	$eventdata = '';
	$channel=0;
	rewind($fh_vir);
	while (!feof($fh_vir))
	{
		$line = fgets($fh_vir);
		$channel++;
		$tok=preg_split("/ +/", $line);
		$c=count($tok);
		if($c>1)
		{
			foreach($tok as $i=>$val)
			{
				$eventdata .= chr($val+0);
			}
		}
	}
	fwrite($fh,base64_encode($eventdata));
	fwrite($fh,sprintf("</EventValues>\n"));
	/*fwrite($fh,sprintf("<LoadableData>\n"));
	fwrite($fh,sprintf("<IAddInData>\n"));
	fwrite($fh,sprintf("<IAddIn name=\"RGB Tree profiler\" />\n"));
	fwrite($fh,sprintf("<IAddIn name=\"Vixen Routine Loader\" />\n"));
	fwrite($fh,sprintf("</IAddInData>\n"));
	fwrite($fh,sprintf("</LoadableData>\n"));
	fwrite($fh,sprintf("<EngineType>Standard</EngineType>\n"));
	fwrite($fh,sprintf("<Extensions>\n"));
	fwrite($fh,sprintf("<Extension type=\".vix\" />\n"));
	fwrite($fh,sprintf("</Extensions>\n"));
	fwrite($fh,sprintf("<WindowSize>808,604</WindowSize>\n"));
	fwrite($fh,sprintf("<ChannelWidth>149</ChannelWidth>\n"));*/
	fwrite($fh,sprintf("</Program>\n"));
	fclose($fh);
	fclose($fh_vir);
}
