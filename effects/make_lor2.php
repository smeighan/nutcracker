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
<meta name="description" content="RGB Sequence builder for LOR, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, LOR, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
//
require("../effects/read_file.php");
echo "<pre>";
echo "max_execution_time =" . ini_get('max_execution_time') . "\n"; 
set_time_limit(300);
echo "max_execution_time =" . ini_get('max_execution_time') . "\n"; 
echo "</pre>";
//show_array($_SERVER,"SERVER");
// [QUERY_STRING] => make_lor.php?base=ZZ_ZZ+USER2?full_path=workspaces/2/ZZ_ZZ+USER2_d_1.dat?frame_delay=200?member_id=2?seq_duration=2?sequencer=lors2
// base=ZZ_ZZ+USER2?full_path=workspaces/2/ZZ_ZZ+USER2_d_1.dat?frame_delay=200?member_id=2?seq_duration=2?sequencer=lors2
//http://meighan.net/nutcracker_sean/effects/make_lor.php?base=AA+BARBERPOLE_180?full_path=workspaces/2/AA+BARBERPOLE_180_d_1.dat?frame_delay=50?member_id=2?seq_duration=9?sequencer=lors2?pixel_count=100
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
if($sequencer=="lors2") $file_type="lms";
if($sequencer=="lor_lcb") $file_type="lcb";
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
$supported = array('lor','hls','lors2','lor_lcb');
//echo "<pre";
//echo "sequencer=$sequencer\n";
if(!in_array($sequencer,$supported))
{
	echo "<pre>";
	echo "Your sequencer is not yet supported.\n";
	echo "-----------------------------------\n";
	echo "Currently spported sequencers:\n";
	echo "lor .... LOR 2.1 vir file\n";
	echo "hls ...... Joe Hinkle's new sequencer, HLS\n";
	echo "lors2 .... LOR S2 \n";
	echo "lor_lcb .... LOR LorClipBoard \n";
	echo "\n\n";
	echo "Soon to be spported sequencers:\n";
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
//$extension = $path_parts['extension'];
$filename  = $path_parts['filename'];
$path=$dirname . "/" . $basename;
/*
* #$path="workspaces/f/SGASE+SEAN33_d_1.dat";
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
*/
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
$maxLoop=1;
//
$full_path= "workspaces/$member_id/$base";
$path_parts = pathinfo($full_path);
$dirname   = $path_parts['dirname'];
$basename  = $path_parts['basename'];
//$extension =$path_parts['extension'];
//echo "<pre>";
//---------------------------------------------------------------------------------------
//	Lets find all the file names that are under the base. TARGET+EFFECT_d_nn.date
//
//	When we exit this loop we have filled the dat_file_array[].
//---------------------------------------------------------------------------------------
$dir = opendir ($dirname); 
$dat_file_array=array();
while (false !== ($file = readdir($dir)))
{
	$dat=strpos($file, '.dat',1);
	$m=strpos($file,$base,0);
	//echo "file =$file. dat,m=$dat,$m \n"; 
	if (strpos($file, '.dat',1) and strpos($file,$base,0)===0 )
	{
		$basen=basename($file,".dat");
		$tokens=explode("_d_",$basen);
		$c=count($tokens);
		if($c>1)
		{
			$i=$tokens[1];
			$dat_file_array[$i]=$file;
		}
		if($i>$maxFrame) $maxFrame=$i;
	}
}
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
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
//echo "</pre>\n";
$seq_file = $dirname . "/" . $base . ".dat";
$seq_srt = $dirname . "/" . $base . ".srt";
$fh_seq=fopen($seq_file,"w") or die ("unable to open $seq_file");
//---------------------------------------------------------------------------------------
//	Loop thru all dat files, and process them 1 by 1 and append them into seq_file. 
//	In other words read every dat file and make one huge file called TARGET+EFFECT.dat
//---------------------------------------------------------------------------------------
//
for ($frame=1;$frame<=$maxFrame;$frame++)
{
	$filename=$dat_file_array[$frame];
	$full_path = $dirname . "/" . $filename;
	$fh = fopen($full_path, 'r') or die("can't open file $full_path");
	//echo "<pre>processing $filename</pre>\n";
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
		//echo "<pre>frame=$frame  $line\n</pre>\n";
		$tok=preg_split("/ +/", $line);
		$l=strlen($line);
		$c= substr($line,0,1);
		//echo "<pre>frame=$frame l=$l, c=$c line=$line</pre>\n";
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
			fwrite($fh_seq,sprintf("%6d %6d %6d %9d # $loop  %s",$string,$user_pixel,$frame,$rgb,$line));
			//printf("%6d %6d %6d %9d # $loop  %s",$string,$user_pixel,$frame,$rgb,$line);
		}
	}
}
fclose($fh_seq);
//
//---------------------------------------------------------------------------------------
//	Ok, now that $basename.dat exists (the concatenated big file of all the dat's
//	sort it so we get the strings togeter. We will output this to TARGET+EFFECT.srt
//---------------------------------------------------------------------------------------
//
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
show_srt_file($seq_srt,$maxFrame,$frame_delay,$maxPixel,$pixel_count);
$loop=$hlsnc_loop=0;
$outBuffer=array();
$old_string=-1;
$lor_lms= $dirname . "/" . $base . ".$file_type";
$fh_lor=fopen($lor_lms,"w") or die("Unable to open $lor_lms");
if($file_type=="lms") lor_lms_header($fh_lor);
$name_clip=$base;
if($file_type=="lcb") lor_lcb_header($fh_lor,$name_clip);
//	how many frames should we do?
//
//	seq_duration = 9.5 seconds
//	frame_delay = 50  (ms)
	//
