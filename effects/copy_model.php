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
<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"> 
<meta name="description" content="RGB Sequence builder for LOR, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, LOR, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Welcome <?php echo $_SESSION['SESS_FIRST_NAME'];?></h1>
<? $menu="effect-form";?>
<? require "../effects/menu.php"; ?>
<?php
//
require("../effects/read_file.php");
$myusername=$_SESSION['SESS_LOGIN'];
//
extract($_GET);
////	sample call
// http://localhost/nutcracker/effects/copy_model.php?filename=AA+METEOR1_GREEN_th?member_id=2
if(!empty($_GET))
{
	$tokens=explode("?",$_SERVER['QUERY_STRING']);
	foreach($tokens as $i=>$pair)
	{
		$t=explode("=",$pair);
		if($t[0]=="filename")
		{
			$tok=explode("~",$t[1]);
			$target=$tok[0];
			$tok2=explode("_th",$tok[1]);
			$effect_name=$tok2[0];
		}
		if($t[0]=="member_id") $member_id=$t[1];
	}
}
$username=get_username($member_id);
$get_effect_user_hdr_array=get_effect_user_hdr($username,$effect_name);
$get_effect_user_dtl_array=get_effect_user_dtl($username,$effect_name);
$new_effect_name=date('YmdHis'); //'201206211612';


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
	
	
echo "<pre>";

foreach($get_effect_user_hdr_array as $i=>$row)
	//foreach($row as $indx=>$value)
{
	//	echo "$i: $indx => $value\n";
	$insert=sprintf ("insert into effects_user_hdr (username,effect_name,effect_class,effect_desc,last_upd) values 
	 ('%s','%s','%s','%s',now())\n",
	$myusername,
	$new_effect_name,
	$row['effect_class'],
	$row['effect_name'],
	'123-123-123');
	echo $insert;
	$result=mysql_query($insert) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $insert . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
}
foreach($get_effect_user_dtl_array as $i=>$row)
	//foreach($row as $indx=>$value)
{
	//	echo "$i: $indx => $value\n";
	if($row['param_name']=="effect_name") $new_value=$new_effect_name;
	else
	$new_value=$row['param_value'];
	$insert=sprintf ("insert into effects_user_dtl (username,effect_name,param_name,param_value,last_upd) values
	 ('%s','%s','%s','%s',now())\n",
	$myusername,
	$new_effect_name,
	$row['param_name'],
	$new_value,
	'123-123-123');
	echo $insert;
	$result=mysql_query($insert) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $insert . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
}
echo "</pre>\n";

echo "<br/><h2>Effect has been copied into your Effects Library under the name $new_effect_name</h2>";
//
/*
Array
(
[0] => Array
(
[effect_class] => meteors
[username] => f
[effect_name] => METEOR1_GREEN
[effect_desc] => desc
[created] => 
[last_upd] => 2012-06-20 21:32:54
)
	)
	Array
(
[0] => Array
(
[username] => f
[effect_name] => METEOR1_GREEN
[param_name] => effect_name
[param_value] => METEOR1_GREEN
[created] => 
[last_upd] => 2012-06-20 21:32:54
)
	[1] => Array
(
[username] => f
[effect_name] => METEOR1_GREEN
[param_name] => end_color
[param_value] => #1ACC06
[created] => 
[last_upd] => 2012-06-20 21:32:54
)
	[2] => Array
(
[username] => f
[effect_name] => METEOR1_GREEN
[param_name] => frame_delay
[param_value] => 50
[created] => 
[last_upd] => 2012-06-20 21:32:54
)
	[3] => Array
(
[username] => f
[effect_name] => METEOR1_GREEN
[param_name] => maxMeteors
[param_value] => 7
[created] => 
[last_upd] => 2012-06-20 21:32:54
)
	[4] => Array
(
[username] => f
[effect_name] => METEOR1_GREEN
[param_name] => maxPhase
[param_value] => 7
[created] => 
[last_upd] => 2012-06-20 21:32:54
)
	[5] => Array
(
[username] => f
[effect_name] => METEOR1_GREEN
[param_name] => meteor_type
[param_value] => 2
[created] => 
[last_upd] => 2012-06-20 21:32:54
)
	[6] => Array
(
[username] => f
[effect_name] => METEOR1_GREEN
[param_name] => seq_duration
[param_value] => 13.2
[created] => 
[last_upd] => 2012-06-20 21:32:54
)
	[7] => Array
(
[username] => f
[effect_name] => METEOR1_GREEN
[param_name] => sparkles
[param_value] => 55
[created] => 
[last_upd] => 2012-06-20 21:32:54
)
	[8] => Array
(
[username] => f
[effect_name] => METEOR1_GREEN
[param_name] => start_color
[param_value] => #1ACC06
[created] => 
[last_upd] => 2012-06-20 21:32:54
)
	[9] => Array
(
[username] => f
[effect_name] => METEOR1_GREEN
[param_name] => window_degrees
[param_value] => 180
[created] => 
[last_upd] => 2012-06-20 21:32:54
)
	)*/
echo "</body>";
echo "</html>";
