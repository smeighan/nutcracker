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
require_once('../effects/f_twinkle.php');
require_once('../effects/read_file.php');
//
//
$get=$_GET;
$username=$_SESSION['SESS_LOGIN'];
$get['OBJECT_NAME']='twinkle';
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
if(!isset($get['rainbow_hue']))   $get['rainbow_hue']="N";
if(!isset($get['handiness']))   $get['handiness']="R";
if(!isset($get['fade_in']))   $get['fade_in']="0";
if(!isset($get['fade_out']))  $get['fade_out']="0";
if(!isset($get['sparkles']))  $get['sparkles']="0";
if(!isset($get['speed']))     $get['speed']="1";
save_user_effect($get);
///*
f_twinkle($_GET);
