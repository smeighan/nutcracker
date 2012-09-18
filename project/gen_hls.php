<?php
function genHLS($username, $project_id, $sepStr=" ") {
	$filedir = "workarea/";
	$fileStr=$username."~".$project_id;
	$NCFile = $fileStr."~master.nc";
	$hlsout = $fileStr.".hlsnc";
	$fhhls = fopen($hlsout, 'w');
	copyHLS($NCFile, $fhhls, $sepStr);
	fclose($fhhls);
	return($hlsout);
}

function copyHLS($NCFile, $fhout, $sepStr=" ") {
	$f = fopen ($NCFile, "r");
	while ($line= fgets ($f)) {
		$outstr="";
		if ($line) {
			if (isInvalidLine($line) == false) {
				$myArray=myTokenizer($line, $sepStr);
				for($x=4;$x<count($myArray);$x++)
					$outstr.=$myArray[$x]." ";
				$outstr.="\n";
				fwrite($fhout,$outstr);
			}
		}
	}
	return;
}
?>