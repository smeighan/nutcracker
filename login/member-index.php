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
/*echo "<pre>";
print_r($_GET);
echo "</pre>";*/
// index.php
extract($_GET);
$gif_model="";
$username= $_SESSION['SESS_LOGIN'];


$number_segments=0;
if(!isset($model_name)) $model_name="";
$WARN=0;
if($WARN==1)
{
	echo "<h1><font color=red>Web page is undoing construction. When this banner goes away, you can use page again</font></h1>";
	//	echo "<h4>" . md5("pepe1pepe1") . "</h4>";
}
$row=show_my_models($username,$model_name);
if(isset($row['number_segments']))
{
	$number_segments=$row['number_segments'];
	$gif_model=$row['gif_model'];
}
else
{
	$number_segments=0;
	$gif_model="single";
	
}
/*echo "<pre>";
print_r($row);
echo "<pre>\n";*/
if(empty($row)) // on our first time, we have no data for this user. initializze some entries so page doesnt throw warnings
{
	$row['object_name']='';
	$row['object_desc']='';
	$row['folds']=1;
	$row['start_bottom']='Y';
	$row['total_strings']=0;
	$row['pixel_count']=0;
	$row['number_rows']=0;
	$row['member_index']='single';
}
$ip=@$REMOTE_ADDR; 
//echo "<b>IP Address= $ip</b>";
/*
Array $row
(
[username] => f
[object_name] => MT
[object_desc] => mt
[model_type] => MTREE
[string_type] => SS
[pixel_count] => 33
[pixel_first] => 1
[pixel_last] => 33
[pixel_length] => 1.00
[total_strings] => 33
[direction] => CW
[orientation] => 1
[topography] => BOT_TOP
[h1] => 111.00
[h2] => 0.00
[d1] => 11.00
[d2] => 1.00
[d3] => 0.00
[d4] => 0.00
[unit_of_measure] => in
)
	*/
