<?php
require_once('../conf/header.php');
	//

require_once('../effects/f_pinwheel.php');
require_once('../effects/read_file.php');

$get=$_GET;
$username=$_SESSION['SESS_LOGIN'];
$get['OBJECT_NAME']='pinwheel';
extract ($get);
$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);
$get['username']=$username;
$get['batch']=$batch;

if(!isset($get['fade_in']))   $get['fade_in']="0";
if(!isset($get['fade_out']))  $get['fade_out']="0";
save_user_effect($get);

f_pinwheel($get);
