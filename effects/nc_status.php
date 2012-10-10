<?php
require_once('../conf/header.php');
	//
?>
<h1>Nutcracker Status Dashboard</h1>
<table border=1>
<tr>
<th>Subject</th>
<th>Status</th>
<th colspan=2>Effects</th>
<th>Bug Description</th>
</tr>
<?php
$menu_array=get_menu();
$effects_array=get_effects();
foreach($menu_array as $i=>$arr)
{
	echo "<tr>";
	$menu_name=$arr['menu_name'];
	echo "<td>" . $menu_name . "</td>";
	//
	//
	$bug_array=get_bugs($menu_name);
	$cnt=count($bug_array);
	$bug_text="";
	if($cnt==0)
	{
		$msg="No Known issues";
		$color="lightgreen";
	}
	else 
	{
		$msg = "$cnt bug(s)";
		foreach ($bug_array as $b=>$arr2)
		{
			$b1=$b+1;
			$bug_text = $bug_text . " $b1: " . $arr2['description'];
		}
		$color="FFAAAA";
	}
	if($menu_name=='Effects')
	{
		$color="#FFFFFF";
		$msg="See each effect";
	}
	echo "<td bgcolor=$color>$msg</td>";
	if($menu_name!='Effects')
		echo "<td colspan=2></td>";
	if($menu_name=='Effects')
	{
		echo "\n<td><table border=1>";
		foreach($effects_array as $j=>$row)
		{
			/*	echo "<pre>i=$i\n";
			print_r($row);
			echo "</pre>";*/
			$effect_class = $row['effect_class'];
			echo "<tr>";
			$color="#FFFFFF";
			//
			$bug_array=get_bugs($effect_class);
			$cnt=count($bug_array);
			$bug_text="";
			if($cnt==0)
			{
				$msg="No Known issues";
				$color="lightgreen";
			}
			else 
			{
				$msg = "$cnt bug(s)";
				foreach ($bug_array as $b=>$arr2)
				{
					$b1=$b+1;
					$bug_text = $bug_text . " $b1: " . $arr2['description'];
				}
				$color="FFAAAA";
			}
		
			echo "<td bgcolor=$color>" . $effect_class . "</td>";
			echo "<td>" . $row['description'] . "</td>";
			echo "<td>$bug_text</td>";
			echo "</tr>\n";
		}
		echo "</table></td>";
	}
	echo "<td>$bug_text</td>";
	echo "</tr>\n";
}
?>
</table>
</body>
</html>
<?php

function get_effects()
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
	//
	$query = "select * from  effects_hdr where active='Y' order by effect_class";
	//echo "<pre>get_number_segments: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . 
	$query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	$effects_array=array();
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$effects_array[]=$row;
	}
	return $effects_array;
}

function get_menu()
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
	//
	$query = "select * from  menu_hdr order by sequence";
	//echo "<pre>get_number_segments: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . 
	$query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	$menu_array=array();
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$menu_array[]=$row;
	}
	return $menu_array;
}

function get_bugs($menu_name)
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
	//
	$query = "select * from  issue_tracker WHERE
	menu_name='$menu_name'
	and status='Open' and type='bug'";
	//echo "<pre>get_number_segments: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . 
	$query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	$bug_array=array();
	while ($row = mysql_fetch_assoc($result))
	{
		extract($row);
		$bug_array[]=$row;
	}
	return $bug_array;
}
