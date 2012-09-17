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


$username= $_SESSION['SESS_LOGIN'];
$sess_member_id= $_SESSION['SESS_MEMBER_ID'];

if(empty($username))
{
	$username = get_username_by_id($sess_member_id);
	$_SESSION['SESS_LOGIN']=$username;
}

if(empty($REQUEST_URI))
	$tokens=array("xx","FLY1");
else
	$tokens=explode("effect=",$REQUEST_URI);
$effect_name=$tokens[1];

$username = $username;

$WARN=0;
if($WARN==1)
{
	echo "<h1><font color=red>Web page is undoing construction. When this banner goes away, you can use page again</font></h1>";
}

$effect_classes=get_effect_classes();
$user_targets=get_user_targets($username);
/*echo "<pre>";
print_r($user_targets);
echo "</pre>";*/

/*
 [0] => Array   user_targets
	(
	    [username] => f
	    [object_name] => AA
	    [object_desc] => aa
	    [model_type] => MTREE
	    [string_type] => 
	    [pixel_count] => 80
	    [pixel_first] => 1
	    [pixel_last] => 80
	    [pixel_length] => 240.00
	    [unit_of_measure] => in
	    [total_strings] => 10
	    [direction] => 
	    [orientation] => 0
	    [topography] => UP_DOWN_NEXT
	    [h1] => 116.00
	    [h2] => 0.00
	    [d1] => 58.00
	    [d2] => 11.00
	    [d3] => 0.00
	    [d4] => 0.00
	)
 */
echo "<h2>Nutcracker: RGB Effects Builder for user $username<br/>
	On this page you select one of your target models and an effects class</h2>"; 

//
////	set defaults if this is the first time we are coming in
if(empty($row['model_type'])) $row['model_type']="MTREE";


if(isset($_GET['submit']))
{
	$name = $_GET['name'];
	echo "User Has submitted the form and entered this name : <b> $name </b>";
	echo "<br>You can use the following form again to enter a new name.";
}


?>
<form action="effect-exec.php" method="get">
<input type="hidden" name="username" value="<?php echo "$username"; ?>">
	<table border="1">
<tr>
<th>Choose one of your target models</th>
<th>Choose an Effect Class.</th>
</tr>
<?php

/*
 [0] => Array
	(
	    [effect_class] => spirals
	    [description] => Create spirals around your RGB device
	    [created] => 2012-03-02 08:21:11
	    [last_upd] => 2012-03-02 08:21:11
	    [php_program] => spirals.php
	)

 */ 
/*
[14] => Array
	(
	    [username] => f
	    [object_name] => ZZ_ZZ
	    [object_desc] => newest test mar 1
	    [model_type] => MTREE
	    [string_type] => 
	    [pixel_count] => 80
	    [pixel_first] => 1
	    [pixel_last] => 80
	    [pixel_length] => 240.00
	    [unit_of_measure] => in
	    [total_strings] => 16
	    [direction] => 
	    [orientation] => 0
	    [topography] => UP_DOWN_NEXT
	    [h1] => 232.00
	    [h2] => 0.00
	    [d1] => 116.00
	    [d2] => 0.00
	    [d3] => 0.00
	    [d4] => 0.00
	)

 */

echo "<tr><td>";

echo "<FONT FACE=\"monospace\" SIZE=\"2\">\n";
echo "<select name=\"user_targets\" STYLE=\"font-family : monospace; font-size : 12pt\" >";
//$active_classes=array('spirals');

$cnt=count($user_targets);
for($i=0;$i<$cnt;$i++)
{

if($user_targets[$i]['model_type']=='SINGLE_STRAND')
	$buff=sprintf ("%16s (1x%d) %s",$user_targets[$i]['object_name'],	
		$user_targets[$i]['total_pixels'],	$user_targets[$i]['object_desc']);
		else
		$buff=sprintf ("%16s (%dx%d) %s",$user_targets[$i]['object_name'],	$user_targets[$i]['total_strings'],
		$user_targets[$i]['pixel_count'],	$user_targets[$i]['object_desc']);

	$object_name=$user_targets[$i]['object_name'];

		$filename = "../targets/" . get_member_id($username) . "/" . $object_name . ".dat";
			if (file_exists($filename)) {
				$fileok=""; $option="";
			} else {
				$option="class=\"red\"";
				$fileok="(NOTE! Target model needs to be resaved for $filename)";	
			}

	printf ("<option value=\"%s\" %s>%s %s</option>\n",$user_targets[$i]['object_name'],$option,$buff,$fileok);
}
echo "</select>\n";
echo "</font>";
echo "</td>";
echo "<td>";
echo "<select name=\"effect_class\">";

