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
//
require_once("read_file.php");
$username= $_SESSION['SESS_LOGIN'];
echo "<h2>Nutcracker: RGB Effects Builder for user $username<br/>
On this page you customize an effects class and save it to your library</h2>"; 
// [QUERY_STRING] => effect_name=BUTTERFLY3?username=f?effect_class=butterfly?user_targets=AA
//
set_time_limit(0);
$directory ="../effects/workspaces";
if (file_exists($directory))
{
	} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}
//echo "<pre>user_effects=$user_effects</pre>\n";
//echo "<pre>query_string = ". $_SERVER['QUERY_STRING'] . "</pre>\n";
$effect_class="";
extract($_GET);

//show_array($_GET,"_GET Settings");
if(!empty($_GET)) // do we have something?
{
//
//	this is if we come here for the first time
// // http://localhost/nutcracker/effects/effect-exec.php?username=f&user_targets=A&user_effects=bars&submit=Submit+Form+to+customize+effect
//
//
//
//	this is if someone click son an existing effect
// http://localhost/nutcracker/effects/effect-exec.php?effect_name=BARS1&username=f&effect_class=bars&user_targets=A
	
	$debug=1;
	if($debug==1)
	{
		echo "<pre>effect_name = $effect_name</pre>\n";
		echo "<pre>username = $username</pre>\n";
		echo "<pre>effect_class = $effect_class</pre>\n";
		echo "<pre>user_targets = $user_targets</pre>\n";
		echo "<pre>effect_name = $effect_name</pre>\n";
	}
	if(isset($effect_name))
	{
		$effect_name=str_replace("%20"," ",$effect_name);
		$effect_user_dtl=get_effect_user_dtl($username,$effect_name);
		/*echo "<pre>effect_user_dtl array";
		print_r($effect_user_dtl);
		echo "</pre>";*/
		$cnt=count($effect_user_dtl);
		$value=array();
		for($i=0;$i<$cnt;$i++)
		{
			$param_name=$effect_user_dtl[$i]['param_name'];
			$value[$param_name]=$effect_user_dtl[$i]['param_value'];
			//echo "<pre>i=$i paran_nam=	$param_name  value[]=" . 	$value[$param_name] . "</pre>\n";
		}
	}
}
else // http://localhost/nutcracker/effects/effect-exec.php?username=gg&user_targets=AA2244&user_effects=bars&submit=Submit+Form+to+customize+effect
// http://localhost/nutcracker/effects/effect-exec.php?username=f&user_targets=A&user_effects=bars&submit=Submit+Form+to+customize+effect
{
	
}

//$effect_class=$user_effects;
show_my_effects($username,$user_targets);
if(isset($effect_class)) $effect_details=get_effect_details($effect_class);
if(isset($user_effects))
{
	$effect_hdr=get_effect_hdr($user_effects);
}
else 
{
	$effect_hdr=array();
}
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
if(isset($effect_hdr[0]['php_program']))
{
	$extra = $effect_hdr[0]['php_program'];
	$desc=$effect_hdr[0]['description'];
}
else
{
	$extra="";
	$desc="";
	$user_effects="";
}
?>
<script type="text/javascript" src="jscolor.js"></script>
<form action="<?php echo "$effect_class.php"; ?>" method="get">
<input type="hidden" name="username" value="<?php echo "$username"; ?>"/>
<input type="hidden" name="user_target" value="<?php echo "$user_targets"; ?>"/>
<input type="hidden" name="effect_class" value="<?php echo "$effect_class"; ?>"/>
<table border="1">
<tr><th>#</th><th>EFFECT_CLASS: <?php echo "$user_effects"; echo "<br/>" . $desc; ?></th></tr>
<?php
// >[QUERY_STRING] => effect_name=GIF1?username=f?effect_class=gif?user_targets=AA
//
extract($_GET);


$member_id=get_member_id($username);
if($member_id<1)
	echo "ERROR. Member_id is not valid. username=$username\n";
