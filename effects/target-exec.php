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
>?

// index.php
require("../effects/read_file.php");


$myusername= $_SESSION['SESS_LOGIN'];
$myusername = str_replace("%20"," ",$myusername);

$tokens=explode("?model=",$REQUEST_URI);
$tokens2=explode("?user=",$tokens[1]);
$model_name=$tokens2[0];
$myusername=$tokens2[1];

$myusername = str_replace("%20"," ",$myusername);
//get_models('f','ZZ');
get_models($myusername,$model_name);


echo "</body>\n";
echo "</html>\n";

function get_models($username,$model_name)
{
	

	//Include database connection details
	require_once('config.php');

	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link) {
		die('Failed to connect to server: ' . mysql_error());
	}

	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db) {
		die("Unable to select database");
	}

	//	SELECT * FROM MODELS WHERE username ='f' and object_name='ZZ'
	//	select * from MODELS where username='f' and object_name='FF'A
	/*
function get_models(f,ZZ)
query=select * from MODELS where username='f' and object_name='ZZ'
Array
(
    [username] => f
    [object_name] => ZZ
    [object_desc] => newest test mar 1
    [model_type] => MTREE
    [string_type] => 
    [pixel_count] => 50
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
	$query ="select * from MODELS where username='$username' and object_name='$model_name'";
	echo "query=$query\n";

	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if (!$result) {
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}

	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0) {
		$NO_DATA_FOUND=1;
	}

	$query_rows=array();

	// While a row of data exists, put that row in $row as an associative array
	// Note: If you're expecting just one row, no need to use a loop
	// Note: If you put extract($row); inside the following loop, you'll
	//       then create $userid, $fullname, and $userstatus
	//

	while ($row = mysql_fetch_assoc($result)) {
		extract($row);
		//print_r($row);
	}

	if($topography=="BOT_TOP")
	{
		$maxPixels=$pixel_count;
		$maxStrands=$total_strings;
	}
	else
	{
		$maxPixels = intval($pixel_count/2); // "UP/DOWN NEXT", "UP/DOWN 180"
		$maxStrands=$total_strings*2;
	}
	echo "</pre>";
	$member_id=get_member_id($username);
	$directory ="../targets/" . $member_id;

	if (file_exists($directory)) {
	} else {
		echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}

	echo "<table border=1>";
	echo "<tr><th>Strand#</th><th colspan=$maxPixels>Nutcracker internal format. Pixel#1 is top of tree. so the far left of this table is the top of your tree</th></tr>";
	echo "<tr><th colspan=$maxPixels>The top row is the internal Nutcracker Pixel#</th></tr>";
	echo "<tr><th></th>";
	for($p=1;$p<=$maxPixels;$p++)
	{
		echo "<th><b>n$p</b></th>";
	}
	echo "</tr>";
	$target_array=array();
	for ($s=1;$s<=$maxStrands;$s++)
	{
		if($topography=="BOT_TOP")
		{
			$inc=-1;
			$start_pixel = $maxPixels;
		}
		else
		{
			if($s%2==1)
			{
				$inc=-1;
				$start_pixel = $maxPixels;
			}
			else
			{
				$inc=1;
				$start_pixel = $maxPixels+1;
			}
		}
		$user_pixel=$start_pixel;
		echo "<tr><td>s=$s</td>";

		for($p=1;$p<=$maxPixels;$p++)
		{
			#echo "s,p  $s,$p    User  $s,$user_pixel\n";
			if($topography=="BOT_TOP")
				$string=$s;
			else
				$string=intval((($s-1)/2) + 1);
			echo "<td>$user_pixel</td>";
			$target_array[$s][$p]['username'] =$username;
			$target_array[$s][$p]['model_name'] =$model_name;
			$target_array[$s][$p]['string'] =$string;
			$target_array[$s][$p]['user_pixel'] =$user_pixel;
			$user_pixel+=$inc;
		}
		echo "</tr>\n";
	}
	echo "</table>";

	$full_path=t1($maxStrands,$h1,$pixel_count,$pixel_length,$directory,$object_name,$d1,$target_array);
	echo "<pre>";

	display_file($full_path);

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
	require_once('config.php');

	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link) {
		die('Failed to connect to server: ' . mysql_error());
	}

	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db) {
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
	foreach ($lines as $line_num => $line) {
		echo "$line";
	}
	echo "</pre>";
}	

function t1($strands,$height,$pixels_per_strand,$pixel_length,$path,$object_name,$bottom_diameter,$target_array)
{
	echo "function t1($strands,$height,$pixels_per_strand,$pixel_length)";
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

	#	10' high, 2.5' radius
	$bottom_radius=$bottom_diameter/2;  

	##	passed in now thru runtime arg, 	strands=16;

	$dat_file = $path . "/" . $object_name . ".dat";
	$fh = fopen($dat_file, 'w') or die("can't open file");
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
	echo "dat_file = $dat_file\n";

	$pixels=$pixels_per_strand;
	$side = sqrt($height*$height + $bottom_radius*$bottom_radius);
	if($pixels_per_strand>0)
	{
		$pixel_spacing = $pixel_length/$pixels_per_strand;
		$pixel_should_be = $side/$pixels_per_strand;
	}
	else
	{
		echo "<pre>ERROR! Something looks wrong. we have zero for pixels per strand</pre>\n";
		$pixel_spacing=1;
		$pixel_should_be = 1;
	}

	$ang=atan2($bottom_radius,$height);
	$rad = $ang;

	$hyp=0;
	$h_dx = cos($rad)*$pixel_spacing;
	$height_sb = $h_dx*$pixels_per_strand;
	$degree_per_segment = 360/$strands;
	echo "<pre><h2>Next we will dislay your target file in internal Nutcracker form. This file assumes that\n";
	echo "pixel #1 is always the top of the tree. When we finally output to LSP,LOR or Vixen the\n";
	echo "Internal Nutcracker Pixel will be mapped to your real pixel#.\n";
	echo "	the above table will be used to do that decoding</pre>";
	//	$target_array[$s][$p]['string'] =$string;
	//$target_array[$s][$p]['user_pixel'] =$user_pixel;


	for ($s=1;$s<=$strands;$s++)
	{
		$hyp=0;
		for ($p=1;$p<=$pixels;$p++)
		{
			$hyp=$p*$pixel_spacing;
			$h = $height - cos($rad)*$hyp;
			//$h = $height - ($p * $h_dx);
			$r = sin($rad)*$hyp;
			$degree_rotation=($s-1)*$degree_per_segment;
			$x2=getx($r,$degree_rotation);
			$y2=gety($r,$degree_rotation);

			//$target_array[$s][$p]['username'] =$username;
			//$target_array[$s][$p]['model_name'] =$model_name;
			//$target_array[$s][$p]['string'] =$string;
			//$target_array[$s][$p]['user_pixel'] =$user_pixel;
			if($hyp<=$side) 
			{
				//	lave the 6yh token 0 as a place holder for the RGB value.
				fwrite($fh,sprintf ("%s %3d %3d %7.3f %7.3f %7.3f 0 %5d %5d %s %s\n", $object_name,$s,$p,$x2,$y2,$h,$target_array[$s][$p]['string'] ,$target_array[$s][$p]['user_pixel'], $target_array[$s][$p]['username'] ,$target_array[$s][$p]['model_name'] ));
			}
		}
		fwrite($fh, "\n" );
	}
	fclose($fh);
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
	echo "$drop_query\n";
	mysql_query($query,$db) or die ("Error on $query");
	echo "$query\n";
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
			$insert="INSERT into MEMBERS (username,role,joined,posts) values ('" . $tok[0] . "','". $tok[1]  . "','FEB-12-2012',0)";
			//echo "<td>$insert</td>";
			mysql_query($insert,$db) or die ("Failed executing $insert");
		}

		//	echo "</tr>";
	}

	echo "</table>";
	fclose($fh);

	$query="SELECT * FROM MEMBERS";
	$result = mysql_query($query,$db) or die("Failed Query");


	echo "<b><center>Database Output</center></b><br><br>";

	$i=0;
	echo "<table border=1>";
	$row=0;
	while ($myrow = mysql_fetch_row($result)) {
		$row++;
		printf("<tr><td>$row</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>n", 
			$myrow[0], $myrow[1], $myrow[2], $myrow[3], $myrow[4]);
	}

	echo "</table>";
}
