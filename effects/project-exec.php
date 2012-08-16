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
set_time_limit(60*60);
extract($_POST);
echo "<pre>";
//print_r($_POST);
//print_r($nc_array);
//
//	If called from project-form:
/*Array
(
[username] => f
[seq_duration] => 
[frame_delay] => 
[target] => 
[target_array] => Array
(
[1] => --
[2] => AA
[3] => --
[4] => --
[5] => --
)
	[frame_delay_array] => Array
(
[1] => 
[2] => 66
[3] => 
[4] => 
[5] => 
)
	[music_object] => 2
[submit] => Submit Form to assign effects to your project
)
	target=AA, frame_delay=66
//	** If called from self **
//  *************************
Array
(
[username] => f
[music_object] => 2
[frame_delay] => 66
[target] => AA
[start_seconds] => Array
(
[1] => 0.000000
[2] => 5.767168
[3] => 10.791595
[4] => 15.990784
[5] => 19.660801
[6] => 29.753344
[7] => 42.467327
[8] => 43.603287
[9] => 47.448063
[10] => 48.540333
[11] => 52.122967
[12] => 58.108585
[13] => 74.317825
[14] => 81.439400
[15] => 84.787666
[16] => 98.847343
[17] => 103.038551
[18] => 113.127617
[19] => 119.989113
[20] => 126.804169
[21] => 140.643265
[22] => 150.987762
[23] => 161.309021
[24] => 169.877182
[25] => 178.480179
[26] => 185.388123
[27] => 192.284439
[28] => 217.292343
[29] => 224.455688
[30] => 231.665482
[31] => 238.805618
[32] => 245.701950
[33] => 253.097504
[34] => 260.179596
[35] => 267.551941
[36] => 277.350739
)
	[nc_array] => Array
(
[phrase1] => AA+BARBERPOLE_180.nc
[phrase2] => --
[phrase3] => --
[phrase4] => --
[phrase5] => --
[phrase6] => --
[phrase7] => --
[phrase8] => --
[phrase9] => --
[phrase10] => --
[phrase11] => --
[phrase12] => --
[phrase13] => --
[phrase14] => --
[phrase15] => --
[phrase16] => --
[phrase17] => --
[phrase18] => --
[phrase19] => --
[phrase20] => --
[phrase21] => --
[phrase22] => --
[phrase23] => --
[phrase24] => --
[phrase25] => --
[phrase26] => --
[phrase27] => --
[phrase28] => --
[phrase29] => --
[phrase30] => --
[phrase31] => --
[phrase32] => --
[phrase33] => --
[phrase34] => --
[phrase35] => --
[phrase36] => --
)
	[submit] => Submit Form to assign effects to your prject
)
	target=AA, frame_delay=66*/
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];
$music_object=$_POST['music_object'];
$cnt=count($target_array);
if($cnt>0)
{
	foreach($target_array as $music_object_id=>$target)
	{
		$frame_delay=$frame_delay_array[$music_object_id];
		update_music_object_hdr($music_object_id,$target,$frame_delay);
	}
	$target=$target_array[$music_object_id];
}
$cnt=count($start_seconds);
if($cnt>0)
{
	update_music_object_dtl($start_seconds,$end_seconds,$nc_array,$frame_delay,$music_object,$username);
}
//echo "target=$target, frame_delay=$frame_delay\n";
//
if($frame_delay<=0) $frame_delay=100;
$member_id=get_member_id($username);
$dir = getcwd() . "/workspaces/" . $member_id;
$dir = "workspaces/" . $member_id;
//echo "<pre>dir=$dir.  getFilesFromDir($dir,$target);</pre>\n";
$array_of_nc=array();
$array_of_nc= getFilesFromDir($dir,$target); 
sort($array_of_nc);
//print_r($_SERVER);

echo "</pre>\n";
?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="username" value="<?php echo "$username"; ?>"/>
<input type="hidden" name="music_object" value="<?php echo "$music_object"; ?>"/>
<input type="hidden" name="frame_delay" value="<?php echo "$frame_delay"; ?>"/>
<input type="hidden" name="target" value="<?php echo "$target"; ?>"/>
<?php
$i=0;
$music_object_hdr=get_music_object_hdr($username);
$checked="";
/*echo "<div";*/
/*echo "<pre>";
print_r($array_of_nc);*/
$max_seconds=get_max_seconds($music_object);
echo "max_seconds=$max_seconds, frame_delay=$frame_delay\n";
if($frame_delay>0)
	$total_frames = intval(($max_seconds*1000)/$frame_delay);
