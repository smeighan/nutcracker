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
*/
require_once('../conf/header_no_auth.php');
//
require("../effects/read_file.php");
set_time_limit(60*60);
extract($_GET);

if(!isset($sort)) $sort="song";
//echo "<pre>sort=$sort</pre>";
$username=$_SESSION['SESS_LOGIN'];
$member_id=$_SESSION['SESS_MEMBER_ID'];
if(isset($_GET['target'])===FALSE or $_GET['target']==NULL)
{
	$target=get_votes_by_user($username);
	//$target=array();
}
else
{
	$target=$_GET['target'];
	update_votes($username,$target);
	insert_audit($username,"Xmas Songs Update");
	}
//print_r($target);
//echo "</pre>\n";
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];
$file = "Wizards_In_Winter.mo";
$dir = 'music_object_files'; 
$votes_summary=vote_stats();
$users=$votes_summary['users'];
$cnt=$votes_summary['cnt'];
echo "<h1>Christmas Songs.</h1><h2> $users Users have cast $cnt votes.</h2>";
echo "<h4>Instructions: Click in the column labeled 'Using It?' for every song you plan on using this year";
echo "<br/>Click submit and your vote will be tallied. Look under the 'Votes' column to see the current tally";
echo "<br/>across all users.</h4>";
echo "<br/><br/>";
$self=$_SERVER['PHP_SELF'];
echo "<form action=\"$self\" method=\"GET\">\n";
echo '<input type="submit" name="submit" value="Submit Form to have your choices recorded" />';
echo "<table border=1>";
show_songs($target,$sort);
echo "</table>";
echo '<input type="submit" name="submit" value="Submit Form to have your choices recorded" />';
echo '</form>';

function show_songs($target_array,$sort)
{
	//Include database connection details
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
	/*DROP TABLE IF EXISTS fubar
	CREATE TEMPORARY TABLE EXISTS fubar SELECT id, name FROM barfu
	With pure SQL those are your two real classes of solutions. I like the second better.
	*/
	$query="DROP TABLE IF EXISTS  music_object_votes_count";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$query="create table  music_object_votes_count select music_object_id, count(*) cnt 
	from music_object_votes group by music_object_id
	order by 1";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	if($sort=="artist")
	{
		$query ="SELECT a.*,b.cnt FROM music_object_hdr a
		left join music_object_votes_count b
		on a.music_object_id = b.music_object_id
		where a.username='f'
		order by artist";
	}
	else if($sort=="votes")
	{
		$query ="SELECT a.*,b.cnt  FROM music_object_hdr a
		left join music_object_votes_count b
		on a.music_object_id = b.music_object_id
		where a.username='f'
		order by b.cnt desc";
	}
	else if($sort=="songid")
	{
		$query ="SELECT a.*,b.cnt  FROM music_object_hdr a
		left join music_object_votes_count b
		on a.music_object_id = b.music_object_id
		where a.username='f'
		order by a.music_object_id";
	}
	else if($sort=="comment")
	{
		$query ="SELECT a.*,b.cnt  FROM music_object_hdr a
		left join music_object_votes_count b
		on a.music_object_id = b.music_object_id
		where a.username='f'
		order by a.desc3";
	}
	else
	{
		$query ="SELECT a.*,b.cnt  FROM music_object_hdr a
		left join music_object_votes_count b
		on a.music_object_id = b.music_object_id
		where a.username='f'
		order by song_name";
	}
	//echo "<pre>query=$query</pre>\n";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	$param_value="";
	$line=0;
	if(!$NO_DATA_FOUND)
	{
		$votes_summary=vote_stats();
		$self=$_SERVER['PHP_SELF'];
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		echo "<tr>";
		echo "<th colspan=2></th>";
		echo "<th colspan=5 bgcolor=lightblue>Click on These Column Headings to sort<br/>Current sort order is by $sort</th>";
		echo "<th colspan=1></th>";
		echo "</tr><tr>";
		echo "<th>Row#</th>";
		echo "<th>Using<br/>It?</th>";
		echo "<th><a href=$self?sort=songid>SongId</a></th>";
		echo "<th><a href=$self?sort=song>Song Name</a></th>";
		$users=$votes_summary['users'];
		$cnt=$votes_summary['cnt'];
		//echo "<th><a href=$self?sort=votes>Votes:<br/>$users users<br/>Have Cast <br/>$cnt votes</a></th>";
		echo "<th><a href=$self?sort=votes>Votes</a></th>";
		echo "<th><a href=$self?sort=artist>Artist</a></th>";
		echo "<th><a href=$self?sort=comment>Comment</a></th>";
		echo "<th>Song URL</th>";
		echo "</tr>";
		//$votes_array=get_votes();
		/*	echo "<pre>";
		print_r($votes_array);
		echo "</pre>\n";
		*/
		$c=count($target_array);
		while ($row = mysql_fetch_assoc($result))
		{
			$line++;
			extract($row);
			$votes=$cnt;
			/*if(isset($votes_array[$music_object_id])===FALSE or $votes_array[$music_object_id]==NULL)
				$votes=0;
			else
			$votes = $votes_array[$music_object_id];*/
			$color="#FFFFFF";
			if($votes>0) $color="#BEFFA6";
			$checked='';
			for($i=0;$i<$c;$i++)
			{
				if(intval($target_array[$i])==intval($music_object_id)) $checked='checked=checked';
			}
			printf ("<tr><td>$line</td><td><input type=\"checkbox\" name=\"target[]\" value=\"%s\" %s >
			<th>M-%s</th>  
			<td align=\"left\">%s</td>
			<td bgcolor=\"%s\">%s</td>
			<td align=\"left\">%s</td>
			<td align=\"left\">%s</td>
			<td align=\"left\"><a href=%s</a>%s</td>
			</tr>\n",$music_object_id,$checked,$music_object_id,$song_name,$color,$votes,$artist,htmlentities($desc3),htmlentities($song_url),htmlentities($song_url));
		}
	}
}

function get_votes()
{
	//Include database connection details
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
	$query ="SELECT music_object_id,count(*) as cnt  FROM `music_object_votes`
	group by music_object_id
	order by music_object_id";
	//echo "<pre>$query</pre>";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	$param_value="";
	$line=0;
	if(!$NO_DATA_FOUND)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$line++;
			extract($row);
			$votes_array[$music_object_id]=$cnt;
		}
	}
	return $votes_array;
}

