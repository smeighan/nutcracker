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
Tun update gallery database
gallery.php?INSERT_NEW_GIFS=1
*/
set_time_limit(0);
//ini_set("memory_limit","2024M");
require_once('../conf/header.php');
require("../effects/read_file.php");
extract ($_GET);
/* Array
(
[submit] => Submit Form to create your target model
[number_gifs] => 100
[sort] => member_id
[effect_class_selected] => Array
(
[0] => all
[1] => bars
[2] => butterfly
)
	[number_segments] => 
)*/
/*echo "<pre>";
print_r($_GET);
echo "</pre>\n";*/
// http://meighan.net/nutcracker/effects/gallery.php?start=101?end=151?number_gifs=50?sort=member_id?effect_class_selected=dummy|garlands|meteors
// QUERY_STRING] => start=101?end=151?number_gifs=50?sort=member_id?effect_class_selected=dummy|garlands|meteors
//
//
//$tokens=explode("?model=",$REQUEST_URI);
$number_gifs=0;
$n=0;
truncate_gallery();
$dir="workspaces";
echo "<pre>";
$n=getFilesFromDir($dir,$n);
echo "<h3>End of Program. N=$n</h3>";

function getFilesFromDir($dir,$n)
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
	$line=$line0=0;
	if ($handle = opendir($dir))
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file != "." && $file != ".." )
			{
				if(is_dir($dir.'/'.$file))
				{
					$dir2 = $dir.'/'.$file; 
					$n=getFilesFromDir($dir2,$n);
				}
				else 
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
					$pos=strpos($file,"_amp.gif");
					$th =strpos($file,"_th.gif");
					if($extension=="gif" and $pos === false and $th>1)
					{
						//$files[] = $dir.'/'.$file; 
						$fullpath = $dir.'/'.$file; 
						//
						//
						/*fullpath=workspaces/104/MATRIX~BARS_SEAN_th.gif
						Array ( [0] => workspaces [1] => 104 [2] => MATRIX~BARS_SEAN_th.gif ) */
						// workspaces/2/AA+LAYER2_th.gif
						$tok=explode("/",$fullpath);
						//	echo "<pre>fullpath=$fullpath</pre>\n";
						//	print_r($tok);
						$member_id=$tok[1];
						$username=get_username($member_id);
						/*if(strpos($tok[2],"+")>0)
							$tok2=explode("+",$tok[2]);*/
						if(strpos($tok[2],"~")>0)
						{
							$tok2=explode("~",$tok[2]);
							$tok3=explode("_th.",$tok2[1]);
							$effect_name=$tok3[0];
							$effect_class = get_eff_class($username,$effect_name);
							if($effect_class=='?')
							{
								$line0++;
							}
							else
							{
								$line++;
								$n++;
								$query = "insert ignore into gallery (fullpath,effect_class,
								username,effect_name,linenumber,member_id) values 
								('$fullpath','$effect_class','$username','$effect_name',$line,$member_id)";
								if($n%200 == 1)
								{
									//echo "<pre>\n $n $fullpath\n $query</pre>\n";
									echo "\n";
								}
								$result = mysql_query($query,$link);
								if(mysql_errno() <> 0)
									echo "<pre><br/>A fatal MySQL error occured.\n<br />Query: " . 
								$query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error();
								echo "+";
							}
							// not '?'
							} // if(strpos($tok[2],"~")>0)
							else
						{
							unlink ($fullpath);
							echo "x";
						}
						} //  if($extension=="gif" and $pos === false and $th>1)
						} // processing a file, not a directory
				} // if ($file != "." && $file != ".." )
				} // while (false !== ($file = readdir($handle)))
		}
	closedir($handle);
	return $n;
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
	$effect_class='?';
	$result=mysql_query($query) or die ("Error on $query");
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
	}
	mysql_close();
	return ($effect_class);
}

function truncate_gallery()
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
	//
	$query = "truncate table gallery";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}
