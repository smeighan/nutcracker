<?php
function myTokenizer($in_str, $sepStr=" ") { // Takes a string and returns an array seperated by value of $sepStr 
	$outarray=array();
	$words = preg_split('/\s/', $in_str);
	foreach ($words as $word) 
		if (strlen(trim($word))!=0) 
			$outarray[]=$word;
	return($outarray);
}

function revTokenizer($in_array,$stripHeader=false, $sepStr=" ") { // Reverse of myTokenizer.  Takes an array and reverts it to a string
	if ($stripHeader) 
		$starti = 4;
	else
		$starti = 0;
	$lenarray = count($in_array);
	$retVal = "";
	for ($x=$starti;$x<$lenarray;$x++)
		$retVal.= $in_array[$x].$sepStr;
	$retVal=rtrim($retVal);
	return($retVal);
}

function isInvalidLine($in_str) { // returns false if the string starts with a # or is empty or is not set (null value)
	$outVal=false;
	if (isset($in_str)==false) { 
		$outVal=true;
	}
	if (strlen(trim($in_str))==0) {
 		$outVal=true;
	} else {
		if (substr($in_str,0,1)=="#") { 
			$outVal=true;
		}
	}
	return($outVal);
}

function appendStr($str_array1,$str_array2,$prepend=false, $sepStr=" ") {  // takes two array of strings and appends them together
	$retArray = array();
	if (count($str_array1) != count($str_array2)) {
		echo "*** ERROR *** arrays must match length!<br />";
	} else { 
		$y=count($str_array1);
		for($x=0;$x<$y;$x++) { 
			if ($prepend) { 
				$retArray[$x]=$str_array2[$x].sepStr.$str_array1[$x];
			} else {
				$retArray[$x]=$str_array1[$x].$sepStr.$str_array2[$x];
			}
		}
	}
	return($retArray);
}

function appendZeros($str_array1, $numZeros, $prepend=false, $sepStr=" ") {  // takes an array of strings and appends a series of zeros (prepends if prepend is true) to the array
	$retArray = array();
	$arr2 = array();
	$lenarr = count($str_array1);
	$zStr=rtrim(str_repeat("0 ",$numZeros));
	for ($x=0;$x<$lenarr;$x++)
		$arr2[$x]=$zStr;
	$retArray=appendStr($str_array1,$arr2, $prepend, $sepStr);
	return($retArray);
}

//
// opens file in $infile and returns an array of strings where each string is a line of data from the file. 
// if $stripHeader is set to true, the function will strip out the first four columns of data (aka the S X P X data)
//
function getFileData($infile,$stripHeader=false, $sepStr=" ") {  
	$retArray = array();
	$f = fopen ($infile, "r");
	$ln= 0;
	while ($line= fgets ($f)) {
		if ($line) {
			if (isInvalidLine($line) == false) {
				$myArray=myTokenizer($line, $sepStr);
				$myStr=revTokenizer($myArray,$stripHeader, $sepStr);
				$retArray[$ln]=$myStr;
				$ln++;
			}
		}
    }
	fclose ($f);
	return($retArray);
}
// 
// opens an array of files in the nc format and returns an array with the values appended
// Array of files need to be of the format <filename> surrounded by quotes which delinates the file to be appended.
// Array of files can also have the format zero:<number> surround by quotes which delinates the number of frames as zero to be appended.
// 
function appendFiles($in_filearray, $prepArray, $sepStr=" ") { 
//	$stripHeader=false;
	$retarr=$prepArray;
	//print_r($retarr);
	foreach($in_filearray as $infile) {
		if (substr($infile,0,6)=="zeros:") {
			$val=substr($infile,6);
			echo "Appending $val zeros to master<br />";
			if (isset($retarr)) {
				$retarr=appendZeros($retarr,$val,false,$sepStr);
				//echo "VAL : $val <br />";
				// print_r($retarr);
			}
		} else {
			echo "Appending file $infile to master<br />";
			$myarr=getFileData($infile,true,$sepStr);
			// print_r($myarr);
			$retarr=appendStr($retarr,$myarr);
			//print_r($retarr);
		}
	}
	return($retarr);
}

function array2File($outfile,$outarray) {//writes out a file from an array
	echo "Writing to $outfile \n";
	$f=fopen($outfile,"w");
	foreach($outarray as $line) {
		fwrite($f, $line."\n");
	}
	fclose($f);
}

function createHeader($outfile,$model_name, $username, $project_id, $sepStr=" "){
	$sql="SELECT member_id FROM members WHERE username='".$username."'";
	$result=nc_query($sql);
	$row=mysql_fetch_array($result,MYSQL_ASSOC);
	$member_id=$row['member_id'];
	$mydir='../targets/'.$member_id.'/';
	$model_file=$mydir.$model_name.".dat";
	$retArray = array();
	$f = fopen ($model_file, "r");
	$w = fopen ($outfile, "w");
	$outstr="#   project id $project_id\n#   target name $model_name\n";
	fwrite($w, $outstr);
	while ($line= fgets ($f)) {
		if ($line) {
			if (isInvalidLine($line) == false) {
				$myArray=myTokenizer($line, $sepStr);
				$outstr = "S".$sepStr.$myArray[1].$sepStr."P".$sepStr.$myArray[2]."\n";
				fwrite($w, $outstr);
			}
		}
    }
	fclose ($f);
	fclose ($w);
}

