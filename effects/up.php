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
<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"/> 
<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
$tokens=explode("=",$_SERVER['QUERY_STRING']);
$uploaddir = $tokens[1];
//$uploaddir;
{
	if (file_exists($uploaddir))
	{
		} else {
		echo "The directory $uploaddir does not exist, creating it";
		mkdir($uploaddir, 0777);
	}
	$dir = opendir($uploaddir); 
	$files = array(); 
	echo "<h2>Here is your current gif library</h2>\n";
	echo "<table border=1>";
	echo "<tr>";
	/*
	Returns a array with 4 elements.
	The 0 index is the width of the image in pixels.
	The 1 index is the height of the image in pixels.
	The 2 index is a flag for the image type:
	1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(orden de bytes intel), 8 = TIFF(orden de bytes motorola), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF, 15 = WBMP, 16 = XBM. 
	*/
	$images=0;
	while($file = readdir($dir))
	{
		$tok=explode(".",$file);
		$ext=$tok[1];
		//	echo "<pre>ext=$ext, file=$file</pre>\n";
		$image_types=array('gif','png','jpg');
		$ext=strtolower($ext);
		if(in_array($ext,$image_types))
		{
			$fullname = $uploaddir . "/" . $file;
			#//	echo "<pre>full=$fullname, file=$file, tok1=$tok[1]</pre>\n";
			$result_array=getimagesize($fullname);
			$images++;
			if($images%8==0) echo "</tr><tr>\n";
			if ($result_array !== false)
			{
				$w=$result_array[0];
				$h=$result_array[1];
				echo "<td><img src=\"$fullname\"/><br/>$file<br/> $w x $h</td>";
			}
			else
			{
				echo "<td>File $file had an error</td>";
			}
		}
	}
	echo "</tr>";
	echo "</table>\n";
}
?>
<form action="./upload.php<?php echo "?uploaddir=$uploaddir"; ?>" method="post" enctype="multipart/form-data">
<p>
<label for="file">Select a file from your computer to add to your Nutcracker GIF library:</label> <input type="file" name="userfile" id="file"> <br />
<button>Upload File</button>
<p>
</form>
