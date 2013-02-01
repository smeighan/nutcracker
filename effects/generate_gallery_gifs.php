<html>
<head>
	<style type="text/css">
		table.gridtable {
			font-family: verdana,arial,sans-serif;
			font-size: 11px;
			color: #333333;
			border-width: 1px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable th {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #dedede;
		}
		table.gridtable td {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #ffffff;
		}
	</style>
	<style type="text/css">
		table.imagetable {
			font-family: verdana,arial,sans-serif;
			font-size: 11px;
			color: #333333;
			border-width: 1px;
			border-color: #999999;
			border-collapse: collapse;
		}
		table.imagetable th {
			background: #b5cfd2 url('cell-blue.jpg');
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #999999;
		}
		table.imagetable td {
			background: #dcddc0 url('cell-grey.jpg');
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #999999;
		}
	</style>
	<!-- Javascript goes in the document HEAD -->
	<script type="text/javascript">
		function altRows(id){
			if(document.getElementsByTagName){  
		
				var table = document.getElementById(id);  
				var rows = table.getElementsByTagName("tr"); 
		 
				for(i = 0; i < rows.length; i++){          
					if(i % 2 == 0){
						rows[i].className = "evenrowcolor";
					}else{
						rows[i].className = "oddrowcolor";
					}      
				}
			}
		}

		window.onload=function(){
			altRows('alternatecolor');
		}
	</script>


	<!-- CSS goes in the document HEAD or added to your external stylesheet -->
	<style type="text/css">
		table.altrowstable {
			font-family: verdana,arial,sans-serif;
			font-size: 11px;
			color: #333333;
			border-width: 1px;
			border-color: #a9c6c9;
			border-collapse: collapse;
		}
		table.altrowstable th {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #a9c6c9;
		}
		table.altrowstable td {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #a9c6c9;
		}
		.oddrowcolor{
			background-color: #d4e3e5;
		}
		.evenrowcolor{
			background-color: #c3dde0;
		}
	</style>

</head>
<body>
<h1>Rebuild Gallery</h1>
<h2>Running this program will do the following.
	<ol>
		<li>Queries the local database for all effects users have created. It sorts this list by the number of effects.</li>
		<li>For each user it finds it will create a target model called M16_50. This is a 16x50 , half megatree. This will be used as the target for the gallery to be generated against.</li>
		<li> Now for each effect, call the effect and generate a thumbnail gif.</li>
	</ol>
</h2>
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
	  
	  
This will rebuild any missing gif images by looking at every effect in the effects_user_hdr table. 
	  
We need a common target for the regeneration. This program will copy ../targets/2/M16_50.dat to each user if it is missing. This a 16x50 half megatree
	  
After running this program rebuild the gallery database by running this
	  
http://localhost/nutcracker/effects/create_gallery.php

*/
{
	
	while(ob_get_level()){
		ob_end_flush();
	}
	// start output buffering
	if(ob_get_length() === false){
		ob_start();
	}
	set_time_limit(0);
	//ini_set("memory_limit","2024M");
	//

	set_error_handler("customError"); // use our custom error report
	error_reporting(E_ALL ^ E_NOTICE);
	//
	
	
	require_once('../conf/header.php');
	require_once("../effects/read_file.php");
	require_once ("../effects/f_bars.php");
	require_once ("../effects/f_spirals.php");
	require_once ("../effects/f_butterfly.php");
	require_once ("../effects/f_countdown.php");
	require_once ("../effects/f_fire.php");
	require_once ("../effects/f_garlands.php");
	require_once ("../effects/f_text.php");
	require_once ("../effects/f_color_wash.php");
	require_once ("../effects/f_gif.php");
	require_once ("../effects/f_life.php");
	require_once ("../effects/f_meteors.php");
	require_once ("../effects/f_pinwheel.php");
	require_once ("../effects/f_snowstorm.php");
	require_once ("../effects/f_user_defined.php");
	require_once ("../effects/f_pictures.php");
	require_once ("../effects/f_single_strand.php");
	require_once ("../effects/f_snowflakes.php");
	require_once ("../effects/f_twinkle.php");
	require_once ("../effects/f_layer.php");
	require_once ("../effects/f_tree.php");
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	$users=array('frankr','brownout');
	$users_array=get_users5();
	echo "<pre>";
	//print_r($users_array);

	foreach($users_array as $arr){
		extract($arr);
	
	
	
		//foreach($users as $username)
		if($cnt>1 and $cnt<999){ // for every user that has at least 2 effects 
			build_effects_for_user($username,$script_start,$cnt);		
		}
	}
	echo "<h3>End of Program. N=$n</h3>";
}
function flush_buffers(){ 
	ob_end_flush(); 
	ob_flush(); 
	flush(); 
	ob_start(); 
} 
function build_effects_for_user($username,$script_start,$cnt){
	$running_elapsed=elapsed($script_start);
	$effect_elapsed=elapsed($effect_start);
	$effect_elapsed_buff=sprintf("%5.2f",$effect_elapsed);
	$running_elapsed_buff=sprintf("%5.1f",$running_elapsed/60);
	//
	$member_id = get_member_id($username);
	echo "<h1>User $username has $cnt effects: member id =$member_id. Elapsed time so far = $running_elapsed_buff minutes</h1>\n";
	show_effects_for_user($username);
	extract ($_GET);
	flush_buffers();

	
	$effect_elapsed_buff=$running_elapsed_buff =$size_buff="0.0";
	$number_gifs=0;
	$n=0;
	
	$model_name="M16_50";
	$target=copy_target($model_name,$username);

	
	
	echo "<table border=1>";
	//echo "<table class=\"altrowstable\" id=\"alternatecolor\" >\n";
	/*echo "<tr><th>#</th>";
	echo "<th>Thumbnail gif name</th>";
	echo "<th>(USER,TARGET,EFFECT,START(s),END(s),FRAME(ms)</th>";
	echo "<th>Status</th>";
	echo "<th>Effect<br/>Creation>br/>Time</th>";
	echo "<th>Elapsed<br/>Time</th>";
	echo "<th>Cleanup after<br/>Building effect</th>";
	echo "</tr>\n";*/
	//
	$user_effects_array=get_usr_effects($username);
	$dir="workspaces";
	$line=0;
	$frame_cnt=50;
	$project_id=31;
	$frame_delay=100;
	$effect_elapsed=$running_elapsed=0;
	//	
	$st=0;
	$end=5;
	echo "<tr>";
	date_default_timezone_set('MST');
	foreach($user_effects_array as $effects_array){
		$line++;
		$effect_name=strtoupper($effects_array['effect_name']);
		if(strlen($effect_name)<1){
			echo "<td>Error: Effect name is blank</td>\n";
			continue;
		}
		
		$filename = strtoupper($model_name) . '~' . $effect_name . "_th.gif";
		$fullpath = "workspaces/$member_id/" . $filename;
		list($usec, $sec) = explode(' ', microtime());
		$effect_start = (float) $sec + (float) $usec;
		$get=get_user_effects($model_name,$effect_name,$username);
		$target_info=get_info_target($username,$model_name);
		//	echo "<pre>";
		//		print_r($target_info);	
		/*	Array
		(
		[target_name] => M16_50
		[model_type] => MTREE
		[total_strings] => 32
		[pixel_count] => 50
		[pixel_length] => 3.00
		[pixel_spacing] => 
		[unit_of_measure] => 
		[topography] => 
		)*/

		//		echo "</pre>\n";
		extract($target_info);
		$batch_type=3;
		$get['batch']=$batch_type;
		$get['username']=$username;
		$get['user_target']=$model_name;
		$get['seq_duration']=($end-$st);
		$frame_delay=100;
		$get['frame_delay']=$frame_delay;
		$effect_class=$get['effect_class'];
		$today = date("F j, g:i a e");
		$today = date("g:i a");
		//
		//
		
		
		
	
		$effect_name = str_replace(' ', "_", $effect_name);
		$effect_name = str_replace("&", "_", $effect_name);
		$effect_name = str_replace("'", "_", $effect_name);
		$effect_name = str_replace(":" , "_", $effect_name);
		
		$pos  = strrpos($effect_name, " ");
		$pos2 = strrpos($effect_name, "&");
		$pos3 = strrpos($effect_name, '\'');
		if(strpos($effect_name, '\'') !== FALSE)		$pos3=1;
		$pos4 = strrpos($effect_name, '\:');
		$pos5 = strrpos($effect_name, '\/');
		/*	echo "<tr><td>$line &nbsp;</td><td>$fullpath ($pos,$pos2,$pos3)</td>";*/
		$running_elapsed=elapsed($script_start);
		$effect_elapsed=elapsed($effect_start);
		$effect_elapsed_buff=sprintf("%5.2f",$effect_elapsed);
		$running_elapsed_buff=sprintf("%5.1f",$running_elapsed/60);
		if($pos >0){ 
			$color="#959595";
			$status='blank.';
			//	echo "<td style=\"background-color:lightred;\">skipping because effect name has a blank</td>";
			$size=0;
		
		}
		elseif($pos2 >0){ 
			$color="#959595";
			$status=':ampersand';
			
			$size=0;
		
		}
		elseif($pos3 >0){ 
			$color="#959595";
			$status=':apostrophe';
			$size=0;
		}
		elseif($pos4 >0){ 
			$color="#959595";
			$status=':colon';
			$size=0;
		}
		elseif($pos5 >0){ 
			$color="#959595";
			$status=':forward_slash';
			$size=0;
		}
		// skip effects that can have problems or that take huge amount of time (snowstorm))
		elseif($effect_class=='single_strand' or $effect_class=='snowstorm'
			or $effect_class=='gif' or $effect_class=='layer'  or $effect_class=='user_defined'){
			$color="#959595";
			$status=': skipping';
			//	echo "<td style=\"background-color:lightred;\">skipping certain effects for rebuild</td>";


			$size=0;
			
			/*echo "<td bgcolor=\"$color\">$status</td><td>".$effect_elapsed_buff.
			"s</td><td>".$running_elapsed_buff."m $today</td>";
			echo "<td>$size_buff mbytes cleaned up</td></tr>\n";*/
		}
		elseif(file_exists($fullpath)){
			$color="#99FF00";
			$status=': ok';
			//echo "<td style=\"background-color:lightgreen;\">Gif already exists, skipping rebuild</td>";
			$size=0;
			
			/*	echo "<td bgcolor=\"$color\">$status</td><td>".$effect_elapsed_buff.
			"s</td><td>".$running_elapsed_buff."m $today</td>";
			echo "<td>$size_buff mbytes cleaned up</td></tr>\n";*/
		}
		else{
			
			$color="#9999FF";
			//	usleep(100000); // sleep for 100000 usec or 0.1 sec
			$status=':rebuilding';
			update_effect_name($username,$effect_name); // fix effect name, if needed
			//	echo "<td>run_effect($username, $model_name, $effect_name, $st, $end, $frame_delay)</td>";
			
			$stat=run_effect($username, $model_name, $effect_name, $st, $end, $frame_delay);
			$size=clean_all($member_id);
			$running_elapsed=elapsed($script_start);
			$effect_elapsed =elapsed($effect_start);
			$effect_elapsed_buff =sprintf("%5.2f",$effect_elapsed);
			$running_elapsed_buff=sprintf("%5.1f",$running_elapsed/60);
			
			$size_buff=sprintf("%5.1f",$size);
			/*echo "<td bgcolor=\"$color\">$status</td><td>".$effect_elapsed_buff.
			"s</td><td>".$running_elapsed_buff."m $today</td>";
			echo "<td>$size_buff mbytes cleaned up</td></tr>\n";*/
		}
		if($line%10==1) echo "<tr>";
		echo "<td bgcolor=\"$color\">$line $status</td>";
		if($line%10==0) echo "</tr>";
	
		// wait for 2 seconds
	
		/*if(ob_get_level()>1){ob_flush();}
		flush();*/
		flush_buffers();
		//print_r($user_effects_array);
		//$n=getFilesFromDir($dir,$n);
	
	}
	echo "</tr>\n";
	echo "</table>\n";
}

function copy_target($model_name,$username){
	$member_id = get_member_id($username);
	
	$file    = realpath("../targets/2/$model_name" . ".dat");
	$target    = realpath('../targets');
	/*$path_parts = pathinfo($file);
	echo "<pre>Path parts for $file:\n";
	echo "dirname    " . $path_parts['dirname'], "\n";
	echo  "basename    " .$path_parts['basename'], "\n";
	echo  "extension    " .$path_parts['extension'], "\n";
	echo  "filename    " .$path_parts['filename'], "\n"; 
	echo "</pre>\n";*/
	/*Path parts for C:\xampp\htdocs\nutcracker\targets\2\A.dat:
	dirname    C:\xampp\htdocs\nutcracker\targets\2
	basename    A.dat
	extension    dat
	filename    A*/


	$directory = "$target/$member_id";
	if(file_exists($directory)){
	}else{
		echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}

	$file  = "$target/2/$model_name" . ".dat";
	$newfile =  "$target/$member_id/$model_name" . ".dat";
	// C:\xampp\htdocs\nutcracker\targets\2
	//echo "<pre>copy($file, $newfile)</pre>\n";
	
	
	
	if(!copy($file, $newfile)){
		echo "failed to copy $file to $newfile\n";
	}
	else{
		//	echo "<h3>New target copied to $newfile</h3>\n";
	}
	// and object_name='M16_50'
	$row_array=execute_sql("select * from models where username='$username' and object_name='M16_50'");
	$cnt=count($row_array);
	if($cnt==0){
		/*	username	object_name	object_desc	model_type	string_type	pixel_count	folds	start_bottom	pixel_first	pixel_last	pixel_length	pixel_spacing	unit_of_measure	total_strings	total_pixels	window_degrees	number_segments	gif_model	direction	orientation	topography	h1	h2	d1	d2	d3	d4	start_channel	date_created	last_updated*/
		$row_array=execute_sql("insert into models (username,object_name,object_desc,model_type,
			pixel_count,folds,start_bottom,	pixel_first,pixel_last,pixel_length,	total_strings,
			total_pixels,window_degrees,gif_model,start_channel,date_created,last_updated) values
			('$username','$model_name','16x40 Megatree used for rebuilding Gallery','MTREE',
			50,1,'Y',1,50,3.00,32,50,180,'single',1,now(),now())");
	}
	$member_id=get_member_id($username);
	//echo "<H2>User name $username. Member id = $member_id</h2>\n";
	return $target;
}
function execute_sql($query){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		return('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		return("Unable to select database");
	}
	//echo "<pre>execute_sql: query=$query</pre>\n";
	$result=mysql_query($query) or die ("Error on $query");
	$row_array=array();
	if($result){
		while($row = mysql_fetch_assoc($result)){
			extract($row);
			$row_array[]=$row;
		}
	}
	return ($row_array);
}
function clean_all($member_id){
	$size=0;
	$dir = "workspaces/$member_id";
	$size+=cleanup( "$dir/*.dat");
	$size+=cleanup( "$dir/*.srt");
	$size+=cleanup( "$dir/*.nc");
	return $size;
}
function get_usr_effects($username){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		return('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		return("Unable to select database");
	}
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$query = "select * from effects_user_hdr where username='$username' order by effect_name ";
	$user_effects_array=array();
	$result=mysql_query($query) or die ("Error on $query");
	if($result){
		while($row = mysql_fetch_assoc($result)){
			extract($row);
			$user_effects_array[]=$row;
		}
	}

	return ($user_effects_array);
}
function get_users5(){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		return('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		return("Unable to select database");
	}
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$query = "select username,count(*) cnt from effects_user_hdr 
	group by username order by 2 desc";
	$user_effects_array=array();
	$result=mysql_query($query) or die ("Error on $query");
	while($row = mysql_fetch_assoc($result)){
		extract($row);
		$user_array[]=$row;
	}

	return ($user_array);
}
function show_effects_for_user($username){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		return('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		return("Unable to select database");
	}
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$query = "select * from effects_user_hdr where username='$username'
	order by effect_name";
	$user_effects_array=array();
	$result=mysql_query($query) or die ("Error on $query");
	echo "<table class=\"imagetable\">\n";
	$column=0;
	while($row = mysql_fetch_assoc($result)){
		extract($row);
		$column++;
		if($column%10 == 1) echo "<tr>";
		$effect_name_orig=$effect_name;
		$effect_name = str_replace(' ', "_", $effect_name);
		$effect_name = str_replace("&", "_", $effect_name);
		$effect_name = str_replace("'", "_", $effect_name);
		$effect_name = str_replace(":" , "_", $effect_name);
		$display=$effect_name;
		if($effect_name_orig!=$effect_name) $display="$effect_name($effect_name_orig)";
		echo "<td>$column $display:$effect_class</td>";
		if($column%10 == 0) echo "</tr>\n";
	}
	echo "</table>\n";

	return ;
}
function get_eff_class($username,$effect_name){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		return('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		return("Unable to select database");
	}
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$clean_effect_name=mysql_real_escape_string ($effect_name);
	$query = "select effect_class from effects_user_hdr where username='$username' and effect_name='$clean_effect_name'";
	$effect_class='?';
	$result=mysql_query($query) or die ("Error on $query");
	while($row = mysql_fetch_assoc($result)){
		extract($row);
	}
	return ($effect_class);
}



function elapsed($script_start){
	
	
	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
	$buff = sprintf ("%10.5f",$elapsed_time);
	return $buff;
}
function  run_effect($username, $model_name, $effect_name, $st, $end, $frame_delay){
	$member_id = get_member_id($username);
	$directory = "workspaces/$member_id";
	if(file_exists($directory)){
	}else{
		echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	//echo "<td>run_effect($username, $model_name, $effect_name, $st, $end, $frame_delay)</td></tr><tr>\n";
	// this function will create the batch call to the effects to create the individual nc files
	$member_id=get_member_id($username);
	$workdir="workspaces/$member_id";
	$outfile=$workdir."$model_name~$effect_name.nc";
	$isValid=true;
	
	$batch_type=3;
	$get=get_user_effects($model_name,$effect_name,$username);
	$get['batch']=$batch_type;
	$get['username']=$username;
	$get['user_target']=$model_name;
	$get['seq_duration']=($end-$st);
	$get['frame_delay']=$frame_delay;
	$effect_class=$get['effect_class'];
	
	// need code to grab the 360 value from the model -- right now the model does not contain 360 info (but will for the future)
	//$get['windows_degrees']=360;  // default the degrees to 360.  This will have to change for the future.
	//$from_file="../effects/workspaces/$member_id/$model_name~$effect_name.nc";
	//$to_file="../project/workarea/$username~$model_name~$effect_name~$frame_cnt.nc";
		
	$ranNC=false;
	//	echo "<td> run_effects is calling $effect_class</td>";
	//
	//	check if effect is already there. this will indicate we nnever finished from a precious run.
	$count = effect_in_audit($username,$effect_name);
	if($count>0){
		echo "<pre>effect_in_audit(username,effect_name) = effect_in_audit($username,$effect_name) failed from previous run</pre>\n";
		return "Skipping $effect_name($effect_class)";
	}
	insert_audit($username,$effect_name);
	//echo "<td>switch(effect_class)=switch($effect_class)</td>";
	switch($effect_class){
		case ('spirals') :
		f_spirals($get);
		$ranNC=true;
		break;
		case ('fire') :
		f_fire($get);
		$ranNC=true;
		break;
		case ('butterfly') :
		f_butterfly($get);
		$ranNC=true;
		break;
		case ('bars') :
		f_bars($get);
		$ranNC=true;
		break;
		case ('countdown') :
		f_countdown($get);
		$ranNC=true;
		break;
		case ('garlands') :
		f_garlands($get);
		$ranNC=true;
		break;
		case ('text') :
		f_text($get);
		$ranNC=true;
		break;
		case ('gif') :
		f_gif($get);
		$ranNC=true;
		break;
		case ('meteors') :
		f_meteors($get);
		$ranNC=true;
		break;
		case ('life') :
		f_life($get);
		$ranNC=true;
		break;
		case ('color_wash') :
		f_color_wash($get);
		$ranNC=true;
		break;
		case ('user_defined') :
		f_user_defined($get);
		$ranNC=true;
		break;
		case ('snowstorm') :
		f_snowstorm($get);
		$ranNC=true;
		break;
		case ('pictures') :
		f_pictures($get);
		$ranNC=true;
		break;
		case ('pinwheel') :
		f_pinwheel($get);
		$ranNC=true;
		break;
		case ('single_strand') :
		f_single_strand($get);
		$ranNC=true;
		break;
		case ('layer') :
		f_layer($get);
		$ranNC=true;
		break;
		case ('snowflakes') :
		f_snowflakes($get);
		$ranNC=true;
		break;
		case ('twinkle') :
		f_twinkle($get);
		$ranNC=true;
		break;
		case ('tree') :
		f_tree($get);
		$ranNC=true;
		break;
		default :
		//echo "<pre>Effect class [$effect_class] not handled yet</pre>";
	}
	delete_audit($username,$effect_name);
	return $effect_class;
}
function insert_audit($username,$effect_name){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		return('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		return("Unable to select database");
	}
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$query = "insert into  effect_audit (username,effect_name,last_update) values
	('$username','$effect_name',now())";
	//	echo "<td>query=$query</td>";
	$result=mysql_query($query) or die ("Error on $query");
}
function effect_in_audit($username,$effect_name){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		return('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$cnt=0;
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		return("Unable to select database");
	}
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$query = "select count(*) cnt from effect_audit where username='$username' and effect_name='$effect_name'";
	//	echo "<td>query=$query</td>";
	$result=mysql_query($query) or die ("Error on $query");
	while($row = mysql_fetch_assoc($result)){
		extract($row);
	}
	return $cnt;
}
function delete_audit($username,$effect_name){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		return('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		return("Unable to select database");
	}
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$query = "delete from effect_audit where username='$username' and effect_name='$effect_name'";
	//	echo "<td>query=$query</td>";
	$result=mysql_query($query) or die ("Error on $query");
}

function cleanup($pattern){
	
	$size=0;

	foreach(glob($pattern) as $filename){
		//	echo "$filename size " . filesize($filename) . "\n";
		$size+=filesize($filename);
		unlink($filename);
		//if (ob_get_level()>1) {ob_flush();}
		//flush();
	}
	$size=$size/1024/1024;
	//echo "<pre>$pattern has deleted " . $size . " megabytes</pre>\n";
	return $size;
}

/*[effect_id] => 9
[username] => f
[effect_name] => SEAN
[param_name] => effect_name
[param_value] => 
[segment] => 0
[created] => 2012-12-10 15:33:28
[last_upd] => 2012-12-10 15:33:28*/
function update_effect_name($username,$effect_name){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		return('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		return("Unable to select database");
	}
	// effect_class	username	effect_name	effect_desc	created	last_upd
	$query = "update effects_user_dtl set param_value='$effect_name'
	WHERE  username='$username' and effect_name='$effect_name' and param_name = 'effect_name'";
	//	echo "<td>query=$query</td>";
	$result=mysql_query($query) or die ("Error on $query");
}