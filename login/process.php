<?php
//*************************************************************************************************
//
//	file: process.php
//	Summary: Called from member-index after user has edited or created a target. Our goal here is to 
//           store his target info into the MODELS table and to display what the string and pixel mapping is 
//           to nutcracker strand and pixel.
//
//
//
//*************************************************************************************************
require_once('../conf/auth.php');
// process.php
/*
*  Specify the field names that are in the form. This is meant
*  for security so that someone can't send whatever they want
*  to the form.
*/
$allowedFields = array(
'username','object_name', 'object_desc', 'model_type', 'string_type', 
'pixel_count', 'pixel_first',  'pixel_last', 
'unit_of_measure','pixel_length',   'pixel_spacing',
'total_strings', 'direction', 'orientation', 'topography', 'h1', 
'h2', 'd1', 'd2', 'd3' , 'd4'
);
// Specify the field names that you want to require...
$requiredFields = array(
'username','object_name', 'object_desc', 'model_type', 'string_type', 
'pixel_count',  'pixel_length',   'pixel_spacing',
'total_strings', 'direction', 'orientation', 'topography', 'h1', 
'd1'
);
echo "<pre>";
echo "POST:\n";
print_r($_POST);
echo "SERVER:";
print_r($_SERVER);
echo "SESSION:\n";
print_r($_SESSION);
echo "</pre>";
$username= $_SESSION['SESS_LOGIN'];
//echo "<pre>";
//echo "process.php username=$username";
//print_r($_SESSION);
//echo "</pre>\n";
// Loop through the $_POST array, which comes from the form...
$errors = array();
/*echo "<pre>process.php:";
print_r($_POST);
echo "</pre>";*/
foreach($_POST AS $key => $value)
{
	$key=strtolower($key);
	// is this a required field?
	if(in_array($key, $requiredFields) && $value == '')
	{
		$errors[] = "The field $key is required.";
	}
	if(is_numeric($value))
		$n=1;
	else
	$n=0;
	if(($key=="pixel_count" and  $n==0) or
	($key=="pixel_spacing" and  $n==0) or
	($key=="total_strings" and  $n==0) or
	($key=="h1" and  $n==0) or
	($key=="d1" and  $n==0) )
		$errors[] = "The field $key must be numeric. bad value = <font color=red>$value</font>";
	if($key=="username" and (strlen($value)<1 or empty($value)) )
		$errors[] = "The field $key \"$value\" must have a value";
}
require "../effects/read_file.php";
extract($_POST);
//show_array($_POST,"POST");
// were there any errors?
//
if(count($errors) > 0)
{
	echo "<pre>";
	print_r($errors);
	$errorString = '<p>There was an error processing the form.</p>';
	$errorString .= '<ul>';
	foreach($errors as $error)
	{
		$errorString .= "<li>$error</li>";
	}
	$errorString .= '</ul>';
	// display the previous form
	$_SESSION['SESS_LOGIN'] = $member['login'];
	session_write_close();
	//	header("location: member-index.php");
	echo "<p><a href=\"member-index.php\">Click here</a> to go and correct missing fields.</p>";
	//include 'member-index.php';
	echo "</pre>";
}
else
{
	$OBJECT_NAME=strtoupper($OBJECT_NAME);
	//
	//	if we have a LOR CCR ,set some values for them
	$PIXEL_FIRST=1;
	$PIXEL_LAST = $PIXEL_COUNT;
	if(empty($H2)) $H2=0;
	if(empty($D2)) $D2=0;
	if(empty($D3)) $D3=0;
	if(empty($D4)) $D4=0;
	$OBJECT_NAME = str_replace ( " " , "_" , $OBJECT_NAME);  // no embedded blanks in onject name
	/*
	username	varchar(15)	latin1_swedish_ci		No			 	 	 	 	 	 	 
	object_name	varchar(8)	latin1_swedish_ci		No			 	 	 	 	 	 	 
	object_desc	varchar(80)	latin1_swedish_ci		Yes	NULL		 	 	 	 	 	 	 
	model_type	varchar(15)	latin1_swedish_ci		Yes	NULL		 	 	 	 	 	 	 
	string_type	varchar(15)	latin1_swedish_ci		Yes	NULL		 	 	 	 	 	 	 
	pixel_count	int(11)			Yes	NULL		 	 	 	 	 	 	
	pixel_first	int(11)			Yes	NULL		 	 	 	 	 	 	
	pixel_last	int(11)			Yes	NULL		 	 	 	 	 	 	
	pixel_length	decimal(13,2)		UNSIGNED	Yes	NULL		 	 	 	 	 	 	
	total_strings	int(11)			Yes	NULL		 	 	 	 	 	 	
	direction	varchar(10)	latin1_swedish_ci		Yes	NULL		 	 	 	 	 	 	 
	orientation	int(11)			Yes	NULL		 	 	 	 	 	 	
	topography	varchar(20)	latin1_swedish_ci		Yes	NULL		 	 	 	 	 	 	 
	h1	decimal(13,2)		UNSIGNED	Yes	NULL		 	 	 	 	 	 	
	h2	decimal(13,2)		UNSIGNED	Yes	NULL		 	 	 	 	 	 	
	d1	decimal(13,2)		UNSIGNED	Yes	NULL		 	 	 	 	 	 	
	d2	decimal(13,2)		UNSIGNED	Yes	NULL		 	 	 	 	 	 	
	d3	decimal(13,2)		UNSIGNED	Yes	NULL		 	 	 	 	 	 	
	d4	decimal(13,2)	
	Error on REPLACE into models( username,object_name, object_desc, model_type, string_type, pixel_count, pixel_first, pixel_last, unit_of_measure, pixel_length, pixel_spacing, total_strings, direction, orientation, topography, h1, h2, d1, d2, d3 , d4) values ('f','ASDF', 'sadf', 'MTREE', '', 44, 1, 44, 'in', ,3, 11, '', 0, 'BOT_TOP', 11.00, 0.00, 11.00, 1.00, 0.00, 0.00)
		*/	
	//$PIXEL_LENGTH=$PIXEL_SPACING*$PIXEL_COUNT;
	if($UNIT_OF_MEASURE=='in') $PIXEL_LENGTH=3;
	else $PIXEL_LENGTH=8;
	$insert = "REPLACE into models( username,object_name, object_desc, model_type,
	pixel_count, pixel_first,  pixel_last, 
	unit_of_measure, pixel_length, total_strings,folds,start_bottom)
		values ('$username','$OBJECT_NAME', '$OBJECT_DESC', '$MODEL_TYPE', 
	$PIXEL_COUNT, $PIXEL_FIRST,  $PIXEL_LAST, 
	'$UNIT_OF_MEASURE', $PIXEL_LENGTH,$TOTAL_STRINGS,$FOLDS,'$START_BOTTOM')";
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
	mysql_query($insert) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $insert . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	//echo "<pre>Target model saved</pre>";
	$date_field= date('Y-m-d');
	$time_field= date("H:i:s");
	$query="INSERT into audit_log values ('$username','$date_field','$time_field','insert','$OBJECT_NAME')";
	$result=mysql_query($query) or die("Failed to execute $query");
	mysql_close();
	$_SESSION['SESS_LOGIN'] = $username;
	//session_write_close();
	//$model_type=$_POST['MODEL_TYPE'];
	/*echo "<pre>";
	print_r($_POST);
	echo "model_type = model_type\n";
	echo "</pre>";*/
	if($MODEL_TYPE=="SINGLE_STRAND")
		header("location: single_strand-form.php?user=$username?total_strings=$TOTAL_STRINGS?object_name=$OBJECT_NAME");
	else
	header("location: target-exec.php?model=$OBJECT_NAME?user=$username");
	exit();
	//setcookie("username", "");
}
