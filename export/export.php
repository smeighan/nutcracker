<?php
require_once('../conf/auth.php');
require_once('../conf/barmenu.php');
require_once ("../project/dbcontrol.php");
$member_id=$_SESSION['SESS_MEMBER_ID'];
$username=$_SESSION['SESS_LOGIN'];

function getEffectArrayFromDB($insql) {
	$result=nc_query($insql);
	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
		$name=$row['effect_name'];
		$class=$row['effect_class'];
		$arrname=$row['param_name'];
		$arrval=$row['param_value'];
		$retArray["name"]=$name;
		$retArray["class"]=$class;
		$currEffs[$arrname]=$arrval;
	}
	while ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
		$arrname=$row['param_name'];
		$arrval=$row['param_value'];
		$currEffs[$arrname]=$arrval;
	}
	$retArray["effects"]=$currEffs;
	return($retArray);
}


?>

<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>JavaScript Toolbox - Option Transfer - Move Select Box Options Back And Forth</title>
<script type="text/javascript" src="../js/barmenu.js"></script>
<script language="JavaScript" src="./OptionTransfer.js"></script>
<script language="JavaScript">
var opt = new OptionTransfer("list1","list2");
opt.setAutoSort(true);
opt.setDelimiter(",");
opt.setStaticOptionRegex("");
opt.saveRemovedLeftOptions("removedLeft");
opt.saveRemovedRightOptions("removedRight");
opt.saveAddedLeftOptions("addedLeft");
opt.saveAddedRightOptions("addedRight");
opt.saveNewLeftOptions("newLeft");
opt.saveNewRightOptions ("newRight");
</script>
<link rel="stylesheet" type="text/css" href="../css/barmenu.css">
<link href="../css/ncFormDefault.css" rel="stylesheet" type="text/css" />
</head>
<body onload="opt.init(document.forms[0])" alink="#00615F" bgcolor="#FFFFFF" link="#00615F" vlink="#00615F">

<?php
$msgstr="";
//print_r($_POST);
if (isset($_POST['exportlist'])) {
	$msgstr = "Exporting Effects<br />";
	$line=$_POST['exportlist'];
	$tok=preg_split("/\|+/", trim($line));
	foreach ($tok as $effectid) {
		$sql = "SELECT ed.param_name, ed.param_value, eh.effect_class, eh.effect_name "
			. "FROM effects_user_dtl AS ed "
			. "LEFT JOIN effects_user_hdr AS eh ON ed.effect_id=eh.effect_id "
			. "WHERE ed.effect_id = ".$effectid." ORDER BY ed.param_name";
		$myArray = getEffectArrayFromDB($sql);
		print_r($myArray);
		echo "<br />";
	}
}
if (isset($_POST['cmdCancelExport'])) {
	$msgstr = "Export Cancelled<br />";
} 
?>
<h2><?=$msgstr?></h2>
<form action="export.php" method="post">
<input type="hidden" name="exportlist" value="">
<table border="0">
</table><table border="0">
<tbody>
<tr><th>Effects to select</th><th>&nbsp;</th><th>Effect to export</th></tr>
<tr>
	<td>
	<select name="list1" multiple="multiple" size="10" ondblclick="opt.transferRight()">
<?php
	$sql = "SELECT `effect_id`, `effect_class`, `username`, `effect_name`, `effect_desc` FROM `effects_user_hdr` WHERE username='".$username."' ORDER BY effect_class, effect_name";
	echo $sql . "<br />";
	$result = nc_query($sql);
	$cnt=0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$val=$row['effect_id'];
		$valname=$row['effect_name']." (".$row['effect_class'].")";
		echo '		<option value="'.$val.'">'.$valname.'</option>'."\n";
	}
?>
	</select>
	</td>
	<td align="center" valign="middle">
		<input name="right" value="&gt;&gt;" onclick="opt.transferRight()" type="button"><br><br>
		<input name="right" value="All &gt;&gt;" onclick="opt.transferAllRight()" type="button"><br><br>
		<input name="left" value="&lt;&lt;" onclick="opt.transferLeft()" type="button"><br><br>
		<input name="left" value="All &lt;&lt;" onclick="opt.transferAllLeft()" type="button">
	</td>
	<td>
	<select name="list2" multiple="multiple" size="10" ondblclick="opt.transferLeft()">	
	</select>
	</td>
</tr>
</tbody></table>
<input type="submit" name="cmdCancelExport" value="Cancel"><input type="button" name="cmdExportEffects" value="Export Effects" onclick="opt.displaylist2()"></form>
</tbody></table>
</body></html>

<? 

?>