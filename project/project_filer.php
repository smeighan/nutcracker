<?php
function myTokenizer($in_str, $sepStr=" ") { // Takes a string and returns an array seperated by value of $sepStr 
	$outarray = array();
	$tokens = $sepStr;
	$tokenized = strtok($in_str, $tokens);
	$cnt=0;
	while ($tokenized) {
		$outarray[$cnt]=$tokenized;
		$cnt+=1;
		$tokenized = strtok($tokens);
   }
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
		echo "*** ERROR *** arrays must match length!\n";
	} else {
		$cnt=0;
		foreach($str_array1 as $val) {
			if ($prepend) 
				$newstr = $str_array2[$cnt].$sepStr.$val;
			else
				$newstr = $val.$sepStr.$str_array2[$cnt];
			
			$retArray[$cnt] = $newstr;
			$cnt+=1;
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
			echo "Adding $val zeros<br />";
			if (isset($retarr)) {
				$retarr=appendZeros($retarr,$val,false,$sepStr);
				//echo "VAL : $val <br />";
				// print_r($retarr);
			}
		} else {
			echo "Reading file $infile<br />";
//			$myarr=getFileData($infile,true,$sepStr);
//			$retarr=appendStr($retarr,$myarr);
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
	//echo "$model_file<br />";
	$retArray = array();
	$f = fopen ($model_file, "r");
	$ln= 0;
	while ($line= fgets ($f)) {
		if ($line) {
			if (isInvalidLine($line) == false) {
				//echo "$line<br />";
				$myArray=myTokenizer($line, $sepStr);
				//print_r($myArray);
				$tempStr="S ".$myArray[1]." P ".$myArray[2];
				//$myStr=revTokenizer($myArray,$stripHeader, $sepStr);
				$retArray[$ln]=$tempStr;
				$ln++;
			}
		}
    }
	fclose ($f);
	return($retArray);
}
?>