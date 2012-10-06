<?php

function nc_query($sql) {
	require_once('../conf/config.php');
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	$result = mysql_query ($sql, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
	return($result);
}

?>