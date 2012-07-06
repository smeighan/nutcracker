<?php

function objectsIntoArray($arrObjData, $arrSkipIndices = array())
{
	$arrData = array();
	// if input is object, convert into array
	if (is_object($arrObjData))
	{
		$arrObjData = get_object_vars($arrObjData);
	}
	if (is_array($arrObjData))
	{
		foreach ($arrObjData as $index => $value)
		{
			if (is_object($value) || is_array($value))
			{
				$value = objectsIntoArray($value, $arrSkipIndices); // recursive call
			}
			if (in_array($index, $arrSkipIndices))
			{
				continue;
			}
			$arrData[$index] = $value;
		}
	}
	return $arrData;
}
//	make sure we are only running this on a local machine
if($_SERVER['HTTP_HOST'] !='localhost')
{
	echo"<pre><h1>ERROR! You cannot run this script on any place except for your local computer<br/>";
	echo "<pre>Here are the values in your _SERVER array\n";
	print_r($_SERVER);
	echo "</pre>";
	die ("\n\nProgram exiting .. ");
}

function load_xml_file($xmlUrl)
{
	require_once('nutcracker/conf/config.php');
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
	//$xmlUrl = "seqbuilder5.xml"; // XML feed file/URL
	$xmlStr = file_get_contents($xmlUrl);
	$xmlObj = simplexml_load_string($xmlStr);
	$arrXml = objectsIntoArray($xmlObj);
	echo "<pre>";
	foreach($arrXml as $table => $arr)
	{
		echo "\n\nTable: $table\n";
		$c=count($arr);
		if($table <> 'comment')
		{
			for($i=0;$i<$c;$i++)
			{
				echo "$i ";
				$cols = $arr[$i];
				//print_r($cols);
				$keys=array_keys($cols);
				$values=array_values($cols);
				$query = "replace into $table (";
				$keyc = count($keys)-1;
				foreach ($keys as $j=>$val)
				{
					$query .= sprintf ("%s",$val);
					if($j<$keyc) $query .= sprintf(",");
				}
				$query .= sprintf (") values (");
				foreach ($values as $j=>$val)
				{
					if(is_string($val))
						$val_clean = addslashes($val);
					else
					$val_clean=$val;
					$query .= sprintf ("'%s'",$val_clean);
					if($j<$keyc) $query .= sprintf(",");
				}
				$query .= sprintf (")");
				echo "$query\n";
				$result=mysql_query($query) or die ("Error on $query");
			}
		}
	}
	echo "</pre>\n";
}
?>