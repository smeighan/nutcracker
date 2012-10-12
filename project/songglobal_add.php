<?php
require_once('../conf/auth.php');
require_once('../conf/barmenu.php');
require_once('project_loader.php');
//require_once('project_filer.php');
require_once('dbcontrol.php');
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];
ini_set("memory_limit","512M");
$msg_str="";
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
	
<?php show_barmenu();
if (isset($_GET['song_id'])) {
	$song_id = $_GET['song_id'];
	echo "Copying ".$song_id." to local library...";
}

select_global_songs();

function select_global_songs()
{
	$sql = "SELECT artist, username, song_name, song.song_id, artist, song_url, min( start_secs )AS MinTime, max( end_secs )AS MaxTime \n"
    . "FROM song \n"
    . "LEFT JOIN song_dtl ON song.song_id = song_dtl.song_id \n"
    . "GROUP BY song_name, song.song_id \n"
    . "HAVING song.username = 'f'";
	//echo $sql . "<br />";
	//$sql2 = "SELECT object_name, model_type FROM models WHERE username='".$username."'";
	$result = nc_query($sql);
	?>
	<h2>Available Songs to Copy from Global</h2>
	<table class="TableProp">
	<?php
	$rowcnt = mysql_num_rows($result);
	if ($rowcnt == 0)
	{
		echo "<tr><th>No more songs available to add!</th></tr>";
	}
	else {
		$SongSel=parseSongs($result);
		echo $SongSel[1];?>
	</table>
	<?php
	}
}


function parseTargetSelect($result)
{
	$retStr='<select name="model_name" class="FormSelect" id="model_name">';
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$model_name=$row['object_name'];
		$model_type=$row['model_type'];
		$retStr.='<option value="'.$model_name.'">'.$model_name.' ('.$model_type.')</option>';
	}
	$retStr.='</select>';
	return($retStr);
}

function parseSongs($result)
{
	$retVal = array();
	$retStr1='<select name="song_id" class="FormSelect" id="song_id">';
	$retStr2='<tr><th>Song Name</th><th>Artist</th><th>Length of song (sec)</th><th>Length of song (min)</th><th>Options</th></tr>';
	$cnt=0;
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$cnt++;
		$artist=$row['artist'];
		$song_name=$row['song_name'];
		$song_id=$row['song_id'];
		$song_url=$row['song_url'];
		$MinTime=$row['MinTime'];
		$MaxTime=$row['MaxTime'];
		$song_length = round(($MaxTime-$MinTime),2);
		$song_length_min = round(($song_length/60),2);
		$retStr1.='<option value='.$song_id.'>'.$song_name.'</option>';
		if ($cnt%2==0) 
		$trStr='<tr>';
		else
		$trStr='<tr class="alt">';
		$retStr2.=$trStr.'<td><a href="'.$song_url.'">'.$song_name.'</a></td><td>'.$artist.'</td><td>'.$song_length.'</td><td>'.$song_length_min.'</td><td><a href="songglobal_add.php?&song_id='.$song_id.'"><img src="../images/edit.png">Copy</a></td></tr>';
	}
	$retStr1.='</select>';
	$retVal[0]=$retStr1;
	$retVal[1]=$retStr2;
	return($retVal);
}

?>

</body>
</html>