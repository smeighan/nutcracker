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
<?php $menu="effect-form"; require "../conf/menu.php"; ?>
<?php
require("../effects/read_file.php");
if( isset($_REQUEST['group']) && $_REQUEST['group'] !='')
{
	$group=$_REQUEST['group'];
}
if( isset($_REQUEST['effect_class']) && $_REQUEST['effect_class'] !='')
{
	$effect_class=$_REQUEST['effect_class'];
}

$group=1;

// copy_model.php?filename=$filename?member_id=$member_id
/*
[fullpath_array] => Array
(
[0] => workspaces/2/AA+FLY_10_2_th.gif
[1] => workspaces/2/AA+FLY_50_2_th.gif
[2] => workspaces/2/S40X50+FLY_360_th.gif
[3] => workspaces/2/AA+GARLAND0_th.gif
)
	*/
foreach($_GET['user_effect_name'] as $i=>$user_effect_name)
{
	if(strlen($user_effect_name)>0)
	{
	//	echo "<pre>user_effect_name: $i $user_effect_name</pre>\n";
		$user_effect_name_array[$i]=$user_effect_name;
	}
}
foreach($_GET['desc'] as $i=>$desc)
{
	if(strlen($desc)>0)
	{
	//	echo "<pre>user_effect_name: $i $user_effect_name</pre>\n";
		$desc_array[$i]=$desc;
	}
}
foreach($_GET['fullpath_array'] as $i=>$fullpath)
{
	$line++;
	$user_effect_name=$user_effect_name_array[$i];
	$desc=$desc_array[$i];
	//echo "<pre>$line: $i; $fullpath. user_effect_name=$user_effect_name, desc=$desc</pre>\n";
	// workspaces/2/AA+FLY_0_0_th.gif. 
	//copy_model.php?filename=$filename?member_id=$member_id
	copy_model($fullpath,$user_effect_name,$desc);
}

function copy_model($fullpath,$user_effect_name,$desc)
{
$myusername=$_SESSION['SESS_LOGIN'];
	$path_parts = pathinfo($fullpath);  // workspaces/nuelemma/MEGA_001+SEAN_d_22.dat
	$dirname   = $path_parts ['dirname']; // workspaces/nuelemma
	$basename  = $path_parts ['basename']; // MEGA_001+SEAN_d_22.dat
	$extension =$path_parts  ['extension']; // .dat
	$filename  = $path_parts ['filename']; // MEGA_001+SEAN_d_22
	$tokens=explode("/",$dirname);
	//	0 = workspaces
	//	1 = nuelemma or id
	//
	$member_id=$tokens[1];
	$tok2=explode("~",$filename);
	$target=$tok2[0];
	$tok3=explode("_th",$tok2[1]);
	$effect_name=$tok3[0];
	/*echo "<pre>$fullpath";
	print_r($tok2);*/
	$username=get_username($member_id);
	$get_effect_user_hdr_array=get_effect_user_hdr($username,$effect_name);
	$get_effect_user_dtl_array=get_effect_user_dtl($username,$effect_name);
	$new_effect_name=date('YmdHis'); //'201206211612';
	if(strlen($user_effect_name)>0) $new_effect_name=$user_effect_name;
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
		$new_desc=$row['effect_name'];
		if(strlen($desc)>0) $new_desc=$desc;
		$insert=sprintf ("replace into effects_user_hdr (username,effect_name,effect_class,effect_desc,last_upd) values 
		('%s','%s','%s','%s',now())\n",
		$myusername,
		$new_effect_name,
		$row['effect_class'],
		$desc,
		'123-123-123');
	//	echo $insert;
		$result=mysql_query($insert) ;
			if (mysql_errno() == 1062)
		{
			echo "<pre>Got duplicate error on $insert</pre>\n";
		}
	}
	foreach($get_effect_user_dtl_array as $i=>$row)
		//foreach($row as $indx=>$value)
	{
		//	echo "$i: $indx => $value\n";
		if($row['param_name']=="effect_name") $new_value=$new_effect_name;
		else
		$new_value=$row['param_value'];
		$insert=sprintf ("replace into effects_user_dtl (username,effect_name,param_name,param_value,last_upd) values
		('%s','%s','%s','%s',now())\n",
		$myusername,
		$new_effect_name,
		$row['param_name'],
		$new_value,
		'123-123-123');
		//echo $insert;
		$result=mysql_query($insert) ;
			if (mysql_errno() == 1062)
		{
			echo "<pre>Got duplicate error on $insert</pre>\n";
		}
	}
	echo "</pre>\n";
	echo "<br/><h2>Effect has been copied into your Effects Library under the name $new_effect_name</h2>";
}
