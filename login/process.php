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
'pixel_count', 'pixel_first',  'pixel_last', 'number_segments','gif_model',
'unit_of_measure','pixel_length',   'pixel_spacing','window_degrees',
'total_strings', 'direction',  'topography'
);
// Specify the field names that you want to require...
$requiredFields = array(
'username','model_type', 'string_type', 
'pixel_count',  'pixel_length',   'pixel_spacing',
'total_strings', 'direction',  'topography'
);
/*echo "<pre>";
print_r($_GET);
echo "</pre>";*/
//die("exit");
$errors = array();
/*echo "<pre>process.php:";
print_r($_GET);
echo "</pre>";*/
foreach($_GET AS $key => $value)
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
	($key=="total_strings" and  $n==0) 
	 )
		$errors[] = "The field $key must be numeric. bad value = <font color=red>$value</font>";
	if($key=="username" and (strlen($value)<1 or empty($value)) )
		$errors[] = "The field $key \"$value\" must have a value";
}
require "../effects/read_file.php";
extract($_GET);
//show_array($_GET,"POST");
//die("die");
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
	if(isset($member['login'])) $_SESSION['SESS_LOGIN'] = $member['login'];
	session_write_close();
	//	header("location: member-index.php");
	echo "<p><a href=\"member-index.php\">Click here</a> to go and correct missing fields.</p>";
	//include 'member-index.php';
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
	
	//$PIXEL_LENGTH=$PIXEL_SPACING*$PIXEL_COUNT;
	if($MODEL_TYPE!="MTREE") $WINDOW_DEGREES=360; // make sure every model type other than Mega-trees are defaulted to 360. This saves the user from screwing up.
	
	
	if(!isset($UNIT_OF_MEASURE)) $UNIT_OF_MEASURE='in';
	if($UNIT_OF_MEASURE=='in') $PIXEL_LENGTH=3;
	else $PIXEL_LENGTH=8;
	$insert = "REPLACE into models( username,object_name, object_desc, model_type,
	pixel_count, pixel_first,  pixel_last, 
	unit_of_measure, pixel_length, total_strings,window_degrees,
	number_segments,gif_model,folds,start_bottom)
		values ('$username','$OBJECT_NAME', '$OBJECT_DESC', '$MODEL_TYPE', 
	$PIXEL_COUNT, $PIXEL_FIRST,  $PIXEL_LAST, 
	'$UNIT_OF_MEASURE', $PIXEL_LENGTH,$TOTAL_STRINGS,$WINDOW_DEGREES,$number_segments,'$gif_model',$FOLDS,'$START_BOTTOM')";
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
	//$model_type=$_GET['MODEL_TYPE'];
	/*echo "<pre>";
	print_r($_GET);
	echo "model_type = model_type\n";
	echo "</pre>";*/
	
	if($MODEL_TYPE=="SINGLE_STRAND")
		header("location: single_strand-form.php?username=$username&total_strings=$TOTAL_STRINGS&object_name=$OBJECT_NAME&number_segments=$number_segments");
	else
	header("location: target-exec.php?model_name=$OBJECT_NAME&username=$username&window_degrees=$WINDOW_DEGREES");
	exit();
	//setcookie("username", "");
}
