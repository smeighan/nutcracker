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
	/*echo "<pre>";
	print_r($get);
	echo "</pre>\n";*/
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
	$get['base']=$base;
	$get['t_dat']=$t_dat;
	$get['minStrand']=$minStrand;
	$get['minPixel']=$minPixel;
	$get['maxStrand']=$maxStrand;
	$get['maxPixel']=$maxPixel;
	$path="../effects/workspaces/". $member_id;
	$get['path']=$path;
	$get['tree_xyz']=$tree_xyz;
	$get['strand_pixel']=$strand_pixel;
	$get['min_max']=$min_max;
	if(!isset($show_frame)) $show_frame='N';
	$get['show_frame']=$show_frame;
	$get['arr']=$arr;
	srand(time());
	$maxFrame = 100;
	//$maxTrees=6;	// how many tree to draw at one time
	$seq_number = 0;
	//require_once("GIFDecoder.class.php");
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
		//
		$resized = "../effects/gifs/$member_id/resized_$file1";
		if($batch==0)
		{
			echo "<br/><img src=\"" . $FIC2 . "\"/><br/>\n";
			echo "<br/><img src=\"$resized\"/><br/>\n";
		}
		//	aspect ratio = width/height
		list($width, $height, $type, $attr) = getimagesize($FIC2);
		if($width>0)
			$aspect = $width/$height;
		else$aspect=1.0;
		$our_aspect = $maxStrand/$maxPixel;
		/*	echo "<pre>width, height, type, attr=$width, $height, $type, $attr</pre>\n";
		echo "<pre>maxStrand,maxPixel=$maxStrand,$maxPixel</pre>\n";
		echo "<pre>aspect=$aspect,our_aspect=$our_aspect </pre>\n";*/
		$new_width=$maxStrand;
		$new_height=$maxPixel/$aspect;
		if($new_height>$maxPixel) // it wont fit, go the other way
		{
			//	echo "<pre>xx: new_width,new_height=$new_width,$new_height";
			//	echo "<pre>new_height>maxPixel</pre>\n";
			$new_height=$maxPixel;
			$new_width=$maxPixel*$aspect;
		}
		//	echo "<pre>new_width,new_height=$new_width,$new_height";
		//
		require_once "gifresizer.php";	//Including our class
		if (!is_dir("../effects/frames"))
			makedir("frames");
		$gr = new gifresizer;	//New Instance Of GIFResizer
		$gr->temp_dir = "frames"; //Used for extracting GIF Animation Frames
		//	$gr->resize("gifs/1.gif","resized/1_resized.gif",200,150); //Resizing the animation into a new file.
		$return_array=$gr->resize($FIC2,$resized,$new_width,$new_height); 
		list($file_array,$offset_left_array,$offset_top_array) = $return_array;
		/*echo "<pre>";
		print_r($file_array);
		print_r($offset_left_array);
		print_r($offset_top_array);
		//echo "</pre>\n";
		*/
		// file=frames/frame_1350361704_00.gif
		foreach($file_array as $i=>$file)
		{
			$tok=explode("_",$file);
			$tok2=explode(".gif",$tok[2]);
			$frame=$tok2[0]+0;
			//	echo "file=$file\n";
			//echo "process_gif_frame($file,$frame,$get);\n";
			$x_dat = $base . "_d_" . $frame . ".dat";
			// for spirals we will use a dat filename starting "S_" and the tree model
			$dat_file[$frame] = $path . "/" . $x_dat;
			$dat_file_array[] = $dat_file[$frame];
			process_gif_frame($file,$frame,$get,$offset_left_array[$i],$offset_top_array[$i]);
			//	unlink($dat_file[$frame]);
		}
		//print_r($dat_file_array);
		$amperage = array();
		$x_dat_base = $base . ".dat";
		make_gp($batch,$arr,$path, $x_dat_base, $t_dat, $dat_file_array, $min_max, $username, 
		$frame_delay,$amperage, $seq_duration, $show_frame);
		list($usec, $sec) = explode(' ', microtime());
		$script_start = (float)$sec + (float)$usec;
		//	echo "<pre>make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out);</pre>\n";
		$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out);
		//
		//
		/*$GIF_frame = fread (fopen ($FIC2,'rb'), filesize($FIC2));
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
		$maxFrame=count ( $frames );*/
		//$file="ball-icon.png";
		//$image_array=get_image($batch,$file);
	}
	else
	{
		if($batch==0) echo "<pre><h2>Your gif file $FIC2 does not exist. please upload it</pre><h2>\n";
	}
}

