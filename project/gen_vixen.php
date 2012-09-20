<?php
function create_vir($fh_buff, $fh_vixen_vir) {
	$old_pixel=$channels=0;
	while (!feof($fh_buff))
	{
		$line = fgets($fh_buff);
		$tok=preg_split("/ +/", $line);
		$l=strlen($line);
		$MaxFrame= count($tok);
		if($tok[0]=='S' and $tok[2]=='P')
		{
			$rStr="";
			$gStr="";
			$bStr="";
			$string=$tok[1];
			$pixel=$tok[3];

			for($f=4;$f<$MaxFrame;$f++)		
			{
				$rgb=$tok[$f];
				$MyR=int2rgb($rgb);
				$rStr.=$MyR[0]." ";
				$gStr.=$MyR[1]." ";
				$bStr.=$MyR[2]." ";
			}
			$rStr.="\n";
			$gStr.="\n";
			$bStr.="\n";
			fwrite($fh_vixen_vir,$rStr);
			fwrite($fh_vixen_vir,$gStr);
			fwrite($fh_vixen_vir,$bStr);
		}
	}
}

function int2rgb ($inval) { //takes an integer value and converts to three RGB values as an array
	$r = ($inval >> 16) & 0xFF;
	$g = ($inval >> 8) & 0xFF;
	$b = $inval & 0xFF;
	$retarray=array($r,$g,$b);
	return ($retarray);
}

function string2Hex($instr) {
	$strHex=dechex($instr);
	if (strlen($strHex)==1) 
		$strHex="0".$strHex;
	$evalstr=array("0"=>0,"1"=>1,"2"=>2,"3"=>3,"4"=>4,"5"=>5,"6"=>6,"7"=>7,"8"=>8,"9"=>9,"A"=>10,"B"=>11,"C"=>12,"D"=>13,"E"=>14,"F"=>15,"a"=>10,"b"=>11,"c"=>12,"d"=>13,"e"=>14,"f"=>15);
	$firstnum=$evalstr[$strHex[0]];
	$secondnum=$evalstr[$strHex[1]];
	$firstnum*=16;
	$retval=chr($firstnum+$secondnum);
	return ($retval);
}

function getEventStr($infile) {
	$fh=fopen($infile,'r');
	$cnt=0;
	$accum="";
	$base64str="";
	while ($line = fgets($fh)) { 
		$colcnt=0;
		$tok=preg_split("/ +/", $line);
		foreach ($tok as $hexStr) {
			$hexStr = trim( preg_replace( '/\s+/', ' ', $hexStr ) ); // remove unwanted gunk
			if (strlen(trim($hexStr))>0) {
				$cnt++;
				$colcnt++;
				$hexVal=string2Hex($hexStr);
				$accum.=$hexVal;
				if ($cnt%3==0) {
					$base64str.=base64_encode($accum);
					$accum="";
				}
				//if (($cnt%100000)==0) 
				//	echo "."; //print out a dot for every 100,000 entries processed.
			}
		}
	}
	if (($cnt%3)!=0) {
		$padNum=3-($cnt%3);
		str_pad($accum,$padNum,"0");
		$base64str.=base64_encode($accum);
	}
	fclose($fh);
	return($base64str);
}

function countCol($infile) {
	$fh=fopen($infile,'r');
	if ($line = fgets($fh)) { 
		$colcnt=0;
		$tok=preg_split("/ +/", $line);
		foreach ($tok as $hexStr) {
			$hexStr = trim( preg_replace( '/\s+/', ' ', $hexStr ) ); // remove unwanted gunk
			if (strlen(trim($hexStr))>0) {
				$colcnt++;
			}
		}
	}
	fclose($fh);
	return($colcnt);
}


function genAllVixen($seq_duration, $frame_delay, $username, $project_id) {
//$timeSec = 0.7;
//$frame_delay = 50;
	$filedir = "workarea/";
	$fileStr=$username."~".$project_id;
	$NCFile = $fileStr."~master.nc";
	$vixout = $fileStr.".vix";
	$virout = $fileStr.".vir";
	$duration = $seq_duration*1000;
	$fh_vixen_vir=fopen($virout,'w');
	$fh_buff=fopen($NCFile,'r');
	create_vir($fh_buff, $fh_vixen_vir);
	fclose($fh_vixen_vir);
	fclose($fh_buff);

	$myEvent=getEventStr($virout);
	//echo "$myEvent\n";
	
	genVix($NCFile, $virout, $seq_duration, $frame_delay);
	make_vix($virout,$duration, $frame_delay, $myEvent);
	$retArr=array($vixout, $virout);
	return($retArr);
}

