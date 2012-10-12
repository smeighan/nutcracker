<?php
//

function f_gif($get)
{
	if(!isset($get['brightness']))   $get['brightness']="0";
	if(!isset($get['fade_in']))  $get['fade_in']="0";
	if(!isset($get['fade_out']))  $get['fade_out']="0";
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	extract ($get);
	/*	Array
	(
	[username] => f
	[user_target] => A
	[effect_class] => gif
	[effect_name] => GIF2
	[file1] => lights11.gif
	[frame_delay] => 111
	[window_degrees] => 180
	[brightness] => 0
	[seq_duration] => 5
	[fade_in] => 0
	[fade_out] => 0
	[submit] => Submit Form to create your effect
	[OBJECT_NAME] => gif
	[batch] => 0
	)*/
	echo "<pre>";
	print_r($get);
	echo "</pre>\n";
	
	// Set window_degrees to match the target
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	//
	set_time_limit(0);
	ini_set("memory_limit","1024M");
	require_once("../effects/read_file.php");
	//
	//
	//show_array($_GET,"_GET");
	if($batch==0) show_array($get,"$effect_class Effect Settings");
	$member_id=get_member_id($username);
	$get['member_id']=$member_id;
	$path ="../effects/workspaces/$member_id";
	$gifpath ="gifs/$member_id";
	$directory=$path;
	if (!file_exists($directory))
	{
		if($batch==0) echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$base = $user_target . "~" . $effect_name;
	$t_dat = $user_target . ".dat";
	$xdat = $user_target ."~".  $effect_name . ".dat";
	$path="../targets/". $member_id;
	$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
	$minStrand = $arr[0];
	// lowest strand seen on target
	$minPixel = $arr[1];
	// lowest pixel seen on skeleton
	$maxStrand = $arr[2];
	// highest strand seen on target
	$maxPixel = $arr[3];
	// maximum pixel number found when reading the skeleton target
	$maxI = $arr[4];
	// maximum number of pixels in target
	$tree_rgb = $arr[5];
	$tree_xyz = $arr[6];
	$file = $arr[7];
	$min_max = $arr[8];
	$strand_pixel = $arr[9];
	$path="../effects/workspaces/". $member_id;
	srand(time());
	$maxFrame = 100;
	//$maxTrees=6;	// how many tree to draw at one time
	$seq_number = 0;
	require_once("GIFDecoder.class.php");
	$FIC="flag.gif";
	$FIC="bells2.gif";
	$FIC="usaCa.gif";
	$FIC="tree.gif";
	$FIC="lights11.gif";
	$FIC="wreath06.gif";
	$FIC="usaCa.gif";
	$FIC=$file1;
	$FIC2=$gifpath . "/" . $FIC;
	$FIC3=$gifpath . "/zz_" . $FIC;
	if($batch==0) echo "<h1>Processing file $FIC2</h1>";
	$src=$FIC2;
	$dst=$FIC3;
	//rsize($src, $dst, $maxStrand, $maxPixel);
	//image_resize($src, $dst, $maxStrand, $maxPixel, 0);
	if(file_exists($FIC2))
	{
		$GIF_frame = fread (fopen ($FIC2,'rb'), filesize($FIC2));
		if($batch==0) echo "<br/><img src=\"" . $FIC2 . "\"/><br/>\n";
		$decoder = new GIFDecoder ($GIF_frame);
		$frames = $decoder->GIFGetFrames();
		for ( $i = 0; $i < count ( $frames ); $i++ )
		{
			$tokens=explode (".",$FIC2);
			$FIC_new = $tokens[0];
			$fname = ( $i < 10 ) ? $FIC_new."_0$i.giftmp" : $FIC_new."_$i.giftmp";
			$hfic=fopen ( $fname, "wb" );
			$file_array[$i]=$fname;
			fwrite ($hfic , $frames [ $i ] );
			fclose($hfic);
		}
		$maxFrame=count ( $frames );
		//$file="ball-icon.png";
		//$image_array=get_image($batch,$file);
		$path = "../effects/workspaces/" . $member_id;
		for ($frame = 1; $frame <= $maxFrame; $frame++)
		{
			$file = $file_array[$frame-1];
			//
			//	read in next frame of gif animation into array
			$image_array=get_image($batch,$file,$frame,$maxStrand,$maxPixel,$window_degrees);
			/*if($batch==0) echo "<pre>";
			print_r($image_array);
			if($batch==0) echo "</pre>\n";*/
			$x_dat = $base . "_d_" . $frame . ".dat";
			// for spirals we will use a dat filename starting "S_" and the tree model
			$dat_file[$frame] = $path . "/" . $x_dat;
			$dat_file_array[] = $dat_file[$frame];
			$fh_dat[$frame] = fopen($dat_file[$frame], 'w') or die("can't open file");
			fwrite($fh_dat[$frame], "#    " . $dat_file[$frame] . "\n");
			//
			//	All pixels of this gif frame are in image_array, write them out to dat file.
			draw_icon($fh_dat[$frame], $image_array, 0, $frame, $minStrand, $maxStrand, $minPixel, $maxPixel, $tree_xyz, $strand_pixel,$brightness,$window_degrees);
			//	draw_icon(	$fh_dat [$frame],$big_image_array[4],66,$frame,$minStrand ,$maxStrand,$minPixel,$maxPixel,$tree_xyz,$strand_pixel);
		}
		if (!isset($show_frame))
			$show_frame = 'N';
		if (!isset($seq_duration))
			$seq_duration = 5;
		if (!isset($frame_delay))
			$frame_delay = 100;
		if (!isset($username))
			$username = 'f';
		$amperage = array();
		$x_dat_base = $base . ".dat";
		make_gp($batch,$arr,$path, $x_dat_base, $t_dat, $dat_file_array, $min_max, $username, $frame_delay,$amperage, $seq_duration, $show_frame);
		list($usec, $sec) = explode(' ', microtime());
		$script_start = (float)$sec + (float)$usec;
		$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out);
	}
	else
	{
		if($batch==0) echo "<pre><h2>Your gif file $FIC2 does not exist. please upload it</pre><h2>\n";
	}
}

