<?php
require_once('../conf/auth.php');
require_once('../conf/barmenu.php');
require_once('project_filer.php');
require_once ("../effects/f_bars.php");
require_once ("../effects/f_spirals.php");
require_once ("../effects/f_butterfly.php");
require_once ("../effects/f_fire.php");
require_once ("../effects/f_garlands.php");
require_once ("../effects/f_text.php");
require_once ("../effects/f_color_wash.php");
require_once ("../effects/f_gif.php");
require_once ("../effects/f_life.php");
require_once ("../effects/f_meteors.php");
require_once ("../effects/f_snowstorm.php");
require_once ("../effects/f_user_defined.php");
require_once ("../effects/f_pictures.php");
require_once ("../effects/f_single_strand.php");
require_once ("../effects/f_snowflakes.php");
require_once ("../effects/f_twinkle.php");
require_once ("../effects/f_layer.php");
require_once ("../effects/f_tree.php");
require_once ("project_loader.php");
require_once ("dbcontrol.php");
require_once ("gen_vixen.php");
require_once ("gen_hls.php");
require_once ("gen_lor.php");
require_once ("gen_lsp.php");
require_once ("gen_xml.php");
ini_set("memory_limit","512M");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="../js/barmenu.js"></script>
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
//
require("../effects/read_file.php");
set_time_limit(0);
	ini_set("memory_limit","1024M"); //  raise the default memory for this program to 1 gig, from 256mrg
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];
extract($_GET);
//
//	start timer
	list($usec, $sec) = explode(' ', microtime()); // <scm>
		$script_start = (float) $sec + (float) $usec; // ,scm>
		
		
$msg_str="";
if (isset($type)) {
	switch ($type) {
		case 1:
			$msg_str=select_song($username);
			break;
		case 2:
			if (isset($project_id)) {
				$msg_str=edit_song($project_id);
			} else {
				$msg_str = "***Error occurred *** no project id<br />";
			}
			break;
		case 3:
			if (isset($project_id)) {
				$msg_str=remove_song($project_id);
			} else {
				$msg_str="***Error occurred *** no project id<br />";
			}
			break;
		default:
			$msg_str= "***Error occurred *** Invalid value for function call<br />";
	}
} else {
    $debug = false;
	if ($debug) print_r($_POST);
	extract($_POST);
	if (isset($NewProjectCancel)) {
		if ($debug) echo "In New Project Cancel<br>";
		$msg_str="*** Song add was cancelled ***";
	} 
	if (isset($NewProjectSubmit)) {
		if ($debug) echo "In New Project Submit<br>";
		//echo "$song_id  , $username,   $frame_delay, $model_name <br />";
		$msg_str=add_song($song_id,$username, $frame_delay, $model_name);
	}
	if (isset($SavePhraseEdit)) {
		if ($debug)	echo "In Save Phrase Edit<br>";
		save_phrases($_POST);
		$msg_str=edit_song($project_id);
		$msg_str="Edit Saved";
	}
	if (isset($CancelPhraseEdit)) {
		if ($debug) echo "In Cancel Phrase Edit<br>";
		$msg_str="Song detail hidden";
	}
	if (isset($LoadPhraseFile) && (!isset($SelectFile))) {
		if ($debug) echo "In Preload phase<br>";
		$directory="uploads/";
		echo showFilesDir($directory, $project_id);
		printFileLoadInstruction();
		$msg_str="";
	}
	if (isset($SelectFile)) {
		if ($debug) echo "In Post Load Phrase<br>";	
		getPhrasesFromFile($project_id, $PhraseFile);
		echo "<br />";
		$tok=preg_split("/~+/", trim($PhraseFile));
		$prettyFilename=$tok[1];
		$sql = "SELECT model_name, song_name, artist, song_url, frame_delay, project.username, last_update_date, last_compile_date FROM project LEFT JOIN song ON project.song_id=song.song_id WHERE project_id=".$project_id;
		$result=nc_query($sql);
		$row=mysql_fetch_array($result,MYSQL_ASSOC);
		$song_name=$row['song_name'];
		$artist=$row['artist'];
		$model_name=$row['model_name'];
		$msg_str="loaded phrase file ".$prettyFilename." for project ".$song_name."/".$model_name;
	}
	if (isset($MasterNCSubmit)) {
		if ($debug) echo "In Generate Phase<br>";
		$sql="UPDATE project SET last_compile_date=NOW() WHERE project_id=".$project_id;
		nc_query($sql);
		if ($outputType!='xml') {
			//echo "getPhraseArray<br />";
			$myarray=getPhraseArray($project_id);
			//echo "setupNCfiles<br />";
			$projectArray=setupNCfiles($project_id,$myarray);
			//echo "prepMasterNCFile<br />";
			$myNCarray=prepMasterNCfile($project_id);
			$numEntries=count($myNCarray);
			//echo "checkValidNCFiles<br />";
			$myarray=checkValidNCFiles($myarray, $numEntries, $project_id);
		} else {
			$myarray=array();
			$projectArray=array();
			$myNCarray=array();
		}
		processMasterNCfile($project_id, $projectArray, $myarray, $outputType, $myNCarray);
	}
	if (isset($EffectEdit)) {
		if ($debug) echo "In Effect Edit<br>";
		foreach($_POST as $key=>$value) {
			if ($key=="project_id")
				$project_id=$value;
			if ($key=="effect_name")
				$effect_name=$value;
			if (($key!="EffectEdit") && ($key!="project_id") && ($key!="type") && ($key!="effect_name")) {
				$sql='UPDATE effects_user_dtl SET param_value="'.$value.'" WHERE username="'.$username.'" AND effect_name= "' .$effect_name . '" AND param_name="'.$key.'"';
				nc_query($sql);
			}
		}
		$msg_str="Effect Edited<br />".edit_song($project_id);
	}
	if (isset($EffectEditCancel)) {
		if ($debug) echo "In Cancel Effect Edit<br>";
		$msg_str="Effect Edit Cancelled<br />".edit_song($project_id);
	}	
}
echo $msg_str;
list($usec, $sec) = explode(' ', microtime()); // <scm>
		$script_end = (float) $sec + (float) $usec;    // <scm>
		$elapsed_time = round($script_end - $script_start, 3);  // <scm>
		echo " ($elapsed_time secs)";  // <scm>
