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
<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Welcome <?php echo $_SESSION['SESS_FIRST_NAME'];?></h1>
<?php $menu="effect-form"; require "../conf/menu.php"; ?>
<?php
//
require("../effects/read_file.php");
//echo "<pre>";
//echo "max_execution_time =" . ini_get('max_execution_time') . "\n"; 
set_time_limit(60*60);
//echo "max_execution_time =" . ini_get('max_execution_time') . "\n"; 
//echo "</pre>";
//show_array($_SERVER,"SERVER");
// [QUERY_STRING] => make_lsp.php?base=AA+BARBERPOLE_180?full_path=workspaces/2/AA+BARBERPOLE_180_d_1.dat?frame_delay=100?member_id=2?seq_duration=8?sequencer=lsp?pixel_count=100?type=1
/*$tokens=explode("?",$_SERVER['QUERY_STRING']);
$c=count($tokens);
$tokens2=explode("base=",$tokens[0]);
$base=$tokens2[1];*/
extract($_POST);
//	basename  =SGASE+SEAN33_d_1.dat
//	extension =dat
//	filename  =SGASE+SEAN33_d_1
/*
Array
(
[10] => SGASE+SEAN33_d_10.dat
[57] => SGASE+SEAN33_d_57.dat
[5] => SGASE+SEAN33_d_5.dat
[37] => SGASE+SEAN33_d_37.dat
[53] => SGASE+SEAN33_d_53.dat
[66] => SGASE+SEAN33_d_66.dat
[54] => SGASE+SEAN33_d_54.dat
[55] => SGASE+SEAN33_d_55.dat
/*echo "<pre>";
print_r($files_array);
echo "</pre>\n";*/
echo "<pre>";
print_r($_SESSION);
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];
$file = "Wizards_In_Winter.mo";
$dir = 'music_object_files'; 
$array_of_mos= getFilesFromDir($dir); 
echo "<pre>";
print_r($array_of_mos);
foreach($array_of_mos as $full_path )
{
	echo "<pre><b>Proccessing $full_path</b></pre>\n";
	$fh = fopen($full_path, 'r') or die("can't open file $full_path");
	unset($mo_array);
	while (!feof($fh))
	{
		$line = fgets($fh);
		$tok=explode("\t", $line);
		$cnt=count($tok);
		//	echo "$username c=$cnt $line";
		//print($tok);
		$start=$tok[0];
		$end=$tok[1];
		$phrase=strtolower(trim($tok[2]));
		$tok2=explode("phrase",$phrase);
		$sequence=$tok2[1];
		$mo_array[]=array($start,$end,$phrase,$sequence);
	}
	//print_r($mo_array);
	echo "\n";
	$tok=explode("/",$full_path);
	$file=$tok[1];
	$music_object_id=get_music_object_id($file);
	if($music_object_id>0)
		insert_mo($music_object_id,$mo_array);
}

function get_music_object_id($music_mo_file)
{
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
	$query ="SELECT music_object_id FROM `music_object_hdr` WHERE  music_mo_file = '$music_mo_file'";
	echo "<pre>get_music_object_id: query=$query</pre>\n";
	$result=mysql_query($query) or die ("Error on $query");
	$music_object_id=0;
	$NO_DATA_FOUND=0;
	echo "rows=" . mysql_num_rows($result) . "\n";
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
	}
	return $music_object_id;
}

function insert_mo($music_object_id,$mo_array)
{
	if($music_object_id==0)
	{
		echo "Bad music_object_id\n";
		return;
	}
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
	$query ="delete from music_object_dtl  where music_object_id=$music_object_id";
	echo "<pre>get_effect_user_hdr: query=$query</pre>\n";
	$result=mysql_query($query) or die ("Error on $query");
	printf("Records deleted for music_object_id $music_object_id: %d\n", mysql_affected_rows());
	//
	//
	foreach($mo_array as $i=>$tok)
	{
		echo "<pre>i=$i";
		print_r($tok);
		echo "</pre>";
		$start=$tok[0];
		$end=$tok[1];
		$phrase=$tok[2];
		$sequence=$tok[3];
		//	echo "$i $start $end $phrase\n";
		if($sequence>=1)
		{
			$insert ="insert into music_object_dtl  
			(music_object_id,phrase_name,start_secs,end_secs,effect_name,sequence,date_created)
				values ($music_object_id,'$phrase','$start','$end','',$sequence,now())";
			//	echo "<pre>$insert</pre>\n";
			$result=mysql_query($insert) or die ("Error on $insert");
		}
	}
}

function getFilesFromDir($dir)
{
	$files = array(); 
	$n=0;
	if ($handle = opendir($dir))
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file != "." && $file != ".." )
			{
				if(is_dir($dir.'/'.$file))
				{
					$dir2 = $dir.'/'.$file; 
					$files[] = getFilesFromDir($dir2);
				}
				else 
				{ 
					$path_parts = pathinfo($file);  // workspaces/nuelemma/MEGA_001+SEAN_d_22.dat
					$dirname   = $path_parts['dirname']; // workspaces/nuelemma
					$basename  = $path_parts['basename']; // MEGA_001+SEAN_d_22.dat
					$extension =$path_parts['extension']; // .dat
					$filename  = $path_parts['filename']; // MEGA_001+SEAN_d_22
					$cnt=count($files);
					$tokens=explode("/",$dirname);
					//	0 = workspaces
					//	1 = nuelemma or id
					//
					if($extension=="mo")
					{
						$files[] = $dir.'/'.$file; 
						$n++;
						//echo "<pre>$cnt $n $file</pre>\n";
					}
					} 
				} 
			} 
		closedir($handle);
	}
	return array_flat($files);
}

function array_flat($array)
{
	$tmp=array();
	foreach($array as $a)
	{
		if(is_array($a))
		{
			$tmp = array_merge($tmp, array_flat($a));
		}
		else 
		{ 
			$tmp[] = $a;
		}
		} 
	return $tmp;
}
