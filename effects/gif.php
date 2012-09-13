<?php
/*
Nutcracker: RGB Effects Builder
Copyright (C) 2012  Sean Meighan
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
require_once('../conf/header.php');
//
// $username = clean($_GET['login']);


require_once ("read_file.php");
require_once ("f_gif.php");
$get=$_GET;
$username=$_SESSION['SESS_LOGIN'];
$get['OBJECT_NAME']='gif';
extract ($get);
$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);
$get['username']=$username;
$get['batch']=$batch;
if(!isset($get['brightness']))   $get['brightness']="0";
if(!isset($get['fade_in']))  $get['fade_in']="0";
if(!isset($get['fade_out']))  $get['fade_out']="0";
save_user_effect($get);
//show_array($_GET,"_GET");

f_gif($get);