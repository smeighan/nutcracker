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
require("../effects/read_file.php");
$segment_array=array();
echo "<pre>";
/*echo "POST:\n";
print_r($_POST);
//echo "SERVER:";
//print_r($_SERVER);
//echo "SESSION:\n";
//print_r($_SESSION);
echo "</pre>";*/
// http://localhost/nutcracker/login/single_strand-form.php?user=f?total_strings=3
// 
//
// QUERY_STRING] => user=f?total_strings=6?object_name=A0
//
//
//$tokens=explode("?model=",$REQUEST_URI);
$tokens=explode("?",$_SERVER['QUERY_STRING']);
$tok2=explode("=",$tokens[0]); $username = $tok2[1];
$tok2=explode("=",$tokens[1]); $total_strings = $tok2[1];
$tok2=explode("=",$tokens[2]); $object_name = $tok2[1];
set_time_limit(0);
if(isset($_POST)===false or $_POST==null ) // First time here? Called by member-index.php
{ // yes
	$pixel_array=get_strands($username,$object_name);
	$segment_array=get_segments($username,$object_name);
	$number_segments_arr=get_number_segments($username,$object_name);
	$number_segments=$number_segments_arr[0];
	$gif_model=$number_segments_arr[1];
	$first_time=1;
}
else
{ // no, so this self submit has values for us to update
	$first_time=0;
	extract($_POST);
	update_strands($username,$object_name,$pixel_array);
	$c=count($segment_array);
	update_number_segments($username,$object_name,$number_segments,$gif_model);
	$c=count($segment_array);
	/*echo "<pre>";
	print_r($segment_array);
	echo "</pre>";*/
	if($c>0) update_segments($username,$object_name,$segment_array);
	/*	POST:
	Array
	(
	[pixel_array] => Array
	(
	[1] => 2
	[2] => 22
	[3] => 6
	[4] => 11
	[5] => 11
	[6] => 33
	)
		[number_segments] => 4
	[submit] => Submit Form to create your target model
	)*/
}
/*echo "<pre>Tokens:\n";
echo "gif_model=$gif_model\n";
echo "number_segments=$number_segments\n";
echo "total_strings=$total_strings\n";
echo "number_segments=$number_segments\n";
echo "</pre>";*/
//
//
echo "<h1>Single Strand</h1>";
$self=$_SERVER['PHP_SELF'];
echo "<form action=\"$self?user=$username?total_strings=$total_strings?object_name=$object_name\" method=\"post\">\n";
?>
<input type="submit" name="submit" value="Submit Form to create your target model" />
<table border="1">
<?php
for($string=1;$string<=$total_strings;$string++)
{
	echo "<tr><td>Enter the number of pixels in String #$string</td>";
	if(isset($pixel_array[$string]))
		$maxPixel=$pixel_array[$string];
	else
	$maxPixel=0;
	echo "<td><input type=\"text\" STYLE=\"background-color: #ABE8EC;\" size=\"5\" maxlength=\"6\" 
	value=\"$maxPixel\" name=\"pixel_array[$string]\"/></td>\n";
	echo "</tr>\n";
}
?>
<tr>
<td>Number of Segments</td>
<td><input type="text" style="background-color: #ABE8EC;" size="8" maxlength="" 
<?php echo "value=\"$number_segments\""; ?> name="number_segments"/><br/>
</td>
</tr>
<tr>
<td>What Type of Gif Model to preview with</td>
<?php $checked_single=$checked_window=$checked_arch="";
if($gif_model=="single") $checked_single="checked"; 
if($gif_model=="window") $checked_window="checked"; 
if($gif_model=="arch") $checked_arch="checked"; ?>
<td><input type="radio" name="gif_model" value="single" <?php echo "$checked_single "; ?> />Straight Line<br/>
<input type="radio" name="gif_model" value="window" <?php echo "$checked_window "; ?> />Window (Assumes four segments)<br/>
<input type="radio" name="gif_model" value="arch" <?php echo "$checked_arch "; ?> />Arch (Each segment makes an arch)</td>
</td>
<td><img src="../images/single_strand.png" /></td>
</tr>
</table>
<?php
$c=count($segment_array);
if($first_time==0 or $c>0) // if not first time, then we have data we can show
{
	echo "<table border=1>";
	for ($loop=1;$loop<=3;$loop++)
	{
		echo "<tr>";
		$pixel=0;
		for($string=1;$string<=$total_strings;$string++)
		{
			$maxPixel=$pixel_array[$string];
			for($p=1;$p<=$maxPixel;$p++)
			{
				$pixel++;
				if($string%2==0) $color="#AAFFAA";
				else $color="#AAAAFF";
				if($loop==1)
				{
					if($pixel==1)
					{
						echo "<th>Virtual<br/>Pixel</th>";
					}
					echo "<th>$pixel</th>";
				}
				if($loop==2)
				{
					if($pixel==1)
					{
						echo "<td>String</td>";
					}
					echo "<td bgcolor=$color>$string</td>";
				}
				if($loop==3)
				{
					if($pixel==1)
					{
						echo "<td>Pixel</td>";
					}
					echo "<td bgcolor=$color>$p</td>";
					$s=1;
					$target_array[$s][$pixel]['string']=$string;
					$target_array[$s][$pixel]['user_pixel']=$p;
				}
			}
		}
		echo "</tr>";
	}
	echo "</table>";
	echo "<br/><h3>Your virtual strand is $pixel Pixels long</h3>\n";
	echo "<table border=1>";
	if($number_segments==null) $number_segments=1;
	for ($segment=1;$segment<=$number_segments;$segment++)
	{
		$pixels_per_segment= intval($pixel/$number_segments);
		$start_pixel = ($segment-1)*$pixels_per_segment + 1;
		echo "<tr>";
		echo "<td>Segment $segment starts at virtual pixel#</td>";
		echo "<td>$start_pixel</td>";
		echo "<td><input type=\"text\" STYLE=\"background-color: #ABE8EC;\" size=\"5\" maxlength=\"6\" 
		value=\"$start_pixel\" name=\"segment_array[$segment]\"></td>\n";
		echo "</tr>\n";
	}
	single_strand($pixel,$gif_model,$username,$object_name,$target_array); // let us actually write the targets/member_id/file.dat
}
?>
</form>
<?php
echo "</body>\n";
echo "</html>\n";

