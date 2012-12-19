<?php
//

function f_text($get)
{
	if(!isset($get['fade_in']))  $get['fade_in']="0";
	if(!isset($get['fade_out']))  $get['fade_out']="0";
	$get['window_degrees'] = get_window_degrees($get['username'],$get['user_target'],$get['window_degrees']); // Set window_degrees to match the target
	extract ($get);
	set_time_limit(0);
	ini_set("memory_limit","1024M");
	require_once("../effects/read_file.php");
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
	$path="../effects/workspaces/". $member_id;
	$full_path = "../effects/dotmatrix";
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
	//$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	//$topPixel=$maxStrand*0.40;
	//$topPixel=$maxPixel/2;
	//$topPixel=5;
	//	$windowWidth = count($window_array);
	//	if($batch==0) print "windowWidth =$windowWidth\n";
	$seq_number=0;
	//	if($batch==0) echo "<pre> window_array";
	//	if($batch==0) print_r($window_array);
	//	if($batch==0) echo "</pre>\n";
	//for($k=$maxK+$windowWidth;$k>=1;$k--);
	$fileno=0;
	//	$base_s = $windowWidth;
	$MaxFrames=$maxK;
	//	$effect_name="SPIRALS1";
	$object_name=$t_dat;
	//
	//
	//	now get info about this target
	//
	//	Create the library_hdr and library_dtl for this effect. This will fill every rgb val to zero.
	//	$base = target+effect. Example: AA+SPIRAL1, AA24+FLY00
	//
	//Include database connection details
	/*require_once('../conf/config.php');
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
	$tree_rgb
	//
	//
	$library_id=create_library($link,$username,$base,$MaxFrames);*/
	$maxFrame=intval(($seq_duration*1000)/$frame_delay);
	if(!isset($speed)) $speed=1;
	if(!isset($direction)) $direction="left";
	if(strtolower($direction)=="up") $direction="up";
	else if(strtolower($direction)=="down") $direction="down";
	else if(strtolower($direction)=="left") $direction="left";
	else $direction="left";
	$scaley=3;
	for($f=1;$f<=$maxFrame;$f++)
	{
		for($j=1;$j<=9;$j++)	//	 display text1. Vertical
		{
			for($k=1;$k<=$f;$k++) // Horizontal
			{
				if($direction=="left")
				{
					$p= $j + $topPixel;
					$p2= $j + $topPixel +10*$scaley;
					if($speed<1) $k1=intval($k*$speed);
					else $k1=$k;
					//if($k1<1) $k1=1;
					$s = $maxStrand-intval(($f-$k)*$speed);
					if(isset($scroll[$j][$k1]) and  $scroll[$j][$k1] ==1) $rgb_val=$text1_color;
					else $rgb_val=0;
					if(isset($scroll2[$j][$k1]) and $scroll2[$j][$k1] ==1) $rgb_val2=$text2_color;
					else $rgb_val2=0;
					for($stmp=1;$stmp<=$scaley;$stmp++)
					{
						$pnew = $p + ($stmp-1);
						$pnew2 = $p2 + ($stmp-1);
						$tree_rgb[$s][$pnew]=$rgb_val;
						$tree_rgb[$s][$pnew2]=$rgb_val2;
					}
					//	echo "<pre>f,s,p,p2,j,k,rgb_val1,rgb_val2=$f,$s,$p,$p2,[$j,$k],$rgb_val,$rgb_val2</pre>\n";
				}
				else if($direction=="up")
				{
					$s= ($maxStrand-$topPixel) - $j;
					$s2= ($maxStrand-$topPixel) - $j -10;
					$p = $maxPixel-intval(($f-$k)*$speed);
					if($speed<1) $k1=intval($k*$speed);
					else $k1=$k;
					if(isset($scroll[$j][$k1]) and  $scroll[$j][$k1] ==1) $rgb_val=$text1_color;
					else $rgb_val=0;
					if(isset($scroll2[$j][$k1]) and $scroll2[$j][$k1] ==1) $rgb_val2=$text2_color;
					else $rgb_val2=0;
					for($stmp=1;$stmp<=$scaley;$stmp++)
					{
							$pnew = $p + ($stmp-1);
						
						$tree_rgb[$s][$pnew]=$rgb_val;
						$tree_rgb[$s2][$pnew]=$rgb_val2;
					}
				//	$tree_rgb[$s][$p]=$rgb_val;
				//	$tree_rgb[$s2][$p]=$rgb_val;
				}
				else if($direction=="down")
				{
					$s= 1+$topPixel + $j;
					$s2= 1+$topPixel + $j + 10;
					$p = 1+ intval(($f-$k)*$speed);
					if($speed<1) $k1=intval($k*$speed);
					else $k1=$k;
					if(isset($scroll[$j][$k1]) and  $scroll[$j][$k1] ==1) $rgb_val=$text1_color;
					else $rgb_val=0;
					if(isset($scroll2[$j][$k1]) and $scroll2[$j][$k1] ==1) $rgb_val2=$text2_color;
					else $rgb_val2=0;
					for($stmp=1;$stmp<=$scaley;$stmp++)
					{
							$pnew = $p + ($stmp-1);
						$pnew2 = $p2 + ($stmp-1);
						$tree_rgb[$s][$pnew]=$rgb_val;
						$tree_rgb[$s2][$pnew]=$rgb_val2;
					}
					// $tree_rgb[$s][$p]=$rgb_val;
					// $tree_rgb[$s2][$p]=$rgb_val;
				}
			}
		}
		$get['tree_xyz']=$tree_xyz;
		$get['strand_pixel']=$strand_pixel;
		$get['base']=$base;
		$get['maxStrand']=$maxStrand;
		$get['maxPixel']=$maxPixel;
		$get['path']=$path;
		$dat_filename = write_frame($get,$tree_rgb,$f,$maxFrame);
		$dat_file_array[]=$dat_filename;
		//
		/*echo "<table border=1>";
		for($p=1;$p<=$maxPixel;$p++)
		{
			echo "<tr>";
			for($s=1;$s<=$maxStrand;$s++)
			{
				$color=$tree_rgb[$s][$p];
				echo "<td bgcolor=$color>&nbsp;</td>";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";*/
	}
	// for($k=1;$k<=$maxK+$windowWidth;$k++);
	//	if($batch==0) echo "make_gp($batch,$path,$base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame);\n";
	$full_path = "../effects/workspaces/2/AA+TEXT2_d_8.dat";
	//fill_in_zeros($arr,$dat_file_array);
	$amperage=array();
	make_gp($batch,$arr,$path,$base,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame);
	$filename_buff=make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in,$fade_out);
}

