<?php

function getVixHeader($timestr, $frame_delay) {
	$retstr ="<?xml version=\"1.0\" encoding=\"utf-8\"?>
<Program>
<Time>".$timestr."</Time>
<EventPeriodInMilliseconds>".$frame_delay."</EventPeriodInMilliseconds>
<MinimumLevel>0</MinimumLevel>
<MaximumLevel>255</MaximumLevel>
<AudioDevice>-1</AudioDevice>
<AudioVolume>0</AudioVolume>
<PlugInData />\n";
	return($retstr);
}

function writeVixHeader($fh, $time, $f_delay) {
	fwrite($fh,getVixHeader($time, $f_delay));
	return;
}

function getNCHeader($file_name, $sepStr=" ") {
	$inFile=$file_name;
	$retArray = array();
	$f = fopen ($inFile, "r");
	$ln= 0;
	while ($line= fgets ($f)) {
		if ($line) {
			if (isInvalidLine($line) == false) {
				$tempArray = array();
				$myArray=myTokenizer($line, $sepStr);
				$tempArray[]=$myArray[1];
				$tempArray[]=$myArray[3];
				//$myStr="S $myArray[1] P $myArray[3]";
				$retArray[$ln++]=$tempArray;
				//print_r($myArray);
				// $ln++;
			}
		}
    }
	fclose ($f);
	return($retArray);
}

function getNCBody($file_name, $sepStr=" ") {
	$inFile=$file_name;
	$retArray = array();
	$f = fopen ($inFile, "r");
	while ($line= fgets ($f)) {
		if ($line) {
			if (isInvalidLine($line) == false) {
				$tempStr = "";
				$myArray=myTokenizer($line, $sepStr);
				for ($x=4; $x<count($myArray); $x++)
					$tempStr.=$myArray[$x]." ";
			}
		}
		$retArray[]=$tempStr;
    }
	fclose ($f);
	return($retArray);
}


function writeFile($fileout, $data) {
	$fh = fopen($fileout, 'a');
	fwrite($fh, $data);
	return;
}

function getMaxSMaxP($inArray) {
	$retArray = array();
	$maxS = -1;
	$maxP = -1;
	foreach ($inArray as $row) {
		$currS = $row[0];
		$currP = $row[1];
		if ($currS>$maxS) $maxS=$currS;
		if ($currP>$maxP) $maxP=$currP;
	}
	$retArray[]=$maxS;
	$retArray[]=$maxP;
	return($retArray);
}

function getChanOut($Sval,$Pval,$maxP, $routput) {
	$goutput = $routput+1;
	$boutput = $routput+2;
	$retStr="<Channel color=\"-65536\" output=\"".$routput."\" id=\"".$routput."\" enabled=\"True\">Channel ".($routput+1)." R</Channel>\n";
	$retStr.="<Channel color=\"-16744448\" output=\"".$goutput."\" id=\"".$goutput."\" enabled=\"True\">Channel ".($goutput+1)." G</Channel>\n";
	$retStr.="<Channel color=\"-16779661\" output=\"".$boutput."\" id=\"".$boutput."\" enabled=\"True\">Channel ".($boutput+1)." B</Channel>\n";
	return($retStr);
}

function getRGB($Inarray) {   // reads an array of frames and returns three arrays one for R, G, and B respectively
	$retArray=array();
	foreach($Inarray as $line) {
		$r_str="";
		$g_str="";	
		$b_str="";
		$myTokens=myTokenizer($line);
		foreach($myTokens as $Item) {
			$rgb=intval($Item);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;
			$r_str.=$r." ";
			$g_str.=$g." ";
			$b_str.=$b." ";
		}
		$tempArray=array($r_str, $g_str, $b_str);
		$retArray[]=$tempArray;
	}
	return ($retArray);
}


function writeVIR($fh, $rgbArray, $sepStr=" ") {
	foreach ($rgbArray as $line) {
		fwrite($fh, $line[0]);
		fwrite($fh, "\n");
		fwrite($fh, $line[1]);
		fwrite($fh, "\n");
		fwrite($fh, $line[2]);
		fwrite($fh, "\n");
	}
	return;
}

function getEventVal($rgbArray) {
	$eventdata='';
	foreach($rgbArray as $rgbLine) 
		foreach ($rgbLine as $rgb)
			$myRGB=myTokenizer($rgb);
			foreach($myRGB as $val)
				$eventdata .= chr($val);
	return(base64_encode($eventdata));
}

function genVIX($timeSec, $frame_delay, $fh, $NCfile, $rgbArray) {
	$timeMSec = $timeSec * 1000;
	writeVixHeader($fh, $timeMSec, $frame_delay);
	$NCArray=getNCHeader($NCfile);
	$SandP=getMaxSMaxP($NCArray);
	$maxP=$SandP[1];
	$maxS=$SandP[0];
	$chan=0;
	fwrite($fh,"<Channels>\n");
	for ($y=0;$y<$maxS;$y++) {
		for ($x=0; $x<$maxP; $x++) {
			fwrite($fh,getChanOut($y,$x,$maxP, $chan));
			$chan+=3;
		}
	}
	fwrite($fh, "</Channels>\n");
	fwrite($fh, "<SortOrders lastSort=\"-1\" />\n");
	fwrite($fh, "<EventValues>");
	fwrite($fh, getEventVal($rgbArray));
	fwrite($fh, "</EventValues>\n");
	fwrite($fh, "</Program>");
}

function genVIR($fh, $NCfile) {
	$NCArray=getNCBody($NCfile);
	//print_r($NCArray);
	//echo "<br />";
	$rgbArray=getRGB($NCArray);
	//print_r($rgbArray);
	writeVIR($fh, $rgbArray);
	return($rgbArray);
}

function genAllVixen($timeSec, $frame_delay, $username, $project_id) {
//$timeSec = 0.7;
//$frame_delay = 50;
	$filedir = "workarea/";
	$fileStr=$username."~".$project_id;
	$NCFile = $fileStr."~master.nc";
	$vixout = $fileStr.".vix";
	$virout = $fileStr.".vir";
	$fhvix = fopen($vixout, 'w');
	$fhvir = fopen($virout, 'w');
	$rgbArray=genVir($fhvir, $NCFile);
	genVix($timeSec, $frame_delay, $fhvix, $NCFile, $rgbArray);
	fclose($fhvix);
	fclose($fhvir);
	$retArr=array($vixout, $virout);
	return($retArr);
}
?>