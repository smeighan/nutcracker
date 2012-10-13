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
	echo "<font color=red>";
	$effects_dtl_array=get_effects_user_dtl($username,$effect_name);
	//print_r($effects_dtl_array);
	foreach($effects_dtl_array as $i =>$array2)
	{
		extract($array2);
		echo "$username $effect_name $param_name $param_value\n";
		if(strlen($param_value)>0) $effect_array[$param_name]=$param_value;
	}
	echo "<font color=red>";
	//print_r($effect_array);
	update_effects_user_dtl($username,$effect_name,$effect_array);
	echo "</font>";
	/*foreach($array1 as $username=>$array2)
	{
		$dtl_array=$effects_dtl_array;
		foreach($array2 as $val3=>$array3)
		{
			$l1=strlen($effect_class);
			$l2=strlen($username);
			if($l1>0 and $l2>0)
			{
				echo "<font color=blue>";
				echo "$effect_class l1=$l1=> $username l2=$l2 val3=$val3\n";
				print_r($array3);
				echo "</font>";
				$pn=$pv='';
				foreach($array3 as $pname =>$pvalue)
				{
					echo "pname=$pname pvalue=$pvalue\n";
					if($pname='param_name') $pn=$pvalue;
					else if($pname='param_value') $pv=$pvalue;
					else
					{
						$dtl_array[$pname]=$pvalue;
					}
				}
				$dtl_array[$pn]=$pv;
			}
		}
		print_r($dtl_array);
	}
	*/
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
	$query = "SELECT * from effects_user_hdr
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

function update_effects_user_dtl($username,$effect_name,$effect_array)
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
	foreach($effect_array as $param_name=>$param_value)
	{
		$query = "REPLACE into `effects_user_dtl`
		(effect_id,username,effect_name,	param_name,	param_value,	segment,	created,	last_upd)
			values ($effect_id,'$username','$effect_name','$param_name','" .
		mysql_real_escape_string($param_value) . "',0,now(),now())";
		echo "<pre>$query</pre>\n";
		$result=mysql_query($query) or die ("Error on $query");
	}
}