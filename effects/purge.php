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

function getFilesFromDir($dir)
{
	$files = array(); 
	$gifn=$gif=$totn=$tot=$nc=$ncn=0;
	echo "<pre>";
	if ($handle = opendir($dir))
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file != "." && $file != "..")
			{
				if(is_dir($dir.'/'.$file))
				{
					$dir2 = $dir.'/'.$file; 
					$files[] = getFilesFromDir($dir2);
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
							echo "DONT DELETE _th.gif: $totn: rm $fullname. dir=$dir\n";
						}
						else // this is any other file that is file.gif form, we will delete
						{
						//echo "<pre>dir=$dir</pre>\n";
							if($dir=="workspaces/2")
								echo "DONT DELETE SEAN: $totn: rm $fullname. dir=$dir\n";
							else
							{
								echo "DELETE GIF: $totn: rm $fullname. dir=$dir\n";
							//	unlink($fullname);
							}
							$totn++;
							$tot+=$filesize;
						}
					}
					else if($tok[1]=="nc") // is this a nutcracker file, file.nc?
					{
						$NC_PURGE=0;
						if($NC_PURGE==1)
						{
							echo " DELETE NC: $totn: rm $fullname. dir=$dir\n";
							unlink($fullname); // temporary delete on Oct 13th. we are at 18gigs
						}
						else
						{
							echo "DONT DELETE NC: $totn: rm $fullname. dir=$dir\n";
						}
						$totn++;
						$ncn++; // yes, count stats , but dont delete
						$nc+=$filesize;
					}
					else // any other file is going to be deleted (*.dat, *.gp,.etc.)
					{
						echo "DELETE: $totn: rm $fullname. dir=$dir\n";
						unlink($fullname);
						$totn++;
						$tot+=$filesize;
					}
					//echo "dir=$dir,   file=$file $filesize</pre>\n";
				}
				} 
			} 
		closedir($handle); 
		echo "dir=$dir\n";
		echo "gifn=$gifn, gif=$gif\n";
		echo "ncn=$ncn, nc=$nc\n";
		echo "totn=$totn, tot=$tot\n";
		echo "</pre>";
	}
	} 

function array_flat($array)
{
	foreach($array as $a)
	{
		if(is_array($a))
		{
			$tmp = array_merge($tmp, array_flat($a));
		}
		else { 
			$tmp[] = $a;
		}
		} 
	return $tmp;
}
// Usage 
$dir = 'workspaces'; 
$foo = getFilesFromDir($dir); 
?>
