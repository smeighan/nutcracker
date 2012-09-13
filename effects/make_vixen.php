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
//echo "<pre>";
//echo "max_execution_time =" . ini_get('max_execution_time') . "\n"; 
set_time_limit(60*60);

//
extract($_GET);
echo "<pre>";
print_r($_GET);
echo "</pre>";

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
	echo "</pre>";
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
$full_path= "../effects/workspaces/$member_id/$base";
if($frame_delay>0)
	$TotalFrames= ($seq_duration*1000)/$frame_delay;
else
{
	echo "<pre>Error! frame_delay was zero</pre>\n";
	$TotalFrames=$MaxFrame;
}

// uneeded , each effect file makes this now:  $filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration); 
$filename_buff= "../effects/workspaces/" . $member_id . "/" . $base . ".nc";
/*$create_srt_file_array=create_srt_file($full_path,$base,$username,$frame_delay,$TotalFrames);
$maxFrame=$create_srt_file_array[0];
$seq_srt=$create_srt_file_array[1];
$fh = fopen($seq_srt, 'r') or die("can't open file $seq_srt");*/
$loop=$hlsnc_loop=0;
$outBuffer=array();
$old_string=-1;
$full_path= "../effects/workspaces/$member_id/$base";
$path_parts = pathinfo($full_path);
$dirname   = $path_parts['dirname'];
$basename  = $path_parts['basename'];
$vixen_vir= $dirname . "/" . $base . ".vir";
$vixen_vix= $dirname . "/" . $base . ".vix";
$vixen_csv= $dirname . "/" . $base . ".txt";
$hlsnc= $dirname . "/" . $base . ".hlsnc";
$fh_buff=fopen($filename_buff,"r") or die("Unable to open $filename_buff");
$fh_vixen_vir=fopen($vixen_vir,"w") or die("Unable to open $vixen_vir");

/*$fh_vixen_csv=fopen($vixen_csv,"w") or die("Unable to open $vixen_csv");*/
//$fh_hlsnc=fopen($hlsnc,"w") or die("Unable to open $hlsnc");
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
while (!feof($fh_buff))
{
	$line = fgets($fh_buff);
	$tok=preg_split("/ +/", $line);
	$l=strlen($line);
	$cnt= count($tok);
	$MaxFrame=$cnt-4;
	//echo "<pre>cnt=$cnt MaxFrame=$MaxFrame, line=$line</pre>\n";
	if($tok[0]=='S' and $tok[2]=='P')
	{
		$string=$tok[1];
		$pixel=$tok[3];
	//	echo "<pre>s,p=$string,$pixel: $line</pre>\n";
		for($rgbLoop=1;$rgbLoop<=3;$rgbLoop++)
		{
			for($f=1;$f<$MaxFrame;$f++)
			{
				$rgb=$tok[$f+3];
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				if($rgbLoop==1)
				{
					$c='R';$color=16711680;
					$val=$r;
				}
				if($rgbLoop==2)
				{
					$c='G';$color=65280;
					$val=$g;
				}
				if($rgbLoop==3)
				{
					$c='B';$color=255;
					$val=$b;
				}
				fwrite($fh_vixen_vir,sprintf("%d ",$val));
				//printf("%d ",$val);
				
			}
			$channels++;
			fwrite($fh_vixen_vir,sprintf("\n"));
			//printf("\n");
		}
	}
}
fclose($fh_vixen_vir);
/*fclose($fh_vixen_csv);
fclose($fh_hlsnc);*/
/*$TotalFrames= ($seq_duration*1000)/$frame_delay;*/
$duration = $seq_duration*1000;
make_vix($vixen_vir,$duration,$frame_delay);  // also make a *.vix file. Pass in the *.vir
if($sequencer=="vixen")
{
	echo "<table border=1>";
	printf ("<tr><td bgcolor=lightgreen><h2>$channels channels have been created for Vixen</h2></td>\n");
	echo "<td>Instructions</td></tr>";
	printf ("<tr><td bgcolor=#98FF73><h2><a href=\"%s\">Right Click here for Vixen vir file. %s</a></h2></td>\n",$vixen_vir,$vixen_vir);
	echo "<td>Save the file to your Vixen/routines directory</td></tr>";
	printf ("<tr><td bgcolor=#98FF73><h2><a href=\"%s\">Right Click here for Vixen vix file. %s</a></h2></td>\n",$vixen_vix,$vixen_vix);
	echo "<td>Save the file to your Vixen/sequences directory</td></tr>";
	echo "</table>";
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
	//$file_vire = $dirname . "/" . $filename . ".vire"; //  enhanced vir file()
	
	
	$fh_vir=fopen($vixen_vir,"r") or die("Unable to open $vixen_vir");
//	$fh_vire=fopen($vixen_vire,"w") or die("Unable to open $vixen_vir");
	
	
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
		//	echo "<pre>make_vix c=$c, $line</pre>\n";
		if($c>1)
		{
			foreach($tok as $i=>$val)
			{
				if($i<=$c-1)
				{
					//echo "<pre>c=$c, i=$i,val=$val</pre>\n";
					if($val>=0 and $val<=255)
					{
						$eventdata .= chr($val);
						//	printf ("<pre>line %d. i=%d val=[%d:%c]</pre>\n",$channel,$i,$val,$val);
					}
					else
					echo "<pre>ERROR! line $channel. i=$i val=[$val]</pre>\n";
				}
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