$cnt=count($effect_classes);
for($i=0;$i<$cnt;$i++)
{
	$effect_class_name=$effect_classes[$i]['effect_class'];
	echo "i=$i effecy_class = $effect_class_name";

	$hl1="<b>"; $hl2="</b>";
	$style="style='background-color: #81B1EF; font-size: 5pt'";
	printf ("<option value=\"%s\" >%s</option>\n",$effect_class_name,$effect_class_name);
}
echo "</select>";
echo "</td></tr>";
?>
			</table>
			<input type="submit" name="submit" value="Submit Form to customize effect" />
		</form>
<br/>
<br/>
<h3>If you want to see a gallery of all the effects other users have created, click below</h3>
<br/>
<a href="gallery.php">Click here to see the gallery of effects other users have created</a>
	</body>
</html>

<?php

function get_effect_classes()
{

	//Include database connection details
	require_once('../conf/config.php');
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
	$query ="select * from effects_hdr where active='Y' order by effect_class";
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

	$effects_classes=array();
	if(!$NO_DATA_FOUND)
	{
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while ($row = mysql_fetch_assoc($result)) {
			extract($row);
			$effects_classes[]=$row;
		}

	}
	return $effects_classes;

}

function get_user_targets($username)
{

	//Include database connection details
	require_once('../conf/config.php');
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
	$query ="select * from models where username='$username'";
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

	$user_targets=array();
	if(!$NO_DATA_FOUND)
	{
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while ($row = mysql_fetch_assoc($result)) {
			extract($row);
			$user_targets[]=$row;
		}

	}
	return $user_targets;
}


function show_my_models($username,$model_name)
{

	//Include database connection details
	require_once('../conf/config.php');

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


	$MODEL_ONLY=0;
	if(strlen($model_name)>1) $MODEL_ONLY=1;

	$query ="select * from models where username='$username'";

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
	echo "<th>pixel count<br/>per string<br/><font color=green>40-130</font></th>";
	echo "<th>How long<br/>from 1st to last pixel<br/><font color=green>Spacing between pixels<br/>Green range: 2-6\"<br/>Green range: 5-30cm</font></th>";
	echo "<th>Unit of<br/>measure</th>";
	echo "<th>topography</th>";
	echo "<th>H1 . height</th>";
	echo "<th>D1 . diameter</th>";
	echo "<th>D2 . diameter</th>";
	echo "<th>H2 . diameter</th>";
	echo "<th>D3 . diameter</th>";
	echo "<th>D4 . diameter</th>";
	echo "</tr>\n";

	if(!$NO_DATA_FOUND)
	{
		while ($row = mysql_fetch_assoc($result)) {
			extract($row);
			//

			if($MODEL_ONLY and $object_name == $model_name) $query_rows = $row;
			echo "<tr>\n";
			echo "<td>$username</td>";
			echo "<td><a href=\"member-index.php?model=$object_name\">$object_name</a></td>";
			echo "<td>$object_desc</td>";
			$model="??";
			if($model_type=="MTREE") $model="Mega-Tree";
			if($model_type=="MTREE_HALF") $model="Mega-Tree Half";
			if($model_type=="MATRIX") $model="Matrix(Grid)";
			if($model_type=="RAY") $model="Ray";
			echo "<td>$model</td>";
			if($string_type=="SS") $string_desc="Lynx Smart String";
			if($string_type=="CCR") $string_desc="LOR CCR";
			//			echo "<td>$string_desc</td>";
			//
			$warn="green";
			if($total_strings < 1 or $total_strings>100) $warn="red";
			echo "<td><font color=$warn>($total_strings)</font></td>";

			if($topography=="BOT_TOP") $strands=$total_strings;
			else
				$strands=$total_strings*2;
			//	echo "<td>$direction</td>";
			//		echo "<td>$orientation</td>";
			//
			//
			$warn="green";
			if($pixel_count < 40 or $pixel_count>130) $warn="red";
			echo "<td><font color=$warn>($pixel_count)</font></td>";
			// $pixel_spacing = $pixel_length/$pixel_count;
			 $pixel_length = $pixel_spacing * $pixel_count; // PIXEL CHANGE
			$buff_pixel_spacing = sprintf("%5.1f",	$pixel_spacing);
			$warn="green";
			if($unit_of_measure=="in" and ($pixel_spacing < 1 or $pixel_spacing>6)) $warn="red";
			if($unit_of_measure=="cm" and ($pixel_spacing < 5 or $pixel_spacing>30)) $warn="red";
			echo "<td>$pixel_length <font color=$warn>($buff_pixel_spacing)</font></td>";
			echo "<td>$unit_of_measure </td>";
			echo "<td>$topography</td>";

			//	assume our christmas tree has twice the height as the diameter.
			//	then the top angle is 76 degrees, sin 76 degrees = .970
			//	the bottom angle is 14 degrees, sin of that = .2419
			//
			//	given the hyptonuse (we are given this as $pixel_length)
			//	we can now do some sanity checks.
			if($topography=="BOT_TOP")
				$hyptonuse=$pixel_length ;
			else
				$hyptonuse=$pixel_length/2 ;
			$guess_h1 = $hyptonuse * 0.97;
			$buff_guess_h1 = sprintf("%5.1f",$guess_h1);
			$warn="green";
			$h1_ratio=$guess_h1/$h1;
			if($h1_ratio<.75 or $h1_ratio>1.25) $warn="red";
			echo "<td>$h1  <font color=$warn>($buff_guess_h1)</font></td>";

			$guess_d1 = 2* $hyptonuse * 0.2419;
			$buff_guess_d1 = sprintf("%5.1f",$guess_d1);
			$warn="green";
			$d1_ratio=$guess_d1/$d1;
			if($d1_ratio<.75 or $d1_ratio>1.25) $warn="red";
			echo "<td>$d1  <font color=$warn>($buff_guess_d1)</font></td>";
			echo "<td>$d2 </td>";
			echo "<td>$h2 </td>";
			echo "<td>$d3 </td>";
			echo "<td>$d4 </td>";
			echo "</tr>\n";
		}
	}
	else
		echo "<tr><td>$username</td><td>No models found under your user name</td></tr>";
	echo "</table>\n";
	echo "<br/><br/>\n";
	mysql_close();
	return ($query_rows);
}

