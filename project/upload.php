<?php
$username = $_GET['username'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Upload CSV file for Nutcracker Phrases</title>
<link href="../css/ncFormDefault.css" rel="stylesheet" type="text/css" />
</head>
<body>
<form enctype="multipart/form-data" action="uploader.php" method="POST" class="SubmitButton" >
<input type="hidden" name="MAX_FILE_SIZE" value="1000" />
<input type="hidden" name="username" value="<?php echo $username;?>">
Choose a file to upload: <input name="uploadedfile" type="file" class="FormFieldName" /><br />
<input type="submit" value="Upload File" class="SubmitButton" /><p />
NOTE : You will have to refresh the loader file after upload!<br />
</form>

</body>
</html>
