 <?php
function sql2sql($table, $where_clause)
{
	require_once('./conf/config.php');
	$SQL_query = "SELECT * FROM " . $table;
	if (strlen($where_clause) != 0)
	{
		$SQL_query .= " WHERE " . $where_clause;
	}
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	$result = mysql_query ($SQL_query, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
	$SQLSTR = "";  //Initialize the output string
	$INSERTSTR = "INSERT INTO ".$table;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {   
	  $sepStr = "";
	  $COLSTR = " (";
	  $VALSTR = " VALUES (";
	  // cells
	  $i = 0;
	  foreach ($row as $cell) {
		// Escaping illegal characters - not tested actually ;)
		$cell = str_replace("&", "&amp;", $cell);
		$cell = str_replace("<", "&lt;", $cell);
		$cell = str_replace(">", "&gt;", $cell);
		$cell = str_replace("\"", "&quot;", $cell);
		$col_name = mysql_field_name($result,$i);
		$COLSTR .= $sepStr."'".$col_name."'";
		$VALSTR .= $sepStr."'".$cell."'";
		$sepStr = ",";
		$i++;
	  }
	  $COLSTR .= ") ";
	  $VALSTR .= ") ";
	  $SQLSTR .= $INSERTSTR.$COLSTR.$VALSTR.";\n";
	 }
	return ($SQLSTR);
}
function sql2xml($table, $where_clause)
{
	require_once('./conf/config.php');
	$SQL_query = "SELECT * FROM " . $table;
	if (strlen($where_clause) != 0)
	{
		$SQL_query .= " WHERE " . $where_clause;
	}
 	$DB_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Could not connect to host.");
	mysql_select_db(DB_DATABASE, $DB_link) or die ("Could not find or access the database.");
	$result = mysql_query ($SQL_query, $DB_link) or die ("Data not found. Your SQL query didn't work... ");
	$XML = "";  //Initialize the output string
	$XML .= "\t<".$table.">\n";  //Encapsulate the upper most tag with result
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

function clearFile($filename, $ext='.xml')
{
	$myFile = $filename;
	if (substr($myFile,-4) != $ext) 
	{
		$myFile .= $ext;
	}
	$myFile = BASE_PATH."/".$myFile;
	$fh = fopen($myFile, 'w') or die("can't open file");
	fclose($fh);
}
	
function writeFile($filename, $outstr, $ext='.xml') 
{
	$myFile = $filename;
	if (substr($myFile,-4) != $ext) 
	{
		$myFile .= $ext;
	}
	$myFile = BASE_PATH."/".$myFile;
	$fh = fopen($myFile, 'a+') or die("can't open file");
	fwrite($fh, $outstr);
	fclose($fh);
}

function exportXML()
{
	// Get XML form from a SQL query
	date_default_timezone_set('UTC');
	$Datetime = date('Y-m-d');
	$username = $_GET['username'];
	$fileStr = $username.'-'.$Datetime;  // this is used both for the outfile and for the outermost tag
	$outfile = "xml/".$fileStr.".xml";  // name of the xml file to write
	//$sqloutfile = "sql/".$fileStr.".sql"; // name of the sql file to write 
	$whereStr = 'username="'.$username.'"';  // the filter  for the database
	clearFile($outfile, '.xml');	// erase previous xml (if exists)
	//clearFile ($sqloutfile, '.sql');	// erase previous sql(if exists)
	$xml = "<".$fileStr.">\n";   // write the outermost tag
	writeFile($outfile,$xml, '.xml'); 
	$tablelist = array('effects_user_hdr','effects_user_dtl','members','models','music_object_hdr');
	foreach ($tablelist as $tablename) {
		$xml = sql2XML($tablename, $whereStr);  // get xml from table for output
		writeFile($outfile,$xml,'.xml'); // write out the XML string to the file
		//$sqlStr = sql2sql($tablename,$whereStr); // get sql from table for output
		//writeFile($sqloutfile,$sqlStr, '.sql'); // write out the SQL string to the file
	}
	// get xml from music_object_dtl table for output
	$xml = sql2XML('music_object_dtl','music_object_id in (select music_object_id from music_object_hdr where username="'.$username.'")');
	writeFile($outfile,$xml, '.xml');		// Write the XML out to the output file
	//$sqlStr = sql2sql('music_object_dtl','music_object_id in (select music_object_id from music_object_hdr where username="'.$username.'")');
	//writeFile($sqloutfile,$sqlStr, '.sql');		// Write the SQL out to the output file
	$xml = "</".$fileStr.">\n";
	writeFile($outfile,$xml, '.xml');  // close the outermost tag
	echo "created file ". $outfile. "<p />";
	echo 'Download the file by right clicking the following link: <a href="'.$outfile.'">'.$outfile.'</a><br />Select "save link as" and save the file into your local nutcracker/xml directory<br /><p />';
	//echo "created file ". $sqloutfile. "<p />";
	//echo 'Download the file by right clicking the following link: <a href="'.$sqloutfile.'">'.$sqloutfile.'</a><br />Select "save link as" and save the file into your local nutcracker/sql directory<br /><p />';	echo '<a href="./login/member-index.php">Return to main page</a>';
}

// MAIN
define('BASE_PATH',realpath('.'));
define('BASE_URL', dirname($_SERVER["SCRIPT_NAME"]));
exportXML();  // this is the function call that needs to be imbedded in the code to call the export on the server side.
?>
