<?php
require_once('../conf/header.php');
	//
	error_reporting(E_ALL);


require_once('../effects/f_bars.php');
require_once('../effects/read_file.php');

$get=$_GET;
$username=$_SESSION['SESS_LOGIN'];
$get['OBJECT_NAME']='bars';
extract ($get);
$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);
$get['username']=$username;
$get['batch']=$batch;
save_user_effect($get);

f_bars($get);
