<?php
// define some variables
$local_file = 'xml2.php';
$server_file = 'xml.php';

$host = 'meighan.net';
$usr = 'nutcracker';
$pwd = 'xfer1011';

// file to move:
$local_file = 'xml2.php';
$ftp_path = 'xml.php';

// connect to FTP server (port 21)
$conn_id = ftp_connect($host, 21) or die ("Cannot connect to host");

// send access parameters
ftp_login($conn_id, $usr, $pwd) or die("Cannot login");
// try to download $server_file and save to $local_file

$files=array('xml.php','vixen.php','lsp.php','lor.php','target_effects.php');
echo "<pre>";
foreach($files as $i => $file)
{
	$local_file = $file . "2";
	$server_file = $file;
	// perform file download
	if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
		echo "Successfully written to $local_file. Cksum=" . md5_file($local_file) . "\n";
	} else {
		echo "There was a problem\n";
	}
}





// close the connection
ftp_close($conn_id);






// turn on passive mode transfers (some servers need this)
// ftp_pasv ($conn_id, true);


