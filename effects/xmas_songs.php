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
//echo "max_execution_time =" . ini_get('max_execution_time') . "\n"; 
set_time_limit(60*60);
extract($_POST);
/*echo "<pre>";
print_r($_POST);
print_r($_SESSION);
echo "</pre>";*/
$username=$_SESSION['SESS_LOGIN'];
$member_id=$_SESSION['SESS_MEMBER_ID'];
if(isset($_POST['target'])===FALSE or $_POST['target']==NULL)
	$target=array();
else
{
	$target=$_POST['target'];
	update_votes($username,$target);
}
//print_r($target);
//echo "</pre>\n";
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];
$file = "Wizards_In_Winter.mo";
$dir = 'music_object_files'; 
echo "<h2>Christmas Songs </h2>";
echo "<h4>Instructions: Click in the column labeled 'Using It?' for every song you plan on using this year";
echo "<br/>Click submit and your vote will be tallied. Look under the 'Votes' column to see the current tally";
echo "<br/>across all users.</h4>";
echo "<br/><br/>";
$self=$_SERVER['PHP_SELF'];
echo "<form action=\"$self\" method=\"POST\">\n";
echo '<input type="submit" name="submit" value="Submit Form to have your choices recorded" />';
echo "<table border=1>";
show_songs($target);
echo "</table>";
echo '<input type="submit" name="submit" value="Submit Form to have your choices recorded" />';
echo '</form>';

function show_songs($target_array)
{
	//Include database connection details
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
	//
	$query ="select *
	from music_object_hdr where username='f' order by song_name,artist";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	$param_value="";
	$line=0;
	if(!$NO_DATA_FOUND)
	{
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		echo "<tr>";
		echo "<th>Using<br/>It?</th>";
		echo "<th>SongId</th>";
		echo "<th>Song Name</th>";
		echo "<th>Votes</th>";
		echo "<th>artist</th>";
		echo "<th>Comment</th>";
		echo "<th>Song URL</th>";
		echo "</tr>";
		$votes_array=get_votes();
		/*	echo "<pre>";
		print_r($votes_array);
		echo "</pre>\n";
		*/
		$c=count($target_array);
		while ($row = mysql_fetch_assoc($result))
		{
			$line++;
			extract($row);
			if(isset($votes_array[$music_object_id])===FALSE or $votes_array[$music_object_id]==NULL)
				$votes=0;
			else
			$votes = $votes_array[$music_object_id];
			$color="#FFFFFF";
			if($votes>0) $color="#BEFFA6";
			$checked='';
			for($i=0;$i<$c;$i++)
			{
				if(intval($target_array[$i])==intval($music_object_id)) $checked='checked=checked';
			}
			printf ("<tr><td><input type=\"checkbox\" name=\"target[]\" value=\"%s\" %s >
			<th>M-%s</th>  
			<td align=\"left\">%s</td>
			<td bgcolor=\"%s\">%s</td>
			<td align=\"left\">%s</td>
			<td align=\"left\">%s</td>
			<td align=\"left\"><a href=%s</a>%s</td>
			</tr>\n",$music_object_id,$checked,$music_object_id,$song_name,$color,$votes,$artist,$desc3,$song_url,$song_url);
		}
	}
}

function get_votes()
{
	//Include database connection details
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
	//
	$query ="SELECT music_object_id,count(*) as cnt  FROM `music_object_votes`
	group by music_object_id
	order by music_object_id";
	//echo "<pre>$query</pre>";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	$param_value="";
	$line=0;
	if(!$NO_DATA_FOUND)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$line++;
			extract($row);
			$votes_array[$music_object_id]=$cnt;
		}
	}
	return $votes_array;
}

function update_votes($username,$target)
{
	//Include database connection details
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
	//  first empty all of their votes
	$query = "delete from  music_object_votes where username='$username'";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	//
	// now, insert their choices
	foreach($target as $music_object_id)
	{
		$insert ="REPLACE INTO music_object_votes (username,music_object_id,date_created,date_updated)
			values ('$username',$music_object_id,now(),now())";
		//echo "<pre>$query</pre>";
		$result = mysql_query($insert) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $insert . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	}
}
