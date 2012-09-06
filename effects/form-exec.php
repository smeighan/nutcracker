<?php
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

$username= $_SESSION['SESS_LOGIN'];

$tokens=explode("effect=",$REQUEST_URI);
$effect_name=$tokens[1];

/*
Array
(
    [username] => f
    [user_targets] => AA
    [user_effects] => spirals
    [submit] => Submit Form to create your target model
)
 */ 

$username = $_POST['username'];
$user_targets = $_POST['user_targets'];
$effect_class = $_POST['user_effects'];



$effect_hdr=get_effect_hdr($user_effects);
//echo "<pre>";
//print_r($effect_hdr);
//if(empty($effect_hdr['php_program'])) die("No program associated to $effect_class");
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = $effect_hdr[0]['php_program'];
//echo "header(Location: http://$host$uri/$extra)";
// header("Location: http://$host$uri/$extra");


$effect_details=get_effect_details($effect_class);
//echo "get_effect_details($effect_class);";
//print_r($effect_details);
echo "</pre>";
/*
(
    [0] => Array
	(
	    [effect_class] => spirals
	    [param_name] => number_spirals
	    [param_prompt] => Number of Spirals
	    [param_desc] => This is the number of spirals that go around the tree
	    [param_range] => 1-99
	    [created] => 2012-03-02 10:03:49
	    [last_upd] => 2012-03-02 10:03:49
	    [sequence] => 1
	)
 */


echo "<h2>Nutcracker: RGB Effects Builder for user $username<h2>"; 
//

$cnt=count($effect_details);
?>
	<form action="<? echo "$extra"; ?>" method="POST">
<input type="hidden" name="username" value="<? echo "$username"; ?>">
<table border="1">
<?php
$cnt=count($effect_details);
display_gif();

for($i=0;$i<$cnt;$i++)
{
	echo "<tr>";
	echo "<td>" . $effect_details[$i]['param_prompt'] . " (" . $effect_details[$i]['param_range'] . "):";
	echo "<input type=\"text\" STYLE=\"background-color: #ABE8EC;\" size=\"32\" maxlength=\"\" \n";
	echo "name=\"" . $effect_details[$i]['param_name'] . "\" <br/>";
	echo "<br/>" . $effect_details[$i]['param_desc'] . "</td>\n";
	echo "</tr>\n";
}
?>
	</table>
	<input type="submit" name="submit" value="Submit Form to create your target model" />
	</form>
	</body>
	</html>


	</body>
	</html>

<?php
function get_effect_details($effect_class)
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
	$query ="select * from effects_dtl where effect_class='$effect_class' order by sequence";
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

	$effects_classes=array();
	if(!$NO_DATA_FOUND)
	{
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while ($row = mysql_fetch_assoc($result)) {
			extract($row);
			$effects_details[]=$row;
		}

	}
	return $effects_details;

}

function get_effect_hdr($effect_class)
{

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
	$query ="select * from effects_hdr where effect_class='$effect_class'";
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
			$effect_hdr[]=$row;
		}

	}
	return $effect_hdr;

}
