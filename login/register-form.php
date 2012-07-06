<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="last-modified" content=" 24 Feb 2012 09:57:45 GMT"/>
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
		<meta name="robots" content="index,follow"/>
		<meta name="googlebot" content="noarchive"/>
		<link rel="shortcut icon" href="targetmodel.ico" type="image/x-icon"> 
		<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
		<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<title>Login Form</title>
<link href="loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
	echo '<ul class="err">';
	foreach($_SESSION['ERRMSG_ARR'] as $msg) {
		echo '<li>',$msg,'</li>'; 
	}
	echo '</ul>';
	unset($_SESSION['ERRMSG_ARR']);
}
?>
<form id="loginForm" name="loginForm" method="post" action="register-exec.php">
<h2>Nutcracker registration Form</h2>
  <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr>
      <th>First Name </th>
      <td><input name="fname" type="text" class="textfield" id="fname" /></td>
    </tr>
    <tr>
      <th>Last Name </th>
      <td><input name="lname" type="text" class="textfield" id="lname" /></td>
    </tr>
    <tr>
      <th width="124">Login</th>
      <td width="168"><input name="login" type="text" class="textfield" id="login" /></td>
    </tr>
    <tr>
      <th>Password</th>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
    </tr>
    <tr>
      <th>Confirm Password </th>
      <td><input name="cpassword" type="password" class="textfield" id="cpassword" /></td>
    </tr>
 <tr>
      <th>Select All Sequencers you will need </th>
      <td><br/>
	LSP 1.8<input type="checkbox" name="sequencers[]" value="LSP1_8" ><br/>
	LSP 2.0<input type="checkbox" name="sequencers[]" value="LSP2_0" ><br/>
	LSP 3.0<input type="checkbox" name="sequencers[]" value="LSP3_0" ><br/>
	LOR S2<input type="checkbox" name="sequencers[]" value="LOR_S2" > <br/>
	LOR S3<input type="checkbox" name="sequencers[]" value="LOR_S3" > <br/>
	Vixen 2.1.1<input type="checkbox" name="sequencers[]" value="VIXEN211" > <br/>
	Vixen 2.5<input type="checkbox" name="sequencers[]" value="VIXEN25" > <br/>
	Vixen 3.0<input type="checkbox" name="sequencers[]" value="VIXEN3" > <br/>
	HLS<input type="checkbox" name="sequencers[]" value="HLS" > <br/>
	Other<input type="checkbox" name="sequencers[]" value="OTHER" > <br/>
	</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Register" /></td>
    </tr>
  </table>
</form>
<p><a href="login-form.php">Click here</a> to login to your account.</p>

</body>
</html>
