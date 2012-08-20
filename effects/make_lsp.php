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
// [QUERY_STRING] => make_lsp.php?base=AA+BARBERPOLE_180?full_path=workspaces/2/AA+BARBERPOLE_180_d_1.dat?frame_delay=100?member_id=2?seq_duration=8?sequencer=lsp?pixel_count=100?type=1
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
$tokens2=explode("type=",$tokens[7]);
$type=$tokens2[1];
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
$supported = array('vixen','hls','lors2','lors3','lsp');
if(!in_array($sequencer,$supported))
{
	echo "<pre>";
	echo "Your sequencer is not yet supported.\n";
	echo "-----------------------------------\n";
	echo "Currently spported sequencers:\n";
	echo "vixen .... Vixen 2.1 vir file\n";
	echo "hls ...... Joe Hinkle\'s new sequencer, HLS\n";
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

/*$create_srt_file_array=create_srt_file($full_path,$base,$username,$frame_delay,$TotalFrames);
$maxFrame=$create_srt_file_array[0];
$seq_srt=$create_srt_file_array[1];
$fh = fopen($seq_srt, 'r') or die("can't open file $seq_srt");*/
$loop=$xml_loop=0;
$outBuffer=array();
$old_string=-1;
$full_path= "workspaces/$member_id/$base";
$path_parts = pathinfo($full_path);
$dirname   = $path_parts['dirname'];
$basename  = $path_parts['basename'];
$xml= $dirname . "/" . $base . ".xm";
$files_array=getFilesFromDir($dirname,$base);
sort($files_array);
/*echo "<pre>";
print_r($files_array);
echo "</pre>\n";*/
$username="f";
$user_targets="AA";
$effect_class="FLY";
$checked="";
?>
<form action="<?php echo "make_lsp-exec.php"; ?>" method="post">
<input type="hidden" name="username" value="<?php echo "$username"; ?>"/>
<input type="hidden" name="user_target" value="<?php echo "$user_targets"; ?>"/>
<input type="hidden" name="effect_class" value="<?php echo "$effect_class"; ?>"/>
<input type="hidden" name="type" value="<?php echo "$type"; ?>"/>
<input type="hidden" name="seq_duration" value="<?php echo "$seq_duration"; ?>"/>
<input type="hidden" name="frame_delay" value="<?php echo "$frame_delay"; ?>"/>
<?php
$i=0;
echo "<ol>";
foreach($files_array as $file)
{
	$i++;
	echo "<li><input type=\"checkbox\" name=\"fullpath_array[$i]\" value=\"$file\"  $checked /> $file ";
}
echo "</ol>";
?>

	<input type="submit" name="submit" value="Submit Form to create a UserPatterns.xml from checked effects"  class="button" />
	</form>
	<?php

function getFilesFromDir($dir,$base)
{
	$files = array(); 
	$n=0;
	$tok=explode("+",$base);
	$target=$tok[0];
	$len=strlen($target);
	if ($handle = opendir($dir))
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file != "." && $file != ".." )
			{
				if(is_dir($dir.'/'.$file))
				{
					$dir2 = $dir.'/'.$file; 
					$files[] = getFilesFromDir($dir2);
				}
				else 
				{ 
					$path_parts = pathinfo($file);  // workspaces/nuelemma/MEGA_001+SEAN_d_22.dat
					$dirname   = $path_parts['dirname']; // workspaces/nuelemma
					$basename  = $path_parts['basename']; // MEGA_001+SEAN_d_22.dat
					$extension =$path_parts['extension']; // .dat
					$filename  = $path_parts['filename']; // MEGA_001+SEAN_d_22
					$cnt=count($files);
					$tokens=explode("/",$dirname);
					//	0 = workspaces
					//	1 = nuelemma or id
					//
					if($extension=="nc" and substr($file,0,$len) == $target)
					{
						$files[] = $dir.'/'.$file; 
						$n++;
						//echo "<pre>$cnt $n $file</pre>\n";
					}
					} 
				} 
			} 
		closedir($handle);
	}
	return array_flat($files);
}

function array_flat($array)
{
	$tmp=array();
	foreach($array as $a)
	{
		if(is_array($a))
		{
			$tmp = array_merge($tmp, array_flat($a));
		}
		else 
		{ 
			$tmp[] = $a;
		}
		} 
	return $tmp;
}
