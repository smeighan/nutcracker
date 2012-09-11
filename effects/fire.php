<<?php
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
require_once("read_file.php");
require_once("f_fire.php");
//
$username=$_SESSION['SESS_LOGIN'];
if($batch==0) echo "<h2>Nutcracker: RGB Effects Builder for user $username<br/>
On this page you build an animation of the spiral class and create an animated GIF</h2>"; 

set_time_limit(0);

$get=$_GET;
$get['OBJECT_NAME']='fire';
extract ($get);
$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);
$get['effect_name']=$effect_name;
$get['username']=$username;
if(!isset($show_frame)) $show_frame='N';
$get['show_frame']=$show_frame;
/*$frame_delay = $_GET['frame_delay'];
$frame_delay = intval((5+$frame_delay)/10)*10; // frame frame delay to nearest 10ms number_format*/
$get['batch']=$batch;
save_user_effect($get);
f_fire($get);
