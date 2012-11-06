<?php
require_once("../effects/read_file.php");

function handleCopy($myArr)
{
	$mySelArray=$myArr['copyeffect'];
	if (count($mySelArray)==0)
		echo "No effects selected for copy!<br />";
	else
	{
		$member_id=$_SESSION['SESS_MEMBER_ID'];
		$newusername=$_SESSION['SESS_LOGIN'];
		//print_r($myArr);  // Array ( [0] => f~BARS1 [1] => f~BARS1 [2] => f~BARS2 )
			//print_r($mySelArray);
		//die;
		foreach($mySelArray as $usereffect)
		{
			$tok=preg_split("/~+/", trim($usereffect));
			$username=$tok[0];
			$effname=$tok[1];
			/*echo "<pre>usereffect=$usereffect\n";
			print_r($tok);
			echo "</pre>\n";*/
			$neweffname=strtoupper($myArr[$usereffect]); // make sure all effects are upper case <scm>
			if (strlen(trim($neweffname))==0)
				echo "Sorry, didn't copy ".$effname. " because you did not give it a new name<br />";
			else {
				echo "Copying " . $effname . " to " . $neweffname . "<br />";
				getEffCopySQL($username, $effname, $newusername, $neweffname);
			}
		}
	}
}

function getEffCopySQL($username, $effname, $myusername, $newname)
{
	$sql="SELECT * FROM effects_user_hdr WHERE username='".$username."' AND effect_name='".$effname."'";
	$result=nc_query($sql);
	while ($row=mysql_fetch_assoc($result))
	{
		extract($row);
		$sql="REPLACE INTO effects_user_hdr (effect_class, effect_name, username, effect_desc, created, last_upd) VALUES ('".$effect_class."','".$newname."'";
		$sql.=",'".$myusername."','".$effect_desc."',NOW(), NOW() );";
		$result2=nc_query($sql);
	}
	$sql="SELECT * FROM effects_user_dtl WHERE username='".$username."' AND effect_name='".$effname."';";
	$result3=nc_query($sql);
	while ($row=mysql_fetch_assoc($result3))
	{
		extract($row);
		$effect_id = get_effect_id($myusername,$newname);
		if ($param_name=="effect_name")
			$param_value=$newname;
		$sql="REPLACE INTO effects_user_dtl (effect_id,effect_name, username, param_name, param_value, segment, created, last_upd) VALUES ('".$effect_id."','" .$newname."'" ;
		$sql.=",'".$myusername."','".$param_name."','".$param_value."',".$segment.", NOW(), NOW() );";
		$result4=nc_query($sql);
	}
	$sql="SELECT * FROM effects_user_segment WHERE username='".$username."' AND effect_name='".$effname."'";
	$result2=nc_query($sql);
	while ($row=mysql_fetch_assoc($result2))
	{
		extract ($row);
		$sql="REPLACE INTO effects_user_segment (effect_name, username, param_name, param_value, segment, created, last_upd) VALUES ('" .$newname."'" ;
		$sql.=",'".$myusername."','".$param_name."','".$param_value."',".$segment.", NOW(), NOW() );";
		$result5=nc_query($sql);
	}
}
?>