//	TotalFrames = (9.5*1000)/50
//	Totalframes = 190
//
if($frame_delay>0)
	$maxFrame= ($seq_duration*1000)/$frame_delay;
else
if($maxFrame>1000)
{
	echo "<font color=red><h2>Limiting current sequences to 1000 frames</h2></font>\n";
	$maxFrame=1000;
}
echo "<h3>$seq_duration seconds of animation with a $frame_delay ms frame timing = $maxFrame frames of animation</h3>\n";
$centiseconds=intval($maxFrame*$frame_delay);
$savedIndex=0;
$maxString=$maxPixel=0;
/*echo "RED:" . hexdec("#FF0000");
echo "GREEN:" . hexdec("#00FF00");
echo "BLUE:" . hexdec("#0000FF");*/
//
//	tok1 = string
//	tok2 = pixel
//	tok3 = frame#
//	tok4 = rgb value
//
if($file_type=="lcb")
{
	fwrite($fh_lor,sprintf("<cellDemarcations>\n"));
	for($f=1;$f<=$maxFrame;$f++)
	{
		$centisecond=intval(($f-1)*$frame_delay/10);
		fwrite($fh_lor,sprintf("<cellDemarcation centisecond=\"%d\"/>\n",$centisecond));
	}
	fwrite($fh_lor,sprintf("</cellDemarcations>\n"));
	fwrite($fh_lor,sprintf("<channels>\n"));
}
//---------------------------------------------------------------------------------------
//
//	Now we read the sorted file TARGET+EFFECT.srt and produce and output file
//	for LOR lms file
/*  1      5      1     26367 # 46  t1    1   46     0.000    33.470     0.284 26367 0 0 1 5
1      5      2      2559 # 46  t1    1   46     0.000    33.470     0.284 2559 0 0 1 5
1      5      3   5112063 # 46  t1    1   46     0.000    33.470     0.284 5112063 0 0 1 5
1      5      4  10354943 # 46  t1    1   46     0.000    33.470     0.284 10354943 0 0 1 5
1      5      5  15139071 # 46  t1    1   46     0.000    33.470     0.284 15139071 0 0 1 5
1      5      6  16711894 # 46  t1    1   46     0.000    33.470     0.284 16711894 0 0 1 5
1      5      7  16711837 # 46  t1    1   46     0.000    33.470     0.284 16711837 0 0 1 5
1      5      8  16711789 # 46  t1    1   46     0.000    33.470     0.284 16711789 0 0 1 5
1      5      9  16711749 # 46  t1    1   46     0.000    33.470     0.284 16711749 0 0 1 5
1      5     10  16711718 # 46  t1    1   46     0.000    33.470     0.284 16711718 0 0 1 5
1      5     11  16711697 # 46  t1    1   46     0.000    33.470     0.284 16711697 0 0 1 5
*/
//---------------------------------------------------------------------------------------
$Sec2=-1;
for($f=1;$f<=$maxFrame;$f++)
{
	$outBuffer[$f]=0;
}

