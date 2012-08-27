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
<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"/> 
<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Nutcracker Install Script</h1>
<?php

if($_SERVER['HTTP_HOST'] != 'localhost')
{
	echo"<pre><h1>ERROR! You cannot run this script on any place except for your local computer<br/>";
	echo "Please RIGHT-CLICK the filename, not left click it. select save as and put<br/>";
	echo "install.php into your c:\wamp\www directory.";
	echo "\nOnce you have downloaded install.php you run it by opening a web page to 'localhost/install.php'\n";
	echo "<pre>Here are the values in your _SERVER array\n";
	print_r($_SERVER);
	echo "</pre>";
	die ("\n\nProgram exiting .. ");
}

{
	if ( !build_nutcracker_database() )
	{

	if ( !isset($_POST['root_pass']) || !isset($_POST['root_user']) )
	{	?>

	<h3>Steps to install Nutcracker</h3>

	<p>
	<ol>
	<li>Download/Install <a href="http://sourceforge.net/projects/xampp/">XAMPP</a> - If you're
		here, you've probably already done this step.</li>
	<li>Download/Install <a href="http://sourceforge.net/projects/gnuplot/files/latest/download?source=files">gnuplot</a> -
	During the install's "Select Additional Tasks" screen be sure you check
	the "Add application directory to your PATH environment variable"</li>
	</ol>
	</p>

	<h3>Installation Details</h3>
	<?php	}	else	{	?>
	<h3>Retry Installation</h3>
	<p>
	Once you believe you have fixed anything causing errors, you can resubmit this form.
	</p>
	<?php	}	?>

	<form name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

	<p>
	Password: <input type="password" name="root_pass" value="<?php
	echo (!empty($_POST['root_pass']) ? $_POST['root_pass'] : ''); ?>" /><br />
	This is the root password for your XAMPP/LAMP MySQL database.  If you just
	installed XAMPP the password is blank.  LAMP users can change it
	<a href="http://localhost/security/xamppsecurity.php">here</a>.
	</p>

	<p>
	Username: <input type="text" name="root_user" value="<?php
	echo (!empty($_POST['root_user']) ? $_POST['root_user'] : 'root'); ?>" /><br />
	This is the username for the root user of your XAMPP/LAMP MySQL database.  The
	default 'root' is probably correct for 95% of users.
	If you're unsure, leave this alone.
	</p>

	<p><input type="submit" value="Submit" /></p>

	</form>

	<?php	}
	else
	{
		echo "<h1>SUCCESS</h1>\n";
		echo "<a href='".dirname($_SERVER['PHP_SELF'])."'>Continue to Nutcracker...</a>\n";
	}
}

function build_nutcracker_database()
{
	if ( !isset($_POST['root_pass']) || !isset($_POST['root_user']) )
	{
		return false;
	}

	echo "<h3>Installing Nutcracker</h3>\n";

	// Include the preferred configuration
	require_once(dirname(__FILE__)."/conf/config.php");

	echo "Connecting to MySQL..."; flush(); ob_flush();
	$conn = mysql_connect('localhost', $_POST['root_user'], $_POST['root_pass']);
	if( !$conn )
	{
		echo "<span class=\"fail\">FAILED!</span><br />\n";
		echo "Failed to connect to MySQL.\n";
		echo "Perhaps the root password is incorrect?<br />\n";
		echo "MySQL Error: " . mysql_error();
		return false;
	}
	else
		echo "<span class=\"pass\">OK</span><br />\n";

	echo "Creating nutcracker database..."; flush(); ob_flush();
	$sql = "CREATE DATABASE IF NOT EXISTS ".DB_DATABASE.";";
	$retval = mysql_query( $sql, $conn );
	if( !$retval )
	{
		echo "<span class=\"fail\">FAILED!</span><br />\n";
		echo "Could not create nutcracker database.\n";
		echo "MySQL Error: " . mysql_error();
		return false;
	}
	else
		echo "<span class=\"pass\">OK</span><br />\n";

	echo "Giving nutcracker database user permissions..."; flush(); ob_flush();
	$sql = "GRANT ALL ON *.* TO '".DB_USER."'@".DB_HOST." IDENTIFIED BY '".DB_PASSWORD."';";
	$retval = mysql_query( $sql, $conn );
	if( !$retval )
	{
		echo "<span class=\"fail\">FAILED!</span><br />\n";
		echo "Could not set permissions for nutcracker database user.<br />\n";
		echo "MySQL Error: " . mysql_error();
		return false;
	}
	else
		echo "<span class=\"pass\">OK</span><br />\n";

	echo "Selecting our new database to populate..."; flush(); ob_flush();
	$retval = mysql_select_db(DB_DATABASE);
	if( !$retval )
	{
		echo "<span class=\"fail\">FAILED!</span><br />\n";
		echo "Could not select our new database.<br />\n";
		echo "MySQL Error: " . mysql_error();
		return false;
	}
	else
		echo "<span class=\"pass\">OK</span><br />\n";

	echo "Creating nutcracker database schema..."; flush(); ob_flush();

	// Thanks to the following link on SO for importing SQL in PHP:
	// http://stackoverflow.com/questions/147821/loading-sql-files-from-within-php
	require_once("sql_parse.php");

	$dbms_schema = 'sql/nutcrackertables.sql';
	$sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema));
	if ( !$sql_query )
	{
		echo "<span class=\"fail\">FAILED!</span><br />\n";
		echo "Could not open sql file for import.<br />\n";
		return false;
	}

	$sql_query = remove_remarks($sql_query);
	$sql_query = split_sql_file($sql_query, ';');

	foreach($sql_query as $sql)
	{
		$retval = mysql_query($sql);
		if( !$retval )
		{
			echo "<span class=\"fail\">FAILED!</span><br />\n";
			echo "Could not populate database.<br />\n";
			echo "MySQL Error: " . mysql_error();
			return false;
		}
	}
	echo "<span class=\"pass\">OK</span><br />\n";

	echo "Populating nutcracker database data..."; flush(); ob_flush();

	$dbms_schema = 'sql/nutcrackerdata.sql';
	$sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema));
	if ( !$sql_query )
	{
		echo "<span class=\"fail\">FAILED!</span><br />\n";
		echo "Could not open sql file for import.<br />\n";
		return false;
	}

	$sql_query = remove_remarks($sql_query);
	$sql_query = split_sql_file($sql_query, ';');

	foreach($sql_query as $sql)
	{
		$retval = mysql_query($sql);
		if( !$retval )
		{
			echo "<span class=\"fail\">FAILED!</span><br />\n";
			echo "Could not populate database.<br />\n";
			echo "MySQL Error: " . mysql_error();
			return false;
		}
	}
	echo "<span class=\"pass\">OK</span><br />\n";

	return true;
}

?>

</body>
</html>
