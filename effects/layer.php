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
require_once('../conf/auth.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nutcracker: RGB Effects Builder</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="last-modified" content=" 24 Feb 2012 09:57:45 GMT"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
<meta name="robots" content="index,follow"/>
<meta name="googlebot" content="noarchive"/>
<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"/> 
<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php $menu="effect-form"; require "../conf/menu.php"; ?>
<?php
//
require("read_file.php");
echo "<pre>\n";
ini_get('max_execution_time'); 
set_time_limit(60*60*8);
ini_get('max_execution_time'); 
echo "_POST\n";
print_r($_POST);

echo "</pre>\n";
///*
/*
Array
(
[username] => f
[user_target] => MT
[effect_class] => garlands
[effect_name] => 44
[number_garlands] => 4
[number_rotations] => 2
[garland_thickness] => 1
[start_color] => #26FF35
[end_color] => #2E35FF
[frame_delay] => 22
[direction] => 2
[submit] => Submit Form to create your target model
)
	*/ 
$array_to_save=$_POST;
extract($_POST);
$array_to_save['OBJECT_NAME']='layer';
if(isset($LAYER_EFFECTS[0])) $array_to_save['file1']=$LAYER_EFFECTS[0];
if(isset($LAYER_EFFECTS[1])) $array_to_save['file2']=$LAYER_EFFECTS[1];

$array_to_save['layer_method']=$lmethod;
extract ($array_to_save);

$frame_delay = $_POST['frame_delay'];
$frame_delay = intval((5+$frame_delay)/10)*10; // frame frame delay to nearest 10ms number_format
$array_to_save['frame_delay']=$frame_delay;
extract ($array_to_save);
save_user_effect($array_to_save);
show_array($_POST,"_POST");
show_array($array_to_save,"Effect Settings");
//show_array($_SESSION,"_SESSION");
//show_array($_SERVER,"_SERVER");
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
$member_id=get_member_id($username);
$path ="workspaces/$member_id";
$directory=$path;
if (file_exists($directory))
{
	} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}
$base = $user_target . "+" . $effect_name;
$t_dat = $user_target . ".dat";
$xdat = $user_target ."+".  $effect_name . ".dat";
$path="../targets/". $member_id;
$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
//	remove old ong and dat files
$mask = $directory . "/*.png";
//array_map( "unlink", glob( $mask ) );
$mask = $directory . "/*.dat";
//array_map( "unlink", glob( $mask ) );
/*
_POST
username	f
user_target	AA
effect_class	garlands
effect_name	CIRCLE2
window_degrees	180
start_color	#3672FF
end_color	#295BFF
frame_delay	20
sparkles	10
seq_duration	5
submit	Submit Form to create your target model
*/
purge_files();
$path="workspaces/". $member_id;
if(empty($show_frame)) $show_frame='N';
if(empty($background_color)) $background_color='#FFFFFF';
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;

