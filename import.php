<?php

function importXML($inFile,$my_username) 
{
	//$newLineStr = "\n";
	$newLineStr = "<br />";
	$inFile = BASE_PATH."\\xml\\".$inFile;
	echo "opening file " . $inFile . $newLineStr;
	$xml = simplexml_load_file($inFile);
	$filename = $xml->getName();
//	echo $xml->getName() . $newLineStr;
	foreach($xml->children() as $tablename)
	{
		$cntRow = 0;
		$tableStr = $tablename->getName();
		echo "importing table " . $tableStr . $newLineStr;
		foreach($tablename->children() as $child)
	  	{
//		  echo $child->getName() . ": " . $child . $newLineStr;
		  if (substr($child->getName(),0,3) == "row")
		  {
			$cntRow += 1;
			$intoStr = "";
			$valStr = "";
			$sepstr = "";
			foreach($child->children() as $grandchild)
			{
			    	if ($grandchild->getName() == "username")
				{     
					$intoStr .= $sepstr."username";
					$valStr .=$sepstr.'"'.$my_username.'"';
				} elseif ($grandchild->getName() == "date_created")
				{
					$intoStr .= $sepstr."date_created";
					$valStr .= $sepstr."NOW()";
				} else {
					$intoStr .= $sepstr.$grandchild->getName();
					$valStr .= $sepstr.'"'.$grandchild.'"';
				}
				$sepstr = ',';
			//	echo $grandchild->getName() . ": " . $grandchild . $newLineStr;
			}
			$sqlStr = "REPLACE INTO " . $tableStr . " (".$intoStr.") VALUES (".$valStr.");";
			//echo $sqlStr .$newLineStr;  // for webpage debug
			if (strlen($sqlStr) > 0)
			{
				doSQL($sqlStr);
			}
		// Do the SQL statement here
		  }

	  	}
		echo "Processed ". $cntRow ." rows of data".$newLineStr;
	}
	?>
	    <br />
		<a href="/nutcracker/login/member-index.php">Return to the main page</a>
	<?php
}
function doSQL($sqlStr)
{
	require_once('./conf/config.php');
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	mysql_query($sqlStr, $DB_link) or die ("Your SQL query didn't work... ");
	// echo $sqlStr."<br />"; // for testing
}

//array of files without directories... optionally filtered by extension
function file_list($d,$x){
	foreach(array_diff(scandir($d),array('.','..')) as $f)
		if (is_file($d.'/'.$f)&&(substr($f,(-1*strlen($x))) == $x))
			$l[]=$f;
	return $l;
} 
function displayFormHeader()
{
	echo '<form method="get" action="import.php" name="import">';    
    echo '<div class="formRow">';
    echo '                        <div class="label">';
    echo '                      <label for="import">Select a file to import into your local database</label>';
    echo '            </div>';
	echo '	<div class="dropDown">';
}

function displayFormFooter()
{
			echo '</div>';
			echo '</div>';
		echo '</form>';
}
 
function fileSelector($d, $username) {
	displayFormHeader();
	echo '<input type="hidden" name="username" value="'.$username.'">';
	echo '<select name="importFile">';
	foreach ($d as $filename) {
		echo '<option value="'.$filename.'">'.$filename.'</option>';
	}
			echo '</select>';
			echo '<input type="submit" name="submit" value="Submit">';
			echo '</div>';
			echo '</div>';
		echo '</form>';
echo '<form method="get" action="login/member-index.php">';   		
	echo '<input type="submit" name="cancel" value="Cancel">';
	displayFormFooter();
}

// MAIN FUNCTION HERE
define('BASE_PATH',realpath('.'));
$username = $_GET['username'];
if (isset($_GET['submit'])) {
	$filename = $_GET['importFile'];
	importXML($filename, $username);
} else {
    $dirname = 'xml/';
//	$username = 'kgustafson';  // For testing
	$dirlist = file_list($dirname,'.xml');
	fileSelector($dirlist, $username);
}
?> 
