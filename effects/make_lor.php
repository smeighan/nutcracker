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

set_time_limit(0);


extract ($_GET);
if($sequencer=="lors2") $file_type="lms";
if($sequencer=="lor_lcb") $file_type="lcb";
$path_parts = pathinfo($full_path); // full_path=../effects/workspaces/426/MTREE~WASH_SP_062_05_MA_d_1.dat
$dirname   = $path_parts['dirname']; // workspaces/2
$basename  = $path_parts['basename']; // AA+CIRCLE1_d_1.dat
//$extension =$path_parts['extension']; // .dat
$filename  = $path_parts['filename']; //  AA+CIRCLE1_d_1

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
$supported = array('vixen','hls','lors2','lors3','lor_lcb');

if(!in_array($sequencer,$supported)){
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
//$extension = $path_parts['extension'];
$filename  = $path_parts['filename'];
$path=$dirname . "/" . $basename;
/*
* #$path="../effects/workspaces/f/SGASE+SEAN33_d_1.dat";
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

list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places

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
$tokens=explode("~",$base);
$target_name=$tokens[0];
$effect_name=$tokens[1];
$target_array= get_info_target($username,$target_name);
$maxString=$target_array['total_strings'];
$maxPixel=$target_array['pixel_count'];
$effect_user_dtl_array=get_effect_user_dtl($username,$effect_name);
$frame_delay=$sequence_duration=0;
foreach($effect_user_dtl_array as $i=>$effect_array){
	/*echo"<pre>i=$i";
	print_r($effect_array);
	echo "</pre>\n";*/
	if($effect_array['param_name']=='frame_delay') $frame_delay=$effect_array['param_value'];
	if($effect_array['param_name']=='seq_duration') $seq_duration=$effect_array['param_value'];
}
//
//
//	Here we read all *.dayt's, sort them , fill in any mising cells and write it out to a *.nc file.
//	we return a file handle to the *.nc file in read mode
//
$full_path= "../effects/workspaces/$member_id/$base";
$path_parts = pathinfo($full_path);
$dirname   = $path_parts['dirname'];
$basename  = $path_parts['basename'];
$lor_lms= $dirname . "/" . $base . ".$file_type";
$fh_lor=fopen($lor_lms,"w") or die("Unable to open $lor_lms");

//
//

$name_clip=$base;
// uneeded now, eacf effect builds buff file:   $filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration); 
$filename_buff= $dirname . "/" . $base . ".nc";
$old_pixel=$old_string=0;
if($frame_delay>0)	$maxFrame= ($seq_duration*1000)/$frame_delay;
else die ("frame_delay is zero, we cannot export any data");
echo "<h3>$seq_duration seconds of animation with a $frame_delay ms frame timing = $maxFrame frames of animation</h3>\n";
$centiseconds=intval($maxFrame*$frame_delay);
$savedIndex=0;
$centiseconds=intval(($maxFrame*$frame_delay)/10);
$savedIndex=0;
$channel_savedIndex=array();
/*echo "RED:" . hexdec("#FF0000");
echo "GREEN:" . hexdec("#00FF00");
echo "BLUE:" . hexdec("#0000FF");*/
//
//	tok1 = string
//	tok2 = pixel
//	tok3 = frame#
//	tok4 = rgb value
//
if($file_type=="lms"){
	lor_lms_header($fh_lor);
	/*fwrite($fh_lor,sprintf ("<pre>lor_lms_header($fh_lor);	</pre>\n"));*/
}
//
$name_clip=$base;
if($file_type=="lcb"){
	lor_lcb_header($fh_lor,$name_clip);
	/*fwrite($fh_lor,sprintf ("<pre>lor_lcb_header($fh_lor,$name_clip);</pre>\n"));*/
}
if($frame_delay<1) $frame_delay=100;
if($frame_delay>0)	$TotalFrames= ($seq_duration*1000)/$frame_delay;
else die ("frame_delay = 0, unable to create any output");
if($file_type=="lcb"){
	fwrite($fh_lor,sprintf("<cellDemarcations>\n"));
	printf("<cellDemarcations>\n");
	for($f=1;$f<=$TotalFrames;$f++){
		$centisecond=intval(($f-1)*$frame_delay/10);
		fwrite($fh_lor,sprintf("<cellDemarcation centisecond=\"%d\"/>\n",$centisecond));
	
	}
	fwrite($fh_lor,sprintf("</cellDemarcations>\n"));
	fwrite($fh_lor,sprintf("<channels>\n"));
}

