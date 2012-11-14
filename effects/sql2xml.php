<?php
/**
* sql2xml prints structured XML
*
* @param string  $sql       - SQL statement
* @param string  $structure - XML hierarchy
*/
$sql = "select * from members";
/*create_csv($filename,"effects_user_hdr","where username='f'");
create_csv($filename,"effects_user_dtl","where username='f'");
create_csv($filename,"effects_user_segment","where username ='f'");
create_csv($filename,"project_dtl","where project_id in (select project_id from project where username='f')");
create_csv($filename,"project","where username='f'"); // NOTE! This must be after the above statement
create_csv($filename,"models_strands","where username ='f'");
create_csv($filename,"models_strand_segments","where username ='f'");*/

$filename="test.xml";
$fp=fopen($filename,"w");
fwrite($fp,"<?xml version='1.0' standalone='yes'?>\n<nutcracker>\n");

sql2xml($fp,"effects_user_hdr","select * from effects_user_hdr where username='f'");
sql2xml($fp,"effects_user_dtl","select * from effects_user_dtl where username='f'");
sql2xml($fp,"project_id","select * from project_dtl where project_id in (select project_id from project where username='f')");
sql2xml($fp,"project","select * from project where username='f'");
sql2xml($fp,"models_strands","select * from models_strands where username='f'");
sql2xml($fp,"models_strand_segments","select * from models_strand_segments where username='f'");
sql2xml($fp,"models","select * from models where username='f'");


//$fp=fopen($filename,"a");
fwrite($fp,"</nutcracker>\n");
fclose($fp);
//
//
display_xml("test.xml");
echo "</pre>\n";
function display_xml($filename)
{
	$resXml = simplexml_load_file($filename); //$requestUrl is where the xml file is located
	echo "<pre>";
print_r($resXml);

/*foreach ($resXml->readCalls->classify->classification->class as $d) {
    $currClassificationName = $d['className'];
    $currClassificationRating = (float) $d['p'];
    echo "$currClassificationName: $currClassificationRating" . "</br>";
}*/
echo "</pre>";
}

function sql2xml($fp,$table,$sql, $structure = 0)
{
	// init variables for row processing
	require_once('../conf/config.php');
	$row_current = $row_previous = null;
	// set MySQL username/password and connect to the database
	$db_cn = mysql_pconnect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_DATABASE, $db_cn);
	//$fp=fopen("text.xml","a");
	fwrite($fp,"<xml_export>\n");
	fwrite($fp,"<table>$table</table>\n");
	$result = mysql_query($sql, $db_cn);
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
?>