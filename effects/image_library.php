<?php
require_once('../conf/header.php');
//
require_once('../effects/f_bars.php');
require_once('../effects/read_file.php');
$get=$_GET;
/*echo "<pre>";
print_r($_POST);
print_r($_GET);*/
extract ($_GET);
if(!isset($member_id)) $member_id=2;
show_images("gif",$member_id);
show_images("pictures",$member_id);

function show_images($effect_class,$member_id)
{
	if($effect_class=="gif")
	{
		$uploaddir = "../effects/gifs/$member_id"; 
		$row=1;
	}
	if($effect_class=="pictures")
	{
		$uploaddir = "../effects/pictures/$member_id"; 
		$row=2;
	}
	echo "<h2>$row) Here is your current $effect_class library</h2>\n";
	if($effect_class=="gif") echo "<h3>&nbsp;&nbsp;&nbsp;Here you should only have animated gif's</h3>\n";
	if($effect_class=="pictures") echo "<h3>&nbsp;&nbsp;&nbsp;Here you should only have static images (gif,jpg,png and bmp)</h3>\n";
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
	if(!is_dir($uploaddir))
	{
		echo "<h2><font color=red>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  There are no files in the $effect_class library</font> </h2>";
	}
	else
	{
		$dir = opendir($uploaddir); 
		$files = array(); 
		while($file = readdir($dir))
		{
			$tok=explode(".",$file);
			$tok=explode(".",$file);
			$ext=$tok[1];
			$ext=strtolower($ext);
			$image_types=array('gif','png','jpg','bmp');
			if(in_array($ext,$image_types))
			{
				$fullname = $uploaddir . "/" . $file;
				$findme="esized_";
				$pos1 = strpos($file, $findme);
				$findme="a_";
				$pos2 = strpos($file, $findme);
				$pos=0;
				if($pos1===true and $pos1==1) $pos=1;
				if($pos2===true and $pos2==0) $pos=2;
				//	echo "<pre>$file,pos,pos1,pos2=$pos,$pos1,$pos2</pre>\n";
				// Note our use of ===.  Simply == would not work as expected
				// because the position of 'a' was the 0th (first) character.
				if ($pos == 0)
				{
					//	echo "<pre>ifull=$fullname, file=$file, tok1=$tok[1]</pre>\n";
					$result_array=getimagesize($fullname);
					$is_animated_gif=is_ani($fullname)+0;
					$images++;
					if($images%10==0) echo "</tr><tr>\n";  // 10 images per row
					if ($result_array !== false)
					{
						$w=$result_array[0];
						$h=$result_array[1];
						$images_array[]=$file;
						$color_ani = "#000000"; 
						$color_bg="#FFFFFF";
						//($is_animated_gif==0) $color_ani = "#AAAAAA";
						//if($effect_class=="gif" and $is_animated_gif==0) $color_ani = "#AAAAAA"; // static images will be grayed out
						//if($effect_class=="pictures" and  $is_animated_gif==1) $color_ani = "#AAAAAA"; // animated images grayed out
						if($effect_class=="gif" and $is_animated_gif==0) $color_bg = "#AAAAAA"; // static images will be grayed out
						if($effect_class=="pictures" and  $is_animated_gif==1) $color_bg = "#AAAAAA"; // animated images grayed out
						//$color_bg=$color_ani;
						//if($color_ani=="#000000") $color_bg="#FFFFFF";
						if($w<100 and $h<100)
							echo "<td bgcolor=$color_bg><a href=\"$fullname\"> <img src=\"$fullname\"/><br/><font color=$color_ani>$file<br/> $w x $h</font></a></td>";
						else
						echo "<td><a href=\"$fullname\"> <img src=\"$fullname\"  height=\"100\" width=\"100\"/><br/><font color=$color_ani>$file<br/> $w x $h</font></a></td>";
					}
					else
					{
						echo "<td>File $file had an error</td>";
					}
				}
			}
		}
	}
	echo "</tr>";
	echo "</table>\n";
}
