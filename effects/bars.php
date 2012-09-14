<?php
require_once('../conf/header.php');
	//

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
if(!isset($get['color3']))    $get['color3']="#FFFFFF";
if(!isset($get['color4']))    $get['color4']="#FFFFFF";
if(!isset($get['color5']))    $get['color5']="#FFFFFF";
if(!isset($get['color6']))    $get['color6']="#FFFFFF";
if(!isset($get['direction'])) $get['direction']="down";
if(!isset($get['fade_3d']))   $get['fade_3d']="N";
if(!isset($get['fade_in']))   $get['fade_in']="0";
if(!isset($get['fade_out']))  $get['fade_out']="0";
if(!isset($get['highlight'])) $get['highlight']="N";
if(!isset($get['speed']))     $get['speed']="1";
save_user_effect($get);

f_bars($get);
