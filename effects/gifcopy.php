<?php
require_once('../conf/auth.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nutcracker: RGB Effects Builder</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="last-modified" content=" 24 Feb 2012 09:57:45 GMT"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
<meta name="robots" content="index,follow"/>
<meta name="googlebot" content="noarchive"/>
<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"> 
<meta name="description" content="RGB Sequence builder for LOR, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, LOR, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php

function getFilesFromDir($dir) { 
	
	$files = array(); 
	$gifn=$gif=$totn=$tot=0;
	$rows=0;
	echo "<table border=1>";
	if ($handle = opendir($dir)) {
		
		while (false !== ($file = readdir($handle))) { 
			if ($file != "." && $file != "..") { 
				if(is_dir($dir.'/'.$file)) { 
					$dir2 = $dir.'/'.$file; 
					$files[] = getFilesFromDir($dir2); 
					
					} 
				else { 
					$fullname = $dir . "/" . $file;
					$filesize=filesize($fullname);
					$tok=explode(".",$file);
					if($tok[1]=="gif") 
					{
						$gifn++;
						$gif+=$filesize;
					}
					else
					{
						//	echo "<pre>rm $fullname</pre>\n";
						//	unlink($fullname);
						$totn++;
						$tot+=$filesize;
					}
					
					//echo "<pre>dir=$dir,   file=$file $filesize</pre>\n";
					
					} 
				} 
			} 
		closedir($handle); 
		$rows++;
		echo "<tr><td>$rows</td>";
		echo "<td>$dir2</td>";
		echo "<td>$dir</td>";
		echo "<td>gifn=$gifn, gif=$gif</td>";
		echo "<td>totn=$totn, tot=$tot</td>";
		
		$tok=explode("/",$dir);
		$member_id=$tok[1];
		$dirFrom="gifs/2";
		$dirTo="gifs/$member_id";
		if($member_id!=2)
		{
			echo "<td>CopyFilesFromDir($dirFrom,$dirTo)</td>";
			CopyFilesFromDir($dirFrom,$dirTo);
		}
		echo "</tr>\n";
		} 
	echo "</table>";
	} 

function array_flat($array) { 
	
	foreach($array as $a) { 
		if(is_array($a)) { 
			$tmp = array_merge($tmp, array_flat($a)); 
			} 
		else { 
			$tmp[] = $a; 
			} 
		} 
	
	return $tmp; 
	} 

function CopyFilesFromDir($dir,$dirTo) { 
	
	$files = array(); 
	$gifn=$gif=$totn=$tot=0;
	echo "<table border=1>";
	if ($handle = opendir($dir)) {
		
		while (false !== ($file = readdir($handle))) { 
			if ($file != "." && $file != "..") { 
				if(is_dir($dir.'/'.$file)) { 
					$dir2 = $dir.'/'.$file; 
					$files[] = getFilesFromDir($dir2); 
					
					} 
				else { 
					$fullname = $dir . "/" . $file;
					$filesize=filesize($fullname);
					$tok=explode(".",$file);
					if($tok[1]=="gif") 
					{
						$gifn++;
						$gif+=$filesize;
						
						$newfile=$dirTo . "/" . $file;
						
						if (file_exists($dirTo)) {
							} else {
							echo "The directory $dirTo does not exist, creating it";
							mkdir($dirTo, 0777);
						}
						
						if (!copy($fullname, $newfile)) {
							echo "failed to copy $file...\n";
						}
						
						
						echo "<td>copy($fullname, $newfile)</td>";
					}
					else
					{
						//	echo "<pre>rm $fullname</pre>\n";
						//	unlink($fullname);
						$totn++;
						$tot+=$filesize;
					}
					
					//echo "<pre>dir=$dir,   file=$file $filesize</pre>\n";
					
					} 
				} 
			} 
		closedir($handle); 
		echo "<tr><td>$dir2</td>";
		echo "<td>$dir</td>";
		echo "<td>gifn=$gifn, gif=$gif</td>";
		echo "<td>totn=$totn, tot=$tot</td>";
		echo "</tr>\n";
		
		} 
	echo "</table>";
	} 
// Usage 
$dir = 'workspaces'; 
$foo = getFilesFromDir($dir); 

?>

