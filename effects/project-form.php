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
//print_r($_POST);
//print_r($nc_array);
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];
$music_object=$_POST['music_object'];
echo "</pre>\n";
copy_songs($song_array,$username); // if any have been self submitted from this form
?>
<div align=left>
<b>What is a Nutcracker Project?</b>
<br>A project are all of the animation effects you attach to a single song. A Nutcracker project will allow your effects to by sybced to music. One Project = 1 mp3
<br/><b>How can the Nutcracker know about the music timings?</b>
<br/>Using Audacity I have marked the music phrases found in popular mp3 files. These files I call music object files.  After creating a music object file, they need to be loaded into the Nutcracker database so they are available for people to use. 
<br/><b>What are the steps for creating a project?</b>
<ol>
<li>Create all the effects you will be using against the Target model. </li>
<li>Copy a song from the available mp3's shown to copy it into your Project library</li>
<li>Select A song, A target model and the frame timing. These are done on this form</li>
<li>Attach effects to each music phrase. These will done on the next form.</li>
<li>Regenerate the effects, if you have changed the sequence duration</li>
<li>Export each effect for particular sequencer you are using</li>
<li>Enjoy!</li>
</ol>
<br/>
<h2><?php echo "$username"; ?>, Here is a table of your current Nutcracker projects</h2>
</div>
<form action="<?php echo "project-exec.php"; ?>" method="post">
<input type="hidden" name="username" value="<?php echo "$username"; ?>"/>
<input type="hidden" name="seq_duration" value="<?php echo "$seq_duration"; ?>"/>
<input type="hidden" name="frame_delay" value="<?php echo "$frame_delay"; ?>"/>
<input type="hidden" name="target" value="<?php echo "$target"; ?>"/>
<?php
$i=0;
$music_object_hdr=get_music_object_hdr($username,"nonzero");
/*echo "<pre>music_object_hdr for $username";
print_r($music_object_hdr);
echo "</pre>";*/
$checked="";
/*echo "<div";*/
/*echo "<pre>";
print_r($array_of_nc);*/
echo "<table border=1>";
echo "<tr>";
echo "<th>Song Name</th>";
echo "<th>Phrases<br/>In File</th>";
echo "<th>Artist</th>";
echo "<th>Length<br/>Minutes</th>";
echo "<th>Target<br/>Model (Select target model to be used with this project)</th>";
echo "<th>Frame Timing (Select Frame Timing (ms)<br/> for this project. <br/>Suggested Range 25-100:</th>";
echo "<th>Purchase song from here</th>";
echo "</tr>\n";
$target_array=get_targets($username); // get current of targets that this user has
$cnt=count($music_object_hdr);
if($cnt<=0)
{
	echo "<tr><td colspan=7>You have no Projects yet</td></tr>\n";
}
else
{
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
		$max_end_secs=$arr2[7];
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
		$length = sprintf("%4.1f",$max_end_secs/60);
		echo "<td>$length</td>";
		pulldown_target($target_array,$music_object_id,$target);
		echo "<td><input type=\"text\" name=\"frame_delay_array[$music_object_id]\" value=\"$frame_delay\"   /> </td>";
		echo "<td><a href=$song_url>$song_url</a></td>";
		echo "</tr>";
	}
}
echo "</table>\n";
//
?>
<input type="submit" name="submit" value="Select a song and click here to go to the next screen, assign effects"  class="button" />
</form>
<form action="<?php echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="username" value="<?php echo "$username"; ?>"/>
<input type="hidden" name="seq_duration" value="<?php echo "$seq_duration"; ?>"/>
<input type="hidden" name="frame_delay" value="<?php echo "$frame_delay"; ?>"/>
<input type="hidden" name="target" value="<?php echo "$target"; ?>"/>
<?php
$sean_username="f";
$music_object_hdr=get_music_object_hdr($sean_username,"nonzero");
?>
<br/>
<br/>
<h2>Availabe songs that you can copy into your Project library</h2>
<?php
echo "<table border=1>";
echo "<tr>";
echo "<th>Copy?</th>";
echo "<th>Song Name</th>";
echo "<th>Phrases<br/>In File</th>";
echo "<th>Artist</th>";
echo "<th>Length<br/>Minutes</th>";
echo "<th>Purchase song from here</th>";
echo "</tr>\n";
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
	$max_end_secs=$arr2[7];
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
	echo "<td><input type=\"checkbox\" name=\"song_array[$music_object_id]\" value=\"$music_object_id\"  $checked /></td>";
	echo "<td>$bold_on $song_name $bold_off</td>\n";
	echo "<td>$music_object_dtl_rows</td>";
	echo "<td>$artist</td>";
	$length = sprintf("%4.1f",$max_end_secs/60);
	echo "<td>$length</td>";
	echo "<td><a href=$song_url>$song_url</a></td>";
	echo "</tr>";
}
echo "</table>\n";
//
?>
<input type="submit" name="submit" value="Submit Form to copy these songs into your project library"  class="button" />
</form>
<?php
//
$music_object_hdr=get_music_object_hdr($sean_username,"zero");
/*echo "<pre>";
print_r($music_object_hdr);*/
?>
<br/>
<br/>
<h2>Songs that that are in the queue waiting for Sean to mark the music phrases</h2>
<?php
echo "<table border=1>";
echo "<tr>";
echo "<th>Song Name</th>";
echo "<th>Phrases<br/>In File</th>";
echo "<th>Artist</th>";
echo "<th>Purchase song from here</th>";
echo "</tr>\n";
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
	echo "<td>$bold_on $song_name $bold_off</td>\n";
	echo "<td>$music_object_dtl_rows</td>";
	echo "<td>$artist</td>";
	echo "<td><a href=$song_url>$song_url</a></td>";
	echo "</tr>";
}
echo "</table>\n";
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

