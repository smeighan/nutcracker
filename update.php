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
		<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"/> 
		<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
		<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Nutcracker Update </h1>

<?php
/*
 *  FILE: update.php
 
 
 Nutcracker: RGB Effects Builder

    Copyright (C) 2012  Sean Meighan

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
	
	
*/
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;

if($_SERVER['HTTP_HOST'] !='localhost')
{
	echo"<pre><h1>ERROR! You cannot run this script on any place except for your local computer<br/>";
	echo "Please RIGHT-CLICK the filename, not left click it. select save as and put<br/>";
	echo "install.php into your c:\wamp\www directory.";
	echo "\nOnce you have downloaded install.php you run it by opening a web page to 'localhost/install.php'\n";
	die ("\n\nProgram exiting .. ");
}

define('CR', "\r");          // carriage return; Mac
define('LF', "\n");          // line feed; Unix
define('CRLF', "\r\n");      // carriage return and line feed; Windows
define('BR', '<br />' . LF); // HTML Break

set_time_limit(300);	// give us 5 minutes to finish.

//	ftp login info
$host = 'meighan.net';
$usr = 'nutcracker';
$pwd = 'xfer1011';

// connect to FTP server (port 21)
$conn_id = ftp_connect($host, 21) or die ("Cannot connect to host");

ob_flush();
// send access parameters
ftp_login($conn_id, $usr, $pwd) or die("Cannot login");

ob_flush();
/*     sample manifest file:


nutcracker                  index.html                       c2a0342a7c2dccb04bdcd543d3802610 11590
nutcracker/css              loginmodule.css                  50288fa14a081329ecccf4de5aa59b52 934
nutcracker/effects          arrow.gif                        5034704a76cd55c1cbcbc58ea6bf523f 66
nutcracker/effects          auth.php                         ec02b95cfa4f58743d1e8fdc1f49b444 268
nutcracker/effects          barberpole.ico                   314c16a7b41aa60be6733d46d9132749 318
nutcracker/effects          butterfly.php                    81827c6395e65aa688b4d7eafd5005f4 9286
 */

//	first lets get a current copy of the manifest from meighan.net
//
echo "<pre>";
//
$local_file = "manifest";
$server_file =   "nutcracker/manifest";
echo "Getting a copy of the current manifest from meighan.net\n";
if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
	printf( "%-64s [downloaded]\n",$local_file);
} else {
	echo "There was a problem with ftp_get(conn_id, $local_file, $server_file, FTP_BINARY)\n";
}


echo "<pre>";
echo "<h2>Now for each entry in the manifest , check its checksum. If it is different update our local copy.</h2>\n";

$fh=fopen("manifest","r") or die ("Unable to open manifest");
while (!feof($fh)) 
{
	$line = fgets($fh);
	$line = str_replace(CRLF, LF, $line);
	$line = str_replace(CR, LF, $line);
	ob_flush();
	flush();
	$tok=preg_split("/ +/", $line);
	//$tok=explode(" ", $line);
	//
	/*

Arraygg
(
    [0] => \nutcracker\effects
    [1] => text.php

)
	 */
	$c=count($tok);
	if($c==4)
	{
		$local_dir=$tok[0];
		$tok2=explode("/",$local_dir);
		$root_dir=$tok2[0];
		$filename = $tok[1];
		$md5 = $tok[2];
		$filesize = $tok[3];

		if (file_exists($local_dir)) {
			//		echo "The directory $local_dir exists\n";
		} else {
			echo "The directory $local_dir does not exist, creating it ..\n";
			mkdir($local_dir);
		}

		ob_flush();

		if(empty($stats[$local_dir]))
			$stats[$local_dir]=0;
		$stats[$local_dir]++;
		$local_file= $local_dir . "/" . $filename;
		$local_file = str_replace(LF, "", $local_file);

		$server_file =   $local_dir . "/" . $filename;
		$server_file = str_replace(LF, "", $server_file);

		//$local_file=str_replace('/','\\',	$local_file);
		//echo " trying ftp_get(conn_id, [$local_file], [$server_file]\n";
		// perform file download
		$md5_local="??"; $fsize=0;
		if(file_exists($local_file) )
		{
			$md5_local=md5_file($local_file);
			$fsize=filesize($local_file);
		}
		if($md5==$md5_local)
			printf( "%-64s %s [ok]\n",$local_file,$md5);
		else
			if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
				if($fsize==0)
					printf( "%-64s %s [New file]\n",$local_file,$md5);
				else
					printf( "%-64s %s [updated. Old size %7d, new size %7d]\n",$local_file,$md5,$fsize,$filesize);
			} else {
				echo "There was a problem with ftp_get(conn_id, $local_file, $server_file, FTP_BINARY)\n";
			}
		ob_flush();
		flush();
	}
}



// close the connection
ftp_close($conn_id);

	
echo "<h2>Update complete</h2>\n";

