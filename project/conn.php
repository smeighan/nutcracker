<?Php
if($_SERVER['HTTP_HOST'] == 'nutcracker123.com')
{
	$dbhost_name = "localhost";
	$database =    "nutcrcom_nutcracker";
	$username =    "nutcrcom_ncuser";
	$password =    "nutcracker123";
}
else // meighan.net and localhost installations
{
	$dbhost_name = "localhost";
	$database = "nutcracker";
	$username = "nc_user";
	$password = "nutcracker123";
}
//////// Do not Edit below /////////
try {
	$dbo = new PDO('mysql:host=localhost;dbname='.$database, $username, $password);
}
catch (PDOException $e)
{
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
}
?> 