function get_votes_by_user($username)
{
	//Include database connection details
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
	$query ="SELECT * FROM `music_object_votes`
	where username = '$username'";
	//echo "<pre>$query</pre>";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	$param_value="";
	$line=0;
	if(!$NO_DATA_FOUND)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$line++;
			extract($row);
			$target_array[]=$music_object_id;
		}
	}
	return $target_array;
}

function update_votes($username,$target)
{
	//Include database connection details
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
	//  first empty all of their votes
	$query = "delete from  music_object_votes where username='$username'";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	//
	// now, insert their choices
	foreach($target as $music_object_id)
	{
		$insert ="REPLACE INTO music_object_votes (username,music_object_id,date_created,date_updated)
			values ('$username',$music_object_id,now(),now())";
		//echo "<pre>$query</pre>";
		$result = mysql_query($insert) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $insert . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	}
}

function vote_stats()
{
	//Include database connection details
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
	$query ="select  count(distinct username) as users, count(*) as cnt
	from music_object_votes";
	//echo "<pre>$query</pre>";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$NO_DATA_FOUND=0;
	if (mysql_num_rows($result) == 0)
	{
		$NO_DATA_FOUND=1;
	}
	$param_value="";
	$line=0;
	if(!$NO_DATA_FOUND)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			extract($row);
		}
	}
	$votes_summary=array('users'=>$users, 'cnt'=>$cnt);
	return $votes_summary;
}

function insert_audit($username,$OBJECT_NAME)
{
	//Include database connection details
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
	$date_field= date('Y-m-d');
	$time_field= date("H:i:s");
	$query="INSERT into audit_log values ('$username','$date_field','$time_field','effect','$OBJECT_NAME')";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}