function process_gif_frame($file,$frame,$get,$offset_left,$offset_top)
{
	//	echo "<pre>function process_gif_frame($file,$frame,$get,$offset_left,$offset_top)</pre>\n";
	$image_path=$file;
	extract($get);
	/*echo "<pre>process_frame:\n";
	print_r($get);
	echo "</pre>\n";
	*/
	//		$path = "../effects/workspaces/" . $member_id;
	//	read in next frame of gif animation into array
	//	$image_array=get_image($batch,$file,$frame,$maxStrand,$maxPixel,$window_degrees);
	$image = imagecreatefromgif($image_path);
	$size = getimagesize($image_path);
	$img_width = $size[0];
	$img_height = $size[1];
	//
	//	Get image into an array of rgb values
	$s=0;
	$precision=1;
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
			// echo "<pre>$rgb_index=imagecolorat( $x1, $y1) left,top=$offset_left,$offset_top</pre>\n";
			$cols = ImageColorsForIndex($image, $rgb_index);
			$r = $cols['red'];
			$g = $cols['green'];
			$b = $cols['blue'];
			$rgbhex = fromRGB ($r,$g,$b);
			$rgb_val = hexdec($rgbhex);
			$s=$x1+$offset_left;
			$p=$y1+$offset_top;
			//$s=$x1;
			//if($s>$img_width) $s=$img_width;
			//if($p>$img_height) $p=$img_height;
			$image_array[$s][$p] = $rgb_val;
			//			if($batch==0) echo "<pre>x,y,rgb= $x,$y,($r,$g,$b), rgbval=$rgb_val</pre>";
		}
	}
	//print_r($image_array);
	//
	//
	$seq_number = 0;
	//
	//	now write data to file.
	$x_dat = $base . "_d_" . $frame . ".dat";
	// for spirals we will use a dat filename starting "S_" and the tree model
	$dat_file[$frame] = $path . "/" . $x_dat;
	$dat_file_array[] = $dat_file[$frame];
	$fh_dat[$frame] = fopen($dat_file[$frame], 'w') or die("can't open file");
	fwrite($fh_dat[$frame], "#    " . $dat_file[$frame] . "\n");
	//echo "<pre>frame=$frame, file=$file, fh=" . $dat_file[$frame] . "\n";
	/*$shift=intval(($f-1)*$speed);
	switch ($direction)
	{
		case 'down':
		$p0-=$shift;
		break;
		case 'up':
		$p0+=$shift;
		break;
		case 'right':
		$s0+=$shift;
		break;
		case 'left':
		$s0-=$shift;
		break;
	}*/
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
			if ($s >= 1 and $s <= $maxStrand and $p >= 1 and $p <= $maxPixel) // and isset($tree_xyz[$s][$p]))
			{
				$xyz = $tree_xyz[$s][$p];
				$seq_number++;
				$string = $user_pixel = 0;
				//		if($s==10) $rgb_val=hexdec("#FFFF00");
				/*if($brightness>0.0)
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
				*/
				//	if($rgb_val!=0) $rgb_val=hexdec("#888888");
				fwrite($fh_dat[$frame], sprintf("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n", $s, $p, $xyz[0], $xyz[1], $xyz[2], $rgb_val, $string, $user_pixel, $strand_pixel[$s][$p][0], $strand_pixel[$s][$p][1], $frame, $seq_number));
				$hex=dechex($rgb_val);
				//	printf ("<pre>t1 %4d %4d %9.3f %9.3f %9.3f %s %d %d %d %d</pre>\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$hex,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$frame,$seq_number);
			}
		}
	}
	fclose($fh_dat[$frame]);
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

