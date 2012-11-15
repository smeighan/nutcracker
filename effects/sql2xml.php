<?php
/**
* sql2xml prints structured XML
*
* @param string  $sql       - SQL statement
* @param string  $structure - XML hierarchy
*/
$sql = "select * from members";
require_once("../effects/read_file.php");
$username='f';
$path ="../export";
$directory=$path;
if (file_exists($directory))
{
	} else {
	if($batch==0) echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}
//
$member_id=get_member_id($username);
$path ="../export/" . $member_id;
$directory=$path;
if (file_exists($directory))
{
	} else {
	if($batch==0) echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}
$filename=$path . "/" . $username . "_" . date('Y-m-d_Hi') . ".xml";
$tables = array (
"effects_user_hdr" => "where username='$username'",
"effects_user_dtl" => "where username='$username'",
"project_dtl" => "where project_id in (select project_id from project where username='$username')",
"project" => "where username='$username'",
"models_strands" => "where username='$username'",
"models_strand_segments" => "where username='$username'",
"models" => "where username='$username'"
);
echo "<pre> ";
print_r($tables);
echo "</pre>filename=$filename\n";
$fp=fopen($filename,"w");
if(!$fp)
{
	die ("Fopen failed to open $filename");
}
fwrite($fp,"<?xml version='1.0' standalone='yes'?>\n<nutcracker>\n");
foreach  ($tables as $db_table => $where)
{
	sql2xml($fp,$username,$db_table,$where);
}
//$fp=fopen($filename,"a");
fwrite($fp,"</nutcracker>\n");
fclose($fp);
//
//
display_xml($filename,$tables);
echo "</pre>\n";

function display_xml($filename,$tables)
{
	//$resXml = simplexml_load_file($filename); //$requestUrl is where the xml file is located
	echo "<pre>";
	//	print "<pre><textarea style=\"width:200%;height:100%;\">"; 
	$Array = simplexml_load_string(file_get_contents($filename)); 
	$xml_array = xml2phpArray($Array,array());
	//print_r($xml_array); 
	$table_array = $xml_array['xml_export'];
	foreach ($table_array as $i => $data_array)
	{
		$db_table = $data_array['db_table'];
		$username = $data_array['login_username'] ;
		$where = $tables[$db_table];
		$delete="DELETE from $db_table $where";
		echo "<pre>i=$i $db_table: $delete </pre>\n";
		$row_array=$data_array['ROW0'] ;
		$loop=0;
		$insert = "INSERT into $db_table (";
		$field_list='';
		foreach ($row_array as $r => $row_data)
		{
			$c=count($row_data);
			//echo "r=$r   c=$c \n";
			$comma="";
			$field_list='';
			$loop++;
			$values ='(';
			foreach ($row_data as $name=>$value)
			{
				//echo "   $name => $value\n";
				if($loop==1)
				{
					$field_list .= $comma . $name;
				}
				$values .= "$comma'" . mysql_real_escape_string ($value) . "'";
				$comma=",";
			}
			if($loop==1)
			{
				$field_list .=") values\n";
				$insert .= $field_list;
			}
			$values .= ")";
			//	echo "<pre>$insert $values</pre>\n";
		}
		//print "</textarea></pre>"; 
		echo "</pre>";
	}
}

function xml2phpArray($xml,$arr)
{
	$iter = 0; 
	foreach($xml->children() as $b)
	{
		$a = $b->getName(); 
		if(!$b->children())
		{
			$arr[$a] = trim($b[0]);
		}
		else{ 
			$arr[$a][$iter] = array(); 
			$arr[$a][$iter] = xml2phpArray($b,$arr[$a][$iter]);
		}
		$iter++;
	}
	return $arr;
}

function sql2xml($fp,$username,$table,$where, $structure = 0)
{
	// init variables for row processing
	$sql = "SELECT * from $table $where";
	$row_current = $row_previous = null;
	// set MySQL username/password and connect to the database
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	//$fp=fopen("text.xml","a");
	fwrite($fp,"<xml_export>\n");
	fwrite($fp,"<db_table>$table</db_table>\n");
	fwrite($fp,"<login_username>$username</login_username>\n");
	//
	$query=$sql;
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	echo "<pre>$sql</pre>\n";
	// get number of columns in result
	$ncols = mysql_num_fields($result);
	// is there a hierarchical structure
	if ($structure == 0)
	{
		$deep = -1;
		$pos = 0;
	}
	else {
		// set hierarchy levels and number of levels
		$hierarchy = explode(',', $structure);
		$deep = count($hierarchy);
		// set flags for opened tags
		for ($i = 0; $i <= $deep; $i++)
		{
			$tagOpened[$i] = false;
		}
		// set initial row
		for ($i = 0; $i < $ncols; $i++)
		{
			$rowPrev[$i] = microtime();
		}
	}
	// loop through result set
	while ($row = mysql_fetch_row($result))
	{
		// loop through hierarchy levels (data set columns)
			for ($level = 0, $pos = 0; $level < $deep; $level++)
		{
			// prepare row segments to compare
			for ($i = $pos; $i < $pos+$hierarchy[$level]; $i++)
			{
				$row_current .= trim($row[$i]);
				$row_previous .= trim($rowPrev[$i]);
			}
			// test row segments between row_current and row_previous
			// it should be "!==" and not "!="
			if ($row_current !== $row_previous)
			{
				// close current tag and all tags below
				for ($i = $deep; $i >= $level; $i--)
				{
					if ($tagOpened[$i])
					{
						fwrite($fp, "</ROW$i>\n");
					}
					$tagOpened[$i] = false;
				}
				// reset the rest of rowPrev
				for ($i = $pos; $i < $ncols; $i++)
				{
					$rowPrev[$i] = microtime();
				}
				// set flag to open
				$tagOpened[$level] = true;
				fwrite($fp, "  <ROW$level>\n");
				// loop through hierarchy levels
				for ($i = $pos; $i < $pos + $hierarchy[$level]; $i++)
				{
					$name = strtoupper(mysql_field_name($result, $i));
					fwrite($fp, "    <$name>");
					fwrite($fp, trim(htmlspecialchars($row[$i],$i)));
					fwrite($fp, "</$name>\n");
				}
			}
			// increment row position
			$pos += $hierarchy[$level];
			// reset row segments (part of columns)
				$row_current = $row_previous = '';
		}
		// fwrite($fp, rest
		fwrite($fp, "<ROW$level>\n");
		for ($i = $pos; $i < $ncols; $i++)
		{
			$name = strtoupper(mysql_field_name($result, $i));
			fwrite($fp, "<$name>");
			fwrite($fp, trim(htmlspecialchars($row[$i],$i)));
			fwrite($fp, "</$name>\n");
		}
		fwrite($fp, "</ROW$level>\n");
		// remember previous row
		$rowPrev = $row;
	}
	// close opened tags
	for ($level = $deep; $level >= 0; $level--)
	{
		if ($tagOpened[$level])
		{
			fwrite($fp, "</ROW$level>\n");
		}
	}
	fwrite($fp,"</xml_export>\n");
	//	fclose($fp);
}

function sql_execute($query)
{
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	$result=mysql_query($query);
	if(mysql_errno()<>0)
	{
		echo "<pre><b>A fatal MySQL error occured</b>.\n<br />Query: $query<br />\nError: (" .
		mysql_errno() . ") " . mysql_error();
	}
}