function draw_icon($fh, $image_array, $offset, $frame, $minStrand, $maxStrand, $minPixe, $maxPixel, $tree_xyz, $strand_pixel,$brightness,$window_degrees)
{
/*echo "<pre>";
print_r($image_array);*/
	$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	/*if($batch==0) echo "<pre>";
	print_r($window_array);
	if($batch==0) echo "</pre>\n";*/
	$seq_number = 0;
	for ($s = 0; $s <= $maxStrand; $s++)
	{
		for ($p = 1; $p <= $maxPixel; $p++)
		{
			if(!isset($image_array[$s][$p]) or $image_array[$s][$p]==null)
			{
				$rgb_val=0;
			}
			else
			{
				$rgb_val = $image_array[$s][$p];
			}
			if ($s >= 1 and $s <= $maxStrand and $p >= 1 and $p <= $maxPixel and isset($tree_xyz[$s][$p]))
			{
				$xyz = $tree_xyz[$s][$p];
				$seq_number++;
				$string = $user_pixel = 0;
				//		if($s==10) $rgb_val=hexdec("#FFFF00");
				if($brightness>0.0)
				{
					$r = ($rgb_val >> 16) & 0xFF;
					$g = ($rgb_val >> 8) & 0xFF;
					$b = $rgb_val & 0xFF;
					$HSV=RGB_TO_HSV($r,$g,$b);
					$H=$HSV['H']; $S=$HSV['S']; $V=$HSV['V'];
					if($V>0.1) $V=$V+$brightness;
					if($V>1) $V=1;
					$HSV['V']=$V;
					echo "<pre>";
					print_r($HSV);
					echo "</pre>";
					$rgb_val=HSV_TO_RGB($H,$S,$V);
				}
		//		if(in_array($s,$window_array)) // Is this strand in our window?, If yes, then we output lines to the dat file
				{
				if($rgb_val!=0) $rgb_val=hexdec("#888888");
					fwrite($fh, sprintf("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n", $s, $p, $xyz[0], $xyz[1], $xyz[2], $rgb_val, $string, $user_pixel, $strand_pixel[$s][$p][0], $strand_pixel[$s][$p][1], $frame, $seq_number));
					$hex=dechex($rgb_val);
//	printf ("<pre>t1 %4d %4d %9.3f %9.3f %9.3f %s %d %d %d %d</pre>\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$hex,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number);
				}
			}
		}
	}
}

