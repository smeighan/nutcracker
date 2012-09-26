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
			if ($cnt>0) {
				$line = trim( preg_replace( '/\s+/', ' ', $line ) ); // remove unwanted gunk
				$tempVal="";
				$tok=str_getcsv($line);
				$phrase_st=$tok[0];
				$phrase_end=$tok[1];
				$phrase_name=$tok[2];
				$sql="INSERT INTO project_dtl (phrase_name, start_secs, end_secs, project_id) VALUES ('".$phrase_name."',".$phrase_st.",".$phrase_end.",".$project_id.")";
				//echo $sql."\n";
				nc_query($sql);
				//print_r($tok);
			}
			else {
				if(!checkValidPhraseFile($line)) {
					fclose($fh);
					echo "Invalid input format for ".$filename."\n";
					return;
				} else {
					removePhrases($project_id);
					$cnt++;
				}
			}
		}
		fclose($fh);
		//$sql = "SELECT phrase_name, start_secs, end_secs FROM song_dtl where song_id = ".$song_id;
	}
	return;
}

function checkValidPhraseFile($line) {
	$retVal=false;
	$tok=str_getcsv($line);
	if ((count($tok)==3) && (substr($tok[0],0,12)=="Phrase Start"))
		$retVal=true;
	return($retVal);
}
function showFilesDir($directory, $project_id) {
	$sql = "SELECT model_name, song_name, artist, song_url, frame_delay, username, last_update_date, last_compile_date FROM project LEFT JOIN song ON project.song_id=song.song_id WHERE project_id=".$project_id;
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
	$files=glob($directory. "*.csv");
	$retStr='<table border="0" cellpadding="1" cellspacing="1">'."\n";
	$retStr.='<form action="project.php" method="post">'."\n";
	$retStr.='<input type="hidden" name="project_id" value="'.$project_id.'">'."\n";
	$retStr.='<input type="hidden" name="LoadPhraseFile" value="XXX">'."\n";
	//$retStr.='<tr><td>Directory</td><td><input class="FormFieldName" type="text" id="directory" name="directory" size="40" class="FormSelect" value="'.$directory.'" /></td><td><input type="submit" name="ChangeDirectory" class="SubmitButton" value="Change Directory"></td></tr>'."\n";
	if (count($files)>0) { 
		$retStr.='<tr><td>CSV File</td><td><select name="PhraseFile" class="FormFieldName" id="PhraseFile">'."\n";
		foreach($files as $file) {
			$path_parts = pathinfo($file);
			$filename = $path_parts['filename'];
			$retStr.="<option value=\"".$file."\">".$filename."</option>\n";
		}
		$retStr.="</td></tr></select>\n";
		$retStr.='<tr><td colspan="3"><input type="submit" name="SelectFile" class="SubmitButton" value="Select File for Input"></td></tr>'."\n";

	} 
	$retStr.='<tr><td colspan=3><a href="upload.html" onclick="window.open(\'upload.html\',\'popup\',\'width=300,height=100,scrollbars=no,resizable=no,toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0\'); return false">Upload a CSV file</a></td></tr>';
	$retStr.="</form>\n";
	if (count($files)==0) 
		$retStr.="<tr><td colspan=2><strong>No files found in the upload directory</strong></td></tr>\n";
	$retStr.="</table>\n";
	return($retStr);
}
function printWarning() {
?>
	<div class="WarnText">Please note that you will be removing all phrases from your already existing project.  
	This load will replace ALL phrases with the new phrases that you are loading from the CSV file.  
	If you wish to cancel out of this operation, simply use the back button on your browser.
	<p>This change is IRREVERSIBLE and permanent.</p></div>
<?php
}	

function printCSVInstruction() {
?>
	<h2>Format of CSV File</h2>
	<div class="FormText">The CSV file should be in the format of three columns with comma delimited fields.  You can use quotes to include things such as commas in your phrase names if you need to, however, quotes are not necessary.  The first field must contain the field names and the field names must match this format:  Phrase Start, Phrase End, Phrase Name.  After this header row, each row will contain the values of the phrases that you wish to add.  They should be in the following format:  &lt;decimal number for phrase start time in seconds&gt;, &lt;decimal number for phrase end time in seconds&gt;, &lt;textual name of phrase&gt; </div><br />
<?php
}
?>