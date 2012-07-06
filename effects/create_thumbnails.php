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
echo "<pre>";
print_r($_SESSION);
echo "</pre>\n";
//create_thumbnails($effect_class,$group);
get_all_effects();
?>
</body>
</html>
<?

function getFilesFromDir($dir)
{
	$files = array(); 
	$n=0;
	if ($handle = opendir($dir))
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file != "." && $file != ".." )
			{
				if(is_dir($dir.'/'.$file))
				{
					$dir2 = $dir.'/'.$file; 
					$files[] = getFilesFromDir($dir2);
				}
				else 
				{ 
					$path_parts = pathinfo($file);  // workspaces/nuelemma/MEGA_001+SEAN_d_22.dat
					$dirname   = $path_parts['dirname']; // workspaces/nuelemma
					$basename  = $path_parts['basename']; // MEGA_001+SEAN_d_22.dat
					$extension =$path_parts['extension']; // .dat
					$filename  = $path_parts['filename']; // MEGA_001+SEAN_d_22
					$cnt=count($files);
					$tokens=explode("/",$dirname);
					//	0 = workspaces
					//	1 = nuelemma or id
					//
					$member_id=$tokens[1];
					$pos=strpos($file,"_amp.gif");
					$th =strpos($file,"_th.gif");
					if($extension=="gif" and $pos === false and $th>1)
					{
						$files[] = $dir.'/'.$file; 
						$n++;
						//echo "<pre>$cnt $n $file</pre>\n";
					}
					} 
				} 
			} 
		closedir($handle);
	}
	return array_flat($files);
}

function array_flat($array)
{
	$tmp=array();
	foreach($array as $a)
	{
		if(is_array($a))
		{
			$tmp = array_merge($tmp, array_flat($a));
		}
		else 
		{ 
			$tmp[] = $a;
		}
		} 
	return $tmp;
}

