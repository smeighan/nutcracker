 <?php
function displayFormHeader()
{
?><form method="post" action="sql2xml.php" name="sql2xml">    
                          <div class="formRow">
                                <div class="label">
                                    <label for="sql2xml">Select a username to export to XML</label>
                                </div>
								<div class="dropDown">
<?php
}
function displayUsernames() 
{
	require_once('./conf/config.php');
	$SQL_query = "SELECT DISTINCT username FROM members ORDER by username;";
	//echo $SQL_query; //uncomment for debugging
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	$result = mysql_query ($SQL_query, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
	echo '<select name="username">';
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
	{
		echo '<option value="'.$row['username'].'">'.$row['username'].'</option>';
	}
	echo '</select>';
	echo '<input type="submit" name="submit" value="Submit">';
	echo '<input type="submit" name="cancel" value="Cancel">';
}
function displayFormFooter()
{
?>
			</div>
			</div>
		</form>
<?php
}
 
function sql2xml($table, $where_clause)
{
	require_once('./conf/config.php');
	$SQL_query = "SELECT * FROM " . $table;
	if (strlen($where_clause) != 0)
	{
		$SQL_query .= " WHERE " . $where_clause;
	}
	// echo $SQL_query; //uncomment for debugging
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	$result = mysql_query ($SQL_query, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
	$XML = "";  //Initialize the output string
	$XML .= "\t<".$table.">\n";  //Encapsulate the upper most tag with result
	// $XML .= "<sql>".$SQL_query."</sql>\n"; //removed since it didn't add anything to the output
	$cnt = 0;
	// rows
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {   
	  $cnt +=1;
	  $XML .= "\t\t<row".$cnt.">\n"; 
	  $i = 0;
	  // cells
	  foreach ($row as $cell) {
		// Escaping illegal characters - not tested actually ;)
		$cell = str_replace("&", "&amp;", $cell);
		$cell = str_replace("<", "&lt;", $cell);
		$cell = str_replace(">", "&gt;", $cell);
		$cell = str_replace("\"", "&quot;", $cell);
		$col_name = mysql_field_name($result,$i);
		// creates the "<tag>contents</tag>" representing the column
		$XML .= "\t\t\t<" . $col_name . ">" . $cell . "</" . $col_name . ">\n";
		$i++;
	  }
	  $XML .= "\t\t</row".$cnt.">\n"; 
	 }
	$XML .= "\t</".$table.">\n";
	return ($XML);
}

function rand_chars($l, $c='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', $u = TRUE) {
 if (!$u) for ($s = '', $i = 0, $z = strlen($c)-1; $i < $l; $x = rand(0,$z), $s .= $c{$x}, $i++);
 else for ($i = 0, $z = strlen($c)-1, $s = $c{rand(0,$z)}, $i = 1; $i != $l; $x = rand(0,$z), $s .= $c{$x}, $s = ($s{$i} == $s{$i-1} ? substr($s,0,-1) : $s), $i=strlen($s));
 return $s;
}

function clearFile($filename)
{
	$myFile = $filename;
	if (substr($myFile,-4) != '.xml') 
	{
		$myFile .= ".xml";
	}
	$fh = fopen($myFile, 'w') or die("can't open file");
	fclose($fh);
}
	
function writeFile($filename, $outstr) 
{
	$myFile = $filename;
	if (substr($myFile,-4) != '.xml') 
	{
		$myFile .= ".xml";
	}
	$fh = fopen($myFile, 'a+') or die("can't open file");
	fwrite($fh, $outstr);
	fclose($fh);
}
// MAIN 
// If this has not been called from a previous selected username, then go ahead and ask the user to input 
// the user that they want through a self-reference form

if (isset($_POST['submit']))
{
		// Get XML form from a SQL query
		$now = new DateTime('now');
		$Datetime = $now->format('Y-m-d');
		$username = $_POST['username'];
		$randTag = rand_chars(4); // create a random 4 character tag to make the file name unique.
		$fileStr = $username.'-'.$Datetime.'-'.$randTag;
		$outfile = 'xml/'.$fileStr.'.xml';
		// erase previous xml (if exists) and writeout the master tag
		clearFile($outfile);
		$xml = "<".$fileStr.">\n";
		writeFile($outfile,$xml);
		// effects_user_hdr output
		$xml = sql2XML('effects_user_hdr','username = "'.$username.'"');
		// Write the XML out to the output file
		writeFile($outfile,$xml);
		// effects_user_dtl output
		$xml = sql2XML('effects_user_dtl','username = "'.$username.'"');
		// Write the XML out to the output file
		writeFile($outfile,$xml);
		// models table output
		$xml = sql2XML('models','username = "'.$username.'"');
		// Write the XML out to the output file
		writeFile($outfile,$xml);
		// music_object_hdr table output
		$xml = sql2XML('music_object_hdr','username = "'.$username.'"');
		// Write the XML out to the output file
		writeFile($outfile,$xml);
		//music_object_dtl table output
		$xml = sql2XML('music_object_dtl','music_object_id in (select music_object_id from music_object_hdr where username="'.$username.'")');
		// Write the XML out to the output file
		writeFile($outfile,$xml);
		//close out the master tag
		$xml = "</".$fileStr.">\n";
		writeFile($outfile,$xml);
		echo "created file ". $fileStr. "<p />";
		echo 'Download the file by right clicking the following link: <a href="'.$outfile.'">'.$outfile.'</a><br />Select "save link as" and save the file into your local nutcracker/xml directory<br />';
} else
{
	if (isset($_POST['cancel']))
	{
		echo "*** Export Cancelled ***<br />";
	}
	displayFormHeader();
	displayUsernames();
	displayFormFooter();
}
?>