function get_image($batch,$file,$frame,$maxStrand,$maxPixel,$window_degrees)
{
	$path = "";
	$directory = $path;
	$image_path = $path . "/" . $file;
	$image_path=$file;
	$tokens = explode(".", $file);
	$image_type = $tokens[1];
	if ($image_type == "png")
		$image = imagecreatefrompng($image_path);
	else if ($image_type == "jpg")
		$image = imagecreatefromjpeg($image_path);
	else if ($image_type == "gif")
		$image = imagecreatefromgif($image_path);
	else if ($image_type == "giftmp")
		$image = imagecreatefromgif($image_path);
	else die("Invalid file type of $image_type");
	$size = getimagesize($image_path);
	$img_width = $size[0];
	$img_height = $size[1];
	/*	include('SimpleImage.php');
	$image = new SimpleImage();
	$image->load($file);
	if($img_width<$img_height)
		$image->resizeToWidth($maxStrand*2);
	else
	$image->resizeToHeight($maxPixel*2);
	$image->save($file2,$img_type_number);
	$file=$file2;*/
	//$image=resizeImage($Image,$maxStrand,$maxPixel); 
	//if($batch==0) echo "<pre>img width,height = $img_width,$img_height  max strand,pixel=$maxStrand,$maxPixel</pre>\n";
	$s = 0;
	//	If window degrees = 360, then leave maxStrand alone otherwise sclae it.
	$maxStrand = intval( $maxStrand * $window_degrees/360);
	$precision = intval((max($img_height,$img_width) / $maxStrand) + 0.5);
	$precision = intval($img_width / $maxStrand + 0.5);
	//if ($img_width < 50)
		$precision = 1;
	/*
	$cc = ImageColorsTotal($im2);
	for($n=0; $n<$cc; ++$n)
	{
		$c = ImageColorsForIndex($im2, $n);
		print 
		sprintf('<span style="background:#%02X%02X%02X">&nbsp;</span>',
		$c['red'], $c['green'], $c['blue']);
	}
	*/
	if($frame==1)
	{
		$cc = ImageColorsTotal($image);
		if($batch==0) echo "<h3>Color palette used in this gif file</h3>\n";
		if($batch==0) echo "<table border=1>";
		if($batch==0) echo "<tr><th>Color<br/>Index</th>";
		if($batch==0) echo "<th>R</th>";
		if($batch==0) echo "<th>R</th>";
		if($batch==0) echo "<th>G</th>";
		if($batch==0) echo "<th>B</th>";
		if($batch==0) echo "<th>Hex</th>";
		if($batch==0) echo "</tr>\n";
		for($n=0; $n<$cc; ++$n)
		{
			$c = ImageColorsForIndex($image, $n);
			$hex = fromRGB ($c['red'],$c['green'],$c['blue']);
			if($batch==0) echo "<tr><td>$n</td><td> ". $c['red'] . "</td><td> " . $c['green'] . "</td><td> " . $c['blue'] . " </td><td bgcolor=\"$hex\">$hex</td></tr>\n";
			sprintf('<span style="background:#%02X%02X%02X">&nbsp;</span>',	$c['red'], $c['green'], $c['blue']);
		}
		if($batch==0) echo "</table>";
	}
	$s=0;
	$w = imagesx($image);
	$h = imagesy($image);
	//	echo "<pre>w,h=$w,$h img_width,img_height=$img_width,$img_height</pre>\n";
	$img_width=$w;
	$img_height=$h;
	$r = $g = $b = 0;
	/*for($y = 0; $y < $h; $y++)
	{
		for($x = 0; $x < $w; $x++)
		{
			$rgb = imagecolorat($img, $x, $y);
			$r += $rgb >> 16;
			$g += $rgb >> 8 & 255;
			$b += $rgb & 255;
		}
		}*/
	for ($x = 0; $x < $img_width; $x += $precision)
	{
		$s++;
		$p_raw = 0;
		$p=0;
		for ($y = 0; $y < $img_height; $y += $precision)
		{
			$p++;
			$x1=$x; $y1=$y;
			if($x1<1) $x1=1; if($x1>$img_width) $x1=$img_width;
			if($y1<1) $y1=1; if($y1>$img_height) $y1=$img_height;
			$rgb_index = imagecolorat($image, $x1, $y1);
			//	echo "<pre>$rgb_index=imagecolorat( $x1, $y1)</pre>\n";
			$cols = ImageColorsForIndex($image, $rgb_index);
			$r = $cols['red'];
			$g = $cols['green'];
			$b = $cols['blue'];
			$rgbhex = fromRGB ($r,$g,$b);
			$rgb_val = hexdec($rgbhex);
			$image_array[$s][$p] = $rgb_val;
			//			if($batch==0) echo "<pre>x,y,rgb= $x,$y,($r,$g,$b), rgbval=$rgb_val</pre>";
		}
	}
	return $image_array;
}
/*

function fromRGB($R, $G, $B)
{
	$hex = "#";
	$hex.= str_pad(dechex($R), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($G), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($B), 2, "0", STR_PAD_LEFT);
	return $hex;
}
*/

