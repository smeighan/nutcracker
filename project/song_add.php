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
	
<?php show_barmenu();?>
<h2>Song Add</h2>
<p>Adding a song to your repository of songs</p>
<form action="songs.php" id="myform" method="POST" enctype="multipart/form-data"> 
<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
<table class="Gallery">
<tr><th colspan="3"><div align="center">Add New Song</div></th></tr>
<tr><td>Song Name</td><td><input type="text" class="FormFieldName" value="" size="75" name="song_name"></td><td>Enter name of the song</td></tr>
<tr class="alt"><td>Song Artist</td><td><input type="text" class="FormFieldName" value="" size="75" name="artist"></td><td>Enter the artist's name</td></tr>
<tr><td>Song URL</td><td><input type="text" class="FormFieldName" value="" size="75" name="song_url"></td><td>Enter the URL of where a person can upload the song</td></tr>
<tr class="alt"><td>Audacity file</td><td><input name="myupfile" type="file" class="FormFieldName" size="75" /></td><td>Enter the local file location of the audacity phrase file</td></tr>
</table>
<table>
<tr><td align="right"><input type="submit" name="addSong" value="Add Song" class="SubmitButton"></td><td align="left"><input type="submit" name="cancelSongAdd" value="Cancel Add" class="SubmitButton"></td></tr>
</table>
</form>
</body>
</html>