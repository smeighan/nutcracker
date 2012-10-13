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

function getFilesFromDir($dir,$disk_stats)
{
	$files = array(); 
	$gifn=$gif=$totn=$tot=$nc=$ncn=0;
	$index_level=0;
	//echo "<pre>dir=$dir</pre>\n";
	//echo "<pre>";
	if ($handle = opendir($dir))
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file != "." && $file != "..")
			{
				if(is_dir($dir.'/'.$file))
				{
					$dir2 = $dir.'/'.$file; 
					//$files[] = getFilesFromDir($dir2,$disk_stats);
					$disk_stats = getFilesFromDir($dir2,$disk_stats);
				}
				else { 
					$fullname = $dir . "/" . $file;
					$filesize=filesize($fullname);
					$tok=explode(".",$file);
					$tok2=explode("_th.",$file);
					$c=count($tok2);
					//		echo "file=$file, c=$c\n";
					if($tok[1]=="gif")
					{
						$gifn++;
						$gif+=$filesize;
						if($c==2) // is this gif a file_th.gif? We leave those along
						{						
					//		echo "DONT DELETE _th.gif: $totn: rm $fullname. dir=$dir\n";
							$disk_stats['th']['cnt']++;
							$disk_stats['th']['size']+=$filesize;
						}
						else // this is any other file that is file.gif form, we will delete
						{
							if($dir=="../effects/workspaces/2")
							{
						//		echo "DONT DELETE SEAN: $totn: rm $fullname. dir=$dir\n";
								$disk_stats['gif_sean']['cnt']++;
								$disk_stats['gif_sean']['size']+=$filesize;
							}
							else
							{
							//	echo "DELETE GIF: $totn: rm $fullname. dir=$dir\n";
								//unlink($fullname);
								$disk_stats['gif']['cnt']++;
								$disk_stats['gif']['size']+=$filesize;
							}
							$totn++;
							$tot+=$filesize;
						}
					}
					else if($tok[1]=="nc") // is this a nutcracker file, file.nc?
					{
					//	echo "DONT DELETE NC: $totn: rm $fullname. dir=$dir\n";
						$ncn++; // yes, count stats , but dont delete
						$nc+=$filesize;
						$disk_stats['nc']['cnt']++;
						$disk_stats['nc']['size']+=$filesize;
					}
					else // any other file is going to be deleted (*.dat, *.gp,.etc.)
					{
					//	echo "DELETE: $totn: rm $fullname. dir=$dir\n";
						//	unlink($fullname);
						$totn++;
						$tot+=$filesize;
						$disk_stats['others']['cnt']++;
						$disk_stats['others']['size']+=$filesize;
					}
					//echo "dir=$dir,   file=$file $filesize</pre>\n";
				}
				} 
			} 
		closedir($handle); 
		return $disk_stats;
	}
	} 
// Usage 
$dir = 'workspaces'; 
$disk_stats=array();
$categories=array('th','gif','gif_sean','nc','others');
foreach($categories as $category)
{
	$disk_stats[$category]['cnt']=0;
	$disk_stats[$category]['size']=0;
}
$disk_stats = getFilesFromDir($dir,$disk_stats); 
echo "<pre>";
//print_r($disk_stats);
foreach($categories as $category)
{
$size=$disk_stats[$category]['size']/1024/1024;
	printf ("%-12s %9d %9d %11.2fmb\n",$category,$disk_stats[$category]['cnt'],
	$disk_stats[$category]['size'],$size);
}
?>