function get_music_object_hdr($username,$sort)
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
		$arr=count_music_object_dtl($music_object_id);
		$music_object_dtl_rows=$arr[0];
		$max_end_secs=$arr[1];
		//echo "<pre>sort=$sort, music_object_dtl_rows=$music_object_dtl_rows,max_end_secs=$max_end_secs</pre>\n";
		if(
		($music_object_dtl_rows>0 and $sort=="nonzero") or
		($music_object_dtl_rows==0 and $sort=="zero"))
			$music_object_hdr[]=array($song_name,$song_url,$music_object_id,$artist,$music_object_dtl_rows,$frame_delay,$target,$max_end_secs);
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
	$query ="SELECT count(*) cnt,max(end_secs) max_end_secs
	FROM `music_object_dtl` WHERE music_object_id = '$music_object_id'
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
	return array($cnt,$max_end_secs);
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

function copy_songs($song_array,$username)
{
	$cnt=count($song_array);
	if($cnt==0) return;
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
	foreach ($song_array as $i=>$music_object_id)
	{
		$query ="INSERT into music_object_hdr (username,song_name,
		artist,frame_delay,target,song_url,audacity_aup,music_mo_file) 
		(select '$username',song_name,artist,frame_delay,target,
		song_url,audacity_aup,music_mo_file
		from music_object_hdr
		WHERE username = 'f' and music_object_id=$music_object_id)";
		echo "<pre>get_music_object_hdr: query=$query</pre>\n";
		$result=mysql_query($query) or die ("Error on $query");
		//
		//
		$query = "select song_name,music_object_id  music_object_id_new
		from music_object_hdr 
		WHERE username = '$username' and song_name in
		(select song_name from music_object_hdr 
		WHERE username = 'f'
		and music_object_id = $music_object_id)";
		echo "<pre>get_music_object_hdr: query=$query</pre>\n";
		$result=mysql_query($query) or die ("Error on $query");
		while ($row = mysql_fetch_assoc($result))
		{
			extract($row);
		}
		//
		//
		$query ="INSERT into music_object_dtl (	music_object_id,phrase_name,
		start_secs,end_secs,date_created)
			(select '$music_object_id_new',phrase_name,start_secs,end_secs,now()
		from music_object_dtl
		WHERE music_object_id=$music_object_id)";
		echo "<pre>get_music_object_hdr: query=$query</pre>\n";
		$result=mysql_query($query) or die ("Error on $query");
	}
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
