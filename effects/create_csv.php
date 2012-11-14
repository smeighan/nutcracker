<?php
$filename = "test.csv";
$fp=fopen($filename,"w");
fwrite($fp,"-- This import file: test.csv\n");
fclose($fp);
create_csv($filename,"effects_user_hdr","where username='f'");
create_csv($filename,"effects_user_dtl","where username='f'");
create_csv($filename,"effects_user_segment","where username ='f'");

create_csv($filename,"project_dtl","where project_id in (select project_id from project where username='f')");
create_csv($filename,"project","where username='f'"); // NOTE! This must be after the above statement
create_csv($filename,"models_strands","where username ='f'");
create_csv($filename,"models_strand_segments","where username ='f'");
///
/// show what we wrote in the export file
///
$fh=fopen($filename,"r");
echo "<pre>";
while (!feof($fh))
{
	$line = fgets($fh);
	echo "$line";
}
echo "</pre>";
fclose($fh);

function create_csv($filename,$table,$where)
{
	require_once('../conf/config.php');
	$select = "Select * from $table " . $where;
	$delete = "Delete from $table " . $where;
	mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_DATABASE);
	$fp = fopen($filename, "a");
	fwrite ($fp,"\n\n-- $table\n--\n");
	// SELECT * FROM $table
	$line = "";
	$comma = "";
	$buff = "$delete" . ";\n\n";
	fwrite ($fp,$buff);
	
	$res = mysql_query($select);
	$rows=mysql_num_rows($res);
	echo "<pre>-- query=$select has $rows rows</pre>\n";
	// fetch a row and write the column names out to the file
	$row = mysql_fetch_assoc($res);
	if($rows>0 )
	{
	fwrite ($fp,"INSERT into $table (");
		foreach($row as $name => $value)
		{
			$line .= $comma . '`' . str_replace('"', '""', $name) . '`';
			$comma = ",";
		}
		$line .= "\n";
		fputs($fp, $line);
		fwrite ($fp,") values ");
		// remove the result pointer back to the start
		mysql_data_seek($res, 0);
		// and loop through the actual data
		$l=0;
		while($row = mysql_fetch_assoc($res))
		{
			$l++;
			$line = "";
			$comma = "";
			fwrite ($fp,"(");
			foreach($row as $value)
			{
				$line .= $comma .  $value ;
				$comma = "|";
			}
			if($l==mysql_num_rows($res))
				$line .= ");\n";
			else
			$line .= "),\n";
			fputs($fp, $line);
		}
		fclose($fp);
	}
}