function display_file($full_path)
{
	$lines = file($full_path);
	echo "<pre>";
	// Loop through our array, show HTML source as HTML source; and line numbers too.
	foreach ($lines as $line_num => $line)
	{
		echo "$line";
	}
	echo "</pre>";
}

function single_strand($maxPixels,$gif_model,$username,$model_name,$target_array)
{
	//echo "<pre>single_strand($maxPixels,$gif_model,$username,$model_name)</pre>\n";
	#
	#	output files are created for each segment
	#	8 segment = t1_8.dat output file
	#	16 segment = t1_16.dat output file
	#	32 segment = t1_32.dat output file
	#
	#
	#
	#	Build a mega-Tree with arbitray strands
	#	House is 40' wide, 25' tall
	$member_id=get_member_id($username);
	$path="../targets/" . $member_id ;
	##	passed in now thru runtime arg, 	strands=16;
	$dat_file = $path . "/" . $model_name . ".dat";
	$fh = fopen($dat_file, 'w') or die("can't open file $fh");
	fwrite($fh,"#    $dat_file\n");
	fwrite($fh,"#    Col 1: Your TARGET_MODEL_NAME\n");
	fwrite($fh,"#    Col 2: Strand number.\n");
	fwrite($fh,"#    Col 3: Nutcracker Pixel#\n");
	fwrite($fh,"#    Col 4: X location in world coordinates\n");
	fwrite($fh,"#    Col 5: Y location in world coordinates\n");
	fwrite($fh,"#    Col 6: Z location in world coordinates\n");
	fwrite($fh,"#    Col 7: User string\n");
	fwrite($fh,"#    Col 8: User pixel\n");
	fwrite($fh,"# \n");
	$s=1;
	for ($p=1;$p<=$maxPixels;$p++)
	{
		$x=$p*3;
		$y=0;
		$h=72;
		fwrite($fh,sprintf ("%s %3d %3d %7.3f %7.3f %7.3f 0 %5d %5d %s %s\n", $model_name,$s,$p,$x,$y,$h,$target_array[$s][$p]['string'] ,$target_array[$s][$p]['user_pixel'], $username ,$model_name));
		//printf ("%s %3d %3d %7.3f %7.3f %7.3f 0 %5d %5d %s %s\n", $model_name,$s,$p,$x,$y,$h,$target_array[$s][$p]['string'] ,$target_array[$s][$p]['user_pixel'], $username ,$model_name);
	}
	fwrite($fh, "\n" );
	fclose($fh);
	echo "</pre>\n";
	return $dat_file;
}

function getx($r,$degree)
{
	$PI = pi();
	$DTOR = $PI/180;
	$RTOD = 180/$PI;
	$radian = $degree * $DTOR;
	$x=$r*sin($radian);
	$y=$r*cos($radian);
	#	print r,degree,radian,x,y;
	return $x;
}

