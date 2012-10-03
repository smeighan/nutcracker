<?php
if (!is_dir("uploads/"))
	mkdir("uploads/");
$target_path = "uploads/";
$username = $_POST['username'];
$max_file_size = $_POST['MAX_FILE_SIZE'];
$filename = $_FILES['uploadedfile']['name'];
$filesize=$_FILES['uploadedfile']['size'];
$infile=$_FILES['uploadedfile']['tmp_name'];

if (!is_file($infile)) {
	echo "The file ".$filename." was not uploaded.  A problem exists! <br />";
	die;
}

if ($filesize>$max_file_size) {
	echo "The file ".$filename." is too big!<br />";
	die;
}

$fh=fopen($infile,'r');
$isValid=true;
while(($line=fgets($fh))&& ($isValid)) {
	if (strlen(trim($line))>0) {
		$tok=preg_split("/ +/", trim($line));
		$isValid=(count($tok)>2);
	}
}
if (!$isValid) {
	echo "This file ".$filename." is not in the correct format<br />";
	die;
}

$target_path = $target_path . $username. "~".basename( $filename); 
if(move_uploaded_file($infile, $target_path)) {
    echo "The file ".  basename( $filename). 
    " has been uploaded";
} else{
    echo "There was an error uploading the file, please try again!";
}
?>