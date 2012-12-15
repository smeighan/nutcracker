<?php
if($_SERVER['HTTP_HOST'] == 'nutcracker123.com')
{
	define('DB_HOST',     'localhost');
	define('DB_USER',     'nutcrcom_ncuser');
	define('DB_PASSWORD', 'nutcracker123');
	define('DB_DATABASE', 'nutcrcom_nutcracker');
}
else // meighan.net and localhost installations
{
	define('DB_HOST',     'localhost');
	define('DB_USER',     'nc_user');
	define('DB_PASSWORD', 'nutcracker123');
	define('DB_DATABASE', 'nutcracker');
}
?>
