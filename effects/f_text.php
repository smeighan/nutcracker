<?php
//

function f_text($get)
{
if(!isset($get['fade_in']))  $get['fade_in']="0";
	if(!isset($get['fade_out']))  $get['fade_out']="0";
	extract ($get);
	set_time_limit(0);
	ini_set("memory_limit","512M");
	require_once("../effects/read_file.php");
	//
	//
	text($get);
	
	
}

function text($get)
{
	extract ($get);
	if($batch==0) show_array($get,"array_to_save");
	$member_id=get_member_id($username);
	$get['member_id']=$member_id;
	$path ="../effects/workspaces/$member_id";
	$gifpath ="gifs/$member_id";
	$directory=$path;
	if (!file_exists($directory))
	{
		if($batch==0) if($batch==0) echo "The directory $directory does not exist, creating it";
		mkdir($directory, 0777);
	}
	$base = $user_target . "~" . $effect_name;
	$t_dat = $user_target . ".dat";
	$xdat = $user_target ."~".  $effect_name . ".dat";
	$path="../targets/". $member_id;
	$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
	$minStrand =$arr[0];  // lowest strand seen on target
	$minPixel  =$arr[1];  // lowest pixel seen on skeleton
	$maxStrand =$arr[2];  // highest strand seen on target
	$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
	$maxI      =$arr[4];  // maximum number of pixels in target
	$tree_rgb  =$arr[5];
	$tree_xyz  =$arr[6];
	$file      =$arr[7];
	$min_max   =$arr[8];
	$strand_pixel=$arr[9];
	$tree_user_text1_pixel   =$arr[9];
	$full_path = "dotmatrix";
	$fh = fopen($full_path, 'r') or die("can't open file $full_path");
	$debug=0;
	$i=0;
	//	load in from dotmatrix into the $c array.
	//	This array is indexed by the characters [a-z, A-Z]
	//	this returns the $letter array, this is a 9x5 array. with 0,1. 1 indicates a pixel to be lit
	//
	if($batch==0) echo "<pre>";
	while (!feof($fh))
	{
		// load up the dotmatrix file into the $letter array
		$line = fgets($fh);
		if(substr($line,0,1) == "=")
		{
			$char = substr($line,1,1);
			$row=0;
			for($i=1;$i<=5;$i++)
				for($j=1;$j<=9;$j++)
				$letter[$j][$i]=0;
		}
		else
		{
			$row++;
			$strLength = strlen($line);
			for($i=0;$i<=$strLength;$i++)
			{
				$val=0;
				if(substr($line,$i,1)=="x")
					$val=1;
				$letter[$row][$i+1]=$val;
				$c[$char]=$letter;
			}
		}
		//$tok=preg_split("/ +/", $line);
	}
	$strLength = strlen($text1);
	$maxK=999;
	for($j=1;$j<=9;$j++)  // prefill scroll array with zeroes
	{
		for($k=1;$k<=$maxK;$k++)
		{
			$scroll[$j][$k]=0;
			$scroll2[$j][$k]=0;
			$scroll3[$j][$k]=0;
		}
	}
	$k=0;
	//	text 1
	$strLength = strlen($text1);
	for($j=1;$j<=9;$j++) //	File the scroll buffer with all characters passed in
	{
		$k=0;
		for($l=0;$l<$strLength;$l++)
		{
			$char = substr($text1,$l,1);
			if($char==' ')
				$k+=6;
			else
			{
				$letter = $c[$char];
				for($i=1;$i<=5;$i++)
				{
					$k++;
					$scroll[$j][$k] = $letter[$j][$i];
				}
				$k++; // skip one blank line between characters
			}
		}
	}
	$maxK1=$k;
	//	Text 2
	$strLength2 = strlen($text2);
	for($j=1;$j<=9;$j++) //	File the scroll buffer with all characters passed in
	{
		$k=0;
		for($l=0;$l<$strLength2;$l++)
		{
			$char = substr($text2,$l,1);
			if($char==' ')
				$k+=6;
			else
			{
				if(empty($c[$char]))
				{
					if($batch==0) echo "<pre> char [$char] is not in array c</pre>\n";
				}
				else
				$letter = $c[$char];
				for($i=1;$i<=5;$i++)
				{
					$k++;
					$scroll2[$j][$k] = $letter[$j][$i];
				}
				$k++; // skip one blank line between characters
			}
		}
	}
	$maxK2=$k;
	$maxK=max($maxK1,$maxK2);
	/*for($s=1;$s<=$maxStrand;$s++) // if($batch==0) print out the blank rows we are not using.
	{
		for($p=1;$p<$topPixel;$p++)
		{
			$x=' ';
			if($batch==0) printf ("%s",$x);
		}
		if($batch==0) print "\n";
	}
	*/
	for($j=1;$j<=9;$j++)	//	 display text1
	{
		for($k=1;$k<=$maxK;$k++)
		{
			if( $scroll[$j][$k] ==1) $x='x';
			else $x=' ';
			if($batch==0) printf ("%s",$x);
		}
		if($batch==0) print "\n";
	}
	for($j=1;$j<=9;$j++)	//	 display text2
	{
		for($k=1;$k<=$maxK2;$k++)
		{
			if( $scroll2[$j][$k] ==1) $x='x';
			else $x=' ';
			if($batch==0) printf ("%s",$x);
		}
		if($batch==0) print "\n";
	}
	/*$last_p=$topPixel+18;
	for($s=1;$s<=$maxStrand;$s++) // print out the blank rows we are not using.
	{
		for($p=$last_p+1;$p<=$topPixel;$p++)
		{
			$x=' ';
			if($batch==0) printf ("%s",$x);
		}
		if($batch==0) print "\n";
	}
	*/
	$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	//$topPixel=$maxStrand*0.40;
	//$topPixel=$maxPixel/2;
	//$topPixel=5;
	$windowWidth = count($window_array);
	if($batch==0) print "windowWidth =$windowWidth\n";
	$seq_number=0;
	if($batch==0) echo "<pre> window_array";
	if($batch==0) print_r($window_array);
	if($batch==0) echo "</pre>\n";
	//for($k=$maxK+$windowWidth;$k>=1;$k--);
	$fileno=0;
	$base_s = $windowWidth;
	$MaxFrames=$maxK;
	$effect_name="SPIRALS1";
	$object_name=$t_dat;
	//
	//
	//	now get info about this target
	//
	//	Create the library_hdr and library_dtl for this effect. This will fill every rgb val to zero.
	//	$base = target+effect. Example: AA+SPIRAL1, AA24+FLY00
	//
	//Include database connection details
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	//
	//
	$library_id=create_library($link,$username,$base,$MaxFrames);
	for($k=1;$k<=$maxK;$k++)
	{
		$fileno++;
		$x_dat= $base ."_d_$fileno.dat";
		$dat_file = $path . "/" . $base . "_d_" . $fileno . ".dat" ; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file_array[]=$dat_file;
		$fh_dat = fopen($dat_file, 'w') or die("can't open file");
		fwrite($fh_dat,"#    $dat_file\n");
		//	fwrite($fh_dat,sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$k,$seq_number));
		fwrite($fh_dat,"#    Token\n");
		fwrite($fh_dat,"#    Strand\n");
		fwrite($fh_dat,"#    Pixel\n");
		fwrite($fh_dat,"#    X\n");
		fwrite($fh_dat,"#    Y\n");
		fwrite($fh_dat,"#    ZZ\n");
		fwrite($fh_dat,"#    rgb_val\n");
		fwrite($fh_dat,"#    User_String\n");
		fwrite($fh_dat,"#    User_pixel\n");
		fwrite($fh_dat,"#    Strand_Pixel[0]\n");
		fwrite($fh_dat,"#    Strand_Pixel[1]\n");
		fwrite($fh_dat,"#    k\n");
		fwrite($fh_dat,"#    seq_number\n");
		/*if($batch==0) echo "<pre>k=$k dat_file=$dat_file windowWidth=$windowWidth</pre>\n";*/
		//print_r($dat_file_array);
		//if($batch==0) echo "w=$w ";
		//print_r($window_array);
		//$kk=$k-$w;
		$w=$kk=0;
		$base_s--;
		$s=1;
		/*	for($s=1;$s<=$maxStrand;$s++) // print out the blank rows we are not using.
		{
			for($p=1;$p<$topPixel;$p++)
			{
				$rgb_val=0;
				$tree_rgb[$s][$p]=$rgb_val;
				$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
				$string=$user_pixel=0;
				fwrite($fh_dat,sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$k,$seq_number));
			}
			}*/
		for($k0=1;$k0<=$k;$k0++) // now print out the actual text.
		{
			/*if($batch==0) echo "for($k0=1;$k0<=$k;$k0++)\n";*/
			$s=$base_s+$k0;
			for($j=1;$j<=9;$j++)
			{
				$p=$topPixel+$j-1;
				/*	if($batch==0) echo "<pre>";
				if($batch==0) echo "s,k,k0,j,p =$s,$k,$k0,$j,$p,$base_s";
				if($batch==0) echo "</pre>\n";*/
				if(in_array($s,$window_array) and $p<=$maxPixel)
				{
					$val=$scroll[$j][$k0];
					//$H=RED;
					//$S=$V=1;
					//$rgb_val=HSV_TO_RGB ($H, $S, $V);
					if($val==1)
						$rgb_val=hexdec($text1_color);
					else
					{
						$rgb_val=0;
					}
					//if($s==1) $rgb_val=hexdec("#00FFFF"); // debug to mark strand 1
					//	if($s==5) $rgb_val=hexdec("#FF0000");
					//	if($p==5) $rgb_val=hexdec("#00FF00");
					$tree_rgb[$s][$p]=$rgb_val;
					$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
					//		fwrite($fh_dat,sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val));
					$seq_number++;
					$string=$user_pixel=0;
					fwrite($fh_dat,sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$k,$seq_number));
					$string=$strand_pixel[$s][$p][0];
					$pixel=$strand_pixel[$s][$p][1];
					if($rgb_val != 0)
					{
						store_into_library_dtl($link,$username,$library_id,$base,$string,$pixel,$k,$rgb_val);
					}
					$val=$scroll2[$j][$k0];
					$p=$topPixel+$j-1 +9;
					//$H=GREEN;
					//$S=$V=1;
					//$rgb_val=HSV_TO_RGB ($H, $S, $V);
					//if($val>0)
						if(in_array($s,$window_array) and $p<=$maxPixel) // Is this strand in our window?, If yes, then we output lines to the dat file;
					{
						if($val==1)
							$rgb_val=hexdec($text2_color);
						else
						{
							$rgb_val=0;
						}
						$tree_rgb[$s][$p]=$rgb_val;
						$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
						/*if($batch==0) printf ("t1 %4d %4d %9.3f %9.3f %9.3f %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val);*/
						$seq_number++;
						$string=$user_pixel=0;
						fwrite($fh_dat,sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$k,$seq_number));
						$string=$strand_pixel[$s][$p][0];
						$pixel=$strand_pixel[$s][$p][1];
						if($rgb_val != 0)
						{
							store_into_library_dtl($link,$username,$library_id,$base,$string,$pixel,$k,$rgb_val);
						}
					}
				}
			}
		}
		$last_p=$p;
		/*for($s=1;$s<=$maxStrand;$s++) // print out the blank rows we are not using.
		{
			for($p=$last_p+1;$p<=$topPixel;$p++)
			{
				$rgb_val=0;
				$tree_rgb[$s][$p]=$rgb_val;
				$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
				$string=$user_pixel=0;
				fwrite($fh_dat,sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$s][$p][0],$strand_pixel[$s][$p][1],$k,$seq_number));
			}
			}*/
		fclose($fh_dat);
		$amperage=array();
		// for($w=0;$w<=$windowWidth;$w++);
	}
	// for($k=1;$k<=$maxK+$windowWidth;$k++);
	//	if($batch==0) echo "make_gp($batch,$path,$base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame);\n";
	$full_path = "../effects/workspaces/2/AA+TEXT2_d_8.dat";
	//fill_in_zeros($arr,$dat_file_array);
	make_gp($batch,$arr,$path,$base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out);
}

