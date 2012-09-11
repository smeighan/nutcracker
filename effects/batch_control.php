<?php
$target="AA2244"; // small target 6x30, runs fast good for testing
$target="AA";  // larger target, 2400 channes
$batch=2;   
//  Batch
//	Level     |   Html  |   Full Size Gifs  |  Thumbnail Gif's   |  NC Data file
// ------------------------------------------------------------------------------
//    0       |   Yes   |        Yes        |       Yes          |      Yes
//    1       |   No    |        Yes        |       Yes          |      Yes
//    2       |   No    |        No         |       Yes          |      Yes
//    3       |   No    |        No         |       No           |      Yes
$song_list[] = array($target,"BARS1","f_bars");
$song_list[] = array($target,"FIRE1","f_fire");
$song_list[] = array($target,"GARLAND0","f_garlands");
$song_list[] = array($target,"FLY_0_0","f_butterfly");
$song_list[] = array($target,"BARS1_TEST","f_bars");
$song_list[] = array($target,"BARBERPOLE","f_spirals");
/*$song_list[] = array($target,"BARS2","f_bars");
$song_list[] = array($target,"BARS3","f_bars");*/
$username='f';
echo "<html>";
echo "<body>";
require_once ("f_bars.php");
require_once ("f_spirals.php");
require_once ("f_butterfly.php");
require_once ("f_fire.php");
require_once ("f_garlands.php");
//
echo "<table border=2>";
echo "<tr>";
echo "<th>#</th>";
echo "<th>Target</th>";
echo "<th>Effect</th>";
echo "<th>Elapsed<br/>Time (secs)</th>";
echo "<th>gif</th>";
echo "</tr>";
list($usec, $sec) = explode(' ', microtime());
$program_start = (float) $sec + (float) $usec;
foreach($song_list as $i=>$arr2)
{
	//
	$target=$arr2[0];
	$effect=$arr2[1];
	$program=$arr2[2];
	echo "<tr>";
	$row=$i+1;
	echo "<td>$row</td>";
	echo "<td>$target</td>";
	echo "<td>$effect</td>";
	$get=get_user_effects($target,$effect,$username);
	$get['batch']=$batch;
	$get['username']='f';
	$get['user_target']=$target;
	extract($get);
	/*echo "<pre>";
	print_r($get);
	echo "</pre>";*/
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	//
	if($program=="f_bars")    f_bars   ($get);
	if($program=="f_spirals") f_spirals($get);
	if($program=="f_butterfly") f_butterfly($get);
	if($program=="f_fire") f_fire($get);
	if($program=="f_garlands") f_garlands($get);
	//
	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
	echo "<td>$elapsed_time</td>";
	$gif="workspaces/2/" . $target . "~" . $effect ."_th.gif";
	echo "<td><img src=\"$gif\" /></td>\n";
	//
	echo "</tr>";
	ob_flush();
	flush();
}
echo "</table>";
list($usec, $sec) = explode(' ', microtime());
$program_end = (float) $sec + (float) $usec;
$elapsed_time = round($program_end - $program_start, 5); // to 5 decimal places
$number_effects=$i+1;
echo "<h2>Total time to process these $number_effects effects was $elapsed_time seconds</h2>\n";
echo "</body>";
echo "</html>";

function get_user_effects($target,$effect,$username)
{
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
	$query = "SELECT hdr.effect_class,hdr.username,hdr.effect_name,
	hdr.effect_desc,hdr.music_object_id,
	hdr.start_secs,hdr.end_secs,hdr.phrase_name,
	dtl.segment,dtl.param_name,dtl.param_value
	FROM `effects_user_hdr` hdr, effects_user_dtl dtl
	where hdr.username = dtl.username
	and hdr.effect_name = dtl.effect_name
	and hdr.username='$username'
	and upper(hdr.effect_name)=upper('$effect')";
	//	echo "<pre>count_gallery: $query</pre>\n";
	//
	//
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$cnt=0;
	$string="";
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		//	if(strncmp($param_name,"color",5)==0 and strncmp($param_value,"#",1)==0) $param_value=hexdec($param_value);
		//	if(strncmp($param_name,"background_color",strlen("background_color"))==0 and strncmp($param_value,"#",1)==0) $param_value=hexdec($param_value);
		$string = $string . "&" . $param_name . "=" . $param_value;
		$get[$param_name]=$param_value;
	}
	// we also need teh effect class from teh header
	$get['effect_class']=$effect_class;
	return $get;
}
