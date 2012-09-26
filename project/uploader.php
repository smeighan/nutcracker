<?php
$target_path = "uploads/";
$username = $_POST['username'];
$max_file_size = $_POST['MAX_FILE_SIZE'];
$filename = $_FILES['uploadedfile']['name'];
$filesize=$_FILES["uploadedfile"]["size"];
$filetype=$_FILES["uploadedfile"]["type"];
//print_r($_FILES);
//$filesize = filesize($filename);
if ($filesize>$max_file_size) {
	echo "The file ".$filename." is too big!<br />";
	die;
}
if ($filetype != "text/csv") {
	echo "The file ".$filename." is not a CSV type file<br />";
	die;
}
$target_path = $target_path . $username. "~".basename( $_FILES['uploadedfile']['name']); 
if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
    echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
    " has been uploaded";
} else{
    echo "There was an error uploading the file, please try again!";
}
?>