else
$total_frames=0;
echo "<table border=1>";
echo "<tr><td>Total Frames that will be used for this song</td<td> $total_frames </td>";
echo "<td>music_object=$music_object</td>";
echo "</tr></table>";
echo "<table border=1>";
$music_object_id=$music_object;
$phrase_array=get_phrases($music_object);
echo "<table border=1>";
echo "<tr>";
echo "<th>Phrase</th>";
echo "<th>Start<br/>Seconds</th>";
echo "<th>End<br/>Seconds</th>";
echo "<th>Duration</th>";
echo "<th>Frames</th>";
echo "<th>Effect<br/>Name</th>";
echo "</tr>\n";
$frames=0;
$cnt=count($phrase_array);
if($cnt>0)
{
	/*foreach($phrase_array  as $j=> $arr2)
	{
		$i++;
		$phrase_name=$arr2[0];
		$start_secs=$arr2[1];
		$end_secs=$arr2[2];
		if($j>1)
		{
			$phrase_array[$j][2]= $phrase_array[$j-1][2];
		}
		echo "<tr><td>$j</td>$phrase_name</td><td>$start_secs</td><td>$end_secs</td></tr>\n";
	}
	*/
	foreach($phrase_array  as $j=> $arr2)
	{
		$i++;
		$phrase_name=$arr2[0];
		$start_secs=$arr2[1];
		$end_secs=$arr2[2];
		$effect_name=$arr2[3];
		echo "<tr>";
		echo "<td><input type=\"radio\" name=\"phrase\" value=\"$phrase_name\"   /> $phrase_name </td>";
		echo "<td><input type=\"text\" name=\"start_seconds[$phrase_name]\" value=\"$start_secs\"   />  </td>";
		echo "<td><input type=\"text\" name=\"end_seconds[$phrase_name]\" value=\"$end_secs\"   />  </td>";
		$delta=$end_secs-$start_secs;
		echo "<td>$delta</td>";
		$frames=intval(($delta*1000)/$frame_delay);
		echo "<td>$frames</td>";
		pulldown($array_of_nc,$phrase_name,$nc_array,$effect_name);
		echo "</tr>";
	}
	$nc_array[]=$music_object;
}
echo "</table>\n";
/*echo "</div>";*/
?>
<input type="submit" name="submit" value="Submit Form to assign effects to your prject"  class="button" />
</form>
<?php
echo "<table border=1>";
echo "<tr>";
foreach($phrase_array as $arr2)
{
	$i++;
	$phrase_name=$arr2[0];
	$start_secs=$arr2[1];
	$end_secs=$arr2[2];
	$effect_name=get_effect_name($username,$phrase_name,$music_object_id);
	$len=strlen($nc_array[$phrase_name]);
	$len2=strlen($effect_name);
	$member_id = get_member_id($username);
	if($len>2)
	{
		$tok=explode(".nc",$nc_array[$phrase_name]);
		//	echo "<td>tok[0]=$tok[0]</td><td>tok[1]=$tok[1]</td>";
		$file = "workspaces/$member_id/" . $tok[0] . "_th.gif";
	}
	else if($len2>1)
	{
		$file = "workspaces/$member_id/" .$target . "+" . $effect_name . "_th.gif";
	}
	else
	{
		$file="../images/blank.gif";
	}
	echo "<td><img src=$file><br/>$phrase_name</td>";
	//echo "<td>len=$len|$file</td>";
}
echo "</tr></table>";

function pulldown($array_of_nc,$phrase_name,$nc_array,$effect_name)
{
	echo "<td>";
	echo "<div align=\"center\">";
	echo "<select name=\"nc_array[$phrase_name]\">";
	echo "<option value=\"--\">No Selection</option>";
	foreach($array_of_nc as $file)
	{
		if($nc_array[$phrase_name] == $file)
		{
			$selected="SELECTED";
		}
		else
		{
			$tok1=explode("+",$file);
			$tok2=explode(".nc",$tok1[1]);
			$effect_name2 = $tok2[0];
			if($effect_name == $effect_name2)
			{
				$selected="SELECTED";
				$gif_array[$phrase_name]=$file;
			}
			else
			$selected="";
		}
		echo "<option value=\"$file\" $selected>$file</option>";
	}
	echo "</select>";
	echo "</div>\n";
	echo "</td>\n";
}

function get_phrases($music_object_id)
{
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	$query ="SELECT * FROM `music_object_dtl` WHERE  music_object_id = '$music_object_id'
	order by start_secs";
	//echo "<pre>get_music_object_hdr: query=$query</pre>\n";
	$result=mysql_query($query) or die ("Error on $query");
	$music_object_id=0;
	$NO_DATA_FOUND=0;
	//echo "rows=" . mysql_num_rows($result) . "\n";
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$phrase_array[]=array($phrase_name,$start_secs,$end_secs,$effect_name);
	}
	return $phrase_array;
}

function get_effect_name($username,$phrase_name,$music_object_id)
{
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	$query ="SELECT * FROM `effects_user_hdr` WHERE  music_object_id = '$music_object_id'
	and username='$username'
	and phrase_name='$phrase_name'";
	//echo "<pre>get_effect_name: query=$query</pre>\n";
	$result=mysql_query($query) or die ("Error on $query");
	$music_object_id=0;
	$NO_DATA_FOUND=0;
	//echo "rows=" . mysql_num_rows($result) . "\n";
	$effect_name="";
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
	}
	return $effect_name;
}