echo "<h2>Nutcracker: RGB Effects Builder for user $username<h2>"; 
//
////	set defaults if this is the first time we are coming in
if(empty($row['model_type'])) $row['model_type']="MTREE";
if(empty($row['string_type'])) $row['string_type']="SINGLE_STRAND";
if(empty($row['orientation'])) $row['orientation']="0";
if(empty($row['unit_of_measure'])) $row['unit_of_measure']="in";
if(empty($row['topography'])) $row['topography']="BOT_TOP";
if(empty($row['direction'])) $row['direction']="CW";
$H1=$D1=0;
?>
<h4>
<form action="process.php" method="get">
<table border="1">
<input type="submit" name="submit" value="Submit Form to create your target model" />
<input type="hidden" name="username" value="<?php echo "$username"; ?>"/>
<input type="hidden" name="number_segments" value="<?php echo "$number_segments"; ?>"/>
<input type="hidden" name="gif_model" value="<?php echo "$gif_model"; ?>"/>
<tr>
<td><b><font color="blue">**Your name for this object (up to 8 characters):</font></b>
<input type="text" STYLE="background-color: #ABE8EC;" size="8" maxlength="" 
value="<?php echo $row['object_name'] ?>" name="OBJECT_NAME"><br/>
You may be building many objects. Big meg-tree, a grid, a small mega-tree.<br/>
This name is going to allow you to have a set of objects
under your account name. <br/>
Examples: MT1, MTREE1, TREE, GRID1, MT12, MT32
</td>
</tr>
<tr>
<td><b>Description of your object (up to 80 characters):</b>
<input type="text" STYLE="background-color: #ABE8EC;" size="80" maxlength="" 
value="<?php echo $row['object_desc'] ?>" name="OBJECT_DESC"><br/>
Example: 16 strand megatree made of Smart Strings. Used in front yard.
</td>
</tr>
<tr>
<td><font color="blue"><b>**Model Type:</b></font> <br/>
<blockquote>
<input type="radio" value="MTREE" 
<?php if( $row['model_type']=="MTREE") echo "checked=\"checked\""; ?>
name="MODEL_TYPE"> Mega-Tree (If you want a half mega tree, choose this and then later in the effects screens set window_degrees to 180)<br />
<input type="radio" value="MATRIX"
<?php if( $row['model_type']=="MATRIX") echo "checked=\"checked\""; ?>
name="MODEL_TYPE"> 	Matrix(Grid) <br />
<input type="radio" value="HORIZ_MATRIX"
<?php if( $row['model_type']=="HORIZ_MATRIX") echo "checked=\"checked\""; ?>
name="MODEL_TYPE"> 	Horizontal Matrix(Grid) <font color=red>(UNDER DEVELOPMENT, DOES NOT WORK YET)</font><br />
<input type="radio" value="SINGLE_STRAND" 
<?php if( $row['model_type']=="SINGLE_STRAND") echo "checked=\"checked\""; ?>
name="MODEL_TYPE">	Single Strand <font color=red>(UNDER DEVELOPMENT, DOES NOT WORK YET)</font><br />
<input type="radio" value="RAY" 
<?php if( $row['model_type']=="RAY") echo "checked=\"checked\""; ?>
name="MODEL_TYPE">	Ray <font color=blue>(UNDER DEVELOPMENT, DOES NOT CREATE GIF. USE HALF MEGATREE FOR NOW)</font><br />
</blockquote>
<table border="1">
<tr>
<th colspan="1">3D models</th>
<th colspan="2">2D models</th>
</tr>
<tr>
<td><img src="../images/mega_tree.png" alt="bottom_to_top"  ></td>
<td><img src="../images/vert_matrix.png" alt="up_down_next"  ></td>
<td><img src="../images/horiz_matrix.png" alt="up_down_next"  ></td>
<td><img src="../images/single_strand.png" alt="up_down_next"  ></td>
<td><img src="../images/ray.png" alt="up_down_180"  ></td>
</tr>
</table>
</td> 
</tr >
<tr>
<td><b><font color="blue">**How many Strings will be in your tree:</b>
</font>	<input type="text" STYLE="background-color: #ABE8EC;" size="5" maxlength="6" 
value="<?php echo $row['total_strings'] ?>" name="TOTAL_STRINGS"></td></tr>
<tr>
<td><table border=1>
<tr>
<td rowspan=3>Do not fill in these 3 questions if you are doing Single Strand Targets</td>
<td><b><font color="blue">**Total number of Pixels on this string </b>
</font>
<input type="text" STYLE="background-color: #ABE8EC;" size="5" maxlength="5" 
value="<?php echo $row['pixel_count'] ?>" name="PIXEL_COUNT"><br />
This is the number of pixels .Sometimes the RGB strings might say 150 LED's
50 Pixels. Enter 50 for this prompt.
</td>
</tr>
<tr>
<td><b><font color="blue">**How many strands will you make out of each of your strings?:</b>
</font>	<input type="text" STYLE="background-color: #ABE8EC;" size="5" maxlength="6" 
value="<?php echo $row['folds'] ?>" name="FOLDS"></td></tr>
<tr>
<td><b><font color="blue">**Do you want your strings to always start at the bottom
of the RGB device (Y or N):</b>
</font>	<input type="text" STYLE="background-color: #ABE8EC;" size="1" maxlength="1" 
value="<?php echo $row['start_bottom'] ?>" name="START_BOTTOM"></td></tr>
</table>
</td></tr>
<!--<tr><td><b><font color="blue">**Unit of Measure:</b><br /> 
</font><blockquote>
<input type="radio" value="in"  
<?php if( $row['unit_of_measure']=="in") echo "checked=\"checked\""; ?>  
name="UNIT_OF_MEASURE">inches
<input type="radio" value="cm"   
<?php if( $row['unit_of_measure']=="cm") echo "checked=\"checked\""; ?> 
name="UNIT_OF_MEASURE">cm
</blockquote>
<br />
</td></tr>-->
</table>
<input type="submit" name="submit" value="Submit Form to create your target model" />
</form>
<?php /*
<form action="<?php echo BASE_URL;?>export.php" method="get">
<input type="hidden" name="username" value="<?php echo $username;?>" />
<input type="submit" name="cmdExport" value="Export files for backup from server" />
</form>
<?php
/*if ($_SERVER['SERVER_NAME'] != 'meighan.net')
{
	// if ($_SERVER['SERVER_NAME'] != 'localhost')
	{
		// this is a test
		?>
		<form action="<?php echo BASE_URL;?>import.php" method="get">
		<input type="hidden" name="username" value="<?php echo $username;?>" />
		<input type="submit" name="cmdImport" value="Import files from a previous backup" />
		</form>
		<?php 
	}
	}*/
show_user_base($username); 
$menu="member-index"; require "../conf/menu.php"; 
echo "</body>";
echo "</html>\n";

