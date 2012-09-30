<?php

function genLOR($fh_lor,$NCFile, $frame_delay, $seq_duration) {
	//$maxFrame= ($seq_duration*1000)/$frame_delay;
	$maxFrame=$seq_duration/$frame_delay;
	$centiseconds=intval(($maxFrame*$frame_delay)/10);
	$savedIndex=0;
	$channel_savedIndex=array();
	$file_type="lms";
//	if($file_type=="lms")
	lor_lms_header($fh_lor);
//	$name_clip=$base;
//	if($file_type=="lcb")
//		lor_lcb_header($fh_lor,$name_clip);
/*	if($file_type=="lcb")
	{
		fwrite($fh_lor,sprintf("<cellDemarcations>\n"));
		printf("<cellDemarcations>\n");
		for($f=1;$f<=$maxFrame;$f++)
		{
			$centisecond=intval(($f-1)*$frame_delay/10);
			fwrite($fh_lor,sprintf("<cellDemarcation centisecond=\"%d\"/>\n",$centisecond));
		
		}
		fwrite($fh_lor,sprintf("</cellDemarcations>\n"));
		fwrite($fh_lor,sprintf("<channels>\n"));
	}
*/
	$fh_NC=fopen($NCFile,"r") or die("Unable to open $NCFile");
	$loop=$channels=$savedIndex=0;
	$myArray=getNCInfo($NCFile);
	$pixel_count=$myArray[2];
	$maxString=$myArray[3];
	$maxPixel=$pixel_count;
	while (!feof($fh_NC))
	{
		$line = fgets($fh_NC);
		$tok=preg_split("/ +/", $line);
		$l=strlen($line);
		$c= count($tok);
		if($tok[0]=='S' and $tok[2]=='P')
		{
			$string=$tok[1];
			$pixel=$tok[3];
			$channel_savedIndex[$string][$pixel]['1']=$savedIndex;
			$channel_savedIndex[$string][$pixel]['2']=$savedIndex+1;
			$channel_savedIndex[$string][$pixel]['3']=$savedIndex+2;
			$rgbChannel_name[$string][$pixel]=sprintf("S%d-P%d",$string,$pixel);
			$loop++;
			$array_write_buffer=write_buffer($tok,$file_type,$fh_lor,$maxFrame,$frame_delay,$savedIndex,$loop,$pixel_count);
			$channels+=3;
			$savedIndex+=3;
		}
	}
	fclose($fh_NC);
	$firstRGBIndex=$lastRGBIndex=-1;
//	if($file_type=="lms")
//	{
	for($string=1;$string<=$maxString;$string++)
	{
		for($pixel=1;$pixel<=$maxPixel;$pixel++)
		{
			if (isset($rgbChannel_name[$string][$pixel]) && $rgbChannel_name[$string][$pixel] != null)
			{
				$name=$rgbChannel_name[$string][$pixel];
				if($firstRGBIndex==-1) $firstRGBIndex=$savedIndex;
				$lastRGBIndex=$savedIndex;
				fwrite($fh_lor,sprintf("<rgbChannel totalCentiseconds=\"%d\" name=\"%s\" savedIndex=\"%d\">\n",$centiseconds,$name,$savedIndex));
				$rgbSavedIndex[$string][$pixel]=$savedIndex;
				$savedIndex++;
				fwrite($fh_lor,sprintf("   <channels>\n"));
				if(isset($channel_savedIndex[$string][$pixel]['1']) && $channel_savedIndex[$string][$pixel]['1']!=null)
					fwrite($fh_lor,sprintf("      <channel savedIndex=\"%d\"/>\n",$channel_savedIndex[$string][$pixel]['1']));
				if(isset($channel_savedIndex[$string][$pixel]['2']) && $channel_savedIndex[$string][$pixel]['2']!=null)
					fwrite($fh_lor,sprintf("      <channel savedIndex=\"%d\"/>\n",$channel_savedIndex[$string][$pixel]['2']));
				if(isset($channel_savedIndex[$string][$pixel]['3']) && $channel_savedIndex[$string][$pixel]['3']!=null)
					fwrite($fh_lor,sprintf("      <channel savedIndex=\"%d\"/>\n",$channel_savedIndex[$string][$pixel]['3']));
				fwrite($fh_lor,sprintf("   </channels>\n"));
				fwrite($fh_lor,sprintf("</rgbChannel>\n"));
			}
		}
	}
	fwrite($fh_lor,sprintf("</channels>\n"));
	fwrite($fh_lor,sprintf("<timingGrids>\n"));
	$grid=$frame_delay/1000;
	fwrite($fh_lor,sprintf("		<timingGrid saveID=\"0\" name=\"Fixed Grid: %5.2f\" type=\"fixed\" spacing=\"10\"/>\n",$grid));
	fwrite($fh_lor,sprintf("	</timingGrids>\n"));
	fwrite($fh_lor,sprintf("	<tracks>\n"));
	fwrite($fh_lor,sprintf("		<track totalCentiseconds=\"%d\" timingGrid=\"0\">\n",$centiseconds));
	fwrite($fh_lor,sprintf("			<channels>\n"));
	for($RGBsavedIndex=$firstRGBIndex;$RGBsavedIndex<=$lastRGBIndex;$RGBsavedIndex++)
	{
		fwrite($fh_lor,sprintf("				<channel savedIndex=\"%d\"/>\n",$RGBsavedIndex));
	}
	fwrite($fh_lor,sprintf("			</channels>\n"));
	fwrite($fh_lor,sprintf("		<loopLevels/>\n"));
	fwrite($fh_lor,sprintf("	</track>\n"));
	fwrite($fh_lor,sprintf("   </tracks>\n"));
	fwrite($fh_lor,sprintf("   <animation rows=\"40\" columns=\"60\" image=\"\" hideControls=\"false\"/>\n"));
	fwrite($fh_lor,sprintf("</sequence>\n"));
//	}
//	else if($file_type=="lcb")
//	{
//		fwrite($fh_lor,sprintf("</channels>\n"));
//		fwrite($fh_lor,sprintf("</channelsClipboard>\n"));
//	}
//	fclose($fh_lor);
//	if($sequencer=="lors2")
//	{

		
//	}
/*	if($sequencer=="lor_lcb")
	{
		
		echo "<table border=1>";
		printf ("<tr><td bgcolor=lightgreen><h2>$channels channels and $Maxframe frames have been created for LOR lcb file</h2></td>\n");
		echo "<td>Instructions</td></tr>";
		printf ("<tr><td bgcolor=#98FF73><h2><a href=\"%s\">Right Click here for  LOR lcb file. %s</a>.</h2></td>\n",$lor_lms,$lor_lms);
		echo "<td>Save lcb file into your light-o-rama/Clipboards directory</td></tr>\n";
		echo "</table>";
	}
*/

}
function lor_lms_header($fh_lor)
{
	fwrite($fh_lor,sprintf ("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n"));
	fwrite($fh_lor,sprintf ("<sequence saveFileVersion=\"7\" createdAt=\"8/10/2006 12:16:28 AM\" >\n"));
	fwrite($fh_lor,sprintf ("<channels>\n"));
}

