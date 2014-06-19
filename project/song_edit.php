<?php
require_once('../conf/auth.php');
require_once('../conf/barmenu.php');
require_once('project_loader.php');
require_once('project_filer.php');
require_once('dbcontrol.php');
ini_set("memory_limit","512M");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="../js/barmenu.js"></script>
<script type="text/javascript" src="../js/popmenu.js"></script>
<script type="text/javascript">
function newPhrase(songid) {
	myform.addPhrase.value = songid;
	myform.action="song_edit.php";
	//alert("GOT HERE");
	myform.submit();
}
</script>
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
	
<?php show_barmenu();
	//print_r($_POST);
	if (isset($_POST['addPhrase'])) {
		echo "Adding a new phrase<br />"; 
		if (isset($_POST['song_id'])) {
			$song_id=$_POST['song_id'];
			$sql="INSERT INTO song_dtl (song_id, phrase_name,start_secs, end_secs) VALUES (".$song_id.",'newPhrase','0.000','0.0000');";
			//echo $sql."<br />";
			nc_query($sql);
			$_GET['song_id']=$song_id;
		}
	}
	extract($_GET);
	$sql = "SELECT song_id, song_name, artist, song_url FROM song WHERE song_id=".$song_id;
	$result = nc_query($sql);
	$cnt=0;
	if (!($row = mysql_fetch_array($result, MYSQL_ASSOC))) {
		echo "A probelm occured with the edit of the song! <br />";
		die;
	}
	$song_id = $row['song_id'];
	$artist = $row['artist'];
	$song_name = $row['song_name'];
	$song_url = $row['song_url'];
	$sql = "SELECT song_dtl_id, phrase_name, start_secs, end_secs FROM song_dtl WHERE song_id=".$song_id." ORDER by start_secs";
	$result2= nc_query($sql);
	$phraseEditStr="";
	$cnt=0;
	while ($row = mysql_fetch_array($result2, MYSQL_ASSOC))  {
		$cnt++;
		$song_dtl_id=$row['song_dtl_id'];
		$phrase_name=$row['phrase_name'];
		$start_secs=$row['start_secs'];
		$end_secs=$row['end_secs'];
		if ($cnt%2==0) 
			$trStr="<tr class=\"alt\">";
		else
			$trStr="<tr>";
		$fieldSuffix="~".$song_dtl_id;
		$phraseEditStr.=$trStr."<td><input type=\"text\" name=\"ph".$fieldSuffix."\" value=\"".$phrase_name."\" class=\"FormFieldName\"></td>\n";
		$phraseEditStr.="<td><input type=\"text\" name=\"st".$fieldSuffix."\" value=\"".$start_secs."\" class=\"FormFieldName\"></td>\n";
		$phraseEditStr.="<td><input type=\"text\" name=\"en".$fieldSuffix."\" value=\"".$end_secs."\" class=\"FormFieldName\"></td>\n";
		$phraseEditStr.="<td><input type=\"checkbox\" name=\"ck".$fieldSuffix."\" value=\"X\"></td></tr>\n";
	}
 ?>
<h2>Song Edit - <?=$song_name?></h2>
<p>Edit the song in the form below.  Press Submit to make the changes to the song.  Press cancel to cancel all changes and return to the Song Manager.
The default song phrase section will pre-populate any project with this phrasing when this song is chosen for a given project.  You will still be able to manipulate
these values in the project after assignment and the values below are used to provide the default values for phrases.</p>
<form action="songs.php" id="myform" method="POST"> 
<input type="hidden" name="song_id" value="<?=$song_id?>">
<input type="hidden" name="addPhrase" value="">
<table class="Gallery">
<tr><th colspan="2"><div align="center">Edit <?=$song_name?></div></th></tr>
<tr><td>Song Name</td><td><input type="text" class="FormFieldName" value="<?=$song_name?>" size="75" name="song_name"></td></tr>
<tr class="alt"><td>Song Artist</td><td><input type="text" class="FormFieldName" value="<?=$artist?>" size="75" name="artist"></td></tr>
<tr><td>Song URL</td><td><input type="text" class="FormFieldName" value="<?=$song_url?>" size="75" name="song_url"></td></tr>
</table>
<table class="Gallery">
<tr><th colspan="4"><div align="center">Default Phrases</div></th></tr>
<tr><th>Phrase Name</th><th>Phrase Start</th><th>Phrase End</th><th>Delete?</th></tr>
<?=$phraseEditStr?>
<tr><td colspan="4"><div align="center"><input type="button" value="Add Phrase" class="SubmitButton" onClick="newPhrase('<?=$song_id?>')"></div></td></tr>
</table>
<table>
<tr><td align="right"><input type="submit" name="editSong" value="Make Changes" class="SubmitButton"></td><td align="left"><input type="submit" name="cancelSongEdit" value="Cancel Edit" class="SubmitButton"></td></tr>
</table>
</form>

</body>
</html>