function show_my_models($username,$model_name)
{
	//Include database connection details
	require_once('../conf/config.php');
	require("../effects/read_file.php");
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
	$MODEL_ONLY=0;
	if(strlen($model_name)>=1) $MODEL_ONLY=1;
	$query ="select * from models where username='$username'";
	
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
	//$username','$OBJECT_NAME', '$OBJECT_DESC', '$MODEL_TYPE', '$STRING_TYPE', 
	//$PIXEL_COUNT, $PIXEL_FIRST,  $PIXEL_LAST, $PIXEL_LENGTH, 
	//$TOTAL_STRINGS, '$DIRECTION', $ORIENTATION, '$TOPOGRAPHY', $H1, $H2, $D1, $D2, $D3, $D4
	//
	echo "<table border=\"1\">\n";
	echo "<tr>\n";
	echo"<th>username</th>";
	echo "<th>object_name</th>";
	echo "<th>object_desc</th>";
	echo "<th>model_type</th>";
	//	echo "<th>Strings <br/>(aka Bundle or <br/>device)</th>";
	echo "<th>total<br/>Strings<br/><font color=\"green\">1-100</font></th>";
	//	echo "<th>direction</th>";
	//	echo "<th>Where does<br/>String#1 start <br/>(in degreees)</th>";
	echo "<th>pixel count<br/>per string<br/><font color=\"green\">10-10000</font></th>";
	echo "<th>Number of<br/>folds</th>";
	echo "<th>Total<br/>pixels</th>";
	
	echo "</tr>\n";
	if(!$NO_DATA_FOUND)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			extract($row);
			//
			if($MODEL_ONLY and $object_name == $model_name) $query_rows = $row;
			echo "<tr>\n";
			echo "<td>$username</td>";
			$filename = "../targets/" . get_member_id($username) . "/" . $object_name . ".dat";
			if (file_exists($filename))
			{
				$fileok="";
			}
			else {
				$fileok="<font color=red>Target model needs to be resaved for $filename</font>";
			}
			echo "<td><a href=\"member-index.php?model_name=$object_name\">$object_name $fileok</a></td>";
			echo "<td>$object_desc</td>";
			$model="??";
			if($model_type=="MTREE") $model="Mega-Tree";
			if($model_type=="MTREE_HALF") $model="Mega-Tree Half";
			if($model_type=="MATRIX") $model="Matrix(Grid)";
			if($model_type=="SINGLE_STRAND") $model="Single Strand";
			if($model_type=="RAY") $model="Ray";
			echo "<td>$model</td>";
			//
			$warn="green";
			if($total_strings < 1 or $total_strings>100) $warn="red";
			echo "<td><font color=$warn>($total_strings)</font></td>";
			$strands=$total_strings;
			$warn="green";
			if($pixel_count < 10 or $pixel_count>10000) $warn="red";
			echo "<td><font color=$warn>($pixel_count)</font></td>";
			//$pixel_spacing = $pixel_length/$pixel_count;
			$pixel_length = $pixel_spacing*$pixel_count; // PIXEL FIX
			$buff_pixel_spacing = sprintf("%5.1f",	$pixel_spacing);
			$warn="green";
			if($unit_of_measure=="in" and ($pixel_spacing < 2 or $pixel_spacing>6)) $warn="red";
			if($unit_of_measure=="cm" and ($pixel_spacing < 5 or $pixel_spacing>30)) $warn="red";
			echo "<td>$folds </td>";
			echo "<td>$total_pixels </td>";
			//
			//
			//
		//	echo "<td>$unit_of_measure </td>";
			if($folds==1)
				$hyptonuse=$pixel_length ;
			else
			$hyptonuse=$pixel_length/2 ;
			$guess_h1 = $hyptonuse * 0.97;
			$buff_guess_h1 = sprintf("%5.1f",$guess_h1);
			$warn="green";
			//echo "<td>$buff_guess_h1</font></td>";
			$h1=$buff_guess_h1;
			$H1=$buff_guess_h1;
			$guess_d1 = 2* $hyptonuse * 0.2419;
			$buff_guess_d1 = sprintf("%5.1f",$guess_d1);
			$warn="green";
		//	echo "<td>$buff_guess_d1</font></td>";
			$d1=$buff_guess_d1;
			$D1=$buff_guess_d1;
			echo "</tr>\n";
		}
	}
	else
	echo "<tr><td>$username</td><td>No models found under your user name</td></tr>";
	echo "</table>\n";
	echo "<br/><br/>\n";
	return ($query_rows);
}

