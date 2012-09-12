<?php
require_once('../conf/auth.php');
require_once('../conf/barmenu.php');
require_once('project_filer.php');
require_once ("../effects/f_bars.php");
require_once ("../effects/f_spirals.php");
require_once ("../effects/f_butterfly.php");
require_once ("../effects/f_fire.php");
//require_once ("../effects/f_garlands.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="../js/barmenu.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nutcracker: RGB Effects Builder</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="last-modified" content=" 24 Feb 2012 09:57:45 GMT"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
<meta name="robots" content="index,follow"/>
<meta name="googlebot" content="noarchive"/>
<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"> 
<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../css/barmenu.css">
<link href="../css/ncFormDefault.css" rel="stylesheet" type="text/css" />
</head>
<body>
	
<?php show_barmenu();
//
require("../effects/read_file.php");
set_time_limit(60*60);
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];
extract($_GET);
$msg_str="";
if (isset($type)) {
	switch ($type) {
		case 1:
			$msg_str=select_song($username);
			break;
		case 2:
			if (isset($project_id)) {
				$msg_str=edit_song($project_id);
			} else {
				$msg_str = "***Error occurred *** no project id<br />";
			}
			break;
		case 3:
			if (isset($project_id)) {
				$msg_str=remove_song($project_id);
			} else {
				$msg_str="***Error occurred *** no project id<br />";
			}
			break;
		default:
			$msg_str= "***Error occurred *** Invalid value for function call<br />";
	}
} else {
	extract($_POST);
	if (isset($NewProjectCancel)) {
		$msg_str="*** Song add was cancelled ***";
	} 
	if (isset($NewProjectSubmit)) {
		//echo "$song_id  , $username,   $frame_delay, $model_name <br />";
		$msg_str=add_song($song_id,$username, $frame_delay, $model_name);
	}
	if (isset($SavePhraseEdit)) {
		save_phrases($_POST);
		$msg_str=edit_song($project_id);
		$msg_str="Edit Saved";
	}
	if (isset($CancelPhraseEdit)) {
		$msg_str="Song detail hidden";
	}
	if (isset($MasterNCSubmit)) {
		$myarray=checkGaps($project_id);
		$projectArray=setupNCfiles($project_id,$myarray);
		$myNCarray=prepMasterNCfile($project_id);
		processMasterNCfile($project_id, $projectArray, $myNCarray);
	}
}
echo $msg_str;

?>
<h2>Current Nutcracker projects</h2>
<form action="<?php echo "project-exec.php"; ?>" method="post">
<input type="hidden" name="username"     value="<?php printf ("$username");    ?> "/>
<table border=1>
<tr>
<th>Song Name</th>
<th>Artist</th>
<th>Purchase song from here</th>
<th>Model</th>
<th>Frame Timing (ms)</th>
<th>Commands</th>
</tr>
<?php
	$sql = "SELECT project_id, song.song_id as song_id, song_name, artist, song_url, frame_delay, model_name FROM project LEFT JOIN song ON project.song_id = song.song_id WHERE username='$username' ORDER BY song_name, model_name";
	//echo "$sql <br />";
	$result = nc_query($sql);
	$cnt=0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$cnt +=1;
		$project_id = $row['project_id'];
		$song_id = $row['song_id'];
		$artist = $row['artist'];
		$song_name = $row['song_name'];
		$song_url = $row['song_url'];
		$frame_delay = $row['frame_delay'];
		$model_name = $row['model_name'];
	?>
<tr>
	<td><?php echo $song_name?></a></td>
	<td><?php echo $artist?></td>
	<td><a href="<?php echo $song_url?>"><?php echo $song_url?></a></td>
	<td><?php echo $model_name?></td>
	<td><?php echo $frame_delay?></td>
	<td><a href="project.php?type=2&project_id=<?php echo $project_id?>"><img src="../images/edit.png">Edit</a>&nbsp;&nbsp;&nbsp;<a href="project.php?type=3&project_id=<?php echo $project_id?>"><img src="../images/delete.png">Remove</a></td>
</tr>
<?php		
	}
	if ($cnt == 0) {
		echo "<tr><td colspan=6>You do not have any current projects</td></tr>";
	}
?>
</table>
<p />
<a href="project.php?type=1">Add a song</a><br />
<?php

