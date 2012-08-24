<?php
//*************************************************************************************************
//
//	file: .php
//	Summary: Validate data from a new user create process, if valid 
//           create a new row in the MEMBERS database
//
//
//
//
//*************************************************************************************************
//Start session
session_start();

//Include database connection details
require_once('../conf/config.php');

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

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

//Function to sanitize values received from the form. Prevents SQL injection
function clean($str) {
	$str = @trim($str);
	if(get_magic_quotes_gpc()) {
		$str = stripslashes($str);
	}
	return mysql_real_escape_string($str);
}

//Sanitize the POST values
$fname = clean($_POST['fname']);
$lname = clean($_POST['lname']);
$username = clean($_POST['login']);
$password = clean($_POST['password']);
$cpassword = clean($_POST['cpassword']);
$sequencers = $_POST['sequencers'];

//Input Validations
if($fname == '') {
	$errmsg_arr[] = 'First name missing';
	$errflag = true;
}
if($lname == '') {
	$errmsg_arr[] = 'Last name missing';
	$errflag = true;
}
if($username == '') {
	$errmsg_arr[] = 'Login ID missing';
	$errflag = true;
}
if($password == '') {
	$errmsg_arr[] = 'Password missing';
	$errflag = true;
}
if($cpassword == '') {
	$errmsg_arr[] = 'Confirm password missing';
	$errflag = true;
}
if( strcmp($password, $cpassword) != 0 ) {
	$errmsg_arr[] = 'Passwords do not match';
	$errflag = true;
}

//Check for duplicate login ID
if($username != '') {
	$qry = "SELECT * FROM members WHERE username='$username'";
	$result = mysql_query($qry);
	if($result) {
		if(mysql_num_rows($result) > 0) {
			$errmsg_arr[] = 'Login ID already in use';
			$errflag = true;
		}
		@mysql_free_result($result);
	}
	else {
		die("Query failed");
	}
}

//If there are input validations, redirect back to the registration form
if($errflag) {
	$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
	session_write_close();
	header("location: register-form.php");
	exit();
}

//Create INSERT query
$cnt=count($sequencers);

$LSP1_8=$LSP2_0=$LSP_3=$LOR_S2=$LOR_S3=$VIXEN211=$VIXEN25=$VIXEN3=$HLS=$OTHER="N";
for($i=0;$i<=$cnt;$i++)
{
	if($sequencers[$i]=="LSP1_8") $LSP1_8="Y";
	if($sequencers[$i]=="LSP2_0") $LSP2_0="Y";
	if($sequencers[$i]=="LSP3_0") $LSP3_0="Y";
	if($sequencers[$i]=="LOR_S2") $LOR_S2="Y";
	if($sequencers[$i]=="LOR_S3") $LOR_S3="Y";
	if($sequencers[$i]=="VIXEN211") $VIXEN211="Y";
	if($sequencers[$i]=="VIXEN25") $VIXEN25="Y";
	if($sequencers[$i]=="VIXEN3") $VIXEN3="Y";
	if($sequencers[$i]=="HLS") $HLS="Y";
	if($sequencers[$i]=="OTHER") $OTHER="Y";

}
$qry = "INSERT INTO members(firstname, lastname, username, passwd,
	LSP1_8,LSP2_0,LSP3_0,LOR_S2,LOR_S3,VIXEN211,VIXEN25,VIXEN3,HLS,OTHER) 
	VALUES('$fname','$lname','$username','".md5($_POST['password'])."',
		'$LSP1_8','$LSP2_0','$LSP3_0','$LOR_S2','$LOR_S3','$VIXEN211','$VIXEN25','$VIXEN3','$HLS','$OTHER')";
$result = @mysql_query($qry);

//Check whether the query was successful or not
if($result) {
	header("location: register-success.php");
	exit();
}else {
	die("Query failed. $qry");
}
?>