$old_pixel=0;

while (!feof($fh))
{
	$line = fgets($fh);
	$tok=preg_split("/ +/", $line);
	$l=strlen($line);
	$c= substr($line,0,1);
	if($l>20 and $c !="#")
	{
		$i++;
		//print_r($tok);
		//print_r($outBuffer);
		$string=$tok[1];	// string#
		$pixel=$tok[2];	// pixel#
		if($string>$maxString) $maxString=$string;
		if($pixel>$maxPixel) $maxPixel=$pixel;
		$frame=$tok[3];	// frame#
		$rgb=$tok[4];	// rgb#
		$rgbhex=dechex($rgb);
		//n		echo "<pre>$i .. $rgbhex $line</pre>\n";
		//echo "<pre>string=$string,$pixel frame=$frame, rgb=$rgb\n";
		//echo "loop=$loop   string,pixe=$string,$pixel,$rgb old_string=$old_string\n";
		if($string>0 and $old_pixel>=1 and ($string!=$old_string or $pixel!=$old_pixel))
		{
			$rgbChannel_name[$old_string][$old_pixel]=sprintf("S%d-P%d",$old_string,$old_pixel);
			$array_write_buffer=write_buffer($outBuffer,$savedIndex,$file_type,$fh_lor,$string,$old_string,$pixel,$old_pixel,$maxPixel,$maxFrame,$frame_delay,$channel_savedIndex,$loop,$pixel_count);
			$savedIndex=$array_write_buffer[0];
			$channel_savedIndex=$array_write_buffer[1];
			$loop=$array_write_buffer[2];
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
//---------------------------------------------------------------------------------------
//	now write the rgb channels.
//---------------------------------------------------------------------------------------
/*
<rgbChannel totalCentiseconds="26427" name="R01-P01" savedIndex="314">
<channels>
<channel savedIndex="0"/>
<channel savedIndex="1"/>
<channel savedIndex="2"/>
</channels>
</rgbChannel>
<rgbChannel totalCentiseconds="26427" name="R01-P02" savedIndex="315">
<channels>
<channel savedIndex="3"/>
<channel savedIndex="4"/>
<channel savedIndex="5"/>
</channels>
</rgbChannel>
*/
if($file_type=="lms")
{
	//echo "<pre>";
	//	print_r($channel_savedIndex);
	//	echo "</pre>\n";
	for($string=1;$string<=$maxString;$string++)
		for($pixel=1;$pixel<=$maxPixel;$pixel++)
	{
		if (!isset($rgbChannel_name[$string][$pixel]) ||$rgbChannel_name[$string][$pixel] == null)
			;
		else
		{
			$name=$rgbChannel_name[$string][$pixel];
			fwrite($fh_lor,sprintf("<rgbChannel totalCentiseconds=\"%d\" name=\"%s\" savedIndex=\"%d\">\n",$centiseconds,$name,$savedIndex));
			$rgbSavedIndex[$string][$pixel]=$savedIndex;
			$savedIndex++;
			//if($SCM==1)
			{
				fwrite($fh_lor,sprintf("   <channels>\n"));
				fwrite($fh_lor,sprintf("      <channel savedIndex=\"%d\"/>\n",$channel_savedIndex[$string][$pixel]['1']));
				fwrite($fh_lor,sprintf("      <channel savedIndex=\"%d\"/>\n",$channel_savedIndex[$string][$pixel]['2']));
				fwrite($fh_lor,sprintf("      <channel savedIndex=\"%d\"/>\n",$channel_savedIndex[$string][$pixel]['3']));
				fwrite($fh_lor,sprintf("   </channels>\n"));
			}
			fwrite($fh_lor,sprintf("</rgbChannel>\n"));
		}
	}
	fwrite($fh_lor,sprintf("</channels>\n"));
	//
	/*	<timingGrids>
	<timingGrid saveID="0" name="Fixed Grid: 0.10" type="fixed" spacing="10"/>
	</timingGrids>
	<tracks>
	<track totalCentiseconds="26427" timingGrid="0">
	<channels>
	<channel savedIndex="0"/>
	<channel savedIndex="1"/>
	<channel savedIndex="2"/>
	<channel savedIndex="3"/>
	<channel savedIndex="4"/>
	<channel savedIndex="409"/>
	<channel savedIndex="410"/>
	<channel savedIndex="411"/>
	<channel savedIndex="412"/>
	<channel savedIndex="413"/>
	</channels>
	<loopLevels/>
	</track>
	</tracks>
	<animation rows="40" columns="60" image="" hideControls="false"/>
	</sequence>
	*/
	fwrite($fh_lor,sprintf("<timingGrids>\n"));
	$grid=$frame_delay/1000;
	fwrite($fh_lor,sprintf("		<timingGrid saveID=\"0\" name=\"Fixed Grid: %5.2f\" type=\"fixed\" spacing=\"10\"/>\n",$grid));
	fwrite($fh_lor,sprintf("	</timingGrids>\n"));
	fwrite($fh_lor,sprintf("	<tracks>\n"));
	fwrite($fh_lor,sprintf("		<track totalCentiseconds=\"%d\" timingGrid=\"0\">\n",$centiseconds));
	fwrite($fh_lor,sprintf("			<channels>\n"));
	for($string=1;$string<=$maxString;$string++)
		for($pixel=1;$pixel<=$maxPixel;$pixel++)
	{
		if (!isset($rgbChannel_name[$string][$pixel]) ||$rgbChannel_name[$string][$pixel] == null)
			;
		else
		{
			$savedIndex=$rgbSavedIndex[$string][$pixel];
			fwrite($fh_lor,sprintf("				<channel savedIndex=\"%d\"/>\n",$savedIndex));
		}
	}
	fwrite($fh_lor,sprintf("			</channels>\n"));
	fwrite($fh_lor,sprintf("		<loopLevels/>\n"));
	fwrite($fh_lor,sprintf("	</track>\n"));
	fwrite($fh_lor,sprintf("   </tracks>\n"));
	// <animation rows="40" columns="60" image="" hideControls="false"/>
	fwrite($fh_lor,sprintf("   <animation rows=\"40\" columns=\"60\" image=\"\" hideControls=\"false\"/>\n"));
	fwrite($fh_lor,sprintf("</sequence>\n"));
}
else if($file_type=="lcb")
{
	fwrite($fh_lor,sprintf("</channels>\n"));
	fwrite($fh_lor,sprintf("</channelsClipboard>\n"));
}
fclose($fh_lor);
if($sequencer=="lors2")
{
	printf ("<h2>$loop channels have been created for LOR lms file</h2>\n");
	printf ("<h2><a href=\"%s\">Click here for LOR lms file. %s</a></h2>\n",$lor_lms,$lor_lms);
}
if($sequencer=="lor_lcb")
{
	printf ("<h2>$loop channels have been created for LOR lcb file</h2>\n");
	printf ("<h2><a href=\"%s\">Click here for LOR lcb (LOR Clip Board) file. %s</a></h2>\n",$lor_lms,$lor_lms);
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
/*
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<sequence saveFileVersion="7" createdAt="8/10/2006 12:16:28 AM" musicFilename="Monique-Danielle-Carol-of-the-Bells.wav">
<channels>
*/

function lor_lms_header($fh)
{
	fwrite($fh,sprintf ("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n"));
	fwrite($fh,sprintf ("<sequence saveFileVersion=\"7\" createdAt=\"8/10/2006 12:16:28 AM\" >\n"));
	fwrite($fh,sprintf ("<channels>\n"));
}

function lor_lcb_header($fh,$name_clip)
{
	fwrite($fh,sprintf ("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n"));
	fwrite($fh,sprintf ("<channelsClipboard version=\"1\" name=\"%s\">\n",$name_clip));
}

function write_buffer($outBuffer,$savedIndex,$file_type,$fh_lor,$string,$old_string,$pixel,$old_pixel,$maxPixel,$maxFrame,$frame_delay,$channel_savedIndex,$loop,$pixel_count)
{
	//	echo "<pre>string,old_string,pixel,old_pixel=$string,$old_string    $pixel,$old_pixel\n";
	//	printf("S%d-P%d\n",$old_string,$old_pixel);
	//	print_r($outBuffer);
	//	echo "</pre>\n";
	$centiseconds=intval($maxFrame*$frame_delay);
	if($old_pixel<$maxPixel and $old_string==$string) //	96,97 (assume maxPixel=100))
	{
		$return_array=fill_missing($fh_lor,$file_type,$channel_savedIndex,$savedIndex,$string,$old_pixel+1,$maxPixel,$maxPixel,$pixel_count);
		$channel_savedIndex=$return_array[0];
		$savedIndex=$return_array[1];
	}
	if($pixel>1 and $old_string!=$string) // 5,6,7,8
	{
		$return_array=fill_missing($fh_lor,$file_type,$channel_savedIndex,$savedIndex,$string,1,$pixel-1,$maxPixel,$pixel_count);
		$channel_savedIndex=$return_array[0];
		$savedIndex=$return_array[1];
	}
	$rgbChannel_name[$old_string][$old_pixel]=sprintf("S%d-P%d",$old_string,$old_pixel); // save for later use
	for($rgbLoop=1;$rgbLoop<=3;$rgbLoop++)
	{
		if($rgbLoop==1)
		{
			$c='R';$color=255;
		}
		if($rgbLoop==2)
		{
			$c='G';$color=65280;
		}
		if($rgbLoop==3)
		{
			$c='B';$color=16711680;
		}
		//	RED:16711680  GREEN:65280   BLUE:255
		// per bob
		//Also the color assignments are wrong. NC has R and B flipped. 
		//Should be R color="255" G color="65280" B color="16711680" 
		// 
		//
		$channel_savedIndex[$old_string][$old_pixel][$rgbLoop]=$savedIndex;
		$unit = 3*($old_pixel-1)+$rgbLoop;
		$channels_per_string = $pixel_count*3;
		$unit0 =(intval(($loop)/$channels_per_string)*$channels_per_string/16)+1;
	//	$hex_string =dechex((float) intval($unit0 ));
		$hex_string = intval($unit0 +0.5 );
	//	echo "<pre>unit0,hex_string = $unit0,$hex_string. pixel_count,loop = $pixel_count,$loop channels_per_string=$channels_per_string</pre>\n";
		$channelName=sprintf("S%d-P%d",$old_string,$old_pixel);
		if($file_type=="lms") fwrite($fh_lor,sprintf("<channel name=\"%s-%s\" color=\"%d\" centiseconds=\"%d\" deviceType=\"LOR\" unit=\"%s\" circuit=\"%d\" savedIndex=\"%d\">\n",$channelName,$c,$color,$centiseconds,$hex_string,$unit,$savedIndex));
		if($file_type=="lcb") fwrite($fh_lor,sprintf("<channel>\n"));
		// <channels>                  <== lcb form
		// <channel>
		//	 <effect type="intensity" startCentisecond="0" endCentisecond="310" startIntensity="0" endIntensity="100"/>
		//     </channel>
		$savedIndex++;
		//printf("%3d-%3d-%s ",$old_string,$old_pixel,$c);
		$f=1;
		$finished = false;                       // we're not finished yet (we just started)
			while ( ! $finished )
		{
			if($outBuffer[$f]>0)
			{
				$rgb = $outBuffer[$f];
				$startF=$f;
				$f2=$f;
				while($outBuffer[$f2]==$rgb and $f2<=$maxFrame)
				{
					$f2++;
				}
				$startCentisecond=intval(($f-1)*$frame_delay/10);
				$endCentisecond  =intval(($f2-1)*$frame_delay/10);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				if($rgbLoop==1)
				{
					$val=$r;
				}
				if($rgbLoop==2)
				{
					$val=$g;
				}
				if($rgbLoop==3)
				{
					$val=$b;
				}
				$Intensity = intval((100*$val)/255);
				if($Intensity>0)
				{
					fwrite($fh_lor,sprintf("<effect type=\"intensity\" startCentisecond=\"%d\" endCentisecond=\"%d\" intensity=\"%d\" />\n",$startCentisecond,$endCentisecond,$Intensity));
					//	printf("<pre>2:[effect type=\"intensity\" startCentisecond=\"%d\" endCentisecond=\"%d\" intensity=\"%d\" /]</pre>\n",$startCentisecond,$endCentisecond,$Intensity);
				}
				$f=$f2-1;
			}
			$f++;
			if($f>$maxFrame) $finished=true;
		}
		# while ( ! $finished )
			$loop++;  // counter for how many channels written
		fwrite($fh_lor,sprintf("</channel>\n"));
	}
	# for($rgbLoop=1;$rgbLoop<=3;$rgbLoop++)
		$array_write_buffer[0]=$savedIndex;
	$array_write_buffer[1]=$channel_savedIndex;
	$array_write_buffer[2]=$loop;
	if(($pixel-$old_pixel)>1 and $old_string==$string)  //46,47,48,52,53
	{
		$return_array=fill_missing($fh_lor,$file_type,$channel_savedIndex,$savedIndex,$string,$old_pixel+1,$pixel-1,$maxPixel,$pixel_count);
		$channel_savedIndex=$return_array[0];
		$savedIndex=$return_array[1];
	}
	return $array_write_buffer;
}

function fill_missing($fh_lor,$file_type,$channel_savedIndex,$savedIndex,$string,$start_pixel,$end_pixel,$maxPixel,$pixel_count)
{
	for($p=$start_pixel;$p<=$end_pixel;$p++)
	{
		$rgbChannel_name[$old_string][$old_pixel]=sprintf("S%d-P%d",$string,$p); // save for later use
		for($rgbLoop=1;$rgbLoop<=3;$rgbLoop++)
		{
			if($rgbLoop==1)
			{
				$c='R';$color=255;
			}
			if($rgbLoop==2)
			{
				$c='G';$color=65280;
			}
			if($rgbLoop==3)
			{
				$c='B';$color=16711680;
			}
			// 
			//
			$channel_savedIndex[$string][$p][$rgbLoop]=$savedIndex;
			$unit = 3*($p-1)+$rgbLoop;
			$channels_per_string = $pixel_count*3;
			$unit0 =(intval(($loop)/$channels_per_string)*$channels_per_string/16)+1;
		//	$hex_string =dechex((float) intval($unit0 ));
			$hex_string = intval($unit0+0.5 );
	//		echo "<pre>missing: unit0,hex_string = $unit0,$hex_string. pixel_count,loop = $pixel_count,$loop channels_per_string=$channels_per_string</pre>\n";
			$channelName=sprintf("S%d-P%d",$string,$p);
			if($file_type=="lms")
			{
				fwrite($fh_lor,sprintf("<channel name=\"%s-%s\" color=\"%d\" centiseconds=\"%d\" deviceType=\"LOR\" unit=\"%s\" circuit=\"%d\" savedIndex=\"%d\">\n",$channelName,$c,$color,$centiseconds,$hex_string,$unit,$savedIndex));
			//	fwrite($fh_lor,sprintf("<effect type=\"intensity\" startCentisecond=\"0\" endCentisecond=\"20\" intensity=\"00\" />\n"));
			}
			fwrite($fh_lor,sprintf("</channel>\n"));
		}
	}
	$return_array[0]=$channel_savedIndex;
	$return_array[1]=$savedIndex;
}