function save_phrases($inphp) {
	//extract($inphp);
	//print_r($inphp);
	foreach($inphp as $key=>$val)
	{
		switch ($key) {
		case "project_id":
			$project_id = $val;
			break;
		case "frame_delay":
			$frame_delay = $val;
			if (isset($project_id)) {
				$sql="UPDATE project SET frame_delay=".$frame_delay." WHERE project_id=".$project_id;
				//echo "$sql <br />";
				$result=nc_query($sql);
			}
			break;
		case "SavePhraseEdit":
			break;
		default:
			switch (substr($key,0,3)) {
			case "en-":
				$key=(substr($key,3));
				$sql="UPDATE project_dtl SET end_secs=".$val." WHERE project_dtl_id=".$key;
				//echo "$sql <br />";				
				$result=nc_query($sql);
				break;
			case "st-":
				$key=(substr($key,3));				
				$sql="UPDATE project_dtl SET start_secs=".$val." WHERE project_dtl_id=".$key;
				//echo "$sql <br />";
				$result=nc_query($sql);
				break;
			default:
				if (strlen($val)==0) {
					$val="NULL";
				} else {
					$val="'".$val."'";
				}
				$sql="UPDATE project_dtl SET effect_name=".$val." WHERE project_dtl_id=".$key;
				//echo "$sql <br />";
				$result=nc_query($sql);
			}
		}
		//echo "$key : $val <br />";
	}
}

function get_effects($username) {
	$sql = "SELECT effect_name, effect_class FROM effects_user_hdr WHERE username='$username' AND effect_name IS NOT NULL ORDER BY effect_name";
	//echo "$sql<br />";
	$result=nc_query($sql);
	$cnt=0;
	while ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
		$effect[$cnt]=$row['effect_name'];
		$cnt+=1;
	}
	return($effect);
}

function edit_song($project_id) {
	$sql = "SELECT song_name, artist, song_url, frame_delay, username FROM project LEFT JOIN song ON project.song_id=song.song_id WHERE project_id=".$project_id;
	$result=nc_query($sql);
	$row=mysql_fetch_array($result,MYSQL_ASSOC);
	$frame_delay=$row['frame_delay'];
	$username=$row['username'];
	$song_url=$row['song_url'];
	$song_name=$row['song_name'];
	$artist=$row['artist'];
	$effect=get_effects($username);
	//print_r($effect);
	$sql = "SELECT project_dtl_id, phrase_name, start_secs, end_secs, effect_name FROM project_dtl WHERE project_id=".$project_id." ORDER BY start_secs";
	//echo "edit song SQL - $sql<br />";
	?>
	<h2>Edit Project Details for <?php echo $song_name;?> by <?php echo $artist;?></h2>
	<form name="project_edit" id="project_edit" action="project.php" method="post">
	<input type="hidden" name="project_id" id="project_id" value=<?php echo $project_id;?>>
	Frame Rate for project : <input class="FormFieldName" type="text" name="frame_delay" id="frame_delay"0 value="<?php echo $frame_delay?>"><br />
	<table border="1" cellpadding="1" cellspacing="1">
	<tr><th>Phrase</th><th>start time (sec)</th><th>end time (sec)</th><th>Effect Assigned</th></tr>
	<?php
	$result3=nc_query($sql);
	$cnt=show_phrases($result3,$effect);
	if ($cnt==0) { // if there currently are no phrases attached to project get them from the library
		insert_proj_detail_from_library($project_id);
		$result3=nc_query($sql);
		$newcnt=show_phrases($result3,$effect);
	}
	?>
	</table>
	<input type="submit" name="SavePhraseEdit"  class="SubmitButton" value="Save these values">&nbsp;&nbsp;&nbsp;<input type="submit"  class="SubmitButton" name="CancelPhraseEdit" value="Hide Detail">
	<p />
	<input type="submit" name="MasterNCSubmit" class="SubmitButton" value="Output Project">
	</form>
	<?php
	//echo "There are $cnt records in details <br />";
	return;
}

function show_phrases($inresult,$effect) {
	$cnt=0;
	while ($row = mysql_fetch_array($inresult, MYSQL_ASSOC)) {
		$cnt +=1;
		$project_dtl_id = $row['project_dtl_id'];
		$phrase_name = $row['phrase_name'];
		$start_secs = $row['start_secs'];
		$end_secs = $row['end_secs'];
		$effect_name = $row['effect_name'];
		$effect_str=effect_select($effect,$effect_name,$project_dtl_id);
		echo "<tr><td class=\"FormFieldName\">$phrase_name</td><td class=\"FormFieldName\" ><input type=\"text\" class=\"FormFieldName\" value=\"$start_secs\" name=\"st-$project_dtl_id\"></td><td class=\"FormFieldName\" ><input type=\"text\" class=\"FormFieldName\" value=\"$end_secs\" name=\"en-$project_dtl_id\"></td><td class=\"FormFieldName\" >$effect_str</td></tr>";
		// echo "$phrase_name : $start_secs : $end_secs : $effect_name<br />";
	}
	return($cnt);
}