function genVix($NCFile, $vixen_vir, $seq_duration, $frame_delay) {
	$fh_buff=fopen($NCFile,"r") or die("Unable to open $filename_buff");
	$fh_vixen_vir=fopen($vixen_vir,"w") or die("Unable to open $vixen_vir");

	$old_pixel=$channels=0;
	//echo "<h3>$seq_duration seconds of animation with a $frame_delay ms frame timing = $TotalFrames frames of animation</h3>\n";
	while (!feof($fh_buff))
	{
		$line = fgets($fh_buff);
		$tok=preg_split("/ +/", $line);
		$l=strlen($line);
		$cnt= count($tok);
		$MaxFrame=$cnt-4;
		//echo "<pre>cnt=$cnt MaxFrame=$MaxFrame, line=$line</pre>\n";
		if($tok[0]=='S' and $tok[2]=='P')
		{
			$string=$tok[1];
			$pixel=$tok[3];
		//	echo "<pre>s,p=$string,$pixel: $line</pre>\n";
			for($rgbLoop=1;$rgbLoop<=3;$rgbLoop++)
			{
				for($f=1;$f<$MaxFrame;$f++)
				{
					$rgb=$tok[$f+3];
					$r = ($rgb >> 16) & 0xFF;
					$g = ($rgb >> 8) & 0xFF;
					$b = $rgb & 0xFF;
					if($rgbLoop==1)
					{
						$c='R';$color=16711680;
						$val=$r;
					}
					if($rgbLoop==2)
					{
						$c='G';$color=65280;
						$val=$g;
					}
					if($rgbLoop==3)
					{
						$c='B';$color=255;
						$val=$b;
					}
					fwrite($fh_vixen_vir,sprintf("%d ",$val));
					//printf("%d ",$val);
					
				}
				$channels++;
				fwrite($fh_vixen_vir,sprintf("\n"));
				//printf("\n");
			}
		}
	}
}
function make_vix($vixen_vir,$duration,$frame_delay,$eventdata)
{
	$NumCols=countCol($vixen_vir);
	$duration=$NumCols*$frame_delay;
	$path_parts = pathinfo($vixen_vir);
	$dirname   = $path_parts['dirname'];
	$basename  = $path_parts['basename']; 
	$extension =$path_parts['extension'];
	$filename  = $path_parts['filename'];
	$file_vix = $dirname . "/" . $filename . ".vix";
	$fh_vir=fopen($vixen_vir,"r") or die("Unable to open $vixen_vir");
	$fh = fopen($file_vix,"w") or die ("unable to open $file_vix");
	fwrite($fh,sprintf("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"));
	fwrite($fh,sprintf("<Program>\n"));
	fwrite($fh,sprintf("<Time>$duration</Time>\n"));
	fwrite($fh,sprintf("<EventPeriodInMilliseconds>$frame_delay</EventPeriodInMilliseconds>\n"));
	fwrite($fh,sprintf("<MinimumLevel>0</MinimumLevel>\n"));
	fwrite($fh,sprintf("<MaximumLevel>255</MaximumLevel>\n"));
	fwrite($fh,sprintf("<AudioDevice>-1</AudioDevice>\n"));
	fwrite($fh,sprintf("<AudioVolume>0</AudioVolume>\n"));
	$lines=0;
	rewind($fh_vir);
	while (!feof($fh_vir))
	{
		$line = fgets($fh_vir);
		$tok=preg_split("/ +/", $line);
		$c=count($tok);
		if($c>1) $lines++;
	}
	rewind($fh_vir);
	$plugin=0;
	if($plugin==1)
	{
		fwrite($fh,sprintf("<PlugInData>\n"));
		fwrite($fh,sprintf("<PlugIn name=\"Adjustable preview\" key=\"-1193625963\" id=\"0\" enabled=\"True\" type=\"Output\" from=\"1\" to=\"$lines\">\n"));
		fwrite($fh,sprintf("<RedirectOutputs>False</RedirectOutputs>\n"));
		fwrite($fh,sprintf("<Display>\n"));
		fwrite($fh,sprintf("<Height>211</Height>\n"));
		fwrite($fh,sprintf("<Width>459</Width>\n"));
		fwrite($fh,sprintf("<PixelSize>3</PixelSize>\n"));
		fwrite($fh,sprintf("<Brightness>5</Brightness>\n"));
		fwrite($fh,sprintf("</Display>\n"));
	}
	else
	{
		fwrite($fh,sprintf("<PlugInData />\n"));
	}
	fwrite($fh,sprintf("<Channels>\n"));
	$channel=0;
	while (!feof($fh_vir))
	{
		$line = fgets($fh_vir);
		$tok=preg_split("/ +/", $line);
		$c=count($tok);
		if($c>1)
		{
			$channel++;
			if($channel%3==1)
			{
				$color=-65536; $rgb="R";
			}
			if($channel%3==2)
			{
				$color=-16744448; $rgb="G";
			}
			if($channel%3==0)
			{
				$color=-16776961; $rgb="B";
			}
			$channel_name = "Channel $channel $rgb";
			$output=$channel-1;
			fwrite($fh,sprintf("<Channel color=\"$color\" output=\"$output\" id=\"0\" enabled=\"True\">$channel_name</Channel>\n"));
		}
	}
	fwrite($fh,sprintf("</Channels>\n"));
	if($plugin==1)
	{
		fwrite($fh,sprintf("</PlugIn>\n"));
		fwrite($fh,sprintf("</PlugInData>\n"));
	}
	fwrite($fh,sprintf("<SortOrders lastSort=\"-1\" />\n"));
	fwrite($fh,sprintf("<EventValues>"));
	fwrite($fh,$eventdata);
	fwrite($fh,sprintf("</EventValues>\n"));
	fwrite($fh,sprintf("</Program>\n"));
	fclose($fh);
	fclose($fh_vir);
}
?>