function gety($r,$degree)
{
	$PI = pi();
	$DTOR = $PI/180;
	$RTOD = 180/$PI;
	$radian = $degree * $DTOR;
	$x=$r*sin($radian);
	$y=$r*cos($radian);
	return $y;
}

function drop_and_create($db,$table,$query)
{
	$drop_query = "drop table " . $table;
	//mysql_query($drop_query,$db) or die("Error on '$drop_query'");
	mysql_query($drop_query,$db);
	mysql_query($query,$db) or die ("Error on $query");
}

function insert_target_model($db,$file)
{
	$fh = fopen($file, 'r') or die("can't open file");
	$line=0;
	$row=0;
	echo "<table border=1>";
	while (!feof($fh))
	{
		$line = fgets($fh);
		#echo "<pre>$line<br/></pre>";
		//$tok=preg_split("/ +/", $line);
		$tok=preg_split('/\t/', $line);
		$row++;
		if(strlen($tok[0])>0)
		{
			$insert="INSERT into members (username,role,joined,posts) values ('" . $tok[0] . "','". $tok[1]  . "','FEB-12-2012',0)";
			//echo "<td>$insert</td>";
			mysql_query($insert,$db) or die ("Failed executing $insert");
		}
		//	echo "</tr>";
	}
	echo "</table>";
	fclose($fh);
	$query="SELECT * FROM members";
	$result = mysql_query($query,$db) or die("Failed Query");
	echo "<b><center>Database Output</center></b><br><br>";
	$i=0;
	echo "<table border=1>";
	$row=0;
	while ($myrow = mysql_fetch_row($result))
	{
		$row++;
		printf("<tr><td>$row</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>n", 
		$myrow[0], $myrow[1], $myrow[2], $myrow[3], $myrow[4]);
	}
	echo "</table>";
}

function update_strands($username,$object_name,$pixel_array)
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
	$delete = "delete from models_strands where username='$username' and object_name='$object_name'";
	//	echo "<pre>update_strands: delete=$delete</pre>\n";
	mysql_query($delete) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $delete . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	//
	foreach ($pixel_array as $string => $maxPixel)
	{
		$insert = "insert into models_strands( username,object_name,string,pixels,last_updated)
			values ('$username','$object_name',$string,$maxPixel,now())";
		//	echo "<pre>update_strands: query=$insert</pre>\n";
		mysql_query($insert) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $insert . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	}
}

function get_strands($username,$object_name)
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
	//
	//
	$query = "select * from models_strands where username='$username' and  object_name='$object_name'
	order by string";
	//echo "<pre>update_strands: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	$pixel_array=array();
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$pixel_array[$string]=$pixels;
	}
	return $pixel_array;
}

function update_segments($username,$object_name,$segment_array)
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
	$delete = "delete from models_strand_segments where username='$username' and object_name='$object_name'";
	//echo "<pre>update_segments: delete=$delete</pre>\n";
	mysql_query($delete) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $delete . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	//
	//echo "<pre>update_segments: segment array:";
	//print_r($segment_array);
	//echo "</pre>\n";
	foreach ($segment_array as $segment => $starting_pixel)
	{
		$insert = "insert into models_strand_segments( username,object_name,segment,starting_pixel,last_updated)
			values ('$username','$object_name',$segment,$starting_pixel,now())";
		//echo "<pre>update_segments: query=$insert</pre>\n";
		mysql_query($insert) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $insert . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	}
}

function get_segments($username,$object_name)
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
	//
	//
	$query = "select * from models_strand_segments where username='$username' and  object_name='$object_name'
	order by segment";
	//echo "<pre>update_segments: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$segment_array=array();
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$segment_array[$segment]=$starting_pixel;
	}
	return $segment_array;
}

function update_number_segments($username,$object_name,$number_segments,$gif_model)
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
	//
	$update = "update models set number_segments=$number_segments,gif_model='$gif_model',last_updated=now()
		where username='$username' and  object_name='$object_name'";
	//echo "<pre>update_number_segments: query=$update</pre>\n";
	mysql_query($update) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $update . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}

function get_number_segments($username,$object_name)
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
	//
	$query = "select * from  models where username='$username' and  object_name='$object_name'";
	//echo "<pre>get_number_segments: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	$number_segments=-1;
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
	}
	/*echo "<pre>get_number_segments:";
	print_r($row);
	echo "</pre>\n";*/
	$number_segments_arr[0]=$number_segments;
	$number_segments_arr[1]=$gif_model;
	return $number_segments_arr;
}
