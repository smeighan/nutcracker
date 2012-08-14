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
<?php $menu="project-form"; require "../conf/menu.php"; ?>
<?php
//
require("../effects/read_file.php");
set_time_limit(60*60);
extract($_POST);
echo "<pre>";
/*print_r($_POST);
print_r($nc_array);*/
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];
$music_object=$_POST['music_object'];
echo "</pre>\n";
?>
<div align=left>
<b>What is a Nutcracker Project?</b>
<br>A project is all of the animation effects you attach to a single song.
<br/><b>What are the steps for creating a project?</b>
<ol>
<li>Create all the effects you will be using against the Target model. </li>
<li>Select A song, A target model and the frame timing. These are done on this form</li>
<li>Attach effects to each music phrase. These will done on the next form.</li>
<li>Regenerate the effects, if you have changed the sequence duration</li>
<li>Export each effect for particular sequencer you are using</li>
<li>Enjoy!</li>
</ol>
</div>
<form action="<?php echo "project-exec.php"; ?>" method="post">
<input type="hidden" name="username" value="<?php echo "$username"; ?>"/>
<input type="hidden" name="seq_duration" value="<?php echo "$seq_duration"; ?>"/>
<input type="hidden" name="frame_delay" value="<?php echo "$frame_delay"; ?>"/>
<input type="hidden" name="target" value="<?php echo "$target"; ?>"/>
<?php
$i=0;
$music_object_hdr=get_music_object_hdr($username);
$checked="";
/*echo "<div";*/
/*echo "<pre>";
print_r($array_of_nc);*/
echo "<table border=1>";
echo "<tr>";
echo "<th>Song Name</th>";
echo "<th>Phrases<br/>In File</th>";
echo "<th>Artist</th>";
echo "<th>Target<br/>Model (Select target model to be used with this project)</th>";
echo "<th>Frame Timing (Select Frame Timing (ms)<br/> for this project. <br/>Suggested Range 25-100:</th>";

echo "<th>Purchase song from here</th>";
echo "</tr>\n";
$target_array=get_targets($username); // get current of targets that this user has
foreach($music_object_hdr as $arr2)
{
	$i++;
	$song_name=$arr2[0];
	$song_url=$arr2[1];
	$music_object_id=$arr2[2];
	$artist=$arr2[3];
	$music_object_dtl_rows=$arr2[4];
	$frame_delay=$arr2[5];
	$target=$arr2[6];
	if($music_object==$music_object_id)
		$checked="checked=\"checked\"";
	else
	{
		$checked="";
	}
	echo "<tr>";
	$bold_on=$bold_off="";
	if($music_object_dtl_rows>0)
	{
		$bold_on="<b>";
		$bold_off="</b>";
	}
	echo "<td><input type=\"radio\" name=\"music_object\" value=\"$music_object_id\"  $checked />$bold_on $song_name $bold_off</td>";
	echo "<td>$music_object_dtl_rows</td>";
	echo "<td>$artist</td>";
	pulldown_target($target_array,$music_object_id,$target);
	echo "<td><input type=\"text\" name=\"frame_delay_array[$music_object_id]\" value=\"$frame_delay\"   /> </td>";
	echo "<td><a href=$song_url>$song_url</a></td>";
	echo "</tr>";
}
echo "</table>\n";
?>
<input type="submit" name="submit" value="Submit Form to assign effects to your project"  class="button" />
</form>
<?php
/*[24] => Array
(
[username] => f
[object_name] => TIM
[object_desc] => anytning
[model_type] => MTREE
[string_type] => 
[pixel_count] => 123
[folds] => 3
[start_bottom] => y
[pixel_first] => 1
[pixel_last] => 123
[pixel_length] => 3.00
[pixel_spacing] => 
[unit_of_measure] => in
[total_strings] => 16
[total_pixels] => 
[direction] => 
[orientation] => 
[topography] => 
[h1] => 
[h2] => 
[d1] => 
[d2] => 
[d3] => 
[d4] => 
[date_created] => 
)
	*/

function pulldown_target($target_array,$music_object_id,$target)
{
	echo "<td>";
	echo "<div align=\"center\">";
	echo "<select name=\"target_array[$music_object_id]\">";
	echo "<option value=\"--\">No Selection</option>";
	foreach($target_array as $i=>$arr2)
	{
		$object_name=$arr2['object_name'];
		$object_desc=$arr2['object_desc'];
		$model_type=$arr2['model_type'];
		$total_strings=$arr2['total_strings'];
		$pixel_count=$arr2['pixel_count'];
		$buff=sprintf("%12s (%dx%d) %12s %s",$object_name,$total_strings,$pixel_count,$model_type,$object_desc);
		$selected="";
		if($object_name==$target) $selected="SELECTED";
		echo "<option value=\"$object_name\" $selected>$buff</option>";
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
	order by phrase_name";
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
		$phrase_array[]=array($phrase_name,$start_secs,$end_secs);
	}
	return $phrase_array;
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
		$music_object_dtl_rows=count_music_object_dtl($music_object_id);
		$music_object_hdr[]=array($song_name,$song_url,$music_object_id,$artist,$music_object_dtl_rows,$frame_delay,$target);
	}
	return $music_object_hdr;
}

function count_music_object_dtl($music_object_id)
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
	$query ="SELECT count(*) cnt FROM `music_object_dtl` WHERE music_object_id = '$music_object_id'
	group by music_object_id";
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
	}
	return $cnt;
}

function get_targets($username)
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
	$query ="SELECT * FROM `models` WHERE username = '$username'
	order by object_name";
	//echo "<pre>get_music_object_hdr: query=$query</pre>\n";
	$result=mysql_query($query) or die ("Error on $query");
	$music_object_id=0;
	$NO_DATA_FOUND=0;
	//echo "rows=" . mysql_num_rows($result) . "\n";
	$target_array=array();
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$target_array[]=$row;
	}
	return $target_array;
}

function getFilesFromDir($dir)
{
	$files = array(); 
	$n=0;
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
					if($extension=="nc")
					{
						//$files[] = $dir.'/'.$file; 
						$files[] = $file; 
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
