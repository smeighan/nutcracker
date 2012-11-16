<?php
/*
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
Tun update gallery database
gallery.php?INSERT_NEW_GIFS=1
*/
//require_once('../conf/header.php');
//require("../effects/read_file.php");
if( isset($_REQUEST['group']) && $_REQUEST['group'] !='')
{
	$group=$_REQUEST['group'];
}
if( isset($_REQUEST['effect_class']) && $_REQUEST['effect_class'] !='')
{
	$effect_class=$_REQUEST['effect_class'];
}
extract ($_GET);
/* Array
(
[submit] => Submit Form to create your target model
[number_gifs] => 100
[sort] => member_id
[effect_class_selected] => Array
(
[0] => all
[1] => bars
[2] => butterfly
)
	[number_segments] => 
)*/
/*echo "<pre>";
print_r($_GET);
echo "</pre>\n";*/
// http://meighan.net/nutcracker/effects/gallery.php?start=101?end=151?number_gifs=50?sort=member_id?effect_class_selected=dummy|garlands|meteors
// QUERY_STRING] => start=101?end=151?number_gifs=50?sort=member_id?effect_class_selected=dummy|garlands|meteors
//
//
//$tokens=explode("?model=",$REQUEST_URI);
$number_gifs=0;
$effect_class_selected=array();
$sort="member_id";
if(isset($_POST)===false or $_POST==null ) // First time here? Called by member-index.php
{ // yes
	/*$tokens=explode("?",$_SERVER['QUERY_STRING']);
	$tok2=explode("=",$tokens[0]); $start_pic = $tok2[1];
	$tok2=explode("=",$tokens[1]); $end_pic = $tok2[1];
	$tok2=explode("=",$tokens[2]); $number_gifs = $tok2[1];
	$tok2=explode("=",$tokens[3]); $sort = $tok2[1];
	$tok2=explode("=",$tokens[4]); $effect_class_selected_array = $tok2[1];*/
	extract ($_GET);
	$self=$_SERVER['PHP_SELF'];
	echo "<form action=\"$self\" method=\"POST\" enctype=\"multipart/form-data\">\n";
	?>
	<label for="file">Filename:</label>
	<input type="file" name="file" id="file" /> 
	<br />
	<input type="submit" name="submit" value="Submit" />
	</form>
	<?php
}
else
{
	extract ($_GET);
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
			$src=$_FILES["file"]["tmp_name"];
			$tgt=$_FILES["file"]["tmp_name"] . ".tmp";
			
				move_uploaded_file($src,$tgt);
				echo "Stored in: " . $tgt;
			}
		}
	}
	else
	{
		echo "Invalid file";
	}
}
//
//
echo "<pre>";
print_r($_GET);
print_r($effect_class_selected);
echo "</pre>\n";