function write_frame($get,$tree_rgb,$f,$maxFrame)
{
	extract ($get);
	/*echo "<pre>frame=$f";
	print_r($tree_rgb);*/
	$show_display=0;
	if($show_display)
	{
		echo "<table border=1>";
		for($p=1;$p<=$maxPixel;$p++)
		{
			echo "<tr><td>f:p $f:$p</td>";
			for($s=1;$s<=$maxStrand;$s++)
			{
				$color=$tree_rgb[$s][$p];
				if($color<>0)
					echo "<td bgcolor=$color>$s,$p</td>";
				else
				echo "<td bgcolor=$color>&nbsp;</td>";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
	}
	$seq_number=0;
	$x_dat = $base . "_d_". $f . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
	$dat_file[$f] = $path . "/" .  $x_dat;
	$filename=$dat_file[$f];
	$dat_file_array[]=$dat_file[$f];
	$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
	fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
	for($s=1;$s<=$maxStrand;$s++)
	{
		for($p=1;$p<=$maxPixel;$p++)
		{
			$string=$user_pixel=0;
			$seq_number++;
			$rgb_val=hexdec($tree_rgb[$s][$p]);
			$xyz=$tree_xyz[$s][$p]; // get x,y,z location from the model.
			fwrite($fh_dat [$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],
			$rgb_val, $string, $user_pixel,$strand_pixel[$s][$p][0],
			$strand_pixel[$s][$p][1],$f,$seq_number));
			//
			/*printf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],
			$rgb_val, $string, $user_pixel,$strand_pixel[$s][$p][0],
			$strand_pixel[$s][$p][1],$f,$seq_number);*/
		}
	}
	fclose($fh_dat [$f]);
	return $filename;
}