function setTransparency($new_image,$image_source)
{
	$transparencyIndex = imagecolortransparent($image_source); 
	$transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255); 
	if ($transparencyIndex >= 0)
	{
		$transparencyColor    = imagecolorsforindex($image_source, $transparencyIndex);
	}
	$transparencyIndex    = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']); 
	imagefill($new_image, 0, 0, $transparencyIndex); 
	imagecolortransparent($new_image, $transparencyIndex);
}

function resizeImage($originalImage,$toWidth,$toHeight)
{
	list($width, $height) = getimagesize($originalImage); 
	$xscale=$width/$toWidth; 
	$yscale=$height/$toHeight; 
	if($xscale<1) $xscale=1;
	if($yscale<1) $yscale=1;
	if ($yscale>$xscale)
	{
		$new_width = round($width * (1/$yscale)); 
		$new_height = round($height * (1/$yscale));
	}
	else { 
		$new_width = round($width * (1/$xscale)); 
		$new_height = round($height * (1/$xscale));
	}
	$imageResized = imagecreatetruecolor($new_width, $new_height); 
	$imageTmp     = imagecreatefromjpeg ($originalImage); 
	imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
	return $imageResized;
}

function image_resize($src, $dst, $width, $height, $crop=0)
{
	if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";
	$type = strtolower(substr(strrchr($src,"."),1));
	if($type == 'jpeg') $type = 'jpg';
	switch($type)
	{
		case 'bmp': $img = imagecreatefromwbmp($src); break;
		case 'gif': $img = imagecreatefromgif($src); break;
		case 'jpg': $img = imagecreatefromjpeg($src); break;
		case 'png': $img = imagecreatefrompng($src); break;
		default : return "Unsupported picture type!";
	}
	// resize
	if($crop)
	{
		if($w < $width or $h < $height) return "Picture is too small!";
		$ratio = max($width/$w, $height/$h);
		$h = $height / $ratio;
		$x = ($w - $width / $ratio) / 2;
		$w = $width / $ratio;
	}
	else{
		if($w < $width and $h < $height) return "Picture is too small!";
		$ratio = min($width/$w, $height/$h);
		$width = $w * $ratio;
		$height = $h * $ratio;
		$x = 0;
	}
	$new = imagecreatetruecolor($width, $height);
	// preserve transparency
	if($type == "gif" or $type == "png")
	{
		imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
		imagealphablending($new, false);
		imagesavealpha($new, true);
	}
	imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);
	switch($type)
	{
		case 'bmp': imagewbmp($new, $dst); break;
		case 'gif': imagegif($new, $dst); break;
		case 'jpg': imagejpeg($new, $dst); break;
		case 'png': imagepng($new, $dst); break;
	}
	return true;
}
/*Example that I use when uploading new images to the server.
This saves the original picture in the form:
original.type
and creates a new thumbnail:
100x100.type
$pic_type = strtolower(strrchr($picture['name'],"."));
$pic_name = "original$pic_type";
move_uploaded_file($picture['tmp_name'], $pic_name);
if (true !== ($pic_error = @image_resize($pic_name, "100x100$pic_type", 100, 100, 1)))
{
	if($batch==0) echo $pic_error;
	unlink($pic_name);
}
else if($batch==0) echo "OK!";
*/

function rsize($src,$dst,$width,$height)
{
	$imagick = new Imagick($src);
	$format = $imagick->getImageFormat();
	if ($format == 'GIF')
	{
		$imagick = $imagick->coalesceImages();
		do {
			$imagick->resizeImage($height, $width, Imagick::FILTER_BOX, 1);
		}
		while ($imagick->nextImage());
		$imagick = $imagick->deconstructImages();
		$imagick->writeImages($dst, true);
		// can be added some more gifs
		/*$imagick = $imagick->coalesceImages();
		do {
			$imagick->resizeImage(100, 100, Imagick::FILTER_BOX, 1);
		}
		while ($imagick->nextImage());
		$imagick = $imagick->deconstructImages();
		$imagick->writeImages('new_100x100.gif', true);*/
	}
	$imagick->clear();
	$imagick->destroy();
}
?>
