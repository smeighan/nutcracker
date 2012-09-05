<?php
require_once('../conf/auth.php');
require_once('../conf/barmenu.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="../js/barmenu.js"></script>
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
<link rel="stylesheet" type="text/css" href="../css/barmenu.css">
<link href="../css/ncFormDefault.css" rel="stylesheet" type="text/css" />
</head>
<body>
	
<?php show_barmenu();
//
require("../effects/read_file.php");
set_time_limit(60*60);
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];
extract($_GET);
$msg_str="";
if (isset($type)) {
	switch ($type) {
		case 1:
			$msg_str=select_song($username);
			break;
		case 2:
			$msg_str="Editing Song";
			if (isset($id)) {
				$msg_str.="$id<br />";
			} else {
				$msg_str = "***Error occurred *** no song id selected<br />";
			}
			break;
		case 3:
			if (isset($id)) {
				$msg_str=remove_song($id,$username,$model_name);
			} else {
				$msg_str="***Error occurred *** no song id selected<br />";
			}
			break;
		default:
			$msg_str= "***Error occurred *** Invalid value for function call<br />";
	}
} else {
	extract($_POST);
	if (isset($NewProjectCancel)) {
		$msg_str="Song add was cancelled";
	} 
	if (isset($NewProjectSubmit)) {
		//echo "$song_id  , $username,   $frame_delay, $model_name <br />";
		$msg_str=add_song($song_id,$username, $frame_delay, $model_name);
	}
}
echo $msg_str;
?>
<h2>Current Nutcracker projects</h2>
<form action="<?php echo "project-exec.php"; ?>" method="post">
<input type="hidden" name="username"     value="<?php printf ("$username");    ?> "/>
<?php /*<input type="hidden" name="seq_duration" value="<?php printf( "$seq_duration");?> "/>
<input type="hidden" name="frame_delay"  value="<?php echo "$frame_delay"; ?> "/>
<input type="hidden" name="target"       value="<?php echo "$target";      ?> "/> */
?>
<table border=1>
<tr>
<th>Song Name</th>
<th>Artist</th>
<th>Purchase song from here</th>
<th>Model</th>
<th>Frame Timing (ms)</th>
<th>Commands</th>
</tr>
<?php
	$sql = "SELECT song.song_id as song_id, song_name, artist, song_url, frame_delay, model_name FROM project LEFT JOIN song ON project.song_id = song.song_id WHERE username='$username'";
	//echo "$sql <br />";
	require_once('../conf/config.php');
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	$result = mysql_query ($sql, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
	$cnt=0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$cnt +=1;
		$song_id = $row['song_id'];
		$artist = $row['artist'];
		$song_name = $row['song_name'];
		$song_url = $row['song_url'];
		$frame_delay = $row['frame_delay'];
		$model_name = $row['model_name'];
	?>
<tr>
	<td><a href="project.php?type=2&id=<?php echo $song_id?>"><?php echo $song_name?></a></td>
	<td><?php echo $artist?></td>
	<td><a href="<?php echo $song_url?>"><?php echo $song_url?></a></td>
	<td><?php echo $model_name?></td>
	<td><?php echo $frame_delay?></td>
	<td><a href="project.php?type=2&id=<?php echo $song_id?>"><img src="../images/edit.png">Edit</a>&nbsp;&nbsp;&nbsp;<a href="project.php?type=3&id=<?php echo $song_id?>&model_name=<?php echo $model_name?>"><img src="../images/delete.png">Remove</a></td>
</tr>
<?php		
	}
	if ($cnt == 0) {
		echo "<tr><td colspan=6>You do not have any current projects</td></tr>";
	}
?>
</table>
<p />
<a href="project.php?type=1">Add a song</a><br />
<?php
function remove_song($song_id, $username, $model_name) {
	$song_name=getSongName($song_id);
	$sql = "DELETE FROM project WHERE song_id=$song_id AND username='$username' AND model_name='$model_name'";
	// echo "$sql <br />";
	require_once('../conf/config.php');
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	$result = mysql_query ($sql, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
	return("Song '$song_name' removed");
}

function add_song($song_id, $username, $frame_delay, $model_name) {
	$song_name=getSongName($song_id);
	$sql2 = 'Select count(*) as songcnt from project WHERE song_id='.$song_id.' AND username="'.$username.'" AND model_name="'.$model_name.'"';
	$sql = "REPLACE INTO project (song_id, username,frame_delay, model_name) VALUES (".$song_id.",'".$username."',".$frame_delay.",\"".$model_name."\")";
	//echo "$sql <br />";
	//echo "$sql2 <br />";
	require_once('../conf/config.php');
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	$result2 = mysql_query ($sql2, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
	$row = mysql_fetch_array($result2, MYSQL_ASSOC);
	if ($row['songcnt'] > 0) {
	return("*** Add Canceled *** Song '$song_name' and Model '$model_name' already exists!");
	} else {
	$result = mysql_query ($sql, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
	return("Song '$song_name' and Target '$model_name' added");
	}
}

function getSongName($song_id) {
	$retVal = "Error occured";
	$sql = "SELECT song_name FROM song WHERE song_id='$song_id'";
	//echo "$sql <br />";
	require_once('../conf/config.php');
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	$result = mysql_query ($sql, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
	if ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$retVal=$row['song_name'];
	} else {
		$retVal="*** ERROR IN getSongName ***";
	}
	return($retVal);
}
function select_song($username) {
	$sql = "SELECT song_name, song.song_id, artist, song_url, min( start_secs )AS MinTime, max( end_secs )AS MaxTime\n"
     . "FROM song\n"
     . "LEFT JOIN song_dtl ON song.song_id = song_dtl.song_id\n"
     . "GROUP BY song_name, song.song_id\n";
    // . "HAVING song.song_id NOT IN (SELECT song_id from project where username='".$username."')";
	$sql2 = "SELECT object_name FROM models WHERE username='$username'";
	//echo "$sql <br />";
	//echo "$sql2 <br />";
	require_once('../conf/config.php');
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	$result = mysql_query ($sql, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
	?>
	<h2>Available Songs</h2>
	<table border="1" cellpadding="1" cellspacing="1">
	<?php
	$rowcnt = mysql_num_rows($result);
	if ($rowcnt == 0)
	{
		echo "<tr><th>No more songs available to add!</th></tr>";
	} else {
	?>
	<?php 
		$SongSel=parseSongs($result);
		echo $SongSel[1];?>
	</table>
	<p />
	<h2>Select a Song</h2>
	<form name="addsong" method="post" action="project.php">
	<input type="hidden" name="intype" value=2>
	<table width="375"	border="0" cellpadding="1" cellspacing="1">
	<tr>
		<td class="FormFieldName"><div align="right">Song</div></td>
		<td class="FormFieldName"><?php echo $SongSel[0];?></td>
	</tr>
	<?php 
	}	?>
    <tr>
      <td class="FormFieldName"><div align="right">Target</div></td>
      <td class="FormFieldName">
	  	 <?php	
	 	$result2 = mysql_query ($sql2, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
		 echo parseTargetSelect($result2); ?> 
	  </td>
    </tr>
    <tr>
      <td class="FormFieldName"><div align="right">Frame Rate (ms)</div></td>
      <td class="FormFieldName"><div align="left">
      <input name="frame_delay" type="text" id="frame_delay" value="50" size="11" maxlength="11" /></div></td>
    </tr>
    <tr>
      <td><div align="center">
        <input name="NewProjectCancel" type="submit" class="SubmitButton" id="NewProjectCancel" value="Cancel" />
      </div></td>
      <td><div align="center">
        <input name="NewProjectSubmit" type="submit" class="SubmitButton" id="NewProjectSubmit" value="Submit New Song" />
      </div></td>
    </tr>
	</table>
	</form>
<?php
}
function parseTargetSelect($result) {
	$retStr='<select name="model_name" class="FormSelect" id="model_name">';
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$model_name=$row['object_name'];
		$retStr.='<option value="'.$model_name.'">'.$model_name.'</option>';
	}
	$retStr.='</select>';
	return($retStr);
}
function parseSongs($result) {
	$retVal = array();
	$retStr1='<select name="song_id" class="FormSelect" id="song_id">';
	$retStr2='<tr><td>Song Name</td><td>Song url</td><td>Length of song (sec)</td><td>Length of song (min)</td></tr>';
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$song_name=$row['song_name'];
		$song_id=$row['song_id'];
		$song_url=$row['song_url'];
		$MinTime=$row['MinTime'];
		$MaxTime=$row['MaxTime'];
		$song_length = round(($MaxTime-$MinTime),2);
		$song_length_min = round(($song_length/60),2);
		$retStr1.='<option value='.$song_id.'>'.$song_name.'</option>';
		$retStr2.='<tr><td><a href="'.$song_url.'">'.$song_name.'</a></td><td><a href="'.$song_url.'">'.$song_url.'</a><td>'.$song_length.'</td><td>'.$song_length_min.'</td></tr>';
	}
	$retStr1.='</select>';
	$retVal[0]=$retStr1;
	$retVal[1]=$retStr2;
	return($retVal);
}

?>