?>
<h2>Current Nutcracker projects</h2>
<form action="<?php echo "project-exec.php"; ?>" method="post">
<input type="hidden" name="username"     value="<?php printf ("$username");    ?> "/>
<table class="Gallery">
<tr>
<th>ID#</th>
<th>Song Name</th>
<th>Artist</th>
<th>Purchase song from here</th>
<th>Model</th>
<th>Frame Timing (ms)</th>
<th>Commands</th>
<?php
	$sql = "SELECT project_id, song.song_id as song_id, song_name, artist, song_url, frame_delay, model_name FROM project LEFT JOIN song ON project.song_id = song.song_id WHERE project.username='$username' ORDER BY song_name, model_name";
	//echo "$sql <br />";
	$result = nc_query($sql);
	$cnt=0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if ($cnt%2==0)
			$trStr="<tr class=\"alt\">";
		else
			$trStr="<tr>";
		$cnt++;
		echo $trStr;
		$project_id = $row['project_id'];
		$song_id = $row['song_id'];
		$artist = $row['artist'];
		$song_name = $row['song_name'];
		$song_url = $row['song_url'];
		$frame_delay = $row['frame_delay'];
		$model_name = $row['model_name'];
		if ($cnt%2==0) 
			$trStr='<tr>';
		else
			$trStr='<tr class="alt">';
	echo $trStr;?>
		<td><?php echo $project_id?></a></td>
	<td><?php echo $song_name?></a></td>
	<td><?php echo $artist?></td>
	<td><a href="<?php echo $song_url?>"><?php echo $song_url?></a></td>
	<td><?php echo $model_name?></td>
	<td><?php echo $frame_delay?></td>
	<td><a href="project.php?type=2&project_id=<?php echo $project_id?>"><img src="../images/edit.png">Edit</a>&nbsp;&nbsp;&nbsp;<a href="project.php?type=3&project_id=<?php echo $project_id?>"><img src="../images/delete.png">Remove</a></td>
</tr>
<?php		
	}
	if ($cnt == 0) {
		echo "<tr><td colspan=6>You do not have any current projects</td></tr>";
	}
?>
</table>
<p />
<a href="project.php?type=1">Add a Project</a><br />
</body>
</html>