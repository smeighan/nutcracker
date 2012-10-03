<?php
require_once('../conf/auth.php');
require_once('../conf/barmenu.php');
require_once('project_loader.php');
require_once('project_filer.php');
ini_set("memory_limit","512M");


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
	
<?php show_barmenu();
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
 ?>
<h2>Song Delete - <?=$song_name?></h2>
<p class="WarnText">If you delete this song, it will delete all references to this song including any projects that are attached to this song!  If you click yes below, it is is IRREVERSBILE.  Proceed with caution!</p>
<p><h3>Are you sure you wish to delete "<?=$song_name?>" ?</h3></p>
<form action="songs.php" id="myform" method="POST"> 
<input type="hidden" name="song_id" value="<?=$song_id?>">
<input type="hidden" name="song_name" value="<?=$song_name?>">
<table>
<tr><td align="right"><input type="submit" name="deleteSong" value="Yes - Delete" class="SubmitButton"></td><td align="left"><input type="submit" name="cancelSongDelete" value="Cancel Delete" class="SubmitButton"></td></tr>
</table>
</form>

</body>
</html>