<?php
//

function f_gif($get)
{
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	//
	if(!isset($get['brightness']))   $get['brightness']="0";
	if(!isset($get['fade_in']))  $get['fade_in']="0";
	if(!isset($get['fade_out']))  $get['fade_out']="0";
	if(!isset($get['autoscale']))  $get['autoscale']=1;
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
	//	
	audit($username,"f_gif","$effect_name,$batch,$seq_duration");
	//
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
		$debug=1;
		if($debug==1) echo "<pre>width, height, type, attr=$width, $height, $type, $attr</pre>\n";
		if($debug==1)echo "<pre>maxStrand,maxPixel=$maxStrand,$maxPixel</pre>\n";
		if($debug==1)echo "<pre>aspect=$aspect,our_aspect=$our_aspect </pre>\n";
		$new_width=$maxStrand;
		$new_height=$maxPixel/$aspect;
		if($new_height>$maxPixel) // it wont fit, go the other way
		{
			if($debug==1)	echo "<pre>xx: new_width,new_height=$new_width,$new_height";
			if($debug==1)	echo "<pre>new_height>maxPixel</pre>\n";
			$new_height=$maxPixel;
			$new_width=$maxPixel*$aspect;
		}
		$autoscale_y= $new_height/$maxPixel;
		$autoscale_x=$new_width/$maxStrand;
		if($debug==1)echo "<pre>autoscale_x,autoscale_y = $autoscale_x,$autoscale_y</pre>\n";
		if($debug==1)echo "<pre>new_width,new_height=$new_width,$new_height";
		//
		if (!is_dir("../effects/frames"))
			mkdir("frames");
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
		$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out);
		//
		//
	}
	else
	{
		if($batch==0) echo "<pre><h2>Your gif file $FIC2 does not exist. please upload it</pre><h2>\n";
	}
	if($batch==0) elapsed_time($script_start);
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
	$autoscale_y= $img_height/$maxPixel;
	$autoscale_x= $img_width/$maxStrand;
	//echo "<pre>frame=$frame   img_width,autoscale_x=$img_width,$autoscale_x   img_height,autoscale_y=$img_height,$autoscale_y</pre>\n";
	//
	//	Get image into an array of rgb values
	$s=0;
	if(!isset($autoscale)) $autoscale=2;  // default to fit to target
	$precision_x=$precision_y=1;
	for ($rx = 0; $rx < $img_width; $rx += $precision_x)
	{
		$x=intval($rx);
		$s++;
		$p_raw = 0;
		$p=0;
		for ($ry = 0; $ry < $img_height; $ry += $precision_y)
		{
			$y=intval($ry);
			$p++;
			if($autoscale==2) // fit to target
			{
				$x1=intval($x*$autoscale_x);
				$y1=intval($y*$autoscale_y);
			}
			else // otherwise maintain aspect ratio
			{
				$x1=$x; $y1=$y;
			}
			if($x1<0) $x1=0; 
			if($x1>$img_width-1) $x1=$img_width-1;
			if($y1<0) $y1=0; 
			if($y1>$img_height-1) $y1=$img_height-1;
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
			$s=$x+1;
			$p=$y+1;
			//$s=$x1;
			//if($s>$img_width) $s=$img_width;
			//if($p>$img_height) $p=$img_height;
			$image_array[$s][$p] = $rgb_val;
			//	if($batch==0) echo "<pre>x,y,rgb= $x,$y,($r,$g,$b), rgbval=$rgb_val</pre>";
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
	}
	*/
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
/** 
* Resizes Animated GIF Files
*
*	///IMPORTANT NOTE: The script needs a temporary directory where all the frames should be extracted. 
*	Create a directory with a 777 permission level and write the path into $temp_dir variable below. 
*	
*	Default directory is "frames".
*/ 
class gifresizer {
	public $temp_dir = "frames";
	private $pointer = 0;
	private $index = 0;
	private $globaldata = array();
	private $imagedata = array();
	private $imageinfo = array();
	private $handle = 0;
	private $orgvars = array();
	private $encdata = array();
	private $parsedfiles = array();
	private $originalwidth = 0;
	private $originalheight = 0;
	private $wr,$hr;
	private $props = array();
	private $decoding = false;
	/** 
	* Public part of the class
	* 
	* @orgfile - Original file path
	* @newfile - New filename with path
	* @width   - Desired image width 
	* @height  - Desired image height
	*/ 
	
	function resize($orgfile,$newfile,$width,$height)
	{
		$this->decode($orgfile);
		$this->wr=$width/$this->originalwidth;
		$this->hr=$height/$this->originalheight;
		$this->resizeframes();
		$this->encode($newfile,$width,$height);
		/*	echo "<pre>";
		print_r($this);*/
		foreach($this->parsedfiles as $i=>$temp_frame)
		{
			$file_array[]=$temp_frame;
			//	echo "<pre>$i" . $this[$i]['offset_left'] . "," . $this->['offset_top'] . "</pre>\n";
		}
		foreach($this->orgvars as $i=>$arr)
		{
			/*echo "<pre>$i" . "offset_left=" . $arr['offset_left'] . "," . "offset_top" .$arr['offset_top'] . "</pre>\n";*/
			//	print_r($arr);
			$offset_left[$i]=$arr['offset_left'];
			$offset_top[$i]=$arr['offset_top'];
		}
		//$this->clearframes();
		//$arr=array(,  $this->['offset_left'] ,$this[$i]['offset_top']);
		$return_array=array($file_array,$offset_left,$offset_top);
		return $return_array;
	}
	/** 
	* GIF Decoder function.
	* Parses the GIF animation into single frames.
	*/
	private function decode($filename)
	{
		$this->decoding = true;            
		$this->clearvariables();
		$this->loadfile($filename);
		$this->get_gif_header();
		$this->get_graphics_extension(0);
		$this->get_application_data();
		$this->get_application_data();
		$this->get_image_block(0);
		$this->get_graphics_extension(1);
		$this->get_comment_data();
		$this->get_application_data();
		$this->get_image_block(1);
		while(!$this->checkbyte(0x3b) && !$this->checkEOF())
		{
			$this->get_comment_data(1);
			$this->get_graphics_extension(2);
			$this->get_image_block(2);
		}
		$this->writeframes(time());		
		$this->closefile();
		$this->decoding = false;
	}
	/** 
	* GIF Encoder function.
	* Combines the parsed GIF frames into one single animation.
	*/
	private function encode($new_filename,$newwidth,$newheight)
	{
		$mystring = "";
		$this->pointer = 0;
		$this->imagedata = array();
		$this->imageinfo = array();
		$this->handle = 0;
		$this->index=0;
		$k=0;
		foreach($this->parsedfiles as $imagepart)
		{
			$this->loadfile($imagepart);
			$this->get_gif_header();
			$this->get_application_data();
			$this->get_comment_data();
			$this->get_graphics_extension(0);
			$this->get_image_block(0);
			//get transparent color index and color
			if(isset($this->encdata[$this->index-1]))
				$gxdata = $this->encdata[$this->index-1]["graphicsextension"];
			else 
			$gxdata = null;
			$ghdata = $this->imageinfo["gifheader"];
			$trcolor = "";
			$hastransparency=($gxdata[3]&&1==1);
			if($hastransparency)
			{
				$trcx = ord($gxdata[6]);
				$trcolor = substr($ghdata,13+$trcx*3,3);
			}
			//global color table to image data;
			$this->transfercolortable($this->imageinfo["gifheader"],$this->imagedata[$this->index-1]["imagedata"]);	
			$imageblock = &$this->imagedata[$this->index-1]["imagedata"];
			//if transparency exists transfer transparency index
			if($hastransparency)
			{
				$haslocalcolortable = ((ord($imageblock[9])&128)==128);
				if($haslocalcolortable)
				{
					//local table exists. determine boundaries and look for it.
					$tablesize=(pow(2,(ord($imageblock[9])&7)+1)*3)+10;
					$this->orgvars[$this->index-1]["transparent_color_index"] = 
					((strrpos(substr($this->imagedata[$this->index-1]["imagedata"],0,$tablesize),$trcolor)-10)/3);
				}
				else{
					//local table doesnt exist, look at the global one.
					$tablesize=(pow(2,(ord($gxdata[10])&7)+1)*3)+10;
					$this->orgvars[$this->index-1]["transparent_color_index"] = 
					((strrpos(substr($ghdata,0,$tablesize),$trcolor)-10)/3);
				}
			}
			//apply original delay time,transparent index and disposal values to graphics extension
			if(!$this->imagedata[$this->index-1]["graphicsextension"]) $this->imagedata[$this->index-1]["graphicsextension"] = chr(0x21).chr(0xf9).chr(0x04).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00);
			$imagedata = &$this->imagedata[$this->index-1]["graphicsextension"];
			$imagedata[3] = chr((ord($imagedata[3]) & 0xE3) | ($this->orgvars[$this->index-1]["disposal_method"] << 2));
			$imagedata[4] = chr(($this->orgvars[$this->index-1]["delay_time"] % 256));
			$imagedata[5] = chr(floor($this->orgvars[$this->index-1]["delay_time"] / 256));
			if($hastransparency)
			{
				$imagedata[6] = chr($this->orgvars[$this->index-1]["transparent_color_index"]);
			}
			$imagedata[3] = chr(ord($imagedata[3])|$hastransparency);
			//apply calculated left and top offset 
			$imageblock[1] = chr(round(($this->orgvars[$this->index-1]["offset_left"]*$this->wr) % 256));
			$imageblock[2] = chr(floor(($this->orgvars[$this->index-1]["offset_left"]*$this->wr) / 256));
			$imageblock[3] = chr(round(($this->orgvars[$this->index-1]["offset_top"]*$this->hr) % 256));
			$imageblock[4] = chr(floor(($this->orgvars[$this->index-1]["offset_top"]*$this->hr) / 256));			
			if($this->index==1)
			{
				if(!isset($this->imageinfo["applicationdata"]) || !$this->imageinfo["applicationdata"]) 
				$this->imageinfo["applicationdata"]=chr(0x21).chr(0xff).chr(0x0b)."NETSCAPE2.0".chr(0x03).chr(0x01).chr(0x00).chr(0x00).chr(0x00);
				if(!isset($this->imageinfo["commentdata"]) || !$this->imageinfo["commentdata"])
					$this->imageinfo["commentdata"] = chr(0x21).chr(0xfe).chr(0x10)."PHPGIFRESIZER1.0".chr(0);
				$mystring .= $this->orgvars["gifheader"]. $this->imageinfo["applicationdata"].$this->imageinfo["commentdata"];
				if(isset($this->orgvars["hasgx_type_0"]) && $this->orgvars["hasgx_type_0"]) $mystring .= $this->globaldata["graphicsextension_0"];
				if(isset($this->orgvars["hasgx_type_1"]) && $this->orgvars["hasgx_type_1"]) $mystring .= $this->globaldata["graphicsextension"];
			}
			$mystring .= $imagedata . $imageblock;
			$k++;
			//	echo "<pre>k=$k imagepart=$imagepart</pre>\n";
			$this->closefile();
		}
		$mystring .= chr(0x3b); 
		//applying new width & height to gif header
		$mystring[6] = chr($newwidth % 256);
		$mystring[7] = chr(floor($newwidth / 256));
		$mystring[8] = chr($newheight % 256);
		$mystring[9] = chr(floor($newheight / 256));
		$mystring[11]= $this->orgvars["background_color"];
		if(file_exists($new_filename))
		{
			unlink($new_filename);
		}
		file_put_contents($new_filename,$mystring);
	}
	/** 
	* Variable Reset function
	* If a instance is used multiple times, it's needed. Trust me.
	*/
	private function clearvariables()
	{
		$this->pointer = 0;
		$this->index = 0;
		$this->imagedata = array();
		$this->imageinfo = array();            
		$this->handle = 0;
		$this->parsedfiles = array();
	}
	/** 
	* Clear Frames function
	* For deleting the frames after encoding.
	*/
	private function clearframes()
	{
		foreach($this->parsedfiles as $temp_frame)
		{
			echo "<pre>$temp_frame</pre>\n";
			//   unlink($temp_frame);
		}
	}
	/** 
	* Frame Writer
	* Writes the GIF frames into files.
	*/
	private function writeframes($prepend)
	{
		for($i=0;$i<sizeof($this->imagedata);$i++)
		{
			file_put_contents($this->temp_dir."/frame_".$prepend."_".str_pad($i,2,"0",STR_PAD_LEFT).".gif",$this->imageinfo["gifheader"].$this->imagedata[$i]["graphicsextension"].$this->imagedata[$i]["imagedata"].chr(0x3b));
			$this->parsedfiles[]=$this->temp_dir."/frame_".$prepend."_".str_pad($i,2,"0",STR_PAD_LEFT).".gif";
		}
	}
	/** 
	* Color Palette Transfer Device
	* Transferring Global Color Table (GCT) from frames into Local Color Tables in animation.
	*/
	private function transfercolortable($src,&$dst)
	{
		//src is gif header,dst is image data block
		//if global color table exists,transfer it
		if((ord($src[10])&128)==128)
		{
			//Gif Header Global Color Table Length
			$ghctl = pow(2,$this->readbits(ord($src[10]),5,3)+1)*3;
			//cut global color table from gif header
			$ghgct = substr($src,13,$ghctl);
			//check image block color table length
			if((ord($dst[9])&128)==128)
			{
				//Image data contains color table. skip.
				}else{
				//Image data needs a color table.
				//get last color table length so we can truncate the dummy color table
				$idctl = pow(2,$this->readbits(ord($dst[9]),5,3)+1)*3;
				//set color table flag and length	
				$dst[9] = chr(ord($dst[9]) | (0x80 | (log($ghctl/3,2)-1)));
				//inject color table
				$dst = substr($dst,0,10).$ghgct.substr($dst,-1*strlen($dst)+10);
			}
			}else{
			//global color table doesn't exist. skip.
		}
	}
	/** 
	* GIF Parser Functions.
	* Below functions are the main structure parser components.
	*/
	private function get_gif_header()
	{
		$this->p_forward(10);
		if($this->readbits(($mybyte=$this->readbyte_int()),0,1)==1)
		{
			$this->p_forward(2);
			$this->p_forward(pow(2,$this->readbits($mybyte,5,3)+1)*3);
		}
		else{
			$this->p_forward(2);
		}
		$this->imageinfo["gifheader"]=$this->datapart(0,$this->pointer);
		if($this->decoding)
		{
			$this->orgvars["gifheader"]=$this->imageinfo["gifheader"];
			$this->originalwidth = ord($this->orgvars["gifheader"][7])*256+ord($this->orgvars["gifheader"][6]);
			$this->originalheight = ord($this->orgvars["gifheader"][9])*256+ord($this->orgvars["gifheader"][8]);
			$this->orgvars["background_color"]=$this->orgvars["gifheader"][11];
		}
	}
	//-------------------------------------------------------
	private function get_application_data()
	{
		$startdata = $this->readbyte(2);
		if($startdata==chr(0x21).chr(0xff))
		{
			$start = $this->pointer - 2;
			$this->p_forward($this->readbyte_int());
			$this->read_data_stream($this->readbyte_int());
			$this->imageinfo["applicationdata"] = $this->datapart($start,$this->pointer-$start);
		}
		else{
			$this->p_rewind(2);
		}
	}
	//-------------------------------------------------------
	private function get_comment_data()
	{
		$startdata = $this->readbyte(2);
		if($startdata==chr(0x21).chr(0xfe))
		{
			$start = $this->pointer - 2;
			$this->read_data_stream($this->readbyte_int());
			$this->imageinfo["commentdata"] = $this->datapart($start,$this->pointer-$start);
		}
		else{
			$this->p_rewind(2);
		}
	}
	//-------------------------------------------------------
	private function get_graphics_extension($type)
	{
		$startdata = $this->readbyte(2);
		if($startdata==chr(0x21).chr(0xf9))
		{
			$start = $this->pointer - 2;
			$this->p_forward($this->readbyte_int());
			$this->p_forward(1);
			if($type==2)
			{
				$this->imagedata[$this->index]["graphicsextension"] = $this->datapart($start,$this->pointer-$start);
			}
			else if($type==1)
			{
				$this->orgvars["hasgx_type_1"] = 1;
				$this->globaldata["graphicsextension"] = $this->datapart($start,$this->pointer-$start);
			}
			else if($type==0 && $this->decoding==false)
			{
				$this->encdata[$this->index]["graphicsextension"] = $this->datapart($start,$this->pointer-$start);
			}
			else if($type==0 && $this->decoding==true)
			{
				$this->orgvars["hasgx_type_0"] = 1;
				$this->globaldata["graphicsextension_0"] = $this->datapart($start,$this->pointer-$start);
			}
			}else{
			$this->p_rewind(2);
		}
	}
	//-------------------------------------------------------
	private function get_image_block($type)
	{
		if($this->checkbyte(0x2c))
		{
			$start = $this->pointer;
			$this->p_forward(9);
			if($this->readbits(($mybyte=$this->readbyte_int()),0,1)==1)
			{
				$this->p_forward(pow(2,$this->readbits($mybyte,5,3)+1)*3);
			}
			$this->p_forward(1);
			$this->read_data_stream($this->readbyte_int());
			$this->imagedata[$this->index]["imagedata"] = $this->datapart($start,$this->pointer-$start);
			if($type==0)
			{
				$this->orgvars["hasgx_type_0"] = 0;
				if(isset($this->globaldata["graphicsextension_0"]))
					$this->imagedata[$this->index]["graphicsextension"]=$this->globaldata["graphicsextension_0"];
				else
				$this->imagedata[$this->index]["graphicsextension"]=null;
				unset($this->globaldata["graphicsextension_0"]);
			}
			elseif($type==1)
			{
				if(isset($this->orgvars["hasgx_type_1"]) && $this->orgvars["hasgx_type_1"]==1)
				{
					$this->orgvars["hasgx_type_1"] = 0;
					$this->imagedata[$this->index]["graphicsextension"]=$this->globaldata["graphicsextension"];
					unset($this->globaldata["graphicsextension"]);
				}
				else{
					$this->orgvars["hasgx_type_0"] = 0;
					$this->imagedata[$this->index]["graphicsextension"]=$this->globaldata["graphicsextension_0"];
					unset($this->globaldata["graphicsextension_0"]);
				}
			}
			$this->parse_image_data();
			$this->index++;
		}
	}
	//-------------------------------------------------------
	private function parse_image_data()
	{
		$this->imagedata[$this->index]["disposal_method"] = $this->get_imagedata_bit("ext",3,3,3);
		$this->imagedata[$this->index]["user_input_flag"] = $this->get_imagedata_bit("ext",3,6,1);
		$this->imagedata[$this->index]["transparent_color_flag"] = $this->get_imagedata_bit("ext",3,7,1);
		$this->imagedata[$this->index]["delay_time"] = $this->dualbyteval($this->get_imagedata_byte("ext",4,2));
		$this->imagedata[$this->index]["transparent_color_index"] = ord($this->get_imagedata_byte("ext",6,1));
		$this->imagedata[$this->index]["offset_left"] = $this->dualbyteval($this->get_imagedata_byte("dat",1,2));
		$this->imagedata[$this->index]["offset_top"] = $this->dualbyteval($this->get_imagedata_byte("dat",3,2));
		$this->imagedata[$this->index]["width"] = $this->dualbyteval($this->get_imagedata_byte("dat",5,2));
		$this->imagedata[$this->index]["height"] = $this->dualbyteval($this->get_imagedata_byte("dat",7,2));
		$this->imagedata[$this->index]["local_color_table_flag"] = $this->get_imagedata_bit("dat",9,0,1);
		$this->imagedata[$this->index]["interlace_flag"] = $this->get_imagedata_bit("dat",9,1,1);
		$this->imagedata[$this->index]["sort_flag"] = $this->get_imagedata_bit("dat",9,2,1);
		$this->imagedata[$this->index]["color_table_size"] = pow(2,$this->get_imagedata_bit("dat",9,5,3)+1)*3;
		$this->imagedata[$this->index]["color_table"] = substr($this->imagedata[$this->index]["imagedata"],10,$this->imagedata[$this->index]["color_table_size"]);
		$this->imagedata[$this->index]["lzw_code_size"] = ord($this->get_imagedata_byte("dat",10,1));
		if($this->decoding)
		{
			$this->orgvars[$this->index]["transparent_color_flag"] = $this->imagedata[$this->index]["transparent_color_flag"];
			$this->orgvars[$this->index]["transparent_color_index"] = $this->imagedata[$this->index]["transparent_color_index"];
			$this->orgvars[$this->index]["delay_time"] = $this->imagedata[$this->index]["delay_time"];
			$this->orgvars[$this->index]["disposal_method"] = $this->imagedata[$this->index]["disposal_method"];
			$this->orgvars[$this->index]["offset_left"] = $this->imagedata[$this->index]["offset_left"];
			$this->orgvars[$this->index]["offset_top"] = $this->imagedata[$this->index]["offset_top"];
			//
			// ,scm>
			//echo "<pre>offset_left,offset_top" . $this->imagedata[$this->index]["offset_left"] . "," .
			$this->imagedata[$this->index]["offset_top"] . "</pre>\n";
		}
	}
	//-------------------------------------------------------
	private function get_imagedata_byte($type,$start,$length)
	{
		if($type=="ext")
			return substr($this->imagedata[$this->index]["graphicsextension"],$start,$length);
		elseif($type=="dat")
			return substr($this->imagedata[$this->index]["imagedata"],$start,$length);
	}
	//-------------------------------------------------------
	private function get_imagedata_bit($type,$byteindex,$bitstart,$bitlength)
	{
		if($type=="ext")
			return $this->readbits(ord(substr($this->imagedata[$this->index]["graphicsextension"],$byteindex,1)),$bitstart,$bitlength);
		elseif($type=="dat")
			return $this->readbits(ord(substr($this->imagedata[$this->index]["imagedata"],$byteindex,1)),$bitstart,$bitlength);
	}
	//-------------------------------------------------------
	private function dualbyteval($s)
	{
		$i = ord($s[1])*256 + ord($s[0]);
		return $i;
	}
	//------------   Helper Functions ---------------------
	private function read_data_stream($first_length)
	{
		$this->p_forward($first_length);
		$length=$this->readbyte_int();
		if($length!=0)
		{
			while($length!=0)
			{
				$this->p_forward($length);
				$length=$this->readbyte_int();
			}
		}
		return true;
	}
	//-------------------------------------------------------
	private function loadfile($filename)
	{
		$this->handle = fopen($filename,"rb");
		$this->pointer = 0;
	}
	//-------------------------------------------------------
	private function closefile()
	{
		fclose($this->handle);
		$this->handle=0;
	}
	//-------------------------------------------------------
	private function readbyte($byte_count)
	{
		$data = fread($this->handle,$byte_count);
		$this->pointer += $byte_count;
		return $data;
	}
	//-------------------------------------------------------
	private function readbyte_int()
	{
		$data = fread($this->handle,1);
		$this->pointer++;
		return ord($data);
	}
	//-------------------------------------------------------
	private function readbits($byte,$start,$length)
	{
		$bin = str_pad(decbin($byte),8,"0",STR_PAD_LEFT);
		$data = substr($bin,$start,$length);
		return bindec($data);
	}
	//-------------------------------------------------------
	private function p_rewind($length)
	{
		$this->pointer-=$length;
		fseek($this->handle,$this->pointer);
	}
	//-------------------------------------------------------
	private function p_forward($length)
	{
		$this->pointer+=$length;
		fseek($this->handle,$this->pointer);
	}
	//-------------------------------------------------------
	private function datapart($start,$length)
	{
		fseek($this->handle,$start);
		$data = fread($this->handle,$length);
		fseek($this->handle,$this->pointer);
		return $data;
	}
	//-------------------------------------------------------
	private function checkbyte($byte)
	{
		if(fgetc($this->handle)==chr($byte))
		{
			fseek($this->handle,$this->pointer);
			return true;
		}
		else{
			fseek($this->handle,$this->pointer);
			return false;
		}
		}	
	//-------------------------------------------------------
	private function checkEOF()
	{
		if(fgetc($this->handle)===false)
		{
			return true;
		}
		else{
			fseek($this->handle,$this->pointer);
			return false;
		}
		}	
	//-------------------------------------------------------
	/** 
	* Debug Functions. 
	* Parses the GIF animation into single frames.
	*/
	private function debug($string)
	{
		echo "<pre>";
		for($i=0;$i<strlen($string);$i++)
		{
			echo str_pad(dechex(ord($string[$i])),2,"0",STR_PAD_LEFT). " ";
		}
		echo "</pre>";
	}
	//-------------------------------------------------------
	private function debuglen($var,$len)
	{
		echo "<pre>";
		for($i=0;$i<$len;$i++)
		{
			echo str_pad(dechex(ord($var[$i])),2,"0",STR_PAD_LEFT). " ";
		}
		echo "</pre>";
	}
	//-------------------------------------------------------
	private function debugstream($length)
	{
		$this->debug($this->datapart($this->pointer,$length));
	}
	//-------------------------------------------------------
	/** 
	* GD Resizer Device
	* Resizes the animation frames
	*/
	private function resizeframes()
	{
		$k=0;
		foreach($this->parsedfiles as $img)
		{
			$src = imagecreatefromgif($img);
			$sw = $this->imagedata[$k]["width"];
			$sh = $this->imagedata[$k]["height"];
			$nw = round($sw * $this->wr);
			$nh = round($sh * $this->hr);
			$sprite = imagecreatetruecolor($nw,$nh);	
			$trans = imagecolortransparent($sprite);
			imagealphablending($sprite, false);
			imagesavealpha($sprite, true);
			imagepalettecopy($sprite,$src);					
			imagefill($sprite,0,0,imagecolortransparent($src));
			imagecolortransparent($sprite,imagecolortransparent($src));						
			imagecopyresized($sprite,$src,0,0,0,0,$nw,$nh,$sw,$sh);		
			imagegif($sprite,$img);
			imagedestroy($sprite);
			imagedestroy($src);
			$k++;
		}
	}
}
