<?php
require_once('../conf/auth.php');
require_once('../conf/barmenu.php');
require_once('project_loader.php');
require_once('project_filer.php');
require_once('dbcontrol.php');
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];
ini_set("memory_limit","512M");
$msg_str="";
if (isset($_FILES)) {
	if (isset($_FILES['myupfile'])) {
		$filename = $_FILES['myupfile']['name'];
		$filesize=$_FILES['myupfile']['size'];
		$infile=$_FILES['myupfile']['tmp_name'];
	}
}
if (isset($_POST)) {
	//print_r($_POST);
	extract($_POST);
	if (isset($cancelSongEdit)) {
		$msg_str="Cancelled Edit of ".$song_name;
	}
	if (isset($addSong)) {
		$isOK=true;
		if (strlen($song_name)==0) {
			$isOK=false;
			$msg_str.= "<div class=\"WarnText\">You must enter a value for the song name.  Try again!</div>";
		}
		if (strlen($artist)==0) {
			$isOK=false;
			$msg_str.= "<div class=\"WarnText\">You must enter a value for the artist.  Try again!</div>";
		}
		if (strlen($song_url)==0) {
			$isOK=false;
			$msg_str.= "<div class=\"WarnText\">You must enter a value for the song url.  Try again!</div>";
		}
		if (strlen($infile)==0) {
			$isOK=false;
			$msg_str.= "<div class=\"WarnText\">You must enter a value for the Audacity phrase file.  Try again!</div>";
		}
		if ($isOK) {
			if (!is_dir("uploads/"))
				mkdir("uploads/");
			$target_path = "uploads/";
			//$username = $_POST['username'];
			if (isset($_POST['MAX_FILE_SIZE']))
				$max_file_size=$_POST['MAX_FILE_SIZE'];
			else
				$max_file_size = 100000;
			if (!is_file($infile)) {
				$isOK=false;
				$msg_str.= "<div class=\"WarnText\">The file ".$filename." was not uploaded.  A problem exists!</div>";
			}
			if ($filesize>$max_file_size) {
				$isOK=false;
				$msg_str.= "<div class=\"WarnText\">The file ".$filename." is too big!</div>";
			}
			if ($isOK) {
				$fh=fopen($infile,'r');
				$isValid=true;
				while(($line=fgets($fh))&& ($isValid)) {
					if (strlen(trim($line))>0) {
						$tok=preg_split("/[\t ]+/", trim($line));
						$isValid=(count($tok)>2);
					}
				}
				if (!$isValid) {
					$isOK=false;
					$msg_str= "<div class=\"WarnText\">This file ".$filename." is not in the correct format</div>";
				} else {
					$target_path = $target_path . $username. "~".basename( $filename); 
					if(move_uploaded_file($infile, $target_path)) {
						$msg_str="Song '".$song_name."' added";
						$sql="INSERT INTO song (song_name, artist, song_url, last_updated, audacity_aup, username) VALUES ('".$song_name."','".$artist."','".$song_url."',NOW(),'".$filename."','".$username."')";
						//$msg_str.="SQL : ".$sql."<br />";
						//echo $sql;
						nc_query($sql);
						$sql="SELECT song_id FROM song WHERE song_name='".$song_name."' AND artist='".$artist."' AND song_url='".$song_url."'";
						//echo $sql;
						$result=nc_query($sql);
						if ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
							$song_id=$row['song_id'];
							if (!is_file($target_path))
								$msg_str.= "<div class=\"WarnText\">File ".$target_path." not found!</div>";
							else {
								$fh=fopen($target_path,'r');
								$cnt=0;
								while($line=fgets($fh)) {
									$line = trim( preg_replace( '/\s+/', ' ', $line ) ); // remove unwanted gunk
									$tempVal="";
									$tok=preg_split('/\s+/',$line,-1,PREG_SPLIT_NO_EMPTY);
									if (count($tok)>2) {
										$phrase_st=$tok[0];
										$phrase_end=$tok[1];
										$phrase_name=$tok[2];
										$sql="INSERT INTO song_dtl (phrase_name, start_secs, end_secs, song_id) VALUES ('".$phrase_name."','".$phrase_st."','".$phrase_end."',".$song_id.")";
										//echo $sql."<br />";
										nc_query($sql);
										//print_r($tok);
									}
									$cnt++;
								}
								fclose($fh);
							}
						}
						
					} else{
						$msg_str.= "<div class=\"WarnText\">There was an error uploading the file, please try again!</div>";
					}	
				}
			}
		}
	}
	if (isset($cancelSongAdd)) {
		$msg_str="Cancelled Song Add ";
	}
	if (isset($editSong)) {
		$msg_str="Edited Song '". $song_name."'";
		$sql="UPDATE song SET song_name='".$song_name."', song_url='".$song_url."', artist='".$artist."' WHERE song_id=".$song_id;
		//$msg_str.="<br />".$sql;
		nc_query($sql);
		foreach($_POST as $key=>$val) {	
			$subchk=substr($key,0,3);
			if (($subchk == "ph~") || ($subchk == "st~") || ($subchk == "en~") || ($subchk == "ck~")) {
				$song_dtl_id=substr($key,3);
				switch ($subchk) {
					case "ph~" :
						$fldname="phrase_name";
						break;
					case "st~" :
						$fldname="start_secs";
						break;
					case "ck~" :
						break;
					default:
						$fldname="end_secs";
						break;
				}
				if ($subchk != "ck~") {
					$sql="UPDATE song_dtl SET ".$fldname."='".$val."' WHERE song_dtl_id=".$song_dtl_id;
					nc_query($sql);
				} else {
					$sql="DELETE FROM song_dtl WHERE song_dtl_id=".$song_dtl_id;
					//echo $sql."<br />";
					nc_query($sql);
				}
			}
		}
	}
	if (isset($cancelSongDelete)) {
		$msg_str="Cancelled Delete of ".$song_name;
	}	
	if (isset($deleteSong)) {
		$msg_str="Deleted Song ". $song_name;
		$sql="DELETE FROM song WHERE song_id=".$song_id;
		nc_query($sql);

		$sql="DELETE FROM song_dtl WHERE song_id=".$song_id;
		nc_query($sql);

		$sql="DELETE FROM project_dtl WHERE project_id IN (SELECT project_id FROM project WHERE song_id=".$song_id. ")";
		nc_query($sql);
		
		$sql="DELETE FROM project WHERE song_id=".$song_id;
		nc_query($sql);
		

	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="../js/barmenu.js"></script>
<script type="text/javascript" src="../js/popmenu.js"></script>
<script type="text/javascript" src="../js/songedit.js"></script>
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
<link rel="stylesheet" type="text/css" href="../css/barmenu.css">
<link href="../css/ncFormDefault.css" rel="stylesheet" type="text/css" />
</head>
<body>
	
<?php show_barmenu(); ?>
<h2>Song Manager</h2>
<?=$msg_str?>
<p>These songs will be available for you to sequence against.  To add a song to this list for use by projects, you will need to provide the name, the artist, the URL for where the song can be
purchased, and have an audacity phrase file available for setting the default phrases for the song.  </p>
<form action="songs.php" id="myform" onSubmit="ajaxFunction(this.form); return false">
<table class="Gallery">
<tr>
<th>Song Name</th>
<th>Artist</th>
<th>Purchase song from here</th>
<th>Owner</th>
<th>Commands</th>
<?php
	if ($username!='f')
		$sql = "SELECT song_id, song_name, artist, song_url, username FROM song WHERE username IN ('".$username."','f') ORDER BY song_name";
	else
		$sql = "SELECT song_id, song_name, artist, song_url, username FROM song ORDER BY song_name";	
	$result = nc_query($sql);
	$cnt=0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if ($cnt%2==0)
			$trStr="<tr class=\"alt\">";
		else
			$trStr="<tr>";
		$cnt++;
		echo $trStr;
		$song_id = $row['song_id'];
		$artist = $row['artist'];
		$song_name = $row['song_name'];
		$song_url = $row['song_url'];
		$song_owner=$row['username'];
		if ($cnt%2==0) 
			$trStr='<tr>';
		else
			$trStr='<tr class="alt">';
	echo $trStr;?>
	<td><?=$song_name?></a></td>
	<td><?=$artist?></td>
	<td><a href="<?=$song_url?>"><?=$song_url?></a></td>
	<td><?=$song_owner?></td>
	<?php if (($username=='f') || ($song_owner!='f')) {?>
	<td><a href="song_edit.php?song_id=<?=$song_id?>"><img src="../images/edit.png">Edit</a>&nbsp;&nbsp;&nbsp;<a href="song_del.php?song_id=<?=$song_id?>" "><img src="../images/delete.png">Remove</a></td>
	<?php } else {?>
	<td>Global song - No edit</td>
	<?php } ?>
	</tr>
<?php		
	}
	if ($cnt == 0) {
		echo "<tr><td colspan=4>You do not have any songs in your library</td></tr>";
	}
?>
</table>
<p />
<a href="song_add.php">Add a New Song</a>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="songglobal_add.php">Copy song from global library to local library</a><br />
</body>
</html>