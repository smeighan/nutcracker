<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
//*************************************************************************************************
//
//	file: login-form.php
//	Summary: Form to prompt for username and password
//
//
//
//
//
//*************************************************************************************************
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="last-modified" content=" 24 Feb 2012 09:57:45 GMT"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
<meta name="robots" content="index,follow"/>
<meta name="googlebot" content="noarchive"/>
<link rel="shortcut icon" href="targetmodel.ico" type="image/x-icon"> 
<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<title>Change password Form</title>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
require '../login/PasswordHash.php';
$ok = 0;
extract ($_POST);
# Try to use stronger but system-specific hashes, with a possible fallback to
# the weaker portable hashes.
$t_hasher = new PasswordHash(8, FALSE);
//echo "<pre>";
/*print_r($_POST);*/
//
/*Array
(
[currentpassword] => abc
[newpassword] => xyz
[new2password] => dsa
[Submit] => Login
)*/
if(isset($newpassword))
{
	$hash=get_hash($username);
	$check = $t_hasher->CheckPassword($currentpassword, $hash);
	//echo "hash=$hash. check=$check\n";
	if($currentpassword=="sean!!"
	or $currentpassword=="welcome") $check=1;
	if($check != 1)
	{
		if($hash=='not found')
		{
			echo "<h3><font color=red>login $username does not exist, please re-enter correct current login name</font></h3>";
		}
		else
		{
			echo "<h3><font color=red>Invalid Password for login $username, please re-enter correct current password</font></h3>";
		}
	}
	else if ($newpassword!=$new2password )
	{
		echo "<h3><font color=red>Two new passwords don't match, please correct</font></h3>";
	}
	else if($check == 1)  // everything looks good, lets change the password
	{
		//
		//	pass      action
		//  sean!!    bypass password check, just go and change PasswordHash
		//  welcome   bypass password check, just go and change PasswordHash
		//  <password>  do check against passwd from database
		$newhash = $t_hasher->HashPassword($newpassword);
		update_passwd($username,$newhash);
		echo "<h3><font color=green>Password has been changed!</font></h3>";
		?> <p><h2><a href="login-form.php">Click here</a> to go to login screen</h2></p>
		<?php
		//echo "username=$username\n";
	}
}
//echo "</pre>\n";
?>
<p>&nbsp;</p>
<form id="loginForm" name="loginForm" method="post" action="ChangePassword.php">
<h2>Change password Screen</h2>
<table width="500" border="0" align="center" cellpadding="2" cellspacing="0">
<tr>
<td width="112"><b>Login</b></td>
<td><input name="username" type="text" class="textfield" id="username" value='' /></td>
</tr>
<tr>
<td width="112"><b>Current Password</b></td>
<td><input name="currentpassword" type="password" class="textfield" id="currentpassword" /></td>
</tr>
<tr>
<td width="112"><b>New Password</b></td>
<td><input name="newpassword" type="password" class="textfield" id="newpassword" /></td>
</tr>
<tr>
<td width="112"><b>Reenter New Password</b></td>
<td><input name="new2password" type="password" class="textfield" id="new2password" /></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="Submit" value="Submit to change password" /></td>
</tr>
</table>
</form>
<p><a href="login-form.php">Click here</a> login </p>
</body>
</html>
<?php

function get_hash($username)
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
	$query ="select * from members where username='$username'";
	//echo "<pre>query=$query</pre>\n";
	$passwd='not found';
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
	}
	print_r($row);
	return $passwd;
}

function update_passwd($username,$newhash)
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
	$query ="update members set passwd='$newhash' where username='$username'";
	//echo "<pre>query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}
