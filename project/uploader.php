<?php
if (!is_dir("uploads/"))
	mkdir("uploads/");
$target_path = "uploads/";
$username = $_POST['username'];
$max_file_size = $_POST['MAX_FILE_SIZE'];
$filename = $_FILES['uploadedfile']['name'];
$filesize=$_FILES['uploadedfile']['size'];
$infile=$_FILES['uploadedfile']['tmp_name'];
/*
        if($_FILES['image']['error'] == 0){
           echo "success - move uploaded file and process stuff here\n";

        }else{
            echo "'there was an error uploading file' stuff here....\n";    
        }
echo "<pre>";
print_r($_FILES);
echo "</pre>";*/

//
//	Error code returns

/*UPLOAD_ERR_OK
Value: 0; There is no error, the file uploaded with success.

UPLOAD_ERR_INI_SIZE
Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini.

UPLOAD_ERR_FORM_SIZE
Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.

UPLOAD_ERR_PARTIAL
Value: 3; The uploaded file was only partially uploaded.

UPLOAD_ERR_NO_FILE
Value: 4; No file was uploaded.

UPLOAD_ERR_NO_TMP_DIR
Value: 6; Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.

UPLOAD_ERR_CANT_WRITE
Value: 7; Failed to write file to disk. Introduced in PHP 5.1.0.

UPLOAD_ERR_EXTENSION
Value: 8; A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0.
*/
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
		$tok=preg_split("/[\t ]+/", trim($line));
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