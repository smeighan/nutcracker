<?php
echo "<pre>GET:";
print_r($_GET);
echo "POST\n";
print_r($_POST);
echo "FILES\n";
print_r($_FILES);
echo "</pre>\n";


if (($_FILES["file"]["type"] == "text/plain")
		&& ($_FILES["file"]["size"] < 20000))

{
	if ($_FILES["file"]["error"] > 0)
	{
		echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
	}
	else
	{
		echo "Upload: " . $_FILES["file"]["name"] . "<br />";
		echo "Type: " . $_FILES["file"]["type"] . "<br />";
		echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
		echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
		if (file_exists("upload/" . $_FILES["file"]["name"]))
		{
			echo $_FILES["file"]["name"] . " already exists. ";
		}
		else
		{
			move_uploaded_file($_FILES["file"]["tmp_name"],
			"upload/" . $_FILES["file"]["name"]);
			echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
		}
	}
}
else
{
	echo "Invalid file";
}
?>