function create_library($link,$username,$base,$MaxFrames)
{
	return 0;
	$tokens2=explode("~",$base); // AA+CIRCLE1
	$t_dat=$tokens2[0];	// AA
	$effect_name=$tokens2[1];	// CIRCLE1
	$target_info=get_info_target($username,$t_dat);
	if($batch==0) echo "<pre>get_info_target($username,$t_dat);\n";
	if($batch==0) echo "</pre>\n";
	$object_name=$target_info['target_name'];
	$model_type=$target_info['model_type'];
	$total_strings=$target_info['total_strings'];
	$pixel_count=$target_info['pixel_count'];
	$pixel_length=$target_info['pixel_length'];
	$pixel_spacing=$target_info['pixel_spacing']; // PIXEL FIX
	$unit_of_measure=$target_info['unit_of_measure'];
	$topography=$target_info['topography'];
	//
	/*
	username	varchar(25)	latin1_swedish_ci		No			 	 	 	 	 	 	 
	object_name	varchar(16)	latin1_swedish_ci		No			 	 	 	 	 	 	 
	effect_name	varchar(25)	latin1_swedish_ci		No			 	 	 	 	 	 	 
	library_id	int(12)			No		auto_increment	 	 	 	 	 	 	
	date_updated	timestamp		ON UPDATE CURRENT_TIMESTAMP	Yes	CURRENT_TIMESTAMP	on update CURRENT_TIMESTAMP	 	 	 	 	 	 	
	*/
	//
	//dELETE old library entry , if it is there
	//
	$query="SELECT library_id from library_hdr where username='$username' and object_name='$object_name' and effect_name='$effect_name'";
	if($batch==0) echo "<pre>query: $query</pre>\n";
	$result=mysql_query($query,$link);
	if(!$result)
	{
		if($batch==0) echo "Error on $query\n";
		mysql_error();
	}
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
	}
	$delete="DELETE from library_hdr where username='$username' and object_name='$object_name' and effect_name='$effect_name'";
	if($batch==0) echo "<pre>delete: $delete</pre>\n";
	$result=mysql_query($delete,$link) or die("Error on $delete");
	if(isset($library_id))
	{
		$delete2="DELETE from library_dtl where library_id=$library_id";
		if($batch==0) echo "<pre>delete: $delete2</pre>\n";
		$result=mysql_query($delete2,$link) or die("Error on $delete2");
	}
	//
	//
	//	Insert header data for this library object
	//
	$insert = "INSERT into library_hdr( username,object_name,effect_name)
		values ('$username','$object_name','$effect_name')";
	if($batch==0) echo "<pre>insert: $insert</pre>\n";
	$result=mysql_query($insert,$link);
	if(!$result)
	{
		if($batch==0) echo "Error on $insert\n";
		mysql_error();
	}
	// Find the auto generated library_id for what we just inserted.
	//
	//	find the auto generated library_id
	//
	$query="SELECT library_id from library_hdr where username='$username' and object_name='$object_name' and effect_name='$effect_name'";
	if($batch==0) echo "<pre>query: $query</pre>\n";
	$result=mysql_query($query,$link);
	if(!$result)
	{
		if($batch==0) echo "Error on $query\n";
		mysql_error();
	}
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
	}
	//
	//
	/* Now we insert an entry for enery potential rgbval
	library_id	int(12)			No			 	 	 	 	 	 	
	string	int(12)			No			 	 	 	 	 	 	
	pixel	int(12)			No			 	 	 	 	 	 	
	frame	int(12)			No			 	 	 	 	 	 	
	rgb_val	int(12)			No			 	 	 	 	 	 	
	*/
	$rgb_val=0;
	for($string=1; $string<=$total_strings; $string++)
		for($pixel=1;$pixel<=$pixel_count;$pixel++)
		for($frame=1;$frame<=$MaxFrames;$frame++)
	{
		$insert_dtl="INSERT into library_dtl(library_id,string,pixel,frame,rgb_val )
			values ('$library_id','$string','$pixel','$frame','$rgb_val')";
		$result=mysql_query($insert_dtl,$link) or die ("Error on $insert_dtl");
	}
	return $library_id;
}

function store_into_library_dtl($link,$username,$library_id,$base,$string,$pixel,$frame,$rgb_val)
{
	return 0;
	$tokens2=explode("~",$base); // AA+CIRCLE1
	$t_dat=$tokens2[0];	// AA
	$effect_name=$tokens2[1];	// CIRCLE1
	$update_dtl="UPDATE  library_dtl set rgb_val = '$rgb_val'
	where library_id = '$library_id' and string='$string' and pixel='$pixel' and frame='$frame'";
	$result=mysql_query($update_dtl,$link) or die ("Error on $update_dtl");
}
?>