if (file_exists("gifs"))
{
	} else {
	echo "The directory gifs does not exist, creating it";
	mkdir("gifs", 0777);
}
if (file_exists("pictures"))
{
	} else {
	echo "The directory pictures does not exist, creating it";
	mkdir("pictures", 0777);
}
//
//
if($user_effects=="gif"  or $user_effects=="pictures")
{
	if($user_effects=="gif")      $uploaddir = "gifs/$member_id"; 
	if($user_effects=="pictures") $uploaddir = "pictures/$member_id"; 
	if (file_exists($uploaddir))
	{
		} else {
		echo "The directory $uploaddir does not exist, creating it";
		mkdir($uploaddir, 0777);
	}
	$dir = opendir($uploaddir); 
	$files = array(); 
	echo "<h2>Here is your current $user_effects library</h2>\n";
	echo "<table border=1>";
	echo "<tr>";
	/*
	Returns a array with 4 elements.
	The 0 index is the width of the image in pixels.
	The 1 index is the height of the image in pixels.
	The 2 index is a flag for the image type:
	1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(orden de bytes intel), 8 = TIFF(orden de bytes motorola), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF, 15 = WBMP, 16 = XBM. 
	*/
	$images=0;
	while($file = readdir($dir))
	{
		$tok=explode(".",$file);
		$tok=explode(".",$file);
		$ext=$tok[1];
		$ext=strtolower($ext);
		$image_types=array('gif','png','jpg');
		if(in_array($ext,$image_types))
		{
			$fullname = $uploaddir . "/" . $file;
			//	echo "<pre>ifull=$fullname, file=$file, tok1=$tok[1]</pre>\n";
			$result_array=getimagesize($fullname);
			$images++;
			if($images%8==0) echo "</tr><tr>\n";
			if ($result_array !== false)
			{
				$w=$result_array[0];
				$h=$result_array[1];
				$images_array[]=$file;
				echo "<td><img src=\"$fullname\"/><br/>$file<br/> $w x $h</td>";
			}
			else
			{
				echo "<td>File $file had an error</td>";
			}
		}
	}
	echo "</tr>";
	echo "</table>\n";
	echo "<h2>If you want to add images to your $user_effects library, go here: <a href=\"up.php?uploaddir=$uploaddir\">Add $user_effects to your library</a></h2>\n";
}
echo "<table border=1>";
$cnt=count($effect_details);
/*echo "<pre>";
print_r($effect_details);
echo "</pre>\n";*/
$textarea="123 test ...";
$file1=$file2=$layer_method="";
for($i=0;$i<$cnt;$i++)
{
	$sequence=$effect_details[$i]['sequence'];
	if($sequence<90)
	{
		echo "<tr>";
		$numb=$i+1;
		echo "<td>#" . $effect_details[$i]['sequence'] . "</td><td>";
		if($effect_details[$i]['param_name']=='php_program')
		{
			$param_name=$effect_details[$i]['param_name'];
			if (!isset($value[$param_name]) || $value[$param_name] == null)
			{
				$textarea="";
			}
			else
			$textarea= $value[$param_name];
			echo "<textarea rows=\"20\" cols=\"120\" wrap=\"physical\" name=\"php_program\">";
			echo "$textarea  ";
			echo "</textarea>\n";
		}
		else
		{
			echo "<input type=\"text\"   ";
			$mystring = $effect_details[$i]['param_name'];
			$findme="color";
			$pos = strpos($mystring, $findme);
			if ($pos === false)
			{
				echo " class=\"input\" ";
			}
			else {
				echo " class=\"color {hash:true} {pickerMode:'HSV'}\" ";
			}
			$param_name=$effect_details[$i]['param_name'];
			$val="?";
			if (!isset($param_name) || $param_name == null)
			{
				$val="";
				$empty="#1";
			}
			else if (!isset($value[$param_name]) || $value[$param_name] == null)
			{
				$empty="#2 param=[$param_name]";
				//	$val = $value[$param_name];
				$val=0;
			}
			else
			{
				$val = $value[$param_name];
				$empty="3 param=[$param_name] val=[$val]";
				if($value[$param_name] == null) $val='0';
			}
			if (isset($val) and $val != null) 
			echo " value=\"$val\" ";
			echo "STYLE=\"background-color: #ABE8EC;\" size=\"32\"  \n";
			echo "name=\"" . $effect_details[$i]['param_name'] . "\" />";
		}
		echo "</td><td><br/>" . $effect_details[$i]['param_prompt'] . " (" . $effect_details[$i]['param_range'] . "):";
		echo "<br/>" . $effect_details[$i]['param_desc'] . "</td>\n";
		echo "</tr>\n";
		$param_name=$effect_details[$i]['param_name'];
		if($param_name=='file1' or $param_name=='file2')
		{
			if($param_name=='file1' ) $file1=$value[$param_name];
			if($param_name=='file2' ) $file2=$value[$param_name];
		}
		if($param_name=='layer_method' ) $layer_method=$value[$param_name];
		if($effect_details[$i]['effect_class']=='layer' and $effect_details[$i]['param_name']=='file2')
		{
			echo "<tr><td>Select two effects to layer together.<br/></td>";
			echo "<td><table border=1>";
			echo "<tr><th>Filename</th><th>Target</th><th>Window<br/>Degrees</th>";
			echo "<th>Seq_Duration</th></tr>";
			$dir="../effects/workspaces/2";
			$member_id=get_member_id($username);
			$dir="../effects/workspaces/$member_id";
			$files=getFilesFromDir($dir);
			sort($files);
			foreach($files as $filename)
			{
				$tok=explode("/",$filename); //workspaces/2/AA+FLY.nc
				$file=$tok[2];
				$tok2=explode("~",$file);  // AA+FLY.nc
				$target=$tok2[0];
				$tok3=explode(".",$tok2[1]);
				$effect=$tok3[0];
				$checked="";
				if($file==$file1) $checked="CHECKED";
				if($file==$file2) $checked="CHECKED";
				$effect_details2=get_effect_user_dtl2($username,$effect);
				/*	 [15] => Array
				(
				[username] => f
				[effect_name] => BARBERPOLE_180
				[param_name] => window_degrees
				[param_value] => 180array2
				[created] => 
				[last_upd] => 2012-07-26 19:07:49
				)
					*/
				/*echo "<pre>effect_details2:";
				print_r($effect_details2);
				echo "</pre>";*/
				if(isset($effect_details2['window_degrees'])) 	$window_degrees=$effect_details2['window_degrees'];
				$seq_duration=	$effect_details2['seq_duration'];
				echo "<tr><td><input type=\"checkbox\" name=\"LAYER_EFFECTS[]\" value=\"$file\"  $checked /> $file<br /></td>";
				echo "<td>$target</td>";
				//if(!isset($window_degrees) or $window_degrees==null) $window_degrees=0;
				echo "<td>$window_degrees</td>";
				echo "<td>$seq_duration</td></tr>";
				//	echo "<tr><td>$filename</td></tr>";
			}
			echo "</table>";
			echo "</td>";
			echo "<td><table><tr>";
			$cols=0;
			foreach($files as $filename)
			{
				$tok=explode(".",$filename); //workspaces/2/AA+FLY.nc
				$gifname = $tok[0] . "_th.gif";
				$tok2=explode("/",$filename); //workspaces/2/AA+FLY.nc
				$fname = $tok2[2];
				echo "<td>$fname<br/><img src=\"$gifname\"/></td>";
				$cols++;
				if($cols%6==0) echo "</tr><tr>";
			}
			echo "</tr></table></td>";
			echo "</tr>";
			echo "<tr><td>How should layers be joined. </td><td>";
			if($layer_method=="Pri-1")
				echo "<INPUT TYPE=\"RADIO\" NAME=\"lmethod\" VALUE=\"Pri-1\" CHECKED />Priority to first effect<BR>";
			else
			echo "<INPUT TYPE=\"RADIO\" NAME=\"lmethod\" VALUE=\"Pri-1\"         />Priority to first effect<BR>";
			//
			if($layer_method=="Pri-2")
				echo "<INPUT TYPE=\"RADIO\" NAME=\"lmethod\" VALUE=\"Pri-2\" CHECKED />Priority to second effect<BR>";
			else
			echo "<INPUT TYPE=\"RADIO\" NAME=\"lmethod\" VALUE=\"Pri-2\"         />Priority to second effect<BR>";
			//
			if($layer_method=="Avg")
				echo "<INPUT TYPE=\"RADIO\" NAME=\"lmethod\" VALUE=\"Avg\" CHECKED />Average two pixels together<BR>";
			else
			echo "<INPUT TYPE=\"RADIO\" NAME=\"lmethod\" VALUE=\"Avg\"         />Average two pixels together<BR>";
			//
			if($layer_method=="Mask-1")
				echo "<INPUT TYPE=\"RADIO\" NAME=\"lmethod\" VALUE=\"Mask-1\" CHECKED />First effect is mask against second effect<BR>";
			else
			echo "<INPUT TYPE=\"RADIO\" NAME=\"lmethod\" VALUE=\"Mask-1\"         />First effect is mask against second effect<BR>";
			//
			if($layer_method=="Mask-2")
				echo "<INPUT TYPE=\"RADIO\" NAME=\"lmethod\" VALUE=\"Mask-2\" CHECKED />Second effect is mask against first effect<BR>";
			else
			echo "<INPUT TYPE=\"RADIO\" NAME=\"lmethod\" VALUE=\"Mask-2\"         />Second effect is mask against first effect<BR>";
			echo "</td></tr>";
		}
	}
}
?>
</table>
<input type="submit" name="submit" value="Submit Form to create your effect"  class="button" />
</form>
</body>
</html>
<?php

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
					if($extension=="nc" )
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
	return ($files);
}