//
//	write header data
//
//
//
//	now, we will read the buff file and create channel info
//
echo "<pre>";
$fh_buff=fopen($filename_buff,"r") or die("Unable to open $filename_buff");
$loop=$channels=$savedIndex=0;
$MaxPixel=$pixel_count;
while(!feof($fh_buff)){
	$line = fgets($fh_buff);
	$tok=preg_split("/ +/", $line);
	$l=strlen($line);
	$c= count($tok);
	//echo "c=$c line=$line\n";
	if($tok[0]=='S' and $tok[2]=='P'){
		$string=$tok[1];
		$pixel=$tok[3];
		$channel_savedIndex[$string][$pixel]['1']=$savedIndex;
		$channel_savedIndex[$string][$pixel]['2']=$savedIndex+1;
		$channel_savedIndex[$string][$pixel]['3']=$savedIndex+2;
		$rgbChannel_name[$string][$pixel]=sprintf("S%d-P%d",$string,$pixel);
		$loop++;
		$array_write_buffer=write_buffer($tok,$file_type,$fh_lor,$maxFrame,$frame_delay,$savedIndex,$loop,$pixel_count);
		//$savedIndex=$array_write_buffer[0];
		//$channel_savedIndex=$array_write_buffer[1];
		$channels+=3;
		$savedIndex+=3;
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
$firstRGBIndex=$lastRGBIndex=-1;
if($file_type=="lms"){
	/*echo "<pre>";
	print_r($channel_savedIndex);
	echo "for(string=1;string<=$maxString;string++)\n";
	echo "for(pixel=1;pixel<=$maxPixel;pixel++)\n";
	echo "</pre>\n";*/
	for($string=1;$string<=$maxString;$string++){
		for($pixel=1;$pixel<=$maxPixel;$pixel++){
			if(!isset($rgbChannel_name[$string][$pixel]) ||$rgbChannel_name[$string][$pixel] == null)				;
			else{
				$name=$rgbChannel_name[$string][$pixel];
				if($firstRGBIndex==-1) $firstRGBIndex=$savedIndex;
				$lastRGBIndex=$savedIndex;
				fwrite($fh_lor,sprintf("<rgbChannel totalCentiseconds=\"%d\" name=\"%s\" savedIndex=\"%d\">\n",$centiseconds,$name,$savedIndex));
				$rgbSavedIndex[$string][$pixel]=$savedIndex;
				$savedIndex++;
				//if($SCM==1)
				{
					fwrite($fh_lor,sprintf("   <channels>\n"));
					if(!isset($channel_savedIndex[$string][$pixel]['1']) or $channel_savedIndex[$string][$pixel]['1']==null)						;//	echo "<pre>Error1: Unknown entry for string=$string, pixel=$pixel [1]</pre>";
					else					fwrite($fh_lor,sprintf("      <channel savedIndex=\"%d\"/>\n",$channel_savedIndex[$string][$pixel]['1']));
					if(!isset($channel_savedIndex[$string][$pixel]['2']) or $channel_savedIndex[$string][$pixel]['2']==null)						;//	echo "<pre>Error2: Unknown entry for string=$string, pixel=$pixel [2]</pre>";
					else					fwrite($fh_lor,sprintf("      <channel savedIndex=\"%d\"/>\n",$channel_savedIndex[$string][$pixel]['2']));
					if(!isset($channel_savedIndex[$string][$pixel]['3']) or $channel_savedIndex[$string][$pixel]['3']==null)						;//	echo "<pre>Error3: Unknown entry for string=$string, pixel=$pixel [3]</pre>";
					else					fwrite($fh_lor,sprintf("      <channel savedIndex=\"%d\"/>\n",$channel_savedIndex[$string][$pixel]['3']));
					fwrite($fh_lor,sprintf("   </channels>\n"));
				}
				fwrite($fh_lor,sprintf("</rgbChannel>\n"));
			}
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
	for($RGBsavedIndex=$firstRGBIndex;$RGBsavedIndex<=$lastRGBIndex;$RGBsavedIndex++){
		fwrite($fh_lor,sprintf("				<channel savedIndex=\"%d\"/>\n",$RGBsavedIndex));
	}
	fwrite($fh_lor,sprintf("			</channels>\n"));
	fwrite($fh_lor,sprintf("		<loopLevels/>\n"));
	fwrite($fh_lor,sprintf("	</track>\n"));
	fwrite($fh_lor,sprintf("   </tracks>\n"));
	// <animation rows="40" columns="60" image="" hideControls="false"/>
	fwrite($fh_lor,sprintf("   <animation rows=\"40\" columns=\"60\" image=\"\" hideControls=\"false\"/>\n"));
	fwrite($fh_lor,sprintf("</sequence>\n"));
}
elseif($file_type=="lcb"){
	fwrite($fh_lor,sprintf("</channels>\n"));
	fwrite($fh_lor,sprintf("</channelsClipboard>\n"));
}
fclose($fh_lor);
if($frame_delay>0)	$TotalFrames= ($seq_duration*1000)/$frame_delay;
if($sequencer=="lors2"){
	echo "<table border=1>";
	printf ("<tr><td bgcolor=lightgreen><h2>$channels channels and $TotalFrames frames have been created for LOR lms file</h2></td>\n");
	echo "<td>Instructions</td></tr>";
	printf ("<tr><td bgcolor=#98FF73><h2><a href=\"%s\">Right Click here for  LOR lms file. %s</a>.</h2></td>\n",$lor_lms,$lor_lms);
	echo "<td>Save lms file into your light-o-rama/sequences directory</td></tr>\n";
	echo "</table>";
	
}
if($sequencer=="lor_lcb"){
	
	echo "<table border=1>";
	printf ("<tr><td bgcolor=lightgreen><h2>$channels channels and $TotalFrames frames have been created for LOR lcb file</h2></td>\n");
	echo "<td>Instructions</td></tr>";
	printf ("<tr><td bgcolor=#98FF73><h2><a href=\"%s\">Right Click here for  LOR lcb file. %s</a>.</h2></td>\n",$lor_lms,$lor_lms);
	echo "<td>Save lcb file into your light-o-rama/Clipboards directory</td></tr>\n";
	echo "</table>";
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

function lor_lms_header($fh_lor){
	fwrite($fh_lor,sprintf ("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n"));
	fwrite($fh_lor,sprintf ("<sequence saveFileVersion=\"7\" createdAt=\"8/10/2006 12:16:28 AM\" >\n"));
	fwrite($fh_lor,sprintf ("<channels>\n"));
}

function lor_lcb_header($fh_lor,$name_clip){
	fwrite($fh_lor,sprintf ("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n"));
	fwrite($fh_lor,sprintf ("<channelsClipboard version=\"1\" name=\"%s\">\n",$name_clip));
}

function write_buffer($tok,$file_type,$fh_lor,$maxFrame,$frame_delay,$InputsavedIndex,$loop,$pixel_count){
	//	echo "<pre>string,old_string,pixel,old_pixel=$string,$old_string    $pixel,$old_pixel\n";
	//	printf("S%d-P%d\n",$old_string,$old_pixel);
	//	print_r($outBuffer);
	//	echo "</pre>\n";
	//echo "<pre>function write_buffer($tok,$file_type,$fh_lor,$maxFrame,$frame_delay,$savedIndex,$loop,$pixel_count)</pre>\n";
	$cnt=count($tok);
	//$maxFrame=$cnt-3;
	$centiseconds=intval(($maxFrame*$frame_delay)/10);
	$string=$tok[1];
	$pixel=$tok[3];
	$old_string=$string;
	$old_pixel=$pixel;
	$rgbChannel_name[$string][$pixel]=sprintf("S%d-P%d",$string,$pixel); // save for later use
	//	printf("<pre>S%d-P%d</pre>\n",$string,$pixel);
	$cnt=count($tok);
	for($rgbLoop=1;$rgbLoop<=3;$rgbLoop++){
		//echo "rgbloop=$rgbLoop, i=$i,rgb=$rgb\n";
		/*(16711680,65280,255)*/
		$savedIndex=$InputsavedIndex-1+$rgbLoop;
		if($rgbLoop==1){
			$c='R';$color=255;
		}
		if($rgbLoop==2){
			$c='G';$color=65280;
		}
		if($rgbLoop==3){
			$c='B';$color=16711680;
		}
		//	RED:16711680  GREEN:65280   BLUE:255
		// per bob
		//Also the color assignments are wrong. NC has R and B flipped. 
		//Should be R color="255" G color="65280" B color="16711680" 
		// 
		//
		//$savedIndex = ($old_string-1) * $pixel_count + ($old_pixel-1)*3 + $rgbLoop-1;
		$channel_savedIndex[$old_string][$old_pixel][$rgbLoop]=$InputsavedIndex-1+$rgbLoop;
		$unit = 3*($old_pixel-1)+$rgbLoop;
		$channels_per_string = $pixel_count*3;
		$unit0 =(intval(($loop)/$channels_per_string)*$channels_per_string/16)+1;
		//	$circuit =dechex((float) intval($unit0 ));
		$circuit_tmp = intval($unit0 +0.5 );
		$network = intval($circuit_tmp/340)+1;
		$circuit=($circuit_tmp%340);
		//	echo "<pre>unit0,circuit = $unit0,$circuit. pixel_count,loop = $pixel_count,$loop channels_per_string=$channels_per_string</pre>\n";
		$channelName=sprintf("S%d-P%d",$old_string,$old_pixel);
		$unit=$old_string;
		$circuit=$rgbLoop+($old_pixel-1)*3;
		if($file_type=="lms"){
			fwrite($fh_lor,sprintf("<channel name=\"%s-%s\" color=\"%d\" centiseconds=\"%d\" deviceType=\"LOR\" unit=\"%s\" circuit=\"%d\" network=\"%d\" savedIndex=\"%d\">\n",$channelName,$c,$color,$centiseconds,$unit,$circuit,$network,$savedIndex));
			//	printf("<pre>channel name=\"%s-%s\" color=\"%d\" centiseconds=\"%d\" deviceType=\"LOR\" unit=\"%s\" circuit=\"%d\" network=\"%d\" savedIndex=\"%d\"</pre>\n",$channelName,$c,$color,$centiseconds,$circuit,$unit,$network,$savedIndex);
		}
		if($file_type=="lcb") fwrite($fh_lor,sprintf("<channel>\n"));
		// <channels>                  <== lcb form
		// <channel>
		//	 <effect type="intensity" startCentisecond="0" endCentisecond="310" startIntensity="0" endIntensity="100"/>
		//     </channel>
		//printf("%3d-%3d-%s ",$old_string,$old_pixel,$c);
		$i=4;
		while($i<$cnt){
			$rgb = $tok[$i];      
			$j=$i;
			$rgb = $tok[$i];
			while($j<$cnt and $rgb == $tok[$j]){
				//echo "<pre>j loop:  i=$i, j=$j rgb=" . $tok[$i] . "</pre>\n";
				$j++;
			}
			$startCentisecond=intval((($i-4)*$frame_delay)/10);
			$endCentisecond  =intval((($j-4)*$frame_delay)/10);
			if($j==$cnt) $endCentisecond  =intval((($j-5)*$frame_delay)/10);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;
			$r1 = 255 <<16;
			$g1 = 255<<8;
			$b1 =255;
			if($rgbLoop==1){
				$val=$r;
			}
			if($rgbLoop==2){
				$val=$g;
			}
			if($rgbLoop==3){
				$val=$b;
			}
			$Intensity = intval((100*$val)/255);
			if($Intensity>=0){
				if($Intensity>0){
					fwrite($fh_lor,sprintf("<effect type=\"intensity\" startCentisecond=\"%d\" endCentisecond=\"%d\" intensity=\"%d\" />\n",$startCentisecond,$endCentisecond,$Intensity));
					/*printf("<pre>2:[effect type=\"intensity\" startCentisecond=\"%d\" endCentisecond=\"%d\" intensity=\"%d\" rgb=(%d,%d,%d)(%d,%d,%d))) /]</pre>\n",$startCentisecond,$endCentisecond,$Intensity,$r,$g,$b,$r1,$g1,$b1);*/
				}
			}
			$i=$j;
		}
		$loop++;  // counter for how many channels written
		fwrite($fh_lor,sprintf("</channel>\n"));
	}
	$array_write_buffer[0]=$savedIndex;
	$array_write_buffer[1]=$channel_savedIndex;
	$array_write_buffer[2]=$loop;
	/*echo "<pre>";
	print_r($channel_savedIndex);
	echo "</pre>\n";*/
	ob_flush();
	flush();
	return $array_write_buffer;
}
