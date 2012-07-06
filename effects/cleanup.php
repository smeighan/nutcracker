<?php
/*
Nutcracker: RGB Effects Builder
Copyright (C) 2012  Sean Meighan
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
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
<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"/> 
<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Welcome <?php echo $_SESSION['SESS_FIRST_NAME'];?></h1>
<?php $menu="cleanup"; require "../conf/menu.php"; ?>
<?php

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


$self=$_SERVER['PHP_SELF'];
/* [SESS_MEMBER_ID] => 2
    [SESS_FIRST_NAME] => sean
    [SESS_LAST_NAME] => MEIGHAN
    [SESS_LOGIN] => f*/
$username=$_SESSION['SESS_LOGIN'];

$member_id=$_SESSION['SESS_MEMBER_ID'];	

if($member_id<1 or !isset($member_id) or !isset($username)) die ("Invalid user. Contact Administrator");



echo "<pre>";
extract ($_POST);
//print_r($_POST);
//print_r($_SESSION);

$c1=count($effect);

$c2=count($target);
echo "Deleting $c2 rows from TARGETS\n";
if($c2>0)
	foreach($target as $i => $object_name)
	{
	$delete=sprintf("delete from models where username='$username' and object_name=\"$object_name\"\n");
	echo $delete . "\n"; 	$result=mysql_query($delete) or die ("Error on $delete");
	}
//print_r($target);

echo "Deleting $c1 rows from EFFECTS\n";
if($c1>0)
	foreach($effect as $i => $effect_name)
	{
	$delete=sprintf("delete from effects_user_dtl where username='$username' and effect_name=\"$effect_name\"\n");
	echo $delete . "\n"; 	$result=mysql_query($delete) or die ("Error on $delete");


	$delete=sprintf("delete from effects_user_hdr where username='$username' and effect_name=\"$effect_name\"\n");
	echo $delete . "\n"; 	$result=mysql_query($delete) or die ("Error on $delete");
	}
//print_r($effect);

echo "</pre>";

unset ($target);
unset ($effect);


echo "<form action=\"$self\" method=\"POST\">\n";

echo '<input type="submit" name="submit" value="Submit Form to delete checked rows" />';
/*$target_info['target_name']=$object_name;
$target_info['total_strings']=$total_strings;
$target_info['pixel_count']=$pixel_count;
$target_info['pixel_length']=$pixel_length;
$target_info['pixel_spacing']=$pixel_spacing; // PIXEL FIX
$target_info['unit_of_measure']=$unit_of_measure;
$target_info['topography']=$topography;*/

$line=0;
echo "<table border=1><tr><th>#</th>";
echo "<th>Object</th>";
echo "<th>Delete?</th>";
echo "<th>Object<br/>Name</th>";
echo "<th>Object<br/>Desc</th>";
echo "<th>Model<br/>Type</th>";
echo "<th>Pixel<br/>Count</th>";
echo "<th>Folds</th>";
echo "</tr>";
//$target_info=function get_info_target($username,$t_dat);

$query = "select * from models where username='$username' order by object_name";
$result=mysql_query($query) or die ("Error on $query");
while ($row = mysql_fetch_assoc($result))
{
	extract($row);
	$line++;
	printf ("<tr><td>$line</td><td bgcolor=lightblue>TARGET</td><td><input type=\"checkbox\" name=\"target[]\" value=\"%s\">
	<th>%s</th> <td>%s</td> <td align=\"left\">%s</td><td align=\"left\">%sx%s</td><td align=\"left\">%s</td></tr>\n",$object_name,$object_name,$object_desc,$model_type,$total_strings,$pixel_count,$folds);
}
$query2 = "SELECT * FROM `effects_user_hdr` WHERE `username`='$username' order by effect_class,effect_name";
//echo "<tr><td>$query2</td></tr>";

$result2=mysql_query($query2) or die ("Error on $query2");
while ($row2 = mysql_fetch_assoc($result2))
{
	extract($row2);
	$line++;
	printf ("<tr><td>$line</td><td bgcolor=lightgreen>EFFECT</td><td><input type=\"checkbox\" name=\"effect[]\" value=\"%s\">
	<th>%s</th> <td>%s</td> <td>%s</td></tr>\n",$effect_name,$effect_name,$effect_desc,$effect_class);
}
echo "</table>";

echo '<input type="submit" name="submit" value="Submit Form to delete checked rows" />';
echo '</form>';
mysql_close();
?>
