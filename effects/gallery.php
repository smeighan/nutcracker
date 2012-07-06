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
/*echo "<pre>";
print_r($_SERVER);
echo "</pre>\n";*/
$tokens=explode("?",$_SERVER['QUERY_STRING']);
$c=count($tokens);
/*echo "<pre>";
print_r($tokens);
echo "</pre>\n";*/
$c=count($tokens);
$group=1;
if($c>0)
{
	$tokens2=explode("=",$tokens[0]);
	$group=$tokens2[1];
		/*echo "<pre>c=$c";
		print_r($tokens);
		echo "</pre>\n";*/
		
		$tokens2=explode("=",$tokens[1]);
		$group_size=$tokens2[1];
}
if(!isset($group) or $group<1) $group=1;
if(!isset($group_size) or $group_size<1) $group_size=40;
$pics_in_group=$group_size;
gallery($group,$pics_in_group);

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

function gallery($group,$pics_in_group)
{

echo "<pre>gallery($group,$pics_in_group)</pre>\n";

	// Usage find all gif files under the workspaces subdirectory 
	echo "<br/>";
	echo "<br/>";
	echo "<br/>";
	echo "<h1>Gallery of Effects by all users of Nutcracker</h1>";
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
	$effect_class="xx";
	$pics=0;
	
	$pics_per_row=8;
	$max_rows=($pics_in_group/$pics_per_row);
	$last_pic=$pics_per_row-1;
	$group_to_show=$group;
	$pics_col=0; $pics_row=$start_pic=$end_pic=0;
	$pic_group=0;
	echo "<table border=1>";
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
		//echo "<pre>$file member=$member_id pos=$pos, th=$th</pre>\n";
		if($extension=="gif" and $pos === false and $th>1 and $member_id>=1 and $member_id < 99999999)
		{
			$tok2=explode("+",$filename);
			$target_model=$tok2[0];
			$effect_name=$tok2[1];   // AA+SPIRAL_th.gif"
			$tok3=explode("_th",$tok2[1]);
			$effect_name=$tok3[0];
			$username = get_username($member_id);
			//$effect_class=get_eff_class($username,$effect_name);
			//if($username=='f' or $effect_class <> 'text')
			{
				$pics++;
				if($pics%$pics_in_group == 1) // check if should output group header
				{
					$pic_group++;
					if($pic_group>=1)
					{
						echo "</tr>";
						// $pic_group
						// $max_rows=4;
						// $pics_per_row=8;
						$start = $pics_in_group * ($pic_group-1) + 1;
						$end=$start + $pics_in_group -1;
						echo "<tr><td><a href=gallery.php?group=$pic_group>($start - $end)<br/> qty40</a></td>";
						$start = $pics_in_group*2 * ($pic_group-1) + 1;
						$end=$start + $pics_in_group*2 -1;
						echo "<td><a href=gallery.php?group=$pic_group?group_size=80>($start - $end) qty80</a></td>";
						$start = $pics_in_group*3 * ($pic_group-1) + 1;
						$end=$start + $pics_in_group*3 -1;
						echo "<td><a href=gallery.php?group=$pic_group?group_size=120>($start - $end)qty120</a></td>";
							echo "</tr>";
					}
					$start_pic=$pics;
				}
				if($pics%$pics_per_row==1) // check if we should advance row
				{
					echo "</tr><tr>";
					$pics_row++;
				}
				if($pic_group==$group_to_show or $group_to_show==0) // should we show gif?
				echo "<td><a href=\"copy_model.php?filename=$filename?member_id=$member_id\"><img src=\"$file\"/></a>$pics</td>\n";
				$end_pic=$pics;
			}
		}
	}
	if($pics%$pics_per_row!=1)
	{
		$pic_group++;
		$start = $pics_in_group * ($pic_group-1) + 1;
		$end=$start + $pics_in_group -1;
		echo "<tr><td><a href=gallery.php?group=$pic_group>($start - $end)</a></td></tr>";
		$start_pic=$pics;
	}
	
	echo "<table border=1>";
	/*echo "<pre>gif_array:";
	print_r($gif_array);
	echo "</pre>\n";*/
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
