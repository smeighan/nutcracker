<?php
function handleCopy($myArr) 
{
	$mySelArray=$myArr['copyeffect'];
	if (count($mySelArray)==0)
		echo "No effects selected for copy!<br />";
	else
	{
		$member_id=$_SESSION['SESS_MEMBER_ID'];
		$newusername=$_SESSION['SESS_LOGIN'];
		foreach($mySelArray as $usereffect) 
		{
			$tok=preg_split("/~+/", trim($usereffect));
			$username=$tok[0];
			$effname=$tok[1];
			$neweffname=$myArr[$usereffect];
			echo "Copying " . $effname . " to " . $neweffname . "<br />";
			getEffCopySQL($username, $effname, $newusername, $neweffname);
		}
	}
}
function getEffCopySQL($username, $effname, $myusername, $newname) 
{
	$sql="SELECT * FROM effects_user_hdr WHERE username='".$username."' AND effect_name='".$effname."'";
	$result=nc_query($sql);
	while ($row=mysql_fetch_assoc($result)) {
		extract($row);
		$sql="REPLACE INTO effects_user_hdr (effect_class, effect_name, username, effect_desc, created, last_upd) VALUES ('".$effect_class."','".$newname."'";
		$sql.=",'".$myusername."','".$effect_desc."',NOW(), NOW() );";
		$result2=nc_query($sql);
	}
	$sql="SELECT * FROM effects_user_dtl WHERE username='".$username."' AND effect_name='".$effname."';";
	$result3=nc_query($sql);
	while ($row=mysql_fetch_assoc($result3)) {
		extract($row);
		$sql="REPLACE INTO effects_user_dtl (effect_name, username, param_name, param_value, segment, created, last_upd) VALUES ('".$newname."'";
		$sql.=",'".$myusername."','".$param_name."','".$param_value."',".$segment.", NOW(), NOW() );";
		$result4=nc_query($sql);
	}
}
?>