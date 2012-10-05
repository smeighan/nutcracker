<?php
require_once('../conf/auth.php');
require_once('../conf/barmenu.php');
function removePhrases($project_id) {
	$sql="DELETE FROM project_dtl WHERE project_id=$project_id";
	nc_query($sql);
	//echo $sql."\n";
	return;
}

function getPhrasesFromFile($project_id, $filename) {
	$retVal=array();
	if (!is_file($filename))
		echo "** File ".$filename." not found!\n";
	else {
		$fh=fopen($filename,'r');
		$cnt=0;
		while($line=fgets($fh)) {
			if ($cnt==0) {
				if(!checkValidPhraseFile($line)) {
					fclose($fh);
					echo "Invalid input format for ".$filename."<br />";
					return;
				} else {
					removePhrases($project_id);
				}				
			}
			$line = trim( preg_replace( '/\s+/', ' ', $line ) ); // remove unwanted gunk
			$tempVal="";
			$tok=preg_split('/\s+/',$line,-1,PREG_SPLIT_NO_EMPTY);
			if (count($tok)>2) {
				$phrase_st=$tok[0];
				$phrase_end=$tok[1];
				$phrase_name=$tok[2];
				$sql="INSERT INTO project_dtl (phrase_name, start_secs, end_secs, project_id) VALUES ('".$phrase_name."',".$phrase_st.",".$phrase_end.",".$project_id.")";
				//echo $sql."\n";
				nc_query($sql);
				//print_r($tok);
			}
			$cnt++;
		}
		fclose($fh);
		//$sql = "SELECT phrase_name, start_secs, end_secs FROM song_dtl where song_id = ".$song_id;
	}
	return;
}

function getUploadFileStat($filename) {
	$fh=fopen($filename,'r');
	$cnt=0;
	while ($line=fgets($fh))
		if (checkValidPhraseFile($line))
			$cnt++;
	fclose($fh);
	return($cnt);
}	

function checkValidPhraseFile($line) {
	$tok=preg_split('/\s+/',$line,-1,PREG_SPLIT_NO_EMPTY);
	$retVal=(count($tok)>2);
	return($retVal);
}

function showFilesDir($directory, $project_id) {
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
	echo '<h2>Loading and replacing Phrases for "'.$song_name.'" by '.$artist.' (Model: '.$model_name.')</h2>';
	echo printWarning();
	echo "Files in the upload directory<br />";
	$files=glob($directory. $username."~*.txt");
	$showFiles='<table class="TableProp">'."\n";
	$retStr='<table border="0" cellpadding="1" cellspacing="1">'."\n";
	$retStr.='<form action="project.php" method="post">'."\n";
	$retStr.='<input type="hidden" name="project_id" value="'.$project_id.'">'."\n";
	$retStr.='<input type="hidden" name="username" value="'.$username.'">'."\n";
	$retStr.='<input type="hidden" name="LoadPhraseFile" value="XXX">'."\n";
	//$retStr.='<tr><td>Directory</td><td><input class="FormFieldName" type="text" id="directory" name="directory" size="40" class="FormSelect" value="'.$directory.'" /></td><td><input type="submit" name="ChangeDirectory" class="SubmitButton" value="Change Directory"></td></tr>'."\n";
	if (count($files)>0) { 
		$showFiles.='<tr><th>Uploaded File</th><th>Number of Phrases</th></tr>'."\n";
		$retStr.='<tr><td colspan="3">Upload File : &nbsp;&nbsp;<select name="PhraseFile" class="FormFieldName" id="PhraseFile">'."\n";
		$cnt=0;
		foreach($files as $file) {
			$cnt++;
			$path_parts = pathinfo($file);
			$filename = $path_parts['filename'];
			$tok=preg_split("/~+/", trim($filename));
			$prettyFilename=$tok[1];
			$retStr.="<option value=\"".$file."\">".$prettyFilename."</option>\n";
			if ($cnt%2==0) 
				$trStr='<tr>';
			else
				$trStr='<tr class="alt">';
			$showFiles.=$trStr.'<td>'.$prettyFilename.'</td><td>'.getUploadFileStat($file).'</td></tr>'."\n";
		}
		$retStr.="</td></tr></select>\n";
		$retStr.='<tr><td colspan="3"><input type="submit" name="SelectFile" class="SubmitButton" value="Select File for Input"></td></tr>'."\n";

	} 
	$retStr.='<tr><td colspan=3>&nbsp;</td></tr>';
	$retStr.='<tr><td><h2>If you do not see your text file listed, upload it here:</td><td colspan=2><a href="upload.php?username='.$username.'" onclick="window.open(\'upload.php?username='.$username.'\',\'popup\',\'width=500,height=150,scrollbars=no,resizable=no,toolbar=no,directories=no,location=no,menubar=no,status=no,left=200,top=200\'); return false">Upload a text file</a></h2></td></tr>';
	$retStr.="</form>\n";
	if (count($files)==0) 
		$retStr.="<tr><td colspan=2><strong>No files found in the upload directory</strong></td></tr>\n";
	$showFiles.="</table>\n";
	$retStr.="</table>\n";
	return($retStr.$showFiles);
}
function printWarning() {
?>
	<div class="WarnText">Please note that you will be removing all phrases from your already existing project.  
	This load will replace ALL phrases with the new phrases that you are loading from the Audacity text file.  
	If you wish to cancel out of this operation, simply use the back button on your browser.
	<p>This change is IRREVERSIBLE and permanent.</p></div>
<?php
}	

function printFileLoadInstruction() {
?>
	<h2>Format of Audacity Text File</h2>
	<div class="FormText">The Audacity text file should be in the format of three columns with spaces between the three fields. Each row will contain a phrase of data.  The fields are in this order:  &lt;Start Time&gt; &lt;End Time&gt; &lt;Phrase Name&gt;</div>  <br />
<?php
}
?>