function effect_select($effect_array, $ineffect, $project_dtl_id) {
	$retStr='<select class="FormFieldName" name='.$project_dtl_id.' id='.$project_dtl_id.'>';
	if (strlen($ineffect)==0) {
		$defstr=" selected";
	} else {
		$defstr="";
	}
	$retStr.='<option value=""'.$defstr.'>No Effect Selected</option>';
	foreach ($effect_array as $effect) {
		if ($effect == $ineffect) {
			$defstr = " selected";
		} else {
			$defstr = "";
		}
		$retStr.='<option value="'.$effect.'"'.$defstr.'>'.$effect.'</option>';
	}
	$retStr.='</select>';
	return($retStr);
}

function nc_query($sql) {
	//echo "sql = $sql<br />";
	require_once('../conf/config.php');
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	$result = mysql_query ($sql, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
	return($result);
}

function insert_proj_detail_from_library($project_id) {
	$sql = "SELECT song_id FROM project WHERE project_id = ".$project_id;
	$result=nc_query($sql);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$song_id=$row['song_id'];
	$sql = "SELECT phrase_name, start_secs, end_secs FROM song_dtl where song_id = ".$song_id;
	$cnt=0;
	//echo "$sql<br />";
	$result2=nc_query($sql);
	while ($row = mysql_fetch_array($result2, MYSQL_ASSOC)) {
		$cnt +=1;
		$phrase_name = $row['phrase_name'];
		$start_secs = $row['start_secs'];
		$end_secs = $row['end_secs'];
		$sql="INSERT INTO project_dtl (phrase_name, start_secs, end_secs, project_id) VALUES ('".$phrase_name."',".$start_secs.",".$end_secs.",".$project_id.")";
		//echo "$sql <br />";
		$result3=nc_query($sql);
	}
	echo "Inserted $cnt new records into project detail<br />";
	return;
}

function remove_song($project_id) {
	$sql = "SELECT song_id, model_name FROM project where project_id=".$project_id;
	// echo "$sql <br />";
	$result2 = nc_query($sql);
	$row = mysql_fetch_array($result2, MYSQL_ASSOC);
	$song_id = $row['song_id'];
	$model_name = $row['model_name'];
	$sql = "DELETE FROM project_dtl WHERE project_id=$project_id";
	$result=nc_query($sql);
	$sql = "DELETE FROM project WHERE project_id=$project_id";
	$result = nc_query($sql);
	$song_name=getSongName($song_id);
	return("Song '$song_name' and Model '$model_name' removed");
}

function add_song($song_id, $username, $frame_delay, $model_name) {
	$song_name=getSongName($song_id);
	$sql2 = 'Select count(*) as songcnt from project WHERE song_id='.$song_id.' AND username="'.$username.'" AND model_name="'.$model_name.'"';
	$sql = "REPLACE INTO project (song_id, username,frame_delay, model_name) VALUES (".$song_id.",'".$username."',".$frame_delay.",\"".$model_name."\")";
	//echo "$sql <br />";
	//echo "$sql2 <br />";
	$result2 = nc_query($sql2);
	$row = mysql_fetch_array($result2, MYSQL_ASSOC);
	if ($row['songcnt'] > 0) {
	return("*** Add Canceled *** Song '$song_name' and Model '$model_name' already exists!");
	} else {
	$result =nc_query($sql);
	return("Song '$song_name' and Target '$model_name' added");
	}
}

function getSongName($song_id) {
	$retVal = "Error occured";
	$sql = "SELECT song_name FROM song WHERE song_id='$song_id'";
	//echo "$sql <br />";
	$result = nc_query($sql);
	if ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$retVal=$row['song_name'];
	} else {
		$retVal="*** ERROR IN getSongName ***";
	}
	return($retVal);
}
function select_song($username) {
	$sql = "SELECT song_name, song.song_id, artist, song_url, min( start_secs )AS MinTime, max( end_secs )AS MaxTime\n"
     . "FROM song\n"
     . "LEFT JOIN song_dtl ON song.song_id = song_dtl.song_id\n"
     . "GROUP BY song_name, song.song_id\n";
    // . "HAVING song.song_id NOT IN (SELECT song_id from project where username='".$username."')";
	$sql2 = "SELECT object_name FROM models WHERE username='$username'";
	//echo "$sql <br />";
	//echo "$sql2 <br />";
	$result = nc_query($sql);
	?>
	<h2>Available Songs</h2>
	<table border="1" cellpadding="1" cellspacing="1">
	<?php
	$rowcnt = mysql_num_rows($result);
	if ($rowcnt == 0)
	{
		echo "<tr><th>No more songs available to add!</th></tr>";
	} else {
	?>
	<?php 
		$SongSel=parseSongs($result);
		echo $SongSel[1];?>
	</table>
	<p />
	<h2>Select a Song</h2>
	<form name="addsong" method="post" action="project.php">
	<input type="hidden" name="intype" value=2>
	<table width="375"	border="0" cellpadding="1" cellspacing="1">
	<tr>
		<td class="FormFieldName"><div align="right">Song</div></td>
		<td class="FormFieldName"><?php echo $SongSel[0];?></td>
	</tr>
	<?php 
	}	?>
    <tr>
      <td class="FormFieldName"><div align="right">Target</div></td>
      <td class="FormFieldName">
	  	 <?php	
	 	$result2 =nc_query($sql2);
		 echo parseTargetSelect($result2); ?> 
	  </td>
    </tr>
    <tr>
      <td class="FormFieldName"><div align="right">Frame Rate (ms)</div></td>
      <td class="FormFieldName"><div align="left">
      <input name="frame_delay" type="text" id="frame_delay" value="50" size="11" maxlength="11" /></div></td>
    </tr>
    <tr>
      <td><div align="center">
        <input name="NewProjectCancel" type="submit" class="SubmitButton" id="NewProjectCancel" value="Cancel" />
      </div></td>
      <td><div align="center">
        <input name="NewProjectSubmit" type="submit" class="SubmitButton" id="NewProjectSubmit" value="Submit New Song" />
      </div></td>
    </tr>
	</table>
	</form>
<?php
}
function parseTargetSelect($result) {
	$retStr='<select name="model_name" class="FormSelect" id="model_name">';
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$model_name=$row['object_name'];
		$retStr.='<option value="'.$model_name.'">'.$model_name.'</option>';
	}
	$retStr.='</select>';
	return($retStr);
}
function parseSongs($result) {
	$retVal = array();
	$retStr1='<select name="song_id" class="FormSelect" id="song_id">';
	$retStr2='<tr><td>Song Name</td><td>Song url</td><td>Length of song (sec)</td><td>Length of song (min)</td></tr>';
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$song_name=$row['song_name'];
		$song_id=$row['song_id'];
		$song_url=$row['song_url'];
		$MinTime=$row['MinTime'];
		$MaxTime=$row['MaxTime'];
		$song_length = round(($MaxTime-$MinTime),2);
		$song_length_min = round(($song_length/60),2);
		$retStr1.='<option value='.$song_id.'>'.$song_name.'</option>';
		$retStr2.='<tr><td><a href="'.$song_url.'">'.$song_name.'</a></td><td><a href="'.$song_url.'">'.$song_url.'</a><td>'.$song_length.'</td><td>'.$song_length_min.'</td></tr>';
	}
	$retStr1.='</select>';
	$retVal[0]=$retStr1;
	$retVal[1]=$retStr2;
	return($retVal);
}

