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
require_once('../conf/header.php');
require("../effects/read_file.php");
extract ($_GET);
$arr = get_effects_hdr();
echo "<pre>";
//print_r($arr);
foreach($arr as $array1)
{
	extract($array1); // now we have effect_cass, effect_name
	echo "Effect_Class = $effect_class, username=$username, effect_name=$effect_name\n";
	$effects_class_array = get_effects_dtl($effect_class);
	echo "<font color=blue>";
	$effect_array=array();
	foreach($effects_class_array as $i =>$array2)
	{
		extract($array2);
		//	echo "$username $effect_name $param_name $param_value\n";
		$effect_array[$param_name]=$default_value;
	}
	//print_r($effect_array);
	echo "</font>";
	$effects_dtl_array=get_effects_user_dtl($username,$effect_name);
	//print_r($effects_dtl_array);
	foreach($effects_dtl_array as $i =>$array2)
	{
		extract($array2);
		echo "$username $effect_name $param_name $param_value\n";
		if($param_name=="start_color")
		{
			update_effects_user_dtl($username,$effect_name,"start_color","color1");
		}
		if($param_name=="end_color")
		{
			update_effects_user_dtl($username,$effect_name,"end_color","color2");
		}
	}
	insert_effects_user_dtl($username,$effect_name,"speed","1");
	insert_effects_user_dtl($username,$effect_name,"snowflake_type","2");
	insert_effects_user_dtl($username,$effect_name,"background_color","#000000");
	insert_effects_user_dtl($username,$effect_name,"number_snowflakes","12");
	insert_effects_user_dtl($username,$effect_name,"colour_model","1");
	update_background_color($username,$effect_name,"background_color","$000000");
	update_background_color($username,$effect_name,"speed","0.5");
	update_background_color($username,$effect_name,"number_snowflakes","12");
	update_background_color($username,$effect_name,"colour_model","1");
}
echo "</pre>";
//
//

function get_effects_dtl($effect_class)
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
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$query = "SELECT *
	FROM `effects_dtl`
	WHERE effect_class='$effect_class'
	ORDER BY sequence";
	$result=mysql_query($query) or die ("Error on $query");
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$arr[]=$row;
	}
	return ($arr);
}

function get_effects_user_dtl($username,$effect_name)
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
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$query = "SELECT *
	FROM `effects_user_dtl`
	WHERE username='$username' and effect_name='$effect_name'
	ORDER BY username, effect_name";
	$result=mysql_query($query) or die ("Error on $query");
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$arr[]=$row;
	}
	return ($arr);
}

function get_effects_hdr()
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
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$query = "SELECT * from effects_user_hdr where effect_class='snowflakes'
	order by username,effect_name";
	$array_effect_hdr=array();
	$result=mysql_query($query) or die ("Error on $query");
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$array_effect_hdr[]=array('username'=>$username,
		'effect_class'=>$effect_class,
		'effect_name'=>$effect_name);
	}
	return $array_effect_hdr;
}

function update_effects_user_dtl($username,$effect_name,$current_param_name,$new_param_name)
{
	echo "<pre>username,effect_name=$username,$effect_name</pre>\n";
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
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$effect_id = get_effect_id($username,$effect_name);
	$query = "UPDATE  effects_user_dtl set param_name='$new_param_name'
	where username='$username' and effect_name='$effect_name'
	and param_name='$current_param_name'";
	//
	//mysql_real_escape_string($param_value) . "',0,now(),now())";
	echo "<pre>$query</pre>\n";
	$result=mysql_query($query);
	echo "<pre>Error after update = " . mysql_errno() . "</pre>\n";
}
//

function insert_effects_user_dtl($username,$effect_name,$param_name,$param_value)
{
	//	echo "<pre>username,effect_name=$username,$effect_name</pre>\n";
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
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$effect_id = get_effect_id($username,$effect_name);
	$query="INSERT INTO `effects_user_dtl` (`effect_id`, `username`, `effect_name`, `param_name`,
	`param_value`, `segment`, `created`, `last_upd`) VALUES
	($effect_id, '$username', '$effect_name', '$param_name', '$param_value', 0, now(), now())";
	//
	//mysql_real_escape_string($param_value) . "',0,now(),now())";
	echo "<pre>$query</pre>\n";
	$result=mysql_query($query);
	if (mysql_errno() == 1062)
	{
		print 'duplicate entry';
	}
}

function update_background_color($username,$effect_name,$param_name,$param_value)
{
	echo "<pre>username,effect_name=$username,$effect_name</pre>\n";
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
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$effect_id = get_effect_id($username,$effect_name);
	$query = "UPDATE  effects_user_dtl set param_value='$param_value'
	where username='$username' and effect_name='$effect_name'
	and param_name='$param_name'";
	//
	//mysql_real_escape_string($param_value) . "',0,now(),now())";
	echo "<pre>$query</pre>\n";
	$result=mysql_query($query);
	if(mysql_errno()>0)
		echo "<pre>Error after update = " . mysql_errno() . "</pre>\n";
}
