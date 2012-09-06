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
/*echo "<pre>";
echo "POST:\n";
print_r($_POST);
echo "SERVER:";
print_r($_SERVER);
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
	$number_segments=get_number_segments($username,$object_name);
	$first_time=1;
}
else
{ // no, so this self submit has values for us to update
	$first_time=0;
	extract($_POST);
	update_strands($username,$object_name,$pixel_array);
	update_number_segments($username,$object_name,$number_segments);
	update_segments($username,$object_name,$segment_array);
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
/*echo "<pre>Tokens:";
print_r($tokens);
echo "total_strings=$total_strings\n";
echo "number_segments=$number_segments\n";
echo "</pre>";*/
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
	$maxPixel=$pixel_array[$string];
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
</table>
<?php
if($first_time==0)
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
}
?>
</form>
<?php
echo "</body>\n";
echo "</html>\n";

function get_models($username,$model_name)
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
	/*
	
	function get_models(f,ZZ)
		query=select * from models where username='f' and object_name='ZZ'
	Array
	(
	[username] => f
	[object_name] => ZZ
	[object_desc] => newest test mar 1
	[model_type] => MTREE
	[string_type] => 
	[pixel_count] => 50
	[folds] => 50
	[pixel_first] => 1
	[pixel_last] => 50
	[pixel_length] => 200.00
	[unit_of_measure] => in
	[total_strings] => 24
	[direction] => 
	[orientation] => 0
	[topography] => UP_DOWN_NEXT
	[topography] => BOT_TOP
	[h1] => 120.00
	[h2] => 0.00
	[d1] => 40.00
	[d2] => 0.00
	[d3] => 0.00
	[d4] => 0.00
	)
		*/
	//	
	$query ="select * from models where username='$username' and object_name='$model_name'";
	echo "query=$query\n";
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
	$query_rows=array();
	// While a row of data exists, put that row in $row as an associative array
	// Note: If you're expecting just one row, no need to use a loop
	// Note: If you put extract($row); inside the following loop, you'll
	//       then create $userid, $fullname, and $userstatus
	//
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
	}
	$pixel_count_even=$folds * intval($pixel_count/$folds); // this is the total pixels that are evenly divisible.
	if($folds==1)
	{
		$maxPixels=$pixel_count;
		$maxStrands=$total_strings;
	}
	else
	{
		$maxPixels = intval($pixel_count/$folds); // 
		$maxStrands=intval(0.5+($total_strings*$pixel_count)/$maxPixels);
		if(strtoupper($start_bottom)=='Y')
		{
			$maxStrands=intval(0.5 + ($total_strings*$pixel_count_even)/$maxPixels);
		}
	}
	echo "pixel_count=$pixel_count, pixel_count_even=$pixel_count_even, maxStrands=$maxStrands, maxPixels=$maxPixels</pre>";
	$directory ="../targets";
	if (file_exists($directory))
	{
		} else {
		echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$member_id=get_member_id($username);
	$directory ="../targets/" . $member_id;
	if (file_exists($directory))
	{
		} else {
		echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	echo "<table border=1>";
	echo "<tr><th colspan=$maxStrands>The top of this table is the top of your RGB device</th></tr>";
	//echo "<tr><th></th>";
	//for($p=1;$p<=$maxPixels;$p++)
	//{
		//echo "<th><b>n$p</b></th>";
	//}
	//echo "</tr>";
	$target_array=array();
	$n=0;
	$p=0;
	echo "<table border=1>";
	for($string=1;$string<=$total_strings;$string++)
	{
		$pixel_countN=$pixel_count;
		if(strtoupper($start_bottom)=='Y')
		{
			$pixel_countN=$pixel_count_even;
		}
		for($user_pixel=1;$user_pixel<=$pixel_countN;$user_pixel++)
		{
			$n++;
			$mod=($n%$maxPixels);
			$s=intval(($n-1)/$maxPixels)+1;
			if($mod==1)
			{
				if($folds==1)
				{
					$inc=-1;
					$p=$maxPixels+1;
				}
				else if ($string%2==1 or $folds%2==0)	// if we have even number of folds or odd strand
				{
					if($folds==1 or $s%2==1) // Odd strands
					{
						$inc=-1;
						$p=$maxPixels+1;
					}
					else    // these are the even strands
					{
						$inc=1;
						$p=0;
					}
				}
				else if($mod==1)  // we have an odd number of folds
				{
					if($folds==1 or $s%2==1) // Odd strands
					{
						$inc=1;
						$p=0;
						if(strtoupper($start_bottom)=='Y' and $s%$folds==1 and $string%2==0 and $mod==1)
						{
							$inc=-1;
							$p=$maxPixels+1;
						}
					}
					else    // these are the odd strands
					{
						$inc=-1;
						$p=$maxPixels+1;
						if(strtoupper($start_bottom)=='Y' and $s%$folds==1 and $string%2==0 )
						{
							$inc=-1;
							$p=$maxPixels+1;
						}
					}
				}
			}
			$p+=$inc;
			/*	if($s%2==1) $color="lightgreen";
			else $color="lightblue";
			echo "<tr><td >str=$string</td><td>userp=$user_pixel</td><td bgcolor=\"$color\">s=$s</td><td><b>p=$p</b></td>";
			echo "<td>inc=$inc</td><td>mod=$mod</td><td>n=$n</td>";
			echo "</tr>";*/
			$target_array[$s][$p]['string'] =$string;
			$target_array[$s][$p]['user_pixel'] =$user_pixel;
		}
	}
	echo "</table>";
	echo "<table border=1>";
	$string=1;
	$n2=0;
	echo "pixel_count=$pixel_count, pixel_count_even=$pixel_count_even, maxStrands=$maxStrands, maxPixels=$maxPixels</pre>";
	for($p=1;$p<=$maxPixels;$p++)
	{
		for($s=1;$s<=$maxStrands;$s++)
		{
			$n2++;
			if(strtoupper($start_bottom)=='Y' or $n2<=$n)
			{
				if(isset($target_array[$s][$p]['string']))
				{
					$string=$target_array[$s][$p]['string'] ;
					if($string%2==1) $color="lightblue";
					else $color="lightgreen";
					$user_pixel=$target_array[$s][$p]['user_pixel'];
				}
				else
				{
					$user_pixel=0;
					$color="lightgray";
				}
			}
			else
			{
				$user_pixel=0;
				$color="lightgray";
			}
			echo "<td bgcolor=\"$color\">$user_pixel</td>";
		}
		echo "</tr>\n";
	}
	echo "<tr>";
	for($s=1;$s<=$maxStrands;$s++)
	{
		$s_mod=$s%$folds;
		echo "<td >s=$s<br/>$s_mod</td>\n";
	}
	echo "</tr>";
	echo "<tr>";
	for($string=1;$string<=$total_strings;$string++)
	{
		$string_mod=$string%2;
		echo "<td colspan=$folds >string=$string<br/>$string_mod</td>\n";
	}
	echo "</tr>";
	echo "</table>";
	$target_array2= array($target_array,$username,$model_name) ;
	if($model_type=="MTREE")
	{
		$full_path=megatree($maxStrands,$maxPixels,$pixel_count,$directory,$object_name,$target_array2);
	}
	else if($model_type=="MATRIX" or $model_type=="HORIZ_MATRIX" or $model_type=="RAY")
	{
		$full_path=matrix($folds,$maxStrands,$maxPixels,$pixel_count,$directory,$object_name,$model_type,$target_array2);
	}
	else if($model_type=="SINGLE_STRAND")
	{
		$full_path=single_strand($folds,$maxStrands,$maxPixels,$pixel_count,$directory,$object_name,$model_type,$target_array2);
	}
	else
	{
		echo "<pre>ERROR! Model type $model_type is unknown</pre>\n";
	}
	echo "<pre>";
	//display_file($full_path);
	//print_r($target_array);
	//	insert_target_array($target_array,$username,$model_name,$maxStrands,$maxPixels);
	?>
	<a href="../index.html">Home</a> | <a href="../login/member-index.php">Target Generator</a> | 
	<a href="../effects/effect-form.php">Effects Generator</a> | <a href="../login/logout.php">Logout</a>
	<?php
	return ($query_rows);
}

function insert_target_array($target_array,$username,$model_name,$maxStrand,$maxPixel)
{
	echo "<pre> insert_target_array($target_array,$username,$model_name,$maxStrand,$maxPixel)</pre>\n";
	//delete_rows($username, $model_name);
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
	$delete = "delete from model_dtl where username='$username' and object_name='$model_name'";
	mysql_query($delete) or die ("Error on $insert");
	for($s=1;$s<=$maxStrand;$s++)
		for($p=1;$p<=$maxPixel;$p++)
	{
		$string = $target_array[$s][$p]['string'];
		$user_pixel = $target_array[$s][$p]['user_pixel'];
		$insert = "insert into model_dtl( username,object_name,strand,pixel,string,user_pixel,last_upd)
			values ('$username','$model_name',$s,$p,$string,$user_pixel,now())";
		mysql_query($insert) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $insert . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	}
}

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

function single_strand($folds,$maxStrands,$maxPixels,$pixel_count,$directory,$object_name,$model_type,$target_array2)
{
	#
	#	output files are created for each segment
	#	8 segment = t1_8.dat output file
	#	16 segment = t1_16.dat output file
	#	32 segment = t1_32.dat output file
	#
	#
	#
	#	Build a mega-Tree with arbitray strands
	$PI = pi();
	$DTOR = $PI/180;
	$RTOD = 180/$PI;
	$pixel_spacing=3.0;
	$width_bottom = $pixel_count*3.0;	// assume 3" spacing between nodes.
	$width_top = $pixel_count*3.0;	// assume 3" spacing between nodes.
	if($model_type=="RAY") $width_top=$width_bottom/5;
	$height = $pixel_spacing*$maxPixels;
	$target_array=$target_array2[0];
	$username=$target_array2[1];
	$model_name=$target_array2[2];
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
	$pixels=$maxPixels;
	$x_spacing=$x_spacing_top=$pixel_spacing;
	echo "<pre>";
	echo "x_spacing=x_spacing_top=pixel_spacing;\n";
	echo "$x_spacing=$x_spacing_top=$pixel_spacing;\n";
	if($model_type=="RAY")
		$x_spacing_top=$width_top/$maxStrands;
	echo "<pre>";
	echo "model_type=$model_type";
	echo "maxStrands=$maxStrands,maxPixels=$maxPixels\n ";
	for ($s=1;$s<=$maxStrands;$s++)
	{
		$hyp=0;
		$s2=$maxStrands/2;
		$s_delta = $s2-$s;
		$x2=$s_delta*$x_spacing;
		$x2_top=$s_delta*$x_spacing;
		if($model_type=="RAY")
		{
			$x2_top= $s_delta*$x_spacing_top;
		}
		$y2=0;
		for ($p=1;$p<=$maxPixels;$p++)
		{
			$mod=($p%$maxStrands)+1;
			$mod2 = $maxPixels-$mod+1;
			$mod2 = $maxPixels-$p;
			if($model_type=="MATRIX" or $model_type=="HORIZ_MATRIX")
				$h= ($mod2*$x_spacing);
			else 	if($model_type=="RAY")
				$h= ($mod2*$x_spacing) - $x_spacing;
			if(isset($target_array[$s][$p]['string']))
			{
				$s_orig=$s; $p_orig=$p;
				$s0=$s; $p0=$p;
				if($model_type=="HORIZ_MATRIX")
				{
					if($s_orig%$folds==1)
					{
						$s0=$p;
					}
					else{
						$s0=$maxPixels-$p+1;
					}
					$p0=$s_orig;
					$s2=$maxPixels/2;
					$mod2 = $maxStrands-$p0;
				}
				$x2=$s0*3 - $s2*3;
				$h=$mod2*3;
				fwrite($fh,sprintf ("%s %3d %3d %7.3f %7.3f %7.3f 0 %5d %5d %s %s\n", $object_name,$s0,$p0,$x2,$y2,$h,$target_array[$s][$p]['string'] ,$target_array[$s][$p]['user_pixel'], $username ,$model_name));
				printf ("%s %3d %3d %7.3f %7.3f %7.3f 0 %5d %5d %s %s\n", $object_name,$s0,$p0,$x2,$y2,$h,$target_array[$s][$p]['string'] ,$target_array[$s][$p]['user_pixel'], $username ,$model_name);
			}
		}
		fwrite($fh, "\n" );
	}
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
	//	echo "<pre>update_segments: delete=$delete</pre>\n";
	mysql_query($delete) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $delete . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	//
	foreach ($segment_array as $segment => $starting_pixel)
	{
		$insert = "insert into models_strand_segments( username,object_name,segment,starting_pixel,last_updated)
			values ('$username','$object_name',$segment,$starting_pixel,now())";
		//	echo "<pre>update_segments: query=$insert</pre>\n";
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

function update_number_segments($username,$object_name,$number_segments)
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
	$update = "update models set number_segments=$number_segments,last_updated=now()
		where username='$username' and  object_name='$object_name'";
	//	echo "<pre>update_number_segments: query=$update</pre>\n";
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
	return $number_segments;
}
