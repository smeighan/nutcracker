<?php
//*************************************************************************************************
//
//	file: member-index.php
//	Summary: Shows current targets to user and allows new targets to be created
//
//
//
//
//
//*************************************************************************************************
require_once('../conf/auth.php');
require_once('../conf/barmenu.php');
define('BASE_URL', substr($_SERVER['PHP_SELF'],0,-22));  //this returns the path (minus the login/) string
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
<link rel="shortcut icon" href="targetmodel.ico" type="image/x-icon"> 
<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../css/barmenu.css">
<script type="text/javascript" src="../js/barmenu.js"></script>
</head>
<body>
<?php show_barmenu();?>
Welcome <?php echo $_SESSION['SESS_FIRST_NAME'];?></h1>
<?php
/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";*/
// index.php
$username= $_SESSION['SESS_LOGIN'];
$username=$username;
$tokens=array("","");
$model_name="";
$tokens=explode("model=",$_SERVER['QUERY_STRING']);
$c=count($tokens);
// echo "<pre>c=$c, " . $_SERVER['QUERY_STRING'] . "</pre>\n";
if($c>1)
	$model_name=$tokens[1];
else
$model_name="";
$WARN=0;
if($WARN==1)
{
	echo "<h1><font color=red>Web page is undoing construction. When this banner goes away, you can use page again</font></h1>";
	//	echo "<h4>" . md5("pepe1pepe1") . "</h4>";
}
// echo "<pre>show_my_models($username,$model_name);</pre>\n";
$row=show_my_models($username,$model_name);
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
<form action="process.php" method="POST">
<table border="1">
<input type="submit" name="submit" value="Submit Form to create your target model" />
<input type="hidden" name="H1" value="<?php echo "$H1"; ?>">
<input type="hidden" name="D1" value="<?php echo "$D1"; ?>">
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
<form action="<?php echo BASE_URL;?>export.php" method="POST">
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
		<form action="<?php echo BASE_URL;?>import.php" method="POST">
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
	// check_dat checks to see if all the model .dat files are created. If they are not this function batches them to create automatically
	check_dat($username);
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
//	echo "query=$query";
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
	echo "<th>total<br/>Strings<br/><font color=green>1-100</font></th>";
	//	echo "<th>direction</th>";
	//	echo "<th>Where does<br/>String#1 start <br/>(in degreees)</th>";
	echo "<th>pixel count<br/>per string<br/><font color=green>10-10000</font></th>";
	echo "<th>Number of<br/>folds</th>";
	echo "<th>Unit of<br/>measure</th>";
	echo "<th>H1 . height</th>";
	echo "<th>D1 . diameter</th>";
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
			$filename = "../targets/" . get_mem_id($username) . "/" . $object_name . ".dat";
			if (file_exists($filename))
			{
				$fileok="";
			}
			else {
				$fileok="<font color=red>Re-creating model for $filename</font>";
				//$fileok="<font color=red>Target model needs to be resaved for $filename</font>";
			}
			echo "<td><a href=\"member-index.php?model=$object_name\">$object_name $fileok</a></td>";
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
			echo "<td>$unit_of_measure </td>";
			if($folds==1)
				$hyptonuse=$pixel_length ;
			else
			$hyptonuse=$pixel_length/2 ;
			$guess_h1 = $hyptonuse * 0.97;
			$buff_guess_h1 = sprintf("%5.1f",$guess_h1);
			$warn="green";
			echo "<td>$buff_guess_h1</font></td>";
			$h1=$buff_guess_h1;
			$H1=$buff_guess_h1;
			$guess_d1 = 2* $hyptonuse * 0.2419;
			$buff_guess_d1 = sprintf("%5.1f",$guess_d1);
			$warn="green";
			echo "<td>$buff_guess_d1</font></td>";
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
function check_dat($username) {
	require_once('../conf/config.php');
	$SQL_query = "SELECT * from models where username = '$username'";
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	$result=mysql_query ($SQL_query, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
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
	// get a row of data from the sql query
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
	
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
		$directory ="../targets";
		if (file_exists($directory))
		{
			} else {
			echo "The directory $directory does not exist, creating it";
			mkdir($directory, 0777);
		}
		$member_id=get_mem_id($username);
		$directory ="../targets/" . $member_id;
		//echo "Checking $object_name ...";
		if (file_exists($directory))
		{
			} else {
			echo "The directory $directory does not exist, creating it";
			mkdir($directory, 0777);
		}
		$filename = "../targets/" . get_mem_id($username) . "/" . $object_name . ".dat";
		if (file_exists($filename)) {
			//echo "found <br />";
		} else
		{
			//echo "not found - creating new dat file<br />";
			$target_array=array();
			$n=0;
			$p=0;
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
					$target_array[$s][$p]['string'] =$string;
					$target_array[$s][$p]['user_pixel'] =$user_pixel;
				}
			}
			$string=1;
			$n2=0;
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
				}
			}
			for($s=1;$s<=$maxStrands;$s++)
			{
				$s_mod=$s%$folds;
			}
			for($string=1;$string<=$total_strings;$string++)
			{
				$string_mod=$string%2;
			}
			$target_array2= array($target_array,$username,$object_name) ;
			if($model_type=="MTREE")
			{
				$full_path=mega_write($maxStrands,$maxPixels,$pixel_count,$directory,$object_name,$target_array2);
			}
			else if($model_type=="MATRIX" or $model_type=="HORIZ_MATRIX" or $model_type=="RAY")
			{
				$full_path=matrix_write($folds,$maxStrands,$maxPixels,$pixel_count,$directory,$object_name,$model_type,$target_array2);
			}
			else if($model_type=="SINGLE_STRAND")
			{
				$full_path=single_write($folds,$maxStrands,$maxPixels,$pixel_count,$directory,$object_name,$model_type,$target_array2);
			}
			else
			{
				echo "<pre>ERROR! Model type $model_type is unknown</pre>\n";
			}
		}
	}
	return;
}	

