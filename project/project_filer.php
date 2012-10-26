<?php

function myTokenizer($in_str, $sepStr=" ")
{
	// Takes a string and returns an array seperated by value of $sepStr 
	$outarray=array();
	$tempArray = explode($sepStr, $in_str);
	foreach ($tempArray as $token)
	{
		$mytoken=trim($token);
		if (strlen($mytoken)>0)
		{
			$outarray[]=$mytoken;
		}
		};
	return($outarray);
}

function revTokenizer($in_array,$stripHeader=false, $sepStr=" ")
{
	// Reverse of myTokenizer.  Takes an array and reverts it to a string
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

function isInvalidLine($in_str)
{
	// returns false if the string starts with a # or is empty or is not set (null value)
		$outVal=false;
	if (isset($in_str)==false)
	{
		$outVal=true;
	}
	if (strlen(trim($in_str))==0)
	{
		$outVal=true;
	}
	else {
		if (substr($in_str,0,1)=="#")
		{
			$outVal=true;
		}
	}
	return($outVal);
}

function appendStr($str_array1,$str_array2,$prepend=false, $sepStr=" ")
{
	// takes two array of strings and appends them together
	$retArray = array();
	$cnt1=count($str_array1) ;
	$cnt2=count($str_array2) ;
	if ($cnt1 != $cnt2)
	{
		echo "*** ERROR *** arrays must match length! (str_array1=$cnt1,str_array2=$cnt2)<br />";
		if($cnt1>0) $retArray=$str_array1; // <scm>
	}
	else { 
		$y=count($str_array1);
		for($x=0;$x<$y;$x++)
		{
			if ($prepend)
			{
				$retArray[$x]=$str_array2[$x].sepStr.$str_array1[$x];
			}
			else {
				$retArray[$x]=$str_array1[$x].$sepStr.$str_array2[$x];
			}
		}
	}
	return($retArray);
}

function appendZeros($str_array1, $numZeros, $prepend=false, $sepStr=" ")
{
	// takes an array of strings and appends a series of zeros (prepends if prepend is true) to the array
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

function getFileData($infile, $numEntries, $sepStr=" ")
{
	$retVal=array();
	if(file_exists($infile)) // verify files exists before opening it <scm>
	{
		$fh=fopen($infile, 'r');
		$numEntries+=4;
		$retVal=array();
		while($line=fgets($fh))
		{
			$line = trim( preg_replace( '/\s+/', ' ', $line ) ); // remove unwanted gunk
			$tempVal="";
			$tok=preg_split("/ +/", trim($line));
			$numZeros=0;
			if (($tok[0]=="S") and ($tok[2]=="P") and ($tok[0]!="#"))
			{
				$arrayCnt=count($tok);
				if ($arrayCnt < $numEntries)
				{
					$numZeros = $numEntries-$arrayCnt;
					$arrayEnd=$arrayCnt;
				}
				else {
					$numZeros=0;
					$arrayEnd=$numEntries;
				}
				for($x=4;$x<$arrayEnd;$x++) 
				$tempVal.=$tok[$x]." ";
			}
			if ($numZeros > 0)
			{
				$zStr=rtrim(str_repeat("0 ",$numZeros));
				$tempVal.=$zStr;
			}
			if (strlen($tempVal)>0)
				$retVal[]=$tempVal;
		}
		fclose($fh);
	}
	return($retVal);
}

function array2File($outfile,$outarray)
{
	//writes out a file from an array
	echo "Writing to $outfile \n";
	$f=fopen($outfile,"w");
	foreach($outarray as $line)
	{
		fwrite($f, $line."\n");
	}
	fclose($f);
}

function createHeader($outfile,$model_name, $username, $project_id, $sepStr=" ")
{
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
	while ($line= fgets ($f))
	{
		if ($line)
		{
			if (isInvalidLine($line) == false)
			{
				$myArray=myTokenizer($line, $sepStr);
				$outstr = "S".$sepStr.$myArray[1].$sepStr."P".$sepStr.$myArray[2]."\n";
				fwrite($w, $outstr);
			}
		}
	}
	fclose ($f);
	fclose ($w);
}

function getHeader($model_name, $username, $project_id, $sepStr=" ")
{
	$sql="SELECT member_id FROM members WHERE username='".$username."'";
	$stripHeader = true;
	$result=nc_query($sql);
	$row=mysql_fetch_array($result,MYSQL_ASSOC);
	$member_id=$row['member_id'];
	$mydir='../targets/'.$member_id.'/';
	$model_file=$mydir.$model_name.".dat";
	$retArray = array();
	$f = fopen ($model_file, "r");
	$ln= 0;
	while ($line= fgets ($f))
	{
		if ($line)
		{
			if (isInvalidLine($line) == false)
			{
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

function showThumbs($project_id)
{
	$sql = "SELECT p.username, pd.phrase_name, ue.effect_name, pd.effect_id, model_name FROM `project_dtl` AS pd \n"
	. "LEFT JOIN project AS p ON p.project_id=pd.project_id\n"
	. "LEFT JOIN effects_user_hdr as ue ON ue.effect_id=pd.effect_id\n"
	. "WHERE pd.project_id=$project_id ORDER BY pd.start_secs;";
	echo "<table><tr>";
	$result=nc_query($sql);
	while ($row=mysql_fetch_assoc($result))
	{
		extract($row);
		if (strlen($effect_name)==0)
		{
			$gifLoc="../images/blank.gif";
			$effect_name="None";
			$editEffect="";
			$editEffectClose="";
		}
		else {
			$editEffect='<a href="effect_popup.php?project_id='.$project_id.'&effect_id='.$effect_id.'">';
			$editEffectClose='</a>';
			$sql="SELECT member_id FROM members WHERE username=\"$username\";";
			$result2=nc_query($sql);
			$row=mysql_fetch_assoc($result2);
			$member_id=$row['member_id'];
			$fileLoc="../effects/workspaces/$member_id/".$model_name."~".$effect_name."_th.gif";
			if (is_file($fileLoc))
			{
				$gifLoc=$fileLoc;
			}
			else 
			if (createThumb($model_name, $effect_name, $member_id)) 
			$gifLoc=$fileLoc;
			else
			$gifLoc="../images/noThumb.gif";
		}
		echo "<td class=\"smallText\">".$editEffect."<img  height=\"100\" width=\"50\" title=\"".$phrase_name."\n".$effect_name."\" alt=\"".$phrase_name.":".$effect_name."\" src=\"".$gifLoc."\"><br />".$phrase_name."</td>".$editEffectClose."\n";
	}
	echo "</tr></table>";
}

function createThumb($model_name, $effect_name, $member_id)
{
	$retVal=false;
	$basefile="../effects/workspaces/".$member_id."/".$model_name."~".$effect_name;
	$gp_file=$basefile.".gp";
	$gif_file=$basefile.".gif";
	$th_file=$basefile."_th.gif";
	if (is_file($gp_file))
	{
		if($_SERVER['HTTP_HOST'] != 'meighan.net')
		{
			$shellCommand = $_SERVER['DOCUMENT_ROOT']."nutcracker/gnuplot/bin/gnuplot.exe " . $gp_file;
			system($shellCommand,$output);
			$shellCommand = "del ".$gif_file;
			system($shellCommand, $output);
			$retVal=is_file($th_file);
		}
	}
	return($retVal);
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
	and hdr.username='".$username."'
	and upper(hdr.effect_name)=upper('$effect')";
	$result = nc_query($sql);
	$cnt=0;
	$string="";
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);		//	if(strncmp($param_name,"background_color",strlen("background_color"))==0 and strncmp($param_value,"#",1)==0) $param_value=hexdec($param_value);
		$string = $string . "&" . $param_name . "=" . $param_value;
		$get[$param_name]=$param_value;
		$effect_class=$row['effect_class'];
	}
	// we also need teh effect class from the header
	$get['effect_class']=$effect_class;
	return $get;
}

function save_phrases($inphp)
{
	foreach($inphp as $key=>$val)
	{
		switch ($key)
		{
			case "project_id":
			$project_id = $val;
			$sql="UPDATE project SET last_update_date=NOW() WHERE project_id=".$project_id;
			$result=nc_query($sql);
			break;
			case "frame_delay":
			$frame_delay = $val;
			if (isset($project_id))
			{
				$sql="UPDATE project SET frame_delay=".$frame_delay." WHERE project_id=".$project_id;
				$result=nc_query($sql);
			}
			break;
			case "SavePhraseEdit":
			break;
			case "outputType" :
			break;
			default:
			switch (substr($key,0,3))
			{
				case "en-":
				$key=(substr($key,3));
				$sql="UPDATE project_dtl SET end_secs=".$val." WHERE project_dtl_id=".$key;
				$result=nc_query($sql);
				break;
				case "st-":
				$key=(substr($key,3));				
				$sql="UPDATE project_dtl SET start_secs=".$val." WHERE project_dtl_id=".$key;
				$result=nc_query($sql);
				break;
				default:
				if (strlen($val)==0)
				{
					$val="NULL";
				}
				else {
					$val="'".$val."'";
				}
				$sql="UPDATE project_dtl SET effect_id=".$val." WHERE project_dtl_id=".$key;
				$result=nc_query($sql);
			}
		}
	}
}

function get_effects($username)
{
	$sql = "SELECT effect_name, effect_id, effect_class FROM effects_user_hdr WHERE username='$username' AND effect_name IS NOT NULL ORDER BY effect_name";
	$effect=array();
	$efftype=array();
	$effid=array();
	$result=nc_query($sql);
	while ($row=mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$effect[]=$row['effect_name'];
		$efftype[]=$row['effect_class'];
		$effid[]=$row['effect_id'];
	}
	$retVal=array($effect, $efftype, $effid);
	return($retVal);
}

function edit_song($project_id)
{
	$sql = "SELECT model_name, song_name, artist, song_url, frame_delay, project.username, last_update_date, last_compile_date FROM project LEFT JOIN song ON project.song_id=song.song_id WHERE project_id=".$project_id;
	$result=nc_query($sql);
	$row=mysql_fetch_array($result,MYSQL_ASSOC);
	$frame_delay=$row['frame_delay'];
	$username=$row['username'];
	$song_url=$row['song_url'];
	$song_name=$row['song_name'];
	$artist=$row['artist'];
	$model_name=$row['model_name'];
	$last_update_date=$row['last_update_date'];
	$last_compile_date=$row['last_compile_date'];
	$effectArr=get_effects($username);
	$effect=$effectArr[0];
	$effType=$effectArr[1];
	$effid=$effectArr[2];
	$sql = "SELECT project_dtl_id, phrase_name, start_secs, end_secs, effect_id, effect_name FROM project_dtl WHERE project_id=".$project_id." ORDER BY start_secs";
	?>
	<h2>Edit Project Details for "<?php echo $song_name;?>" by <?php echo $artist;?> (Model: <?php echo $model_name;?>)</h2>
	<table border="0" cellspacing="1" cellpadding="1">
	<tr><td>Last Update :</td><td><strong><?php if (strlen(trim($last_update_date))==0) echo "Never"; else echo date("jS F Y (g:ia)",strtotime($last_update_date)); ?></strong></td></tr>
	<tr><td>Last Output :</td><td><strong><?php if (strlen(trim($last_compile_date))==0) echo "Never"; else echo date("jS F Y (g:ia)",strtotime($last_compile_date)); ?></strong></td></tr>
	</table>
	<br />
	<form name="project_edit" id="project_edit" action="project.php" method="post">
	<input type="hidden" name="project_id" id="project_id" value=<?php echo $project_id;?>>
	Frame Rate for project : <input class="FormFieldName" type="text" name="frame_delay" id="frame_delay"0 value="<?php echo $frame_delay?>"><br />
	<table class="Gallery">
	<tr><th>Phrase</th><th>Start Time (sec)</th><th>End Time (sec)</th><th>Duration</th><th>Frames</th><th>Effect Assigned</th></tr>
	<?php
	$result3=nc_query($sql);
	$cnt=show_phrases($result3,$effect, $effType, $frame_delay, $effid);
	if ($cnt==0)
	{
		// if there currently are no phrases attached to project get them from the library
		insert_proj_detail_from_library($project_id);
		$result3=nc_query($sql);
		$newcnt=show_phrases($result3,$effect, $effType, $frame_delay, $effid);
	}
	?>
	</table>
	<input type="submit" name="SavePhraseEdit"  class="SubmitButton" value="Save these values">&nbsp;&nbsp;&nbsp;<input type="submit"  class="SubmitButton" name="CancelPhraseEdit" value="Hide Detail">
	<p /><input type="submit"  class="SubmitButton" name="LoadPhraseFile" value="Load Phrases from Audacity save file">
	<p />
	<h2>Time Line of Effects</h2>
	<?php showThumbs($project_id); ?>
	<table border="0" cellpadding="1" cellspacing="1">
	<tr><td>
	<select class="FormFieldName" name="outputType" id="outputType">
	<option value="">Select Output Type</option>
	<option value="vixen">Vixen 2.1 and 2.5</option>
	<option value="hls">HLS versions 3a and greater</option>
	<option value="lsp">Light Show Pro</option>
	<option value="lor">Light-O-Rama - LMS file</option>
	<option value="lcb">Light-O-Rama - LCB file</option>
	<option value="xml">XML (text) file</option>
	</select> </td></tr>
	<tr><td>
	<input type="submit" name="MasterNCSubmit" class="SubmitButton" value="Output Project">
	</td></tr></table>
	</form>
	<?php
	return;
}

function show_phrases($inresult,$effect, $effType, $frame_delay, $effid)
{
	$cnt=0;
	while ($row = mysql_fetch_array($inresult, MYSQL_ASSOC))
	{
		$cnt +=1;
		$project_dtl_id = $row['project_dtl_id'];
		$effect_id=$row['effect_id'];
		$phrase_name = $row['phrase_name'];
		$start_secs = $row['start_secs'];
		$end_secs = $row['end_secs'];
		$duration = $end_secs - $start_secs;
		$frames = sec2frame($duration, $frame_delay);
		$effect_name = $row['effect_name'];
		$effect_str=effect_select($effect,$effect_name,$project_dtl_id, $effType, $effect_id, $effid);
		if ($cnt%2==0)
			$trStr="<tr class=\"alt\">";
		else
		$trStr="<tr>";
		echo $trStr."<td>".$phrase_name."</td>";
		echo "<td><input type=\"text\" class=\"FormFieldName\" value=\"".$start_secs."\" name=\"st-".$project_dtl_id."\"></td>";
		echo "<td><input type=\"text\" class=\"FormFieldName\" value=\"".$end_secs."\" name=\"en-".$project_dtl_id."\"></td>";
		echo "<td>".$duration."</td>";
		echo "<td>".$frames."</td>";
		echo "<td>".$effect_str."</td></tr>";
	}
	return($cnt);
}

function effect_select($effect_array, $ineffect, $project_dtl_id, $effType, $effect_id,$effid)
{
	$retStr='<select class="FormFieldName" name='.$project_dtl_id.' id='.$project_dtl_id.'>';
	if (strlen($ineffect)==0)
	{
		$defstr=" selected";
	}
	else {
		$defstr="";
	}
	$retStr.='<option value=""'.$defstr.'>No Effect Selected</option>';
	for($x=0;$x<count($effect_array);$x++)
	{
		$effect=$effect_array[$x];
		$effect_class=$effType[$x];
		$curreffid=$effid[$x];
		if ($curreffid == $effect_id)
		{
			$defstr = " selected";
		}
		else {
			$defstr = "";
		}
		$retStr.='<option value="'.$curreffid.'"'.$defstr.'>'.$effect.' ('.$effect_class.')</option>';
	}
	$retStr.='</select>';
	return($retStr);
}

function insert_proj_detail_from_library($project_id)
{
	$sql = "SELECT song_id FROM project WHERE project_id = ".$project_id;
	$result=nc_query($sql);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$song_id=$row['song_id'];
	$sql = "SELECT phrase_name, start_secs, end_secs FROM song_dtl where song_id = ".$song_id;
	$cnt=0;
	$result2=nc_query($sql);
	while ($row = mysql_fetch_array($result2, MYSQL_ASSOC))
	{
		$cnt +=1;
		$phrase_name = $row['phrase_name'];
		$start_secs = $row['start_secs'];
		$end_secs = $row['end_secs'];
		$sql="INSERT INTO project_dtl (phrase_name, start_secs, end_secs, project_id) VALUES ('".$phrase_name."',".$start_secs.",".$end_secs.",".$project_id.")";
		$result3=nc_query($sql);
	}
	echo "Inserted $cnt new records into project detail<br />";
	return;
}

function remove_song($project_id)
{
	$sql = "SELECT song_id, model_name FROM project where project_id=".$project_id;
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

function add_song($song_id, $username, $frame_delay, $model_name)
{
	$song_name=getSongName($song_id);
	$sql2 = 'Select count(*) as songcnt from project WHERE song_id='.$song_id.' AND username="'.$username.'" AND model_name="'.$model_name.'"';
	$sql = "REPLACE INTO project (song_id, username,frame_delay, model_name) VALUES (".$song_id.",'".$username."',".$frame_delay.",\"".$model_name."\")";
	$result2 = nc_query($sql2);
	$row = mysql_fetch_array($result2, MYSQL_ASSOC);
	if ($row['songcnt'] > 0)
	{
		return("*** Add Canceled *** Song '$song_name' and Model '$model_name' already exists!");
	}
	else {
		$result =nc_query($sql);
		return("Song '$song_name' and Target '$model_name' added");
	}
}

function getSongName($song_id)
{
	$retVal = "Error occured";
	$sql = "SELECT song_name FROM song WHERE song_id='$song_id'";
	$result = nc_query($sql);
	if ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$retVal=$row['song_name'];
	}
	else {
		$retVal="*** ERROR IN getSongName ***";
	}
	return($retVal);
}

function select_song($username)
{
	$sql = "SELECT username, song_name, song.song_id, artist, song_url, min( start_secs )AS MinTime, max( end_secs )AS MaxTime \n"
	. "FROM song \n"
	. "LEFT JOIN song_dtl ON song.song_id = song_dtl.song_id \n"
	. "GROUP BY song_name, song.song_id \n"
	. "HAVING MaxTime>0 AND song.username IN ('f','".$username."')";
	//echo $sql . "<br />";
	$sql2 = "SELECT object_name, model_type FROM models WHERE username='".$username."'";
	$result = nc_query($sql);
	?>
	<h2>Available Songs</h2>
	<table class="TableProp">
	<?php
	$rowcnt = mysql_num_rows($result);
	if ($rowcnt == 0)
	{
		echo "<tr><th>No more songs available to add!</th></tr>";
	}
	else {
		?>
		<?php 
		$SongSel=parseSongs($result);
		echo $SongSel[1];?>
		</table>
		<br />
		<a href="">Add a Song to the Library</a>
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

function parseTargetSelect($result)
{
	$retStr='<select name="model_name" class="FormSelect" id="model_name">';
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$model_name=$row['object_name'];
		$model_type=$row['model_type'];
		$retStr.='<option value="'.$model_name.'">'.$model_name.' ('.$model_type.')</option>';
	}
	$retStr.='</select>';
	return($retStr);
}

function parseSongs($result)
{
	$retVal = array();
	$retStr1='<select name="song_id" class="FormSelect" id="song_id">';
	$retStr2='<tr><th>Song Name</th><th>Song url</th><th>Length of song (sec)</th><th>Length of song (min)</th></tr>';
	$cnt=0;
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$cnt++;
		$song_name=$row['song_name'];
		$song_id=$row['song_id'];
		$song_url=$row['song_url'];
		$MinTime=$row['MinTime'];
		$MaxTime=$row['MaxTime'];
		$song_length = round(($MaxTime-$MinTime),2);
		$song_length_min = round(($song_length/60),2);
		$retStr1.='<option value='.$song_id.'>'.$song_name.'</option>';
		if ($cnt%2==0) 
		$trStr='<tr>';
		else
		$trStr='<tr class="alt">';
		$retStr2.=$trStr.'<td><a href="'.$song_url.'">'.$song_name.'</a></td><td><a href="'.$song_url.'">'.$song_url.'</a><td>'.$song_length.'</td><td>'.$song_length_min.'</td></tr>';
	}
	$retStr1.='</select>';
	$retVal[0]=$retStr1;
	$retVal[1]=$retStr2;
	return($retVal);
}

function sec2frame($inval, $frame_delay)
{
	$retval=round($inval*1000/$frame_delay, 0);
	return($retval);
}

function joinPhraseArray($inArray)
{
	//$phrase_name,$st_secs, $end_secs, $dur_secs, $frame_cnt, $frame_st, $frame_end, $effect_name
	$savephrase_name=$inArray[0][0];
	$savest_time=$inArray[0][1];
	$saveend_time=$inArray[0][2];
	$saveduration=$inArray[0][3];
	$saveframe_cnt=$inArray[0][4];
	$savest_phrase=$inArray[0][5];
	$saveend_phrase=$inArray[0][6];
	$saveeffect_name=$inArray[0][7];
	$saveeffect_id=$inArray[0][8];
	$cnt=0;
	$retArray=array();
	foreach($inArray as $phrase)
	{
		if ($cnt>0)
		{
			$phrase_name=$phrase[0];
			$st_time=$phrase[1];
			$end_time=$phrase[2];
			$duration=$phrase[3];
			$frame_cnt=$phrase[4];
			$st_phrase=$phrase[5];
			$end_phrase=$phrase[6];
			$effect_name=$phrase[7];
			$effect_id=$phrase[8];
			if ($saveeffect_name==$effect_name)
			{
				$saveend_time=$end_time;
				$saveduration+=$duration;
				$saveend_phrase=$end_phrase;
				$saveframe_cnt+=$frame_cnt;
			}
			else {
				$arrayEntry=array($savephrase_name,$savest_time,$saveend_time, $saveduration, $saveframe_cnt, $savest_phrase, $saveend_phrase, $saveeffect_name, $saveeffect_id);
				$retArray[]=$arrayEntry;
				$savephrase_name=$phrase_name;
				$savest_time=$st_time;
				$saveend_time=$end_time;
				$saveduration=$duration;
				$saveframe_cnt=$frame_cnt;
				$savest_phrase=$st_phrase;
				$saveend_phrase=$end_phrase;
				$saveeffect_name=$effect_name;
				$saveeffect_id=$effect_id;
			}
		}
		$cnt++;
	}
	$arrayEntry=array($savephrase_name,$savest_time,$saveend_time, $saveduration, $saveframe_cnt, $savest_phrase, $saveend_phrase, $saveeffect_name, $saveeffect_id);
	$retArray[]=$arrayEntry;
	return($retArray);
}

function getPhraseArray($project_id, $join_phrase=true)
{
	$sql = "SELECT pd.phrase_name, pd.start_secs, pd.end_secs, pd.effect_id, ue.effect_name, p.frame_delay, p.username, p.model_name, m.member_id \n"
	. "FROM `project_dtl` as pd \n"
	. "LEFT JOIN project as p ON p.project_id=pd.project_id \n"
	. "LEFT JOIN members as m ON m.username=p.username \n"
	. "LEFT JOIN effects_user_dtl as ue ON ue.effect_id=pd.effect_id \n"
	. "WHERE pd.project_id = ".$project_id." \n"
	. "ORDER BY pd.start_secs";
	//echo "SQL in getPhraseArray : ".$sql."<br />";
	$result=nc_query($sql);
	//echo "GOT to getPhraseArray<br />";
	$retArray=array();
	while ($row=mysql_fetch_array($result,MYSQL_ASSOC))
	{
		$effect_id=$row['effect_id'];
		$phrase_name=$row['phrase_name'];
		$st_secs=$row['start_secs'];
		$frame_delay=$row['frame_delay'];
		$username=$row['username'];
		$member_id=$row['member_id'];
		$end_secs=$row['end_secs'];
		$effect_name=$row['effect_name'];
		$model_name=$row['model_name'];
		$dur_secs=$end_secs-$st_secs;
		$frame_cnt=sec2frame($dur_secs, $frame_delay);
		$frame_st=sec2frame($st_secs, $frame_delay);
		$frame_end=$frame_st+$frame_cnt;
		$phraseArray=array($phrase_name,$st_secs, $end_secs, $dur_secs, $frame_cnt, $frame_st, $frame_end, $effect_name, $effect_id);
		$retArray[]=$phraseArray;
	}
	if ($join_phrase)
		$retArray=joinPhraseArray($retArray);
	$retArray=checkPhraseArray($retArray, $frame_delay);
	$retArray= fixEffectFrames($retArray);
	return($retArray);
}

function printPhrase($phraseArray)
{
	foreach($phraseArray as $phrase)
		printf("<pre>%f\t%f\t%f\t%d\t%d\t%d\t%s\n</pre>", $phrase[1],$phrase[2],$phrase[3],$phrase[4],$phrase[5],$phrase[6],$phrase[7]);
	return;
}

function getFrameCnt($phraseArray)
{
	$retVal=0;
	foreach($phraseArray as $phrase)
		if ($phrase[6]>$retVal)
		$retVal=$phrase[6];
	return($retVal);
}

function getTotalCnt($phraseArray, $frame_delay)
{
	return($frame_delay*getFrameCnt($phraseArray));
}

function checkPhraseArray($phraseArray, $frame_delay)
{
	$cnt=0;
	$end_time=$start_time=0.0;
	$retArray=array();
	foreach($phraseArray as $phrase)
	{
		$ph_st_time=$phrase[1];
		$ph_end_time=$phrase[2];
		$ph_end_phrase=$phrase[6];
		$effect_id=$phrase[8];
		if ($end_time < $ph_st_time)
		{
			// gap exists.  Must fill with zero counts
			$phrase_name="blank     ";
			$new_st=$end_time;
			$new_end=$ph_st_time;
			$dur_secs=$new_end-$new_st;
			$frame_cnt=sec2frame($dur_secs, $frame_delay);
			$frame_st=sec2frame($new_st, $frame_delay);
			$frame_end=$frame_st+$frame_cnt;
			$effect_name="None";
			$effect_id=0;
			if ($frame_cnt>0)
			{
				$phraseArray=array($phrase_name,$new_st, $new_end, $dur_secs, $frame_cnt, $frame_st, $frame_end, $effect_name, $effect_id);
				$retArray[]=$phraseArray;
				$cnt++;
			}
		}
		if ($end_time > $ph_st_time)
		{
			// overlap.  Need to adjust previous end time
			if ($cnt>1)
			{
				$prevCnt=$cnt-1;
				$st_time=$retArray[$prevCnt][1];
				$end_time=$ph_st_time;
				$dur_secs=$end_time-$st_time;
				$frame_cnt=sec2frame($dur_secs, $frame_delay);
				$frame_st=sec2frame($st_time, $frame_delay);
				$frame_end=$frame_st+$frame_cnt;
				$retArray[$prevCnt][1]=$st_time;
				$retArray[$prevCnt][2]=$end_time;
				$retArray[$prevCnt][3]=$dur_secs;
				$retArray[$prevCnt][4]=$frame_cnt;
				$retArray[$prevCnt][5]=$frame_st;
				$retArray[$prevCnt][6]=$frame_end;
			}
		}
		if (strlen($phrase[7])==0)
			$phrase[7]="None";
		$retArray[]=$phrase;
		$start_time=$ph_st_time;
		$end_time=$ph_end_time;
		$cnt++;
	}
	return($retArray);
}

function fixEffectFrames($phraseArray)
{
	$phrase_end=$phrase_start=0;
	$cnt=1;
	$numPhrase=count($phraseArray);
	foreach($phraseArray as $phrase)
	{
		$effect=$phrase[7];
		$phrase_start=$phrase[5];
		if ($cnt<$numPhrase) 
		$phrase_end=$phraseArray[($cnt)][5]-1;
		else
		$phrase_end=$phrase[6];
		$phraseArray[$cnt-1][6]=$phrase_end;
		$phraseArray[$cnt-1][5]=$phrase_start;
		$cnt++;
	}
	return($phraseArray);
}

function checkNCInfo($infile)
{
	$retArray=array();
	if(file_exists($infile)) // verify file exists before we try to open it <scm>
	{
		$retArray=array();
		$fh=fopen($infile,'r');
		$oldColumns=-1;
		$retArray=array();
		while($line=fgets($fh))
		{
			$tok=preg_split("/ +/", trim($line));
			if (($tok[0]=="S") and ($tok[2]=="P"))
			{
				$numColumns=count($tok)-4;
				if ($oldColumns<0) 
				$oldColumns=$numColumns;
				$outcome=($numColumns==$oldColumns);
				$retArray[]=$outcome;
				if ($outcome)
					$oldColumns=$numColumns;
			}
		}
		fclose($fh);
	}
	return($retArray);
}

function getNCInfo($infile)
{
	//$retVal=array($numColumns, $numElements, $maxString, $maxPixel);
	$retVal=array(0,0,0,0); // initialize a dummary array in case we fail top open nc file
	if(file_exists($infile))
	{
		$fh=fopen($infile,'r');
		$numElements=$numColumns=$maxString=$maxPixel=0;
		while($line=fgets($fh))
		{
			$tok=preg_split("/ +/", trim($line));
			if (($tok[0]=="S") and ($tok[2]=="P"))
			{
				$string=$tok[1];
				$pixel=$tok[3];
				if ($string>$maxString)
					$maxString=$string;
				if ($pixel>$maxPixel)
					$maxPixel=$pixel;
				$numColumns=count($tok)-4;
				$numElements++;
			}
		}
		fclose($fh);
		$numElements*=3; //account for the RGBs
		$retVal=array($numColumns, $numElements, $maxString, $maxPixel);
	}
	return($retVal);
}

function getModelInfo($project_id)
{
	$proj_array=getProjDetails($project_id);
	$frame_delay=$proj_array['frame_delay'];
	$model_name=$proj_array['model_name'];
	$username=$proj_array['username'];
	$member_id=$proj_array['member_id'];
	$infile="../targets/".$member_id."/".$model_name.".dat";
	$fh=fopen($infile,'r');
	$numElements=$numColumns=$maxString=$maxPixel=0;
	while($line=fgets($fh))
	{
		$tok=preg_split("/ +/", trim($line));
		if ($tok[0] != "#")
		{
			//echo "<pre>Size of Tok : ".count($tok)."</pre><br />";
			if (count($tok)>2)
			{
				//print_r($tok);
				$string=$tok[1];
				$pixel=$tok[2];
				if ($string>$maxString)
					$maxString=$string;
				if ($pixel>$maxPixel)
					$maxPixel=$pixel;
				$numElements++;
			}
			//else 
			//	echo "<pre>Not sure what to do with this line: ".$line."<br /></pre>";
		}
	}
	fclose($fh);
	$numElements*=3; //account for the RGBs
	$retVal=array($model_name, $numElements, $maxString, $maxPixel);
	//print_r($retVal);
	return($retVal);
}

function isValidNCModel($project_id, $infile)
{
	$modInfo=getModelInfo($project_id);
	$ModnumElements=$modInfo[1];
	$ModmaxString=$modInfo[2];
	$ModmaxPixel=$modInfo[3];
	$NCInfo=getNCInfo($infile);
	$NCnumElements=$NCInfo[1];
	$NCmaxString=$NCInfo[2];
	$NCmaxPixel=$NCInfo[3];
	/*echo "Model Elements : ".$ModnumElements.", Strings : ".$ModmaxString.", Pixels : ".$ModmaxPixel."<br />";
	echo "NC Elements    : ".$NCnumElements.", Strings : ".$NCmaxString.", Pixels : ".$NCmaxPixel."<br />";*/
	//$retVal=(($ModnumElements==$NCnumElements) && ($ModmaxString==$NCmaxString) && ($ModmaxPixel==$NCmaxPixel));
	$retVal=($ModnumElements==$NCnumElements);
	/*if ($retVal) 
	echo "VALID!<br />";
	else
	echo "INVALID!<br />";*/
	return($retVal);
}

function isValidNC($infile)
{
	$valArray=checkNCInfo($infile);
	/*echo "<pre>";
	print_r($valArray);
	echo "</pre>\n";*/
	$overcheck=true;
	foreach($valArray as $currflag) 
	$overcheck=$overcheck && $currflag;
	return($overcheck);
}

function getProjDetails($project_id)
{
	$sql = "SELECT frame_delay, p.username, model_name, member_id \n"
	. "FROM project as p \n"
	. "LEFT JOIN members as m ON m.username=p.username \n"
	. "WHERE p.project_id = $project_id\n";
	$retArray=array();
	$result=nc_query($sql);
	$retArray=array();
	if ($row=mysql_fetch_array($result,MYSQL_ASSOC))
	{
		$frame_delay=$row['frame_delay'];
		$username=$row['username'];
		$model_name=$row['model_name'];
		$member_id=$row['member_id'];
		$retArray=array('frame_delay'=>$frame_delay, 'username'=>$username, 'model_name'=>$model_name, 'member_id'=>$member_id);
	}
	return($retArray);
}

function setupNCfiles($project_id,$phrase_array)
{
	// create each of the effect nc files (or make sure they are created for each of the times
	$proj_array=getProjDetails($project_id);
	$frame_delay=$proj_array['frame_delay'];
	$model_name=$proj_array['model_name'];
	$username=$proj_array['username'];
	$member_id=$proj_array['member_id'];
	$cnt=0;
	$outarray=array();
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
	echo "<table border=1>";
	foreach ($phrase_array as $curr_array)
	{
		$phrase_name=$curr_array[0];
		$st_secs=$curr_array[1];
		$end_secs=$curr_array[2];
		$dur_secs=$curr_array[3];
		$frame_cnt=$curr_array[4];
		$frame_st=$curr_array[5];
		$frame_end=$curr_array[6];
		$effect_name=$curr_array[7];
		$effect_id=$curr_array[8];
		echo "<tr><td>$phrase_name</td>";
		list($usec, $sec) = explode(' ', microtime()); // <scm>
		$script_start = (float) $sec + (float) $usec; // ,scm>
		if ($effect_name=="None")
		{
			echo "<td>Generating ".$frame_cnt." frames of zeros</td><td>Zeros made</td><td>No file needed</td>";
			$outstr="zeros:$frame_cnt";
		}
		else {
			echo "<td>Generating ". $effect_name . "</td>";
			$outstr=createSingleNCfile($username, $model_name, $effect_name, $frame_cnt, $st_secs, $end_secs, $project_id, $frame_delay, $effect_id);
		}
		list($usec, $sec) = explode(' ', microtime()); // <scm>
		$script_end = (float) $sec + (float) $usec;    // <scm>
		$elapsed_time = round($script_end - $script_start, 3);  // <scm>
		echo "<td>$elapsed_time secs</td>";  // <scm>
		$outarray[$cnt++]=$outstr;
		$i++;
		echo "</tr>";
		showProgress($i, $total);
	}
	echo "</table>";
	echo '<script language="javascript">document.getElementById("information").innerHTML="Effect generation completed";
	document.body.style.cursor = "default";</script>';
	return($outarray);
}

function showProgress($i, $total)
{
	$percent = intval($i/$total * 100)."%";
	if ($i > ($total-1))
		$i=($total-1);
	// Javascript for updating the progress bar and information
	echo '<script language="javascript">
	document.body.style.cursor = "wait";
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

function showMessage($outStr)
{
	echo "<h3>$outStr</h3><br />";
	echo str_repeat(' ',1024*64);
	// Send output to browser immediately
	flush();
	ob_flush();
	return;
}

function createSingleNCfile($username, $model_name, $eff, $frame_cnt, $st, $end, $project_id, $frame_delay, $effect_id)
{

	// this function will create the batch call to the effects to create the individual nc files
	$workdir="workarea/";
	$outfile=$workdir."$username~$model_name~$eff~$frame_cnt.nc";
	$isValid=true;
	if (file_exists($outfile))
	{
		$inHash=getProjHash($project_id, $effect_id);
		$checkHasher=checkHash($inHash,$project_id, $effect_id);
		//echo "<pre>File ".$outfile." already is here.  Now I gotta check it!<br />";
		if (!$checkHasher)
		{
			$isValid=false;
			// Check to see if the values of the effect have changed, if so, we need to regen the effect (remove the existing nc file if it exists)
			removeNCFiles($outfile);
			echo "<td>Looks like the effect ".$eff." has changed since the NC file created</td>";
		}
		if (($isValid) && (!isValidNC($outfile)))
		{
			// Check to see if the existing NC file exists and is valid, if not, we need to regen the effect (remove the existing nc file)
			removeNCFiles($outfile);
			$isValid=false;
			echo "<td>NC file is invalid...</td>";
		}
		if (($isValid) && (!isValidNCModel($project_id, $outfile)))
		{
			// Check to see if the nc file matches the model (same number of strings/pixels), if not, we will need to regen the effect (remove the existing nc file)
			removeNCFiles($outfile);
			$isValid=false;
			echo "<td>Model and Effect DO NOT match.  Erasing file...</td>";
		}
		echo "</pre>";
	}
	if (file_exists($outfile))
	{
		echo "<td>Found file</td><td bgcolor=#B5C6FF>$outfile already exist </td>";
	}
	else {
		echo "<td bgcolor=#8CA7FF>Generating $outfile</td>";
		$batch_type=3;
		$get=getUserEffect($model_name,$eff,$username);
		$get['batch']=$batch_type;
		$get['username']=$username;
		$get['user_target']=$model_name;
		$get['seq_duration']=($end-$st);
		$get['frame_delay']=$frame_delay;
		$effect_class=$get['effect_class'];
		$member_id=getMemberID($username);
		// need code to grab the 360 value from the model -- right now the model does not contain 360 info (but will for the future)
			//$get['windows_degrees']=360;  // default the degrees to 360.  This will have to change for the future.
		$from_file="../effects/workspaces/$member_id/$model_name~$eff.nc";
		$to_file="../project/workarea/$username~$model_name~$eff~$frame_cnt.nc";
		$sql='UPDATE effects_user_dtl SET param_value='.($end-$st).' WHERE username="'.$username.'" AND effect_name="'.$eff.'" AND param_name="seq_duration"';
		nc_query($sql);
		$sql='UPDATE effects_user_dtl SET param_value='.$frame_delay.' WHERE username="'.$username.'" AND effect_name="'.$eff.'" AND param_name="frame_delay"';
		nc_query($sql);
		$ranNC=false;
		switch ($effect_class)
		{
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
			case ('single_strand') :
			f_single_strand($get);
			$ranNC=true;
			break;
			default :
			echo "$effect_class not handled yet<br />";
		}
		if ($ranNC)
		{
			checkDir('workarea');
			if(!file_exists($from_file))
				echo "<pre>from_file $from_file does not exist</pre>\n";
			else if(!file_exists($from_file))
				echo "<pre>to_file $to_file does not exist</pre>\n";
			else
			{
				copy($from_file, $to_file);
			}
		}
	}
	updateHash($project_id,$effect_id);
	return($outfile); // this will be the file created 
}

function prepMasterNCfile($project_id)
{
	showMessage('Prepping the Master NC File');
	$proj_array=getProjDetails($project_id);
	$frame_delay=$proj_array['frame_delay'];
	$model_name=$proj_array['model_name'];
	$username=$proj_array['username'];
	$member_id=$proj_array['member_id'];
	$testarr = getHeader($model_name, $username, $project_id);
	return($testarr);
}

function checkDir($inDir)
{
	if (!is_dir($inDir)) 
	mkdir($inDir);
	return;
}

function getSongTime($project_id)
{
	$sql = "SELECT max(end_secs) AS totLength FROM project_dtl WHERE project_id=$project_id";
	$result=nc_query($sql);
	$row=mysql_fetch_array($result,MYSQL_ASSOC);
	$retval=intval($row['totLength']);
	return($retval);
}

function processMasterNCfile($project_id, $projectArray, $workArray, $outputType, $NCArray)
{
	$proj_array=getProjDetails($project_id);
	$frame_delay=$proj_array['frame_delay'];
	$model_name=$proj_array['model_name'];
	$username=$proj_array['username'];
	$member_id=$proj_array['member_id'];
	if ($outputType!='xml')
	{
		showMessage('Erasing gaps and joining effects');
		echo "<table border=1>";
		foreach($workArray as $curr_array)
		{
			$phrase_name=$curr_array[0];
			$st_secs=$curr_array[1];
			$end_secs=$curr_array[2];
			$dur_secs=$curr_array[3];
			$frame_cnt=$curr_array[4];
			$frame_st=$curr_array[5];
			$frame_end=$curr_array[6];
			$effect_name=$curr_array[7];
			$numFrames = ($frame_end-$frame_st)+1;
			$NCArraySize=count(myTokenizer($NCArray[0]))-4;
			echo "<tr><td>$phrase_name</td><td>$effect_name</td>";
			if ($effect_name=="None")
			{
				$NCArray=appendZeros($NCArray,$numFrames);
				echo "<td>Adding $numFrames zeros from frame $frame_st to frame $frame_end</td>";
			}
			else {
				$infile="workarea/".$username."~".$model_name."~".$effect_name."~".$frame_cnt.".nc<";
				$effectData=getFileData($infile, $numFrames);
				$NCArray=appendStr($NCArray,$effectData);
				echo "<td bgcolor=\"#9EFF7A\">Adding $numFrames of effect $effect_name from frame $frame_st to frame $frame_end</td>";
			}
			$NCArraySize=count(myTokenizer($NCArray[0]))-4;
			echo "</tr>";
		}
		echo "</table>";
		$outfile="workarea/".$username."~".$project_id."~master.nc";
		array2File($outfile, $NCArray);
		$myArray=getNCInfo($outfile);
		$numFrames=$myArray[0];
		$numEntities=$myArray[1];
		$song_tot_time=$numFrames*$frame_delay;
	}
	if (isset($outputType))
	{
		switch ($outputType)
		{
			case 'vixen' :
			$VixArr=genAllVixen($song_tot_time, $frame_delay, $username, $project_id);
			$vixFile=$VixArr[0];
			$virFile=$VixArr[1];
			echo "<table cellpadding=\"1\" cellspacing=\"1\"><tr class=\"SaveFile\"><td>Right click save the following VIX file to your computer</td>\n";
			echo "<td><a href=\"$vixFile\" class=\"SaveFile\">$vixFile</a></td></tr>\n";
			echo "<tr class=\"SaveFile\"><td>Right click save the following VIR file to your computer</td>\n";
			echo "<td><a href=\"$virFile\" class=\"SaveFile\">$virFile</a></td></tr></table>\n";
			break;
			case 'hls' :
			$hlsFile=genHLS($username, $project_id);
			echo "<table cellpadding=\"1\" cellspacing=\"1\"><tr class=\"SaveFile\"><td>Right click save the following HLSNC file to your computer</td>\n";
			echo "<td><a href=\"$hlsFile\" class=\"SaveFile\">$hlsFile</a></td></tr></table>\n";
			break;
			case 'lsp' :
			$NCFile=$outfile;
			$type = 1;
			$XMLFile="workarea/".$username."~".$project_id."~UserPattern.xml";
			$fh_xml=fopen($XMLFile, 'w');
			make_HdrPattern_header($fh_xml);
			make_xml($fh_xml,$NCFile,$type,$frame_delay);
			fclose($fh_xml);
			echo "<table cellpadding=\"1\" cellspacing=\"1\"><tr class=\"SaveFile\"><td>Right click save the following LSP file to your computer</td>\n";
			echo "<td><a href=\"$XMLFile\" class=\"SaveFile\">$XMLFile</a></td></tr><table>\n";
			break;
			case 'lor' :
			$LORFile="workarea/".$username."~".$project_id.".lms";
			$fh_lor=fopen($LORFile, 'w');
			genLOR($fh_lor,$NCFile, $frame_delay, $song_tot_time);
			fclose($fh_lor);
			echo "<table class=\"TableProp\">";
			//printf ("<tr><td bgcolor=lightgreen><h2>$channels channels and $Maxframe frames have been created for LOR lms file</h2></td>\n");
			echo "<tr><th colspan=2>Instructions</th></tr>";
			printf ("<tr class=\"alt\"><td><h2><a href=\"%s\">Right Click here for  LOR lms file</a></h2></td>\n",$LORFile);
			echo "<td>Save lms file into your light-o-rama/sequences directory</td></tr>\n";
			echo "</table>";
			break;
			case 'lcb' :
			$NCFile=$outfile;
			$LCBFile="workarea/".$username."~".$project_id.".lcb";
			$fh_lcb=fopen($LCBFile, 'w');
			genLCB($fh_lcb,$NCFile, $frame_delay, $song_tot_time, $LCBFile);
			//fclose($fh_lcb);
			echo "<table class=\"TableProp\">";
			//printf ("<tr><td bgcolor=lightgreen><h2>$channels channels and $Maxframe frames have been created for LOR lcb file</h2></td>\n");
			echo "<tr><th colspan=2>Instructions</th></tr>";
			printf ("<tr class=\"alt\"><td><h2><a href=\"%s\">Right Click here for  LOR lcb file</a></h2></td>\n",$LCBFile);
			echo "<td>Save lcb file into your light-o-rama/sequences directory</td></tr>\n";
			echo "</table>";
			break;
			case 'xml' :
			$xmlFile=genXML($username, $project_id);
			echo "<table class=\"TableProp\">";
			echo "<tr><th colspan=2>Instructions</th></tr>";
			printf ("<tr class=\"alt\"><td><h2><a href=\"%s\">Right Click here for XML file</a></h2></td>\n",$xmlFile);
			echo "</table>";				
			break;
			default :
			echo "This sequencer is not support<br />";
		}
		} 
	return;
}

function printArray($inArray)
{
	foreach($inArray as $currarray)
	{
		print_r($currarray);
		echo "<br />";
	}
}

function checkValidNCFiles($myarray, $numEntries, $project_id)
{
	$proj_array=getProjDetails($project_id);
	$frame_delay=$proj_array['frame_delay'];
	$model_name=$proj_array['model_name'];
	$username=$proj_array['username'];
	$member_id=$proj_array['member_id'];
	$modStr="workarea/".$username."~".$model_name."~";
	$cnt=0;
	showMessage('checking NC Files');
	echo "<table border=1>";
	foreach($myarray as $curr_array)
	{
		$validFlag=false;
		$phrase_name=$curr_array[0];
		$st_secs=$curr_array[1];
		$end_secs=$curr_array[2];
		$dur_secs=$curr_array[3];
		$frame_cnt=$curr_array[4];
		$frame_st=$curr_array[5];
		$frame_end=$curr_array[6];
		$effect_name=$curr_array[7];
		$effect_id=$curr_array[8];
		$fileName=$modStr.$effect_name."~".$frame_cnt.".nc";
		if (is_file($fileName))
		{
			$NCArray=getNCInfo($fileName);
			$validFlag=($NCArray[1]==($numEntries*3));
			if ($validFlag) 
			$validFlag=isValidNC($fileName);
		}
		echo "<tr><td>".$phrase_name."</td><td>".$effect_name."</td>";
		if ($effect_name != "None") 
			if(!$validFlag)
			{
				//	echo "<pre>$fileName: <font color=red>INVALID </font>(NCArray[1]==(numEntries*3),($NCArray[1]==($numEntries*3)</pre>";
				echo "<td>$fileName</td><td bgcolor=FF7096>INVALID </td>";
				if(!isset($NCArray[1])) $NCArray[1]=0;
				echo "<td>(NCArray[1]==(numEntries*3),($NCArray[1]==($numEntries*3)</td>";
			}
			else
			{
				//echo "<pre>$fileName: <font color=green>Valid</font> (NCArray[1]==(numEntries*3),($NCArray[1]==($numEntries*3)</pre>";
				echo "<td>$fileName</td> <td bgcolor=9EFF7A>Valid</td>";
				echo "<td>(NCArray[1]==(numEntries*3),($NCArray[1]==($numEntries*3)</td>";
			}
		else
			echo "<td>No file needed</td><td bgcolor=9EFF7A>Valid</td><td>No test needed</td>";
		if (!$validFlag) 
		$myarray[$cnt][7]="None"; // if the NC file is bad, skip the effect
		$cnt++;
		echo "</tr>";
	}
	echo "</table>\n";
	return($myarray);
}

function getHash($effect_id)
{
	$sql = "SELECT param_value FROM effects_user_dtl WHERE effect_id=".$effect_id;
	
	//echo "SQL from getHash : ".$sql."<br />";
	$result=nc_query($sql);
	$valStr="";
	while ($row=mysql_fetch_assoc($result))
	{
		$valStr.=trim($row['param_value']);
	}
	$checksum = md5($valStr);
	return($checksum);
}

function getProjHash($project_id, $effect_id)
{
	$sql = "SELECT check_sum FROM project_dtl WHERE project_id=".$project_id." AND effect_id=".$effect_id;
	$result=nc_query($sql);
	$valStr="";
	if ($row=mysql_fetch_assoc($result))
	{
		$check_hash=$row['check_sum'];
		if (strlen($check_hash)> 0)
		{
			// hash exists and has a value
			$retVal=$check_hash;
		}
		else { // hash exists but doesn't have value
			$retVal=updateHash($project_id, $effect_id);
		}
		} else { // This project ID doesn't exist
		$retVal="XXX";
	}
	return($retVal);
}

function removeNCFiles($testFile)
{
	if (is_file($testFile))
		unlink($testFile);
	return;
}

function updateHash($project_id, $effect_id)
{
	$hashVal=getHash($effect_id);
	$sql="UPDATE project_dtl SET check_sum='".$hashVal."' WHERE project_id=$project_id and effect_id=".$effect_id;
	$result=nc_query($sql);
	return($hashVal);
}

function checkHash($inHash, $project_id, $effect_id)
{
	$retVal=false;
	$retVal=($inHash == getHash($effect_id));
	/*echo "Incoming Hash : ".$inHash."<br />";
	echo "Hash check : ".getHash($effect_id)."<br />";
	if ($retVal)
		echo "They match <br />";
	else
		echo "They do not match<br />";
	die; */
	return($retVal);
}
?>