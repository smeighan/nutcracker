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
$arr = get_effects_user_dtl();
echo "<pre>";
//print_r($arr);
foreach($arr as $effect_class=>$array1)
{
	echo "Effect_Class = $effect_class\n";
	$effects_dtl_array=get_effects_dtl($effect_class);
	echo "<font color=red>";
	print_r($effects_dtl_array);
	echo "</font>";
	foreach($array1 as $username=>$array2)
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
}
echo "</pre>";
//
//

function get_effects_user_dtl()
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
	$query = "SELECT hdr.effect_class, dtl. * 
	FROM effects_user_hdr hdr,  `effects_user_dtl` dtl
	WHERE hdr.username = dtl.username
	AND hdr.effect_name = dtl.effect_name
	ORDER BY dtl.username, dtl.effect_name";
	$result=mysql_query($query) or die ("Error on $query");
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$arr[$effect_class][$username][$effect_name]=$row;
	}
	return ($arr);
}

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
	$query = "SELECT param_name,default_value from effects_dtl
	where effect_class = '$effect_class'
	order by sequence";
	$result=mysql_query($query) or die ("Error on $query");
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$array_effect_classes[$param_name]=$default_value;
	}
	return $array_effect_classes;
}

function get_max_date_gallery()
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
	$query = "SELECT max(created) created, count(*) cnt from gallery";
	$result=mysql_query($query);
	$row = mysql_fetch_assoc($result);
	extract ($row);
	$arr=array($created,$cnt);
	return $arr;
}
