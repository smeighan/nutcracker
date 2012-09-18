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
// index.php
require_once("read_file.php");
/*echo "<pre>";
echo "POST:";
print_r($_POST);
echo "GET:";
print_r($_GET);
echo "</pre>\n";*/
/*GET:Array
(
[username] => 
[fullpath_array] => Array
(
[0] => workspaces/2/A+BARS1_TEST_th.gif
)
	[user_effect_name] => Array
(
[0] => COPY_BARS4
[1] => 
[2] => 
[3] => 
[4] => 
[5] => 
[6] => 
[7] => 
[8] => 
[9] => 
)
	[desc] => Array
(
[0] => 
[1] => 
[2] => 
[3] => 
[4] => 
[5] => 
[6] => 
[7] => 
[8] => 
[9] => 
)
	[submit] => Submit
)*/
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
$line=0;
foreach($_GET['fullpath_array'] as $i=>$fullpath)
{
	$line++;
	$user_effect_name=$user_effect_name_array[$i];
	if(isset($desc_array[$i])) $desc=$desc_array[$i];
	else
	$desc='';
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
	$tok2a=explode("+",$filename);
	$c=count($tok2);
	$ca=count($tok2a);
	/*echo "<pre>";
	echo "copy_model($fullpath,$user_effect_name,$desc)\n";
	print_r($path_parts);
	echo "c=$c, ca=$ca\n";
	echo "</pre>";*/
	if($c==1 and $ca==2) $tok2=$tok2a; // we have a TAR+EFF file instead of TAR~EFF
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
	
	foreach($get_effect_user_hdr_array as $i=>$row)
		//foreach($row as $indx=>$value)
	{
		/*echo "<pre>HDR:";
		foreach($row as $indx=>$value)
		{
			echo "i=$i  $indx=>$value\n";
		}
		echo "</pre>";*/
		//
		//
		/*HDR:i=0  effect_class=>bars
		i=0  username=>f
		i=0  effect_name=>BARS1_TEST
		i=0  effect_desc=>desc
		i=0  music_object_id=>
		i=0  start_secs=>
		i=0  end_secs=>
		i=0  phrase_name=>
		i=0  created=>
		i=0  last_upd=>2012-09-18 07:16:51*/
		
		//
		//
		//	echo "$i: $indx => $value\n";
		$new_desc=$row['effect_name'];
		if(strlen($desc)>0) $new_desc=$desc;
		$insert=sprintf ("replace into effects_user_hdr (username,effect_name,effect_class,effect_desc,last_upd) values 
		('%s',toupper('%s'),'%s','%s',now())\n",
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
	/*	echo "<pre>DTL:";
		foreach($row as $indx=>$value)
		{
			echo "i=$i  $indx=>$value\n";
		}
		echo "</pre>";*/
		//
		//
		/*DTL:i=0  username=>f
		i=0  effect_name=>BARS1_TEST
		i=0  param_name=>color1
		i=0  param_value=>#FF0000
		i=0  segment=>0
		i=0  created=>
		i=0  last_upd=>2012-09-18 07:16:51
		DTL:i=1  username=>f
		i=1  effect_name=>BARS1_TEST
		i=1  param_name=>color2
		i=1  param_value=>#2BFF00
		i=1  segment=>0
		i=1  created=>
		i=1  last_upd=>2012-09-18 07:16:51*/
		//
		//
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