function checkGaps($project_id) {  // from a finished project, start by returning an array of arrays that hold start,end,effect 
	$start_time=0.0;
	$subarray=array();
	$cnt=0;
	$outarray=array();
	$sql="SELECT phrase_name, start_secs,end_secs, effect_name FROM `project_dtl` WHERE project_id=$project_id ORDER BY start_secs"; 
	$result2=nc_query($sql);
	while ($row=mysql_fetch_array($result2,MYSQL_ASSOC)) {
		$st_time=$row['start_secs'];
		$end_time=$row['end_secs'];
		$effect_name=$row['effect_name'];
		if ($start_time < $st_time) {
			$subarray[0]=$start_time;
			$subarray[1]=$st_time;
			$subarray[2]="zzeross";
			$outarray[$cnt]=$subarray;
			$cnt+=1;
		}
		if (strlen($effect_name)==0)
			$effect_name="zzeross";
		$subarray[0]=$st_time;
		$subarray[1]=$end_time;
		$subarray[2]=$effect_name;
		$outarray[$cnt]=$subarray;
		$cnt+=1;
		$start_time=$end_time;
	}
	return($outarray);
}

function getProjInfo($project_id) { // gets project info from database and returns it as an array
	$retArray=array();
	$sql="SELECT username, frame_delay,model_name FROM project WHERE project_id=$project_id";
	$result=nc_query($sql);
	$row=mysql_fetch_array($result,MYSQL_ASSOC);	
	$retArray=$row;
	return($retArray);
}