function lor_lcb_header($fh_lor,$name_clip)
{
	fwrite($fh_lor,sprintf ("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n"));
	fwrite($fh_lor,sprintf ("<channelsClipboard version=\"1\" name=\"%s\">\n",$name_clip));
}

function write_buffer($tok,$file_type,$fh_lor,$maxFrame,$frame_delay,$InputsavedIndex,$loop,$pixel_count)
{
	$cnt=count($tok);
	$centiseconds=intval(($maxFrame*$frame_delay)/10);
	$string=$tok[1];
	$pixel=$tok[3];
	$old_string=$string;
	$old_pixel=$pixel;
	$rgbChannel_name[$string][$pixel]=sprintf("S%d-P%d",$string,$pixel); // save for later use
	$cnt=count($tok);
	for($rgbLoop=1;$rgbLoop<=3;$rgbLoop++)
	{
		$savedIndex=$InputsavedIndex-1+$rgbLoop;
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
		$channel_savedIndex[$old_string][$old_pixel][$rgbLoop]=$InputsavedIndex-1+$rgbLoop;
		$unit = 3*($old_pixel-1)+$rgbLoop;
		$channels_per_string = $pixel_count*3;
		$unit0 =(intval(($loop)/$channels_per_string)*$channels_per_string/16)+1;
		$circuit_tmp = intval($unit0 +0.5 );
		$network = intval($circuit_tmp/340)+1;
		$circuit=($circuit_tmp%340);
		$channelName=sprintf("S%d-P%d",$old_string,$old_pixel);
		$unit=$old_string;
		$circuit=$rgbLoop+($old_pixel-1)*3;
		if($file_type=="lms")
			fwrite($fh_lor,sprintf("<channel name=\"%s-%s\" color=\"%d\" centiseconds=\"%d\" deviceType=\"LOR\" unit=\"%s\" circuit=\"%d\" network=\"%d\" savedIndex=\"%d\">\n",$channelName,$c,$color,$centiseconds,$unit,$circuit,$network,$savedIndex));
		if($file_type=="lcb") fwrite($fh_lor,sprintf("<channel>\n"));
		$i=4;
		while ($i<$cnt)
		{
			$rgb = $tok[$i];      
			$j=$i;
			$rgb = $tok[$i];
			while ($j<$cnt and $rgb == $tok[$j])
				$j++;
			$startCentisecond=intval((($i-4)*$frame_delay)/10);
			$endCentisecond  =intval((($j-4)*$frame_delay)/10);
			if($j==$cnt) $endCentisecond  =intval((($j-5)*$frame_delay)/10);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;
			$r1 = 255 <<16;
			$g1 = 255<<8;
			$b1 =255;
			if($rgbLoop==1)
				$val=$r;
			if($rgbLoop==2)
				$val=$g;
			if($rgbLoop==3)
				$val=$b;
			$Intensity = intval((100*$val)/255);
			if($Intensity>=0)
				if($Intensity>0)
					fwrite($fh_lor,sprintf("<effect type=\"intensity\" startCentisecond=\"%d\" endCentisecond=\"%d\" intensity=\"%d\" />\n",$startCentisecond,$endCentisecond,$Intensity));
			$i=$j;
		}
		$loop++;  // counter for how many channels written
		fwrite($fh_lor,sprintf("</channel>\n"));
	}
	$array_write_buffer[0]=$savedIndex;
	$array_write_buffer[1]=$channel_savedIndex;
	$array_write_buffer[2]=$loop;
	ob_flush();
	flush();
	return $array_write_buffer;
}