function show_user_base($username)
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
	$query ="select * from members where member_id>4";
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
	// While a row of data exists, put that row in $row as an associative array
	// Note: If you're expecting just one row, no need to use a loop
	// Note: If you put extract($row); inside the following loop, you'll
	//       then create $userid, $fullname, and $userstatus
	//
	//$username','$OBJECT_NAME', '$OBJECT_DESC', '$MODEL_TYPE', '$STRING_TYPE', 
	//$PIXEL_COUNT, $PIXEL_FIRST,  $PIXEL_LAST, $PIXEL_LENGTH, 
	//$TOTAL_STRINGS, '$DIRECTION', $ORIENTATION, '$TOPOGRAPHY', $H1, $H2, $D1, $D2, $D3, $D4
	//
	$cnt_LSP1_8=$cnt_LSP2_0=$cnt_LSP3_0=$cnt_LOR_S2=$cnt_LOR_S3=0;
	$cnt_VIXEN211=$cnt_VIXEN25=$cnt_VIXEN3=$cnt_HLS=$cnt_OTHER=0;
	$total_users=0;
	echo "<br/><br/><h2>Sequencers that the users want supported</h2>";
	echo "<table border=\"1\">";
	echo "<tr>\n";
	echo "<th>LSP 1.8 </th>";
	echo "<th>LSP 2.0 </th>";
	echo "<th>LSP 3.0 </th>";
	echo "<th>LOR S2 </th>";
	echo "<th>LOR S3 </th>";
	echo "<th>Vixen 2.1.1 </th>";
	echo "<th>Vixen 2.5 </th>";
	echo "<th>Vixen 3.0 </th>";
	echo "<th>HLS </th>";
	echo "<th>Other </th>";
	echo "</tr>\n";
	if(!$NO_DATA_FOUND)
	{
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while ($row = mysql_fetch_assoc($result))
		{
			extract($row);
			$total_users++;
			if($LSP1_8=="Y") $cnt_LSP1_8++;
			if($LSP2_0=="Y") $cnt_LSP2_0++;
			if($LSP3_0=="Y") $cnt_LSP3_0++;
			if($LOR_S2=="Y") $cnt_LOR_S2++;
			if($LOR_S3=="Y") $cnt_LOR_S3++;
			if($VIXEN211=="Y") $cnt_VIXEN211++;
			if($VIXEN25=="Y") $cnt_VIXEN25++;
			if($VIXEN3=="Y") $cnt_VIXEN3++;
			if($HLS=="Y") $cnt_HLS++;
			if($OTHER=="Y") $cnt_OTHER++;
		}
		echo "<tr>\n";
		echo "<td>$cnt_LSP1_8 </td>";
		echo "<td>$cnt_LSP2_0 </td>";
		echo "<td>$cnt_LSP3_0 </td>";
		echo "<td>$cnt_LOR_S2 </td>";
		echo "<td>$cnt_LOR_S3 </td>";
		echo "<td>$cnt_VIXEN211 </td>";
		echo "<td>$cnt_VIXEN25 </td>";
		echo "<td>$cnt_VIXEN3 </td>";
		echo "<td>$cnt_HLS </td>";
		echo "<td>$cnt_OTHER </td>";
		echo "</tr>\n";
	}
	else
	echo "<tr><td>Retrieve  of user data produced no rows</td></tr>";
	echo "</table>\n";
	echo "<br/><br/>\n";
	echo "<h2>Total registered users of Nutcracker = $total_users. Note: Users can vote for more than one sequencer.</h2>\n";
}
//
////	Create a empty megatree target. Top of the tree is pixel #1, strands go around tree.
//
//

function t1($strands,$height,$pixels_per_strand,$pixel_length)
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
	$bottom_radius=3.0*12;  
	##	passed in now thru runtime arg, 	strands=16;
	$dat_file = "t1_" . $strands . "_" . $pixels_per_strand . ".dat";
	$fh = fopen($dat_file, 'w') or die("can't open file");
	fwrite($fh,"#    $dat_file\n");
	$pixels=$pixels_per_strand;
	$pixel_spacing = 3.5;
	$side = sqrt($height*$height + $bottom_radius*$bottom_radius);
	//$pixel_spacing = $pixels_per_strand/$side;
	$pixel_spacing = $pixel_length/$pixels_per_strand;
	$pixel_should_be = $side/$pixels_per_strand;
	$ang=atan2($bottom_radius,$height);
	$rad = $ang;
	$hyp=0;
	$h_dx = cos($rad)*$pixel_spacing;
	$height_sb = $h_dx*$pixels_per_strand;
	$degree_per_segment = 360/$strands;
	echo "<pre>pixel_spacing = $pixel_spacing, pixel_should_be=$pixel_should_be, side=$side, height=$height, $bottom_radius</pre>\n";
	echo "<pre>rad=$rad, h_dx=$h_dx,height_sb=$height_sb</pre>\n";
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
			if($hyp<=$side) fwrite($fh,sprintf ("t1 %3d %3d %7.3f %7.3f %7.3f\n", $s,$p,$x2,$y2,$h ));
			echo "<pre>s,p=$s,$p: hyp=$hyp, h=$h, r=$r, height=$height, height_sb=$height_sb\n</pre>";
		}
		fwrite($fh, "\n" );
	}
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

function insert_users($db,$file)
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
		printf("<tr><td>$row</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>n", $myrow[0], $myrow[1], $myrow[2], $myrow[3], $myrow[4]);
	}
	echo "</table>";
}
