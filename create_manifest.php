<?php
require_once('conf/config.php');
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
$query ="select * from manifest order by path,filename";
echo "<pre>get_effect_hdr query: $query</pre>\n";
$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
if (!$result)
{
	$message  = 'Invalid query: ' . mysql_error() . "\n";
	$message .= 'Whole query: ' . $query;
	die($message);
}
$NO_DATA_FOUND=0;
if (mysql_num_rows($result) == 0)
{
	$NO_DATA_FOUND=1;
}
$effects_classes=array();
$fh=fopen("manifest","w") or die("unable to open manifest for write");
echo "<table border=1>\n";
$effect_hdr=array();
if(!$NO_DATA_FOUND)
{
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$full_path = "../" . $path . "/" . $filename;
		$md5=md5_file($full_path);
		$fsize=filesize($full_path);
		echo "<tr><td>$path</td><td>$filename</td><td>$md5</td><td>$fsize</td></tr>\n";
		fwrite($fh,sprintf("%-32s %-32s %s %d\n",$path,$filename,$md5,$fsize));
		$query2=sprintf("update manifest set cksum='%s',filesize=%d where path='%s' and filename='%s'",
		$md5,$fsize,$path,$filename);
		//echo "<tr><td>$query2</td></tr>";
		$result2=mysql_query($query2) or die ("Error on $query2");
	}
}
echo "</table>\n";
fclose($fh);
