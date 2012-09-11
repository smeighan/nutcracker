<?php
require_once('../conf/header.php');
	//
	error_reporting(E_ALL);


require_once('f_bars.php');

$username=$_SESSION['SESS_LOGIN'];
$_GET['username']=$username;

f_bars($_GET);