function getHeader($model_name, $username, $project_id, $sepStr=" "){
	$sql="SELECT member_id FROM members WHERE username='".$username."'";
	//echo "$sql <br />";
	$stripHeader = true;
	$result=nc_query($sql);
	$row=mysql_fetch_array($result,MYSQL_ASSOC);
	$member_id=$row['member_id'];
	$mydir='../targets/'.$member_id.'/';
	$model_file=$mydir.$model_name.".dat";
	echo "$model_file<br />";
	$retArray = array();
	$f = fopen ($model_file, "r");
	$ln= 0;
	while ($line= fgets ($f)) {
		if ($line) {
			if (isInvalidLine($line) == false) {
				$myArray=myTokenizer($line, $sepStr);
				$tempStr="S ".$myArray[1]." P ".$myArray[2];
				$retArray[$ln]=$tempStr;
				$ln++;
			}
		}
    }
	fclose ($f);
	return($retArray);
}
function getMemberID($username) 
{
	$sql = "select member_id from members where username='$username'";
	$result = nc_query($sql);
	$retval = "";
	if ($row=mysql_fetch_assoc($result))
		$retval=$row['member_id'];
	return ($retval);
}

function getUserEffect($target,$effect,$username)
{
	$sql = "SELECT hdr.effect_class,hdr.username,hdr.effect_name,
	hdr.effect_desc,hdr.music_object_id,
	hdr.start_secs,hdr.end_secs,hdr.phrase_name,
	dtl.segment, dtl.param_name,dtl.param_value
	FROM `effects_user_hdr` hdr, effects_user_dtl dtl
	where hdr.username = dtl.username
	and hdr.effect_name = dtl.effect_name
	and hdr.username='$username'
	and upper(hdr.effect_name)=upper('$effect')";
	// echo "$sql <br />";
	$result = nc_query($sql);
	$cnt=0;
	$string="";
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);		//	if(strncmp($param_name,"background_color",strlen("background_color"))==0 and strncmp($param_value,"#",1)==0) $param_value=hexdec($param_value);
		$string = $string . "&" . $param_name . "=" . $param_value;
		$get[$param_name]=$param_value;
	}
	// we also need teh effect class from the header
	$get['effect_class']=$effect_class;
	return $get;
}
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
?>
<!-- Progress bar holder -->
<div id="progress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress information -->
<div id="information" style="width"></div>
<p />
<p />
<?php
	$total=count($phrase_array);
	$i=0;
	showProgress($i, $total);
	foreach ($phrase_array as $curr_array) {
		$st=$curr_array[0];
		$end=$curr_array[1];
		$eff=$curr_array[2];
		$frame_cnt_raw=getFrameCnt($st,$end,$frame_delay);
		$frame_cnt=floor($frame_cnt_raw);
		$frame_cnt_remain=$frame_cnt_raw-$frame_cnt;
		if ($eff=="zzeross") {
			echo "Adding ".$frame_cnt." frames of zeros<br />";
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
		$i++;
		showProgress($i, $total);
	}
	echo '<script language="javascript">document.getElementById("information").innerHTML="Effect generation completed"</script>';
	return($outarray);	
}
function showProgress($i, $total) {
		$percent = intval($i/$total * 100)."%";
		if ($i > ($total-1))
			$i=($total-1);
		// Javascript for updating the progress bar and information
		echo '<script language="javascript">
		document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
		document.getElementById("information").innerHTML="'.$i.' of '.($total-1). ' phrase(s) processed.";
		</script>';
	 
		// This is for the buffer achieve the minimum size in order to flush data
		echo str_repeat(' ',1024*64);
	 
		// Send output to browser immediately
		flush();
		ob_flush();
		return;
}

function createSingleNCfile($username, $model_name, $eff, $frame_cnt, $st, $end, $project_id, $frame_delay) {  // this function will create the batch call to the effects to create the individual nc files
	$workdir="workarea/";
	$outfile=$workdir."$username~$model_name~$eff~$frame_cnt.nc";
	if (file_exists($outfile)) {
		echo "$outfile already exist <br />";
	} else {
		echo "Generating $outfile<br />";
		$batch_type=3;
		$get=getUserEffect($model_name,$eff,$username);
		$get['batch']=$batch_type;
		$get['username']=$username;
		$get['user_target']=$model_name;
		//$get['file_out']=$outfile;
		$get['seq_duration']=($end-$st);
		$get['frame_delay']=$frame_delay;
		$effect_class=$get['effect_class'];
		$member_id=getMemberID($username);
		$from_file="../effects/workspaces/$member_id/$model_name~$eff.nc";
		$to_file="../project/workarea/$username~$model_name~$eff~$frame_cnt.nc";
		//print_r($get);
		//echo "<br />";
		// code to gen a new individual nc file goes here
		$ranNC=false;
		switch ($effect_class) {
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
			case ('garland') :
				f_garlands($get);
				$ranNC=true;
				break;
			default :
				echo "$effect_class not handled yet<br />";
		}
		if ($ranNC) 
			copy($from_file, $to_file);
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
	$outfile="$username~$project_id~master.nc";
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