function get_max_seconds($music_object_id)
{
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	$query ="SELECT max(end_secs) max_seconds FROM `music_object_dtl` WHERE  music_object_id = '$music_object_id'";
	//echo "<pre>get_music_object_hdr: query=$query</pre>\n";
	$result=mysql_query($query) or die ("Error on $query");
	$music_object_id=0;
	$NO_DATA_FOUND=0;
	//echo "rows=" . mysql_num_rows($result) . "\n";
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
		$max_seconds=0;
	}
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
	}
	return $max_seconds;
}

function update_music_object_hdr($music_object,$target,$frame_delay)
{
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	$query ="update `music_object_hdr` set frame_delay=$frame_delay, target='$target'
	where  music_object_id = '$music_object'";
	//echo "<pre>update_music_object_hdr: query=$query</pre>\n";
	$result=mysql_query($query) or die ("Error on $query");
}

function get_music_object_hdr($username)
{
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	$query ="SELECT * FROM `music_object_hdr` WHERE  username = '$username'
	order by music_object_id";
	//echo "<pre>get_music_object_hdr: query=$query</pre>\n";
	$result=mysql_query($query) or die ("Error on $query");
	$music_object_id=0;
	$NO_DATA_FOUND=0;
	//echo "rows=" . mysql_num_rows($result) . "\n";
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$music_object_hdr[]=array($song_name,$song_url,$music_object_id,$artist);
	}
	return $music_object_hdr;
}

function getFilesFromDir($dir,$target)
{
	$files = array(); 
	$n=0;
	//echo "<pre>";
	//echo "function getFilesFromDir($dir,$target)\n";
	if ($handle = opendir($dir))
	{
		while (false !== ($file = readdir($handle)))
		{
		//	echo "file=$file\n";
			if ($file != "." && $file != ".." )
			{
				if(is_dir($dir.'/'.$file))
				{
					$dir2 = $dir.'/'.$file; 
					$files[] = getFilesFromDir($dir2,$target);
				//	echo "dir2=$dir2\n";
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
					//echo "inner file=$file. target_search=$target_search\n";
					if($extension=="nc")
					{
						//$files[] = $dir.'/'.$file;
						$target_search=$target ."+";
						if(!strncmp($file, $target_search, strlen($target_search)))
						{
							$files[] = $file; 
							$n++;
						}
						//echo "<pre>$cnt $n $file</pre>\n";
					}
					} 
				} 
			} 
		closedir($handle);
		echo "</pre>\n";
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

function 	update_music_object_dtl($start_seconds,$end_seconds,
$nc_array,$frame_delay,$music_object,$username)
{
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	foreach ($nc_array as $phrase_name => $file)
	{
		$start_secs=$start_seconds[$phrase_name];
		$end_secs=$end_seconds[$phrase_name];
		$delta = $end_secs-$start_secs;
		$seq_duration = sprintf("%7.3f",$delta);
		$tok1=explode("+",$file);
		$tok2=explode(".nc",$tok1[1]);
		$effect_name = $tok2[0];
		// music_object_id	phrase_name	start_secs	end_secs	effect_name	sequence	
		if(strlen($effect_name)>0)
		{
			$query ="UPDATE music_object_dtl set effect_name='$effect_name' 
			WHERE  music_object_id=$music_object and
			phrase_name = '$phrase_name'";
			//	echo "<pre>$query</pre>\n";
			$result=mysql_query($query) or die ("Error on $query");
			// 	effect_class	username	effect_name	effect_desc	music_object_id	start_secs	end_secs	phrase_name	created	last_upd
			$query ="UPDATE  effects_user_hdr 
			set music_object_id=$music_object,
			start_secs=$start_secs,
			end_secs=$end_secs,
			phrase_name='$phrase_name'
			WHERE  username='$username' and
			effect_name = '$effect_name'";
			//	echo "<pre>$query</pre>\n";
			$result=mysql_query($query) or die ("Error on $query");
			//
			$query ="UPDATE  effects_user_dtl 
			set param_value='$frame_delay'
			WHERE  username='$username' and
			effect_name = '$effect_name'
			and param_name='frame_delay'";
			//	echo "<pre>$query</pre>\n";
			$result=mysql_query($query) or die ("Error on $query");
			//
			$query ="UPDATE  effects_user_dtl 
			set param_value='$seq_duration'
			WHERE  username='$username' and
			effect_name = '$effect_name'
			and param_name='seq_duration'";
			//	echo "<pre>$query</pre>\n";
			$result=mysql_query($query) or die ("Error on $query");
		}
	}
	$query ="SELECT * FROM `music_object_hdr` WHERE  music_object_id = '$music_object_id'
	order by music_object_id";
	//echo "<pre>get_music_object_hdr: query=$query</pre>\n";
	/*$result=mysql_query($query) or die ("Error on $query");
	$music_object_id=0;
	$NO_DATA_FOUND=0;
	//echo "rows=" . mysql_num_rows($result) . "\n";
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$music_object_hdr[]=array($song_name,$song_url,$music_object_id,$artist);
	}
	return $music_object_hdr;*/
}