function get_effect_hdr($effect_class)
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
	$query ="select * from effects_hdr where effect_class='$effect_class'";
	//echo "<pre>get_effect_hdr query: $query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if (!$result)
	{
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	$effects_classes=array();
	$effect_hdr=array();
	if(!$NO_DATA_FOUND)
	{
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while ($row = mysql_fetch_assoc($result))
		{
			extract($row);
			$effect_hdr[]=$row;
		}
	}
	return $effect_hdr;
}

function get_effect_details($effect_class)
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
	$query ="select * from effects_dtl where effect_class='$effect_class' and active='Y' order by sequence";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if (!$result)
	{
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	$effects_details=array();
	if(!$NO_DATA_FOUND)
	{
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while ($row = mysql_fetch_assoc($result))
		{
			extract($row);
			$effects_details[]=$row;
		}
	}
	return $effects_details;
}

function show_my_effects($username,$user_targets)
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
	$query ="select * from effects_user_hdr where username='$username'";
	$query="select effect_class,count(*) from effects_user_hdr where username='$username'
	group by effect_class
	order by effect_class";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if (!$result)
	{
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$effect_class_array[]=$effect_class;
	}
	if($NO_DATA_FOUND==0)
		$cnt=count($effect_class_array);
	else
	$cnt=0;
	echo "<table border=\"1\">\n";
	echo "<tr><th colspan=3>Your Library of Effects</th></tr>\n";
	/*echo "<tr>";
	for($i=0;$i<$cnt;$i++)
		echo "<th>" . $effect_class_array[$i] ."</th>";
	echo "</tr>";*/
	echo "<tr>";
	$query_rows=array();
	$j=0;
	for($i=0;$i<$cnt;$i++)
	{
		$query ="select * from effects_user_hdr where username='$username'
		and effect_class = '$effect_class_array[$i]' 
		order by effect_class,effect_name";
		$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
		$query_rows=array();
		while ($row = mysql_fetch_assoc($result))
		{
			extract($row);
			$query_rows[]=$row;
			/*echo "<tr>";
			echo "<td><a href=\"effect-exec.php?effect_name=$effect_name?username=$username?effect_class=$effect_class?user_targets=$user_targets\">$effect_name</a></td>";
			echo "<td>$effect_class </td>";
			echo "<td>$effect_desc </td>";
			echo "</tr>\n";*/
			$j++;
			$effects_array[$j]=array($effect_name,$username,$effect_class,$effect_desc);
		}
		}	
	/*echo "<pre>";
	print_r($effects_array);*/
	$rows_per_column=60;
	$maxj=$j;
	$rows_per_column=1+intval($maxj/4);
	echo "<td>";
	echo "<table border=\"1\">\n";
	echo "<tr>";
	$old_effect_class="";
	$effect_class_counter=0;
	for($l=1;$l<=$rows_per_column;$l++)
	{
		for($k=1;$k<=$maxj;$k+=$rows_per_column)
		{
			$check = $l%$rows_per_column;
			if($check==0)
				$j=$k+$rows_per_column-1;
			else
			$j=$k+$check-1;
			/*	if(isset($effects_array[$j])===false or $effects_array[$j]==null)
				$eff_array="";
			else*/
			if(isset($effects_array[$j]))
			{
				$eff_array=$effects_array[$j];
				$effect_name=$eff_array[0];
				$username=$eff_array[1];
				$effect_class=$eff_array[2];
				$effect_desc=$eff_array[3];
			}
			else
			{
				$eff_array=array();
				$effect_name="";
				$username="";
				$effect_class="";
				$effect_desc="";
			}
			if($j<=$maxj)
			{
				if($effect_class <> $old_effect_class) $effect_class_counter++;
				echo "<td>$j:<a href=\"effect-exec.php?effect_name=$effect_name&username=$username&effect_class=$effect_class&user_targets=$user_targets\">$effect_name</a></td>";
				echo "<td>$effect_class </td>";
				echo "<td>$effect_desc </td>";
				$old_effect_class=$effect_class;
			}
		}
		echo "</tr><tr>";
	}
	echo "</table>\n";
	echo "</td>";
	echo "</tr>";
	echo "</table>\n";
	echo "<br/><br/>\n";
	mysql_close();
	return ($query_rows);
}

function  get_value_effect_user_dtl($username,$effect_name,$param_name)
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
	$query ="select * from effects_user_dtl where effect_name='$effect_name' and username = '$username' and param_name='$param_name'";
	echo "<pre>get_value_effect_user_dtl: $query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if (!$result)
	{
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	$param_value="";
	if(!$NO_DATA_FOUND)
	{
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while ($row = mysql_fetch_assoc($result))
		{
			extract($row);
		}
	}
	return $param_value;
}

function images_pulldown($images_array)
{
	echo "<td>";
	echo "<div align=\"center\">";
	echo "<select name=\"images[]\">";
	echo "<option value=\"--\">No Selection</option>";
	foreach($images_array as $file)
	{
		if($nc_array[$phrase_name] == $file)
		{
			$selected="SELECTED";
		}
		else
		{
			$selected="";
		}
		echo "<option value=\"$file\" $selected>$file</option>";
	}
	echo "</select>";
	echo "</div>\n";
	echo "</td>\n";
}
