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
require_once('../conf/header.php');
//
require("../effects/read_file.php");
set_time_limit(3600);


extract ($_GET);

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
$supported = array('vixen','hls','lors2','lors3');
if(!in_array($sequencer,$supported))
{
	echo "<pre>";
	echo "Your sequencer is not yet supported.\n";
	echo "-----------------------------------\n";
	echo "Currently spported sequencers:\n";
	echo "vixen .... Vixen 2.1 vir file\n";
	echo "hls ...... Joe Hinkle's new sequencer, HLS\n";
	echo "lors2 .... LOR S2 \n";
	echo "lors3 .... LOR S3\n";
	echo "\n\n";
	echo "Soon to be spported sequencers:\n";
	echo "lsp2 ..... LSP 2.0 )\n";
	echo "lsp3 ..... LSP 3.0 \n";
	?>
	<a href="../index.html">Home</a> | <a href="../login/member-index.php">Target Generator</a> | 
	<a href="effect-form.php">Effects Generator</a> | <a href="../login/logout.php">Logout</a>
	<?php
	exit();
}
extract($_GET);
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
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
//if($description = 'Total Elapsed time for this effect:')
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
if($frame_delay>0)
	$TotalFrames= ($seq_duration*1000)/$frame_delay;
else
{
	echo "<pre>Error! frame_delay was zero</pre>\n";
	$TotalFrames=$MaxFrame;
}
//$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration); 
$filename_buff= "workspaces/" . $member_id . "/" . $base . ".nc";
/*$create_srt_file_array=create_srt_file($full_path,$base,$username,$frame_delay,$TotalFrames);
$maxFrame=$create_srt_file_array[0];
$seq_srt=$create_srt_file_array[1];
$fh = fopen($seq_srt, 'r') or die("can't open file $seq_srt");*/
$loop=$hlsnc_loop=0;
$outBuffer=array();
$old_string=-1;
$full_path= "workspaces/$member_id/$base";
$path_parts = pathinfo($full_path);
$dirname   = $path_parts['dirname'];
$basename  = $path_parts['basename'];
$hlsnc= $dirname . "/" . $base . ".hlsnc";
$fh_hlsnc=fopen($hlsnc,"w") or die("Unable to open $hlsnc");
$fh_buff=fopen($filename_buff,"r") or die("Unable to open $filename_buff");
/*$fh_vixen_csv=fopen($vixen_csv,"w") or die("Unable to open $vixen_csv");*/
//	how many frames should we do?
//
//	seq_duration = 9.5 seconds
//	frame_delay = 50  (ms)
	//
//	TotalFrames = (9.5*1000)/50
//	Totalframes = 190
//
$old_pixel=$channels=0;
echo "<h3>$seq_duration seconds of animation with a $frame_delay ms frame timing = $TotalFrames frames of animation</h3>\n";
$line_counter=0;
while (!feof($fh_buff))
{
	$line_counter++;
	$line = fgets($fh_buff);
	$tok=preg_split("/ +/", $line);
	$l=strlen($line);
	$cnt= count($tok);
	$MaxFrame=$cnt-4;
	//echo "cnt=$cnt MaxFrame=$MaxFrame, line=$line\n";
	if($tok[0]=='S')  // "S 1 P 1 12345 1234 1234 1234"
	{
		$string=$tok[1];
		$pixel=$tok[3];
		for($f=1;$f<=$MaxFrame;$f++)
		{
			fwrite($fh_hlsnc,sprintf("%d ",$tok[$f+3]));
		}
		$channels++;
		fwrite($fh_hlsnc,sprintf("\n"));
	}
}
fclose($fh_hlsnc);
/*fclose($fh_vixen_csv);
fclose($fh_hlsnc);*/
/*$TotalFrames= ($seq_duration*1000)/$frame_delay;*/
$duration = $seq_duration*1000;
echo "<table border=1>";
printf ("<tr><td bgcolor=lightgreen><h2>$channels channels have been created for HLS ($line_counter lines read)</h2></td>\n");
echo "<td>Instructions</td></tr>";
printf ("<tr><td bgcolor=#98FF73><h2><a href=\"%s\">Right Click here for HLSNC file. %s</a>.</h2></td>\n",$hlsnc,$hlsnc);
echo "<td>Save hlsnc file into your HLS directory </td></tr>\n";
echo "</table>";
$description ="Total Elapsed time for this effect:";
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
//if($description = 'Total Elapsed time for this effect:')
	printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
?>
<a href="../index.html">Home</a> | <a href="../login/member-index.php">Target Generator</a> | 
<a href="effect-form.php">Effects Generator</a> | <a href="../login/logout.php">Logout</a>
