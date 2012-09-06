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
// http://localhost/nutcracker/login/single_strand-form.php?user=f?total_strings=6?object_name=A0
// 
//
// QUERY_STRING] => user=f?total_strings=6?object_name=A0
//
//
//$tokens=explode("?model=",$REQUEST_URI);
/*$tokens=explode("?",$_SERVER['QUERY_STRING']);
$tok2=explode("=",$tokens[0]); $username = $tok2[1];
$tok2=explode("=",$tokens[1]); $total_strings = $tok2[1];
$tok2=explode("=",$tokens[2]); $object_name = $tok2[1];*/
set_time_limit(0);
if(isset($_POST)===false or $_POST==null ) // First time here? Called by member-index.php
{ // yes
	$first_time=1;
}
else
{ // no, so this self submit has values for us to update
	$first_time=0;
	extract($_POST);
}
$total_count=count_gallery();
echo "<h1>Gallery has $total_count effects in it</h1>";
$self=$_SERVER['PHP_SELF'];
echo "<form action=\"gallery.php\" method=\"POST\">\n";
?>
<input type="submit" name="submit" value="Submit Form to create your target model" />
<table border="1">
<tr>
<td>How many gif's to show at a time</td>
<td>
<INPUT TYPE=RADIO NAME="number_gifs" VALUE="50"  CHECKED        >50<br/>
<INPUT TYPE=RADIO NAME="number_gifs" VALUE="100"         >100<br/>
<INPUT TYPE=RADIO NAME="number_gifs" VALUE="250"  >250<br/>
<INPUT TYPE=RADIO NAME="number_gifs" VALUE="500"         >500<br/>
<INPUT TYPE=RADIO NAME="number_gifs" VALUE="1000"         >1000<P>
</td>
</tr>
<tr>
<td>How to Sort?</td>
<td>
<INPUT TYPE=RADIO NAME="sort" VALUE="member_id"   CHECKED  >user id, effect name<br/>
<INPUT TYPE=RADIO NAME="sort" VALUE="effect_class"          >effect_class,user_id,effect_name<br/>
<INPUT TYPE=RADIO NAME="sort" VALUE="effect_name"          >effect_name<P>
</td>
</tr>
<tr>
<td>What Effect Class(es) to Show??</td>
<td>
<INPUT TYPE=CHECKBOX NAME="effect_class_selected[]" VALUE="all"  CHECKED       >all<br/>
<?php
$effect_class_array=get_effect_classes();
foreach($effect_class_array as $effect_class=>$description)
{
	printf ("<INPUT TYPE=CHECKBOX NAME=\"effect_class_selected[]\" VALUE=\"%s\"  >%s - %s<BR/>\n",$effect_class,
	$effect_class,$description);
}
?>
</td>
</tr>
<tr>
<td><input type="text" STYLE="background-color: #ABE8EC;" size="8" maxlength="" 
<?php echo "value=\"\""; ?> name="number_segments"><br/>
</td>
</tr>
</table>
<?php
if($first_time==0)
{
}
?>
</form>
<?php
echo "</body>\n";
echo "</html>\n";

function get_effect_classes()
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
	$date_field= date('Y-m-d');
	$time_field= date("H:i:s");
	$query="select * from effects_hdr where active='Y' order by effect_class";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$effect_class_array[$effect_class]=$description;
	}
	return $effect_class_array;
}

function count_gallery()
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
	$query="select count(*) cnt from gallery";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
	}
	return $cnt;
}