function get_username_by_id($sess_member_id)
{
	//Include database connection details
	require_once('../conf/config.php');

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


	$MODEL_ONLY=0;
	if(strlen($model_name)>1) $MODEL_ONLY=1;

	$query ="select * from members where member_id='$sess_member_id'";

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
	if(!$NO_DATA_FOUND)
	{
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while ($row = mysql_fetch_assoc($result)) {
			extract($row);
		}

	}
	return ($login);
}
function getFilesFromDir($dir) { 

	$files = array(); 
	if ($handle = opendir($dir)) { 
		while (false !== ($file = readdir($handle))) { 


			if ($file != "." && $file != ".." ) { 
				if(is_dir($dir.'/'.$file)) { 
					$dir2 = $dir.'/'.$file; 
					$files[] = getFilesFromDir($dir2); 
				} 
				else { 
					$files[] = $dir.'/'.$file; 
				} 
			} 
		} 
		closedir($handle); 
	} 

	return array_flat($files); 
} 

function array_flat($array) { 

	$tmp=array();
	foreach($array as $a) { 
		if(is_array($a)) { 
			$tmp = array_merge($tmp, array_flat($a)); 
		} 
		else { 
			$tmp[] = $a; 
		} 
	} 

	return $tmp; 
} 

function gallery()
{
	// Usage find all gif files under the workspaces subdirectory 
	echo "<br/>";
	echo "<br/>";
	echo "<br/>";
	echo "<h1>Gallery of Effects by all users of Nutcracker</h1>";
	$dir = 'workspaces'; 
	$foo = getFilesFromDir($dir); 
	echo "<table border=1>";
	$line=0;
	$pics_per_row=6;
	foreach ($foo as $i => $file) {

		$tokens=explode(".",$file);
		$ext=$tokens[1];
		$pos=strpos($file,"_amp.gif");
		if($ext=="gif" and $pos === false)
		{
			$line++;
			if($line%$pics_per_row==1)
			{
				echo "<tr>";
			}
			$tokens=explode("/",$file);
			$username=$tokens[1];
			$filename=basename($tokens[2],".gif");
			$tok2=explode("~",$filename);
			$target_model=$tok2[0];
			$effect_name=$tok2[1];

			echo "<td><a href=\"copy_model.php?model=$target_mode&effect=$effect_name\"><img src=\"$file\"><br/>#$line target:  $target_model<br/>effect:  $effect_name</a></td>\n";
			if($line%$pics_per_row==0) 
			{
				echo "</tr>";
			}
		}

	}
	echo "</table>";
	echo "</pre>";
}