function mega_write($maxStrands,$maxPixels,$pixel_count,$directory,$object_name,$target_array2)
{
	#	Build a mega-Tree with arbitray strands
	$PI = pi();
	$DTOR = $PI/180;
	$RTOD = 180/$PI;
	$ang=atan2(5,10); // assume a 10' tree with a 5' diameter to get the angles to model with
	$rad = $ang;
	$pixel_spacing=3.0;
	$hypt = $pixel_count*$pixel_spacing;	// assume 3" spacing between nodes.
	$hyp=0;
	$h_dx = cos($rad)*$pixel_spacing;
	$height = $h_dx*$maxPixels;
	$bottom_diameter=$height/2;
	$degree_per_segment = 360/$maxStrands;
	#	10' high, 2.5' radius
	$bottom_radius=$bottom_diameter/2;  
	$target_array=$target_array2[0];
	$username=$target_array2[1];
	$model_name=$target_array2[2];
	$member_id=get_mem_id($username);
	$path=$_SERVER['DOCUMENT_ROOT'];
	$path.="nutcracker/targets/" . $member_id ;
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
	fwrite($fh,"#    Col 7: RESERVED\n");
	fwrite($fh,"#    Col 8: User string\n");
	fwrite($fh,"#    Col 9: User pixel\n");
	fwrite($fh,"#    Col 10: **User login who created target\n");
	fwrite($fh,"#    Col 11: **Your TARGET_MODEL_NAME\n");
	fwrite($fh,"#            ** Optional fields\n");
	$pixels=$maxPixels;
	$side = sqrt($height*$height + $bottom_radius*$bottom_radius);
	if($maxPixels>0)
	{
		//	$pixel_spacing = $pixel_length/$maxPixels;
		$pixel_length = $pixel_spacing * $maxPixels;
		$pixel_should_be = $side/$maxPixels;
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
	$height_sb = $h_dx*$maxPixels;
	$degree_per_segment = 360/$maxStrands;
	for ($s=1;$s<=$maxStrands;$s++)
	{
		$hyp=0;
		for ($p=1;$p<=$maxPixels;$p++)
		{
			$hyp=$p*$pixel_spacing;
			$h = $height - cos($rad)*$hyp;
			//$h = $height - ($p * $h_dx);
			$r = sin($rad)*$hyp;
			$degree_rotation=($s-1)*$degree_per_segment;
			$x2=getx($r,$degree_rotation);
			$y2=gety($r,$degree_rotation);
			//if($hyp<=$side)
			{
				//	lave the 6yh token 0 as a place holder for the RGB value.
				fwrite($fh,sprintf ("%s %3d %3d %7.3f %7.3f %7.3f 0 %5d %5d %s %s\n", $object_name,$s,$p,$x2,$y2,$h,$target_array[$s][$p]['string'] ,$target_array[$s][$p]['user_pixel'], $username ,$model_name));
			}
		}
		fwrite($fh, "\n" );
	}
	fclose($fh);
	return $dat_file;
}

function matrix_write($folds,$maxStrands,$maxPixels,$pixel_count,$directory,$object_name,$model_type,$target_array2)
{

	#	Build a matrix with arbitray strands
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
	$member_id=get_mem_id($username);
	$path=$_SERVER['DOCUMENT_ROOT'];
	$path.="nutcracker/targets/" . $member_id ;
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
	$s=1;
	$y2=0;  // y2 is unused
	for ($p=1;$p<=$maxPixels;$p++)
	{
		$mod=($p%$maxStrands)+1;
		$mod2 = $maxPixels-$mod+1;
		$mod2 = $maxPixels-$p;
		
			$h= ($mod2*$x_spacing);
	
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
			} else {
				$s2=0;
			}
			$x2=$s0*3 - $s2*3;
			$h=$mod2*3;
			fwrite($fh,sprintf ("%s %3d %3d %7.3f %7.3f %7.3f 0 %5d %5d %s %s\n", $object_name,$s0,$p0,$x2,$y2,$h,$target_array[$s][$p]['string'] ,$target_array[$s][$p]['user_pixel'], $username ,$model_name));
			//printf ("%s %3d %3d %7.3f %7.3f %7.3f 0 %5d %5d %s %s\n", $object_name,$s0,$p0,$x2,$y2,$h,$target_array[$s][$p]['string'] ,$target_array[$s][$p]['user_pixel'], $username ,$model_name);
		}
	}
	fwrite($fh, "\n" );
	fclose($fh);
	//echo "</pre>\n";
	return $dat_file;
}

function single_write($folds,$maxStrands,$maxPixels,$pixel_count,$directory,$object_name,$model_type,$target_array2)
{
	#	Build a single strand with arbitray strands
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
	$member_id=get_mem_id($username);
	$path=$_SERVER['DOCUMENT_ROOT'];
	$path.="nutcracker/targets/" . $member_id ;
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
				//printf ("%s %3d %3d %7.3f %7.3f %7.3f 0 %5d %5d %s %s\n", $object_name,$s0,$p0,$x2,$y2,$h,$target_array[$s][$p]['string'] ,$target_array[$s][$p]['user_pixel'], $username ,$model_name);
			}
		}
		fwrite($fh, "\n" );
	}
	fclose($fh);
	return $dat_file;
}
function get_mem_id($username)
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
	$query = "select member_id from members where username='$username'";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	$member_id=0;
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
	}
	mysql_close();
	if($member_id==0)
		echo "<pre>ERROR: We did not find username [$username]</pre>\n";
	return ($member_id);
}