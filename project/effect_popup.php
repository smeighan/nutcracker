<?php
require('dbcontrol.php'); 
$project_id=$_GET['project_id'];
$effect_id=$_GET['effect_id'];
$sql="SELECT effect_name, effect_class from effects_user_hdr WHERE effect_id=".$effect_id;
$result=nc_query($sql);
$row= mysql_fetch_array($result, MYSQL_ASSOC);
$effect_name=$row['effect_name'];
$effect_class=$row['effect_class'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<header><title>Get Effect Details</title>
<script type="text/javascript" src="../effects/jscolor.js"></script>
<link href="../css/ncFormDefault.css" rel="stylesheet" type="text/css" />
</header>
<body>
<form action="project.php" method="post">
<input type="hidden" name="project_id" value="<?=$project_id?>"/>
<input type="hidden" name="effect_id" value="<?=$effect_id?>"/>
<input type="hidden" name="type" value="2"/>
<input type="submit" name="EffectEdit" value="Click to save effect values"  class="button" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="EffectEditCancel" value="Cancel">
<h2>Edit values for effect "<?=$effect_name?>" (<?=$effect_class?>)</h2>
<table class="Gallery">
<tr><th>Entry</th><th>Field</th><th>Description</th><th>Range</th></tr>
<?php
projEffectEdit($effect_id, $project_id);
function projEffectEdit($effect_id, $project_id) {
	$sql = "SELECT ed.param_name, ed.param_value, param_prompt, param_desc, param_range FROM `effects_user_hdr` AS e\nLEFT JOIN effects_user_dtl AS ed ON e.effect_id=ed.effect_id\nLEFT JOIN effects_dtl as ed2 ON e.effect_class=ed2.effect_class AND ed.param_name=ed2.param_name\nWHERE e.effect_id=".$effect_id;
	$result=nc_query($sql);
	$cnt=0;
	//echo "SQL = ".$sql. "<br />";
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		extract($row);
		if (($param_name!='frame_delay') && ($param_name!='effect_name') && ($param_name!='seq_duration') && ($param_name!='window_degrees')) {
			if ($cnt%2==0) 
				$trStr='<tr>';
			else
				$trStr='<tr class="alt">';
			//$mystring = $effect_details[$i]['param_name'];
			$findme="color";
			$pos = strpos($param_name, $findme);
			if ($pos === false)
			{
				$classStr=" class=\"input\" ";
			}
			else {
				$classStr=" class=\"color {hash:true} {pickerMode:'HSV'}\" ";
			}
			$fieldstr=$trStr.'<td><input type="text" name="'.$param_name.'" '.$classStr.' value="'.$param_value.'"></td><td>'.$param_prompt.'</td><td>'.$param_desc.'</td><td>'.$param_range.'</td></tr>'."\n";
			echo $fieldstr;
			$cnt++;
		} 
	}	 
}	
?>

</table>
<input type="submit" name="EffectEdit" value="Click to save effect values"  class="button" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="EffectEditCancel" value="Cancel">
</form>
</body>
</html>