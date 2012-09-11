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
require_once('f_butterfly.php');
//
require_once("read_file.php");
set_time_limit(0);
$get=$_GET;
$get['OBJECT_NAME']='butterfly';
extract ($get);
$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);
$frame_delay = $_GET['frame_delay'];
$frame_delay = intval((5+$frame_delay)/10)*10; // frame frame delay to nearest 10ms number_format
$get['frame_delay']=$frame_delay;
save_user_effect($get);
//show_array($_GET,"_GET");
if($batch==0) show_array($get,"Effect Settings");
//show_array($_SESSION,"_SESSION");
//show_array($_SERVER,"_SERVER");

/*
$get['base']=$base;
$get['t_dat']=$t_dat;
$get['xdat']=$xdat;
$get['path']=$path;

$get['background_color']=$background_color;
$get['show_frame']=$show_frame;*/

f_butterfly($get);