function getFrameCnt($st,$end,$frame_delay) {
	$retVal = ($end-$st)/($frame_delay/1000);
	return($retVal);
}

function checkAccum($inval) {
	$retVal=0;
	while ($inval>1) {
		$inval--;
		$retVal++;
	}
	return($retVal);
}

function setupNCfiles($project_id,$phrase_array) {  // create each of the effect nc files (or make sure they are created for each of the times
	$proj_array=getProjInfo($project_id);
	$frame_delay=$proj_array['frame_delay'];
	$model_name=$proj_array['model_name'];
	$username=$proj_array['username'];
	$cnt=0;
	$outarray=array();
	$accumulator=0;
	foreach ($phrase_array as $curr_array) {
		$st=$curr_array[0];
		$end=$curr_array[1];
		$eff=$curr_array[2];
		$frame_cnt_raw=getFrameCnt($st,$end,$frame_delay);
		$frame_cnt=floor($frame_cnt_raw);
		$frame_cnt_remain=$frame_cnt_raw-$frame_cnt;
		if ($eff=="zzeross") {
			$outstr="zeros:$frame_cnt";
		} else {
			$outstr=createSingleNCfile($username, $model_name, $eff, $frame_cnt, $st, $end, $project_id, $frame_delay); 

		}
		$outarray[$cnt++]=$outstr;
		$accumulator+=$frame_cnt_remain;
		$zeroframecnt=checkAccum($accumulator);
		if ($zeroframecnt>0) {
			$outstr="zeros:$zeroframecnt";
			$accumulator-=$zeroframecnt;
			$outarray[$cnt++]=$outstr;
		}
	}
	return($outarray);	
}

function createSingleNCfile($username, $model_name, $eff, $frame_cnt, $st, $end, $project_id, $frame_delay) {  // this function will create the batch call to the effects to create the individual nc files
	$workdir="workarea/";
	$outfile=$workdir."$username~$model_name~$eff~$frame_cnt.nc";
	if (file_exists($outfile)) {
		echo "$outfile already exist <br />";
	} else {
		echo "Generating $outfile<br />";
		$batch_type=2;
		$get=getUserEffect($model_name,$eff,$username);
		$get['batch']=$batch_type;
		$get['username']=$username;
		$get['user_target']=$model_name;
		$get['file_out']=$outfile;
		$get['seq_duration']=($end-$st);
		$get['frame_delay']=$frame_delay;
		$effect_class=$get['effect_class'];
		//print_r($get);
		//echo "<br />";
		// code to gen a new individual nc file goes here
		switch ($effect_class) {
			case ('spirals') :
				f_spirals($get);
				break;
			case ('fire') :
				f_fire($get);
				break;
			case ('butterfly') :
				f_butterfly($get);
				break;
			case ('bars') :
				f_bars($get);
				break;
		}
	}
	return($outfile); // this will be the file created 
}

function prepMasterNCfile($project_id) {
	$proj_array=getProjInfo($project_id);
	$username=$proj_array['username'];
	$model_name=$proj_array['model_name'];
	$testarr = getHeader($model_name, $username, $project_id);
	// print_r($testarr);
	return($testarr);
}

function processMasterNCfile($project_id, $projectArray, $workArray) {
	// 
	// Code to process all the Master NC Files here
	//
	$proj_array=getProjInfo($project_id);
	$username=$proj_array['username'];
	$model_name=$proj_array['model_name'];
	$retArray=appendFiles($projectArray,$workArray);
	$outfile="$username+$project_id+master.nc";
	array2File($outfile, $retArray);
	// print_r($retArray);
	return;
}
	
function printArray($inArray) {
	foreach($inArray as $currarray) {
		print_r($currarray);
		echo "<br />";
	}
}
?>