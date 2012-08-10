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
//echo "<pre>";
//echo "max_execution_time =" . ini_get('max_execution_time') . "\n"; 
set_time_limit(60*60);
//echo "max_execution_time =" . ini_get('max_execution_time') . "\n"; 
//echo "</pre>";
//show_array($_SERVER,"SERVER");
// [QUERY_STRING] => make_lor.php?base=ZZ_ZZ+USER2?full_path=workspaces/2/ZZ_ZZ+USER2_d_1.dat?frame_delay=200?member_id=2?seq_duration=2?sequencer=lors2
// base=ZZ_ZZ+USER2?full_path=workspaces/2/ZZ_ZZ+USER2_d_1.dat?frame_delay=200?member_id=2?seq_duration=2?sequencer=lors2
//
//echo "<pre>sequencer=$sequencer</pre>\n";
echo "<pre>";
print_r($_POST);
extract($_POST);
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
/*[fullpath_array] => Array
(
[1] => workspaces/2/AA+BARBERPOLE_180.nc
[9] => workspaces/2/AA+TEXT1.nc
)*/
/*[username] => f
[user_target] => AA
[effect_class] => FLY
[type] => 
[seq_duration] => 8
[frame_delay] => 100*/
$username=$_POST['username'];
$user_target=$_POST['user_target'];
$effect_class=$_POST['effect_class'];
$type=$_POST['type'];
$seq_duration=$_POST['seq_duration'];
$frame_delay=$_POST['frame_delay'];
$files_array=$_POST['fullpath_array'];
$cnt=count($files_array);
foreach($files_array as $i=>$file0)
{
	; //echo "<pre>$i $file0</pre>\n";
}
$tok=explode("/",$file0);
$dir = $tok[0] . "/" . $tok[1];
$tok2=explode(".nc",$tok[2]);
$base=$tok2[0];
$xml = $dir . "/UserPatterns.xml";
echo "<h3>Adding the following effects into your UserPattern Nutcracker Group</h3>\n";
$fh_xml=fopen($xml,"w") or die("Unable to open $xml");
make_HdrPattern_header($fh_xml,$base);
//
foreach($files_array as $i=>$filename_buff)
{
	make_xml($fh_xml,$filename_buff,$type,$frame_delay);
}
fwrite($fh_xml,sprintf("</ArrayOfPattern>\n"));
fclose($fh_xml);
/*fclose($fh_vixen_csv);
fclose($fh_xml);*/
/*$TotalFrames= ($seq_duration*1000)/$frame_delay;*/
$duration = $seq_duration*1000;
echo "<table border=1>";
printf ("<tr>\n");
echo "<td>Instructions</td></tr>";
printf ("<tr><td bgcolor=#98FF73><h2><a href=\"%s\">Right Click here for %s</a>.</h2></td>\n",$xml,$xml);
echo "<td>gui type=$type. Save this file into your LSP Sequencer directory on top of c:=>Programs(x86)>=GraphXPros=>LSP Sequencer>=UserPatterns.xml) </td></tr>\n";
echo "</table>";
$description ="Total Elapsed time for this effect:";
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
//if($description = 'Total Elapsed time for this effect:')
	printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);

function make_xml($fh_xml,$filename_buff,$type,$frame_delay)
{
	$tok=explode("/",$filename_buff);
	$dir = $tok[0] . "/" . $tok[1];
	$tok2=explode(".nc",$tok[2]);
	$base=$tok2[0];
	$fh_buff=fopen($filename_buff,"r") or die("Unable to open $filename_buff");
	/*$fh_vixen_csv=fopen($vixen_csv,"w") or die("Unable to open $vixen_csv");*/
	//	how many frames should we do?
	make_UserPattern_header($fh_xml,$base,$filename_buff,$type);
	//
	$maxTime=0;
	$channels=$lines=0;
	while (!feof($fh_buff))
	{
		$line = fgets($fh_buff);
		$tok=preg_split("/ +/", $line);
		$l=strlen($line);
		$cnt= count($tok);
		$MaxFrame=$cnt-4;
		//echo "cnt=$cnt MaxFrame=$MaxFrame, line=$line\n";
		if($cnt>4)
		{
			$string=$tok[1];
			$pixel=$tok[3];
			$lines++;
			track_header($fh_xml,$type);
			$last_rgb=0;
			for($f=1;$f<$MaxFrame;$f++)
			{
				//fwrite($fh_xml,sprintf("%d ",$tok[$f+3]));
				$time=$f*50000;
				$time=$f*$frame_delay*1000;
				$time = $time * .882;	// just imperical measurement that one second timing = 88200
				$maxTime=$time+100000;
				$rgb=$tok[$f+3];
				//	fwrite($fh_xml,sprintf("            <TimeInterval eff=\"3\" dat=\"&lt;?xml version=&quot;1.0&quot; encoding=&quot;utf-16&quot;?&gt;&#xD;&#xA;&lt;ec&gt;&#xD;&#xA;  &lt;in&gt;100&lt;/in&gt;&#xD;&#xA;  &lt;out&gt;100&lt;/out&gt;&#xD;&#xA;&lt;/ec&gt;\" gui=\"\" in=\"100\" out=\"100\" pos=\"%d\" sin=\"-1\" att=\"0\" bst=\"%d\" ben=\"%d\" />\n",$time,$rgb,$rgb));
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
			$channels++;
			//	fwrite($fh_xml,sprintf("\n"));
			fwrite($fh_xml,sprintf("        <TimeInterval eff=\"7\" dat=\"\" gui=\"\" in=\"1\" out=\"1\" pos=\"%d\" sin=\"-1\" att=\"0\" />\n",$maxTime));
			fwrite($fh_xml,sprintf("        </Intervals>\n"));
			fwrite($fh_xml,sprintf("  </Track>\n"));
		}
	}
	fwrite($fh_xml,sprintf("     </Tracks>\n"));
	fwrite($fh_xml,sprintf("   </Pattern>\n"));
	$channels*=3;
	//$cells = floatval($MaxFrame)*floatval($channels);
	printf ( "<pre> %s has %d channels </pre>\n",$filename_buff,$channels);
}

function make_HdrPattern_header($fh_xml,$base)
{
	fwrite($fh_xml,sprintf("<?xml version=\"1.0\"?>\n"));
	fwrite($fh_xml,sprintf("<ArrayOfPattern xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">\n"));
}

function make_UserPattern_header($fh_xml,$base,$filename_buff,$type)
{
	fwrite($fh_xml,sprintf("  <Pattern>\n"));
	fwrite($fh_xml,sprintf(" <GroupName>Nutcracker-%d</GroupName>\n",$type));
	fwrite($fh_xml,sprintf("    <Name>%s</Name>\n",$base));
	fwrite($fh_xml,sprintf("    <Image>\n"));
	fwrite($fh_xml,sprintf("      <Width>999</Width>\n"));
	fwrite($fh_xml,sprintf("      <Height>200</Height>\n"));
	$base64=create_bmp($filename_buff);
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

function create_bmp($filename_buff)
{
	require_once 'BMP.php';
	require_once("GIFDecoder.class.php");
	//$filename_buff = workspaces/2/AA+FLY_0_0.nc
	$tok=explode(".",$filename_buff);
	//$file="workspaces/2/AA+FLY_0_0_th.gif";
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