function create_thumbnails($effect_class_passed,$group)
{
	// Usage find all gif files under the workspaces subdirectory 
	echo "<br/>";
	echo "<br/>";
	echo "<br/>";
	echo "<h1>create_thumbnails of Effects by all users of Nutcracker</h1>";
	$dir = 'workspaces'; 
	$foo = getFilesFromDir($dir); 
	$cnt=count($foo);
	$line=0;
	/*
	[939] => workspaces/nuelemma/MEGA_001+SEAN_d_22.dat
	[940] => workspaces/nuelemma/MEGA_001+SEAN_d_29.dat
	[941] => workspaces/nuelemma/MEGA_001+SEAN_d_25.dat
	[942] => workspaces/nuelemma/MEGA_001+SEAN_d_24.dat
	[943] => workspaces/28/SGMEG24F+_d_15.dat
	[944] => workspaces/28/SGMEG24F+_d_9.dat
	[945] => workspaces/28/SGMEG24F+_d_19.dat
	[946] => workspaces/28/SGMEG24F+_d_23.dat
	[947] => workspaces/28/SGMEG24F+_d_6.dat
	*/
	foreach ($foo as $i => $file)
	{
		$path_parts = pathinfo($file);  // workspaces/nuelemma/MEGA_001+SEAN_d_22.dat
		$dirname   = $path_parts['dirname']; // workspaces/nuelemma
		$basename  = $path_parts['basename']; // MEGA_001+SEAN_d_22.dat
		$extension =$path_parts['extension']; // .dat
		$filename  = $path_parts['filename']; // MEGA_001+SEAN_d_22
		$tokens=explode("/",$dirname);
		//	0 = workspaces
		//	1 = nuelemma or id
		//
		$member_id=$tokens[1];
		$pos=strpos($file,"_amp.gif");
		$th=strpos($file,"_th.gif");		
		/*echo "<pre>$file member=$member_id pos=$pos, th=$th</pre>\n";*/
		if($extension=="gif" and $pos === false and $th>1 and $member_id>=1 and $member_id < 99999999)
		{
			$tok2=explode("+",$filename);
			$target_model=$tok2[0];
			$effect_name=$tok2[1];   // AA+SPIRAL_th.gif"
			$tok3=explode("_th",$tok2[1]);
			$effect_name=$tok3[0];
			$username = get_username($member_id);
			$effect_class=get_eff_class($username,$effect_name);
			if($username=='f' or $effect_class <> 'text')
			{
				//echo "<td><a href=\"copy_model.php?model=$target_mode?effect=$effect_name?member_id=$member_id\"><img src=\"$file\"/><br/>#$line target:  $target_model<br/>effect:  $effect_class.$effect_name</a></td>\n";
				$gif_array[$effect_class][$effect_name]=$file;
				$cnt=count($gif_array);
				if(empty($effect_class) and $member_id==2)
				{
					$newphrase = str_replace(" ", "_", $file);
					/*echo "<pre>$cnt $file .. $newphrase\n</pre>\n";*/
					rename($file,$newphrase);
				}
			}
		}
	}
	echo "<table border=1>";
	/*echo "<pre>gif_array:";
	print_r($gif_array);
	echo "</pre>\n";*/
	// 	foreach ($amperage as $i => $n1)     
	//	{
		//foreach ($n1 as $s => $value)
		//{
			if(empty($effect_class_passed)) $effect_class_passed="meteors";
			$effect_class_match =$effect_class_passed;
			$group_match=0;
			$item=0;
			$pics_per_row=6;
			$pics_per_group = $pics_per_row*20;
			foreach ($gif_array as $effect_class => $class)
			{
				sort($class);
				$cnt=count($class);
				$item=0;
				/*echo "<tr><td><a href=\"create_thumbnails.php?effect_class=$effect_class\"><h2>Click Here for</br>$effect_class</h2></a></td>";*/
				echo "<tr><td>CLASS: $class has $cnt gif files</td></tr>";
				foreach ($class as $file)
				{
					$item++;
					$group = intval(($item-1)/$pics_per_group);
					/*if($effect_class_match == $effect_class)
					{
						$line++;
						if($line%$pics_per_row==1)
						{
							//	echo "<tr><td>$effect_class<br/>Group: $group</td><td></td>";
							echo "<tr><td></td><td></td>";
						}
						if( $group_match==$group)
						{
							*/
							$path_parts = pathinfo($file);
							$dirname   = $path_parts['dirname']; // workspaces/2
							$basename  = $path_parts['basename']; // AA+CIRCLE1_d_1.dat
							$extension =$path_parts['extension']; // .dat
							$filename  = $path_parts['filename']; //  AA+CIRCLE1_d_1
							$tok=explode("/",$dirname);
							$member_id=$tok[1];
							//echo "<td>$item: $file</td>\n";
							$tok2=explode("+",$filename);
							$target_model=$tok2[0];
							$effect_name=$tok2[1];
							$username= $_SESSION['SESS_LOGIN'];
							echo "<td><a href=\"copy_model.php?effect_class=$effect_class?effect_name=$effect_name?member_id=$member_id?username=[$username]\"><img src=\"$file\"/></a></td>\n";
						}
						if($line%$pics_per_row==0)
						{
							echo "</tr>";
						}
					}
					echo "</table>";
				}
				
				function get_eff_class($username,$effect_name)
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
					$query = "select effect_class from effects_user_hdr where username='$username' and effect_name='$effect_name'";
					$result=mysql_query($query) or die ("Error on $query");
					while ($row = mysql_fetch_assoc($result))
					{
						extract($row);
					}
					mysql_close();
					return ($effect_class);
				}
				
				function get_all_effects()
				{
					$line=0;
					$query="SELECT * FROM  `effects_user_hdr` a, members m
					WHERE a.username = m.username and a.effect_class='butterfly'
					order by a.username,a.effect_name";
					$link=open_db();
					$result=mysql_query($query,$link);
					if(!$result)
					{
						echo "Error on $query\n";
						mysql_error();
					}
					while ($row = mysql_fetch_assoc($result))
					{
						extract($row);
						$line++;
						$first=ucfirst(strtolower($firstname));
						$last=ucfirst(strtolower($lastname));
				//		echo "<pre>$line CLASS:$effect_class $username $effect_name #$member_id ($first $last}</pre>\n";
						printf("<pre>%4d CLASS: %-12s EFFECT_NAME: %-22s %-16s  #%4d  (%s %s)</pre>\n",$line,$effect_class ,$effect_name,$username ,$member_id,$first,$last);
				}
}