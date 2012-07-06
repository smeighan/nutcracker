<?php
require_once('../conf/auth.php');

// process.php


$username= $_SESSION['SESS_LOGIN'];


$query ="select * from models where username='$username' amd object_name='$object_name'";
$result=mysql_query($query,$db) or die ("Error on $query");
if (!$result) {
	$message  = 'Invalid query: ' . mysql_error() . "\n";
	$message .= 'Whole query: ' . $query;
	die($message);
}

$NO_DATA_FOUND=0;
if (mysql_num_rows($result) == 0) {
	$NO_DATA_FOUND=1;
}


if(!$NO_DATA_FOUND)
{
	while ($row = mysql_fetch_assoc($result)) {
		extract($row);
	}
}

mysql_close($db);

//setcookie("username", "");
}
