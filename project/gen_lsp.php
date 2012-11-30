<?php

function make_xml($filehandles,$NCFile,$type,$songdetails, $workArray, $filenames)
{
	$frame_delay=$songdetails[0];
	$numFrames=$songdetails[1];
	$numFramesPerMin=$songdetails[2];
	$totframes=$numFrames+4;
	$filecnt=0;
	$fh_buff=fopen($NCFile,"r") or die("Unable to open $NCFile");
	$floorFrames=array();
	foreach($filehandles as $fh_xml) {
		$tok=explode("/",$filenames[$filecnt]);
		$tok2=explode(".xml",$tok[1]);
		$currfile=$tok2[0];
		$tok3=explode("~",$tok2[0]);
		$base=$tok3[2];
		make_UserPattern_header($fh_xml,$base,$NCFile,$type);
		$floorFrames[] = 4+(($numFramesPerMin)*$filecnt);
		$filecnt++;
	}
	$firstPrint=true;
	while (!feof($fh_buff))
	{
		$currfilenum=-1;
		$line = fgets($fh_buff);
		$tok=preg_split("/ +/", $line);
		$totframes= count($tok);
		if($totframes>4)
		{
			if ($firstPrint) {
				echo "Number of frames = ".($totframes-4)."\n";	
				$firstPrint=false;
			}
			$last_rgb=-20;
			for($f=4;$f<$totframes;$f++)
			{
				if (in_array($f,$floorFrames)) {
					$currfilenum++;
					$fh_xml=$filehandles[$currfilenum];
					track_header($fh_xml,$type);
				}
				$time=(($f-3)*$frame_delay)/1000;
				$time = $time * 88200;	// just imperical measurement that one second timing = 88200
				$maxTime=$time+100000;
				$rgb=$tok[$f];
				$gui="{DA98BD5D-9C00-40fe-A11C-AD3242573443}";
				if($type==2)
				{
					$gui="{1B0F1B59-7161-4782-B068-98E021A6E048}";
				}
				else if($type==3)
				{
					$gui="{09A9DFBE-9833-413c-95FA-4FFDFEBF896F}";
				}
				else if($type==4)
				{
					$gui="{09A9DFBE-9833-413c-95FA-4FFDFEBF896F}";
				}
				if($last_rgb==$rgb)
				{
					$eff=7;
					$dat="";
					$gui="";
					fwrite($fh_xml,sprintf("            <TimeInterval eff=\"%d\" dat=\"%s\" gui=\"%s\" in=\"100\" out=\"100\" pos=\"%d\" sin=\"-1\" att=\"0\" />\n",$eff,$dat,$gui,$time));
				}
				else 
				{
					$eff=3;
					$dat="&lt;?xml version=&quot;1.0&quot; encoding=&quot;utf-16&quot;?&gt;&#xD;&#xA;&lt;ec&gt;&#xD;&#xA;  &lt;in&gt;100&lt;/in&gt;&#xD;&#xA;  &lt;out&gt;100&lt;/out&gt;&#xD;&#xA;&lt;/ec&gt;";
					fwrite($fh_xml,sprintf("            <TimeInterval eff=\"%d\" dat=\"%s\" gui=\"%s\" in=\"100\" out=\"100\" pos=\"%d\" sin=\"-1\" att=\"0\" bst=\"%d\" ben=\"%d\" />\n",$eff,$dat,$gui,$time,$rgb,$rgb));
				}
				//
				$last_rgb=$rgb;
			}
			foreach($filehandles as $fh_xml) {
				fwrite($fh_xml,sprintf("        </Intervals>\n"));
				fwrite($fh_xml,sprintf("  </Track>\n"));
			}
		}
	}
	foreach($filehandles as $fh_xml) {	
		fwrite($fh_xml,sprintf("     </Tracks>\n"));
		fwrite($fh_xml,sprintf("   </Pattern>\n"));
		fwrite($fh_xml,sprintf("</ArrayOfPattern>\n"));
	}
}

function make_HdrPattern_header($filehandles) {
	foreach($filehandles as $fh_xml) {
		fwrite($fh_xml,sprintf("<?xml version=\"1.0\"?>\n"));
		fwrite($fh_xml,sprintf("<ArrayOfPattern xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">\n"));
	}
}

function make_UserPattern_header($fh_xml,$base,$NCFile,$type)
{
	fwrite($fh_xml,sprintf("  <Pattern>\n"));
	fwrite($fh_xml,sprintf(" <GroupName>Nutcracker-%d</GroupName>\n",$type));
	fwrite($fh_xml,sprintf("    <Name>%s</Name>\n",$base));
	fwrite($fh_xml,sprintf("    <Image>\n"));
	fwrite($fh_xml,sprintf("      <Width>999</Width>\n"));
	fwrite($fh_xml,sprintf("      <Height>200</Height>\n"));
	$base64=create_bmp($NCFile);
	if(strlen($base64)>0)
	{
		fwrite($fh_xml,sprintf("    <BMPBytes>"));
		fwrite($fh_xml,$base64);
		fwrite($fh_xml,sprintf("</BMPBytes>\n"));
	}
	fwrite($fh_xml,sprintf("    </Image>\n"));
	fwrite($fh_xml,sprintf("    <Tracks>\n"));
}

function track_header($fh_xml,$type)
{
	fwrite($fh_xml,sprintf("      <Track>\n"));
	$TrackGuid="60cc0c76-f458-4e67-abb4-5d56a9c1d97c";
	if($type==2)
	{
		$TrackGuid="4e2556ac-d294-490c-8b40-a40dc6504946";
	}
	else if($type==3)
	{
		$TrackGuid="ba459d0f-ce08-42d1-b660-5162ce521997";
	}
	else if($type==4)
	{
		$TrackGuid="a69f7e39-e70d-4f70-8173-b3b2dbeea350";
	}
	fwrite($fh_xml,sprintf("        <TrackGuid>%s</TrackGuid>\n",$TrackGuid));
	fwrite($fh_xml,sprintf("        <IsHidden>false</IsHidden>\n"));
	fwrite($fh_xml,sprintf("        <IsPrimaryTrack>false</IsPrimaryTrack>\n"));
	fwrite($fh_xml,sprintf("        <TrackColorName>Gainsboro</TrackColorName>\n"));
	fwrite($fh_xml,sprintf("        <TrackColorARGB>-2302756</TrackColorARGB>\n"));
	fwrite($fh_xml,sprintf("        <TrackID>0</TrackID>\n"));
	fwrite($fh_xml,sprintf("        <TrackType>0</TrackType>\n"));
	fwrite($fh_xml,sprintf("        <WiiMapping inv=\"0\" ibn=\"\" inbn=\"\" ani=\"0\" ain=\"\" hty=\"-1\" fed=\"0\" wind=\"-1\" wibt=\"0\" cint=\"False\" ceff=\"False\" hefsd=\"True\" lef=\"3\" lefl=\"1\" intb=\"0\" efd=\"0\" />\n"));
	fwrite($fh_xml,sprintf("        <Name />\n"));
	fwrite($fh_xml,sprintf("        <Intervals>\n"));
}

function create_bmp($NCFile)
{
	require_once ("../effects/BMP.php");
	require_once("../effects/GIFDecoder.class.php");
	$tok=explode(".",$NCFile);
	$file = $tok[0] . "_th.gif";
	$fname = $tok[0] . "_tmp.gif";
	$base64="";
	if(file_exists($file))
	{
		$GIF_frame = fread (fopen ($file,'rb'), filesize($file));
		echo "<br/><img src=\"" . $file . "\"/><br/>\n";
		$decoder = new GIFDecoder ($GIF_frame);
		$frames = $decoder->GIFGetFrames();
		$hfic=fopen ( $fname, "wb" );
		fwrite ($hfic , $frames [ 10 ] );
		fclose($hfic);
		$im = imagecreatefromgif($fname);
		//Convert to 24bit
		$w = imagesx($im);
		$h = imagesy($im);
		$im2 = imagecreatetruecolor($w, $h);
		imagecopy($im2, $im, 0, 0, 0, 0, 100, 200);
		//Save as BMP
		$bmp=$tok[0] . ".bmp";
		imagebmp($im2, $bmp);
		$string = file_get_contents($bmp);
		$base64=base64_encode($string);
		unlink($bmp);
	}
	return $base64;
}

?>