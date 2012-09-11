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
/*$tokens=explode("?",$_SERVER['QUERY_STRING']);
$c=count($tokens);
$tokens2=explode("base=",$tokens[0]);
$base=$tokens2[1];*/
extract($_GET);
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
/*echo "<pre>";
print_r($files_array);
echo "</pre>\n";*/
echo "<pre>";
print_r($_SESSION);
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];
$file = "Wizards_In_Winter.mo";
$dir = 'music_object_files'; 
/*nowfall|Peanuts|
Soldiers Silent Night |Mannheim Steamroller/Poem |
Songs City of Prague Philharmonic unless noted otherwise noted | City of Prague Philharmonic |Friday: Movie Night at Johnson's Christmas Corner
Star Wars: A New Hope| City of Prague Philharmonic |Friday: Movie Night at Johnson's Christmas Corner


delete FROM `music_object_hdr` WHERE `music_object_id` >=53


*/
$fh=fopen("xmas_songs.csv","r");
while (!feof($fh))
{
	$line = fgets($fh);
	$tok=explode("|",$line);
	$c=count($tok);
	//printf ("%s|%s|%s\n",$song,$artist,$desc);
	if($c>1)
	{
		echo "<pre>";
		$song=trim($tok[0]);
		$artist=trim($tok[1]);
		$desc=trim($tok[2]);
		insert_music_object_hdr($song,$artist,$desc);
		//print_r($tok);
		echo "</pre>";
	}
}
echo "<pre>";

function insert_music_object_hdr($song,$artist,$desc)
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
	/*$query ="delete from music_object_dtl  where music_object_id=$music_object_id";
	echo "<pre>get_effect_user_hdr: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	printf("Records deleted for music_object_id $music_object_id: %d\n", mysql_affected_rows());*/
	//
	//
	$username='f';
	$active_set='N';
	$song=mysql_real_escape_string($song);
	$artist=mysql_real_escape_string($artist);
	$desc=mysql_real_escape_string($desc);
	$insert ="insert into music_object_hdr (active_set,username,song_name,artist,desc3)
		values ('$active_set','$username','$song','$artist','$desc')";
	echo "<pre>$insert</pre>\n";
	$result = mysql_query($insert) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $insert . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}
