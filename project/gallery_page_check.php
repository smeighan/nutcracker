<?Php
require "conn.php"; // connection details

//////////
/////////////////////////////////////////////////////////////////////////////
$endrecord=$_GET['endrecord'];// 
if(strlen($endrecord) > 0 and !is_numeric($endrecord)){
echo "Data Error";
exit;
} 
if (isset($_GET['mylimit'])) {
	//echo "GOT HERE<br />";
	$limit=$_GET['mylimit'];
}
else
	$limit=100; // Number of records per page

if (isset($_GET['mysort'])) 
	$mysort=$_GET['mysort'];
else
	$mysort=1;
if (isset($_GET['myfilter']))
	$myfilter=$_GET['myfilter'];
else
	$myfilter="all";

//$myfilter="bars";

if ($myfilter=="all")	
		$wherestr= " 1=1 ";
else
		$wherestr= " g.effect_class='".$myfilter."' ";

if (isset($_GET['filterusername'])) {
	$fuser=$_GET['filterusername'];
	if (strlen($fuser) >0)
		$wherestr=$wherestr." AND g.username like '".$fuser."%' ";
}

if (isset($_GET['filtereffect'])) {
	$feffect=$_GET['filtereffect'];
	if (strlen($feffect) > 0)
		$wherestr=$wherestr." AND g.effect_name like '".$feffect."%' ";
}
	
$count=$dbo->prepare("select g.username from gallery as g where ".$wherestr);
$count->execute();
$nume=$count->rowCount(); // Total number of records

if($endrecord < $limit) {$endrecord = 0;}

switch($_GET['direction'])   // Let us know forward or backward button is pressed
{
case "fw":
$eu = $endrecord ;
break;

case "bk":
$eu = $endrecord - 2*$limit;
break;

default:
echo "Data Error";
exit;
break;
}

switch($mysort) 
{
	case 1 :
		$sortstr= " g.username, g.effect_name ";
		break;
	case 2 : 
		$sortstr= " g.effect_class, g.username, g.effect_name ";
		break;
	default :
		$sortstr = " g.effect_name ";
}

if($eu < 0){$eu=0;}
$endrecord =$eu+$limit;

//$sql="select id,name,class as myclass,mark from student limit $eu,$limit"; 
$sql="SELECT g.effect_class AS effclass, g.username, g.effect_name AS effname, g.created, g.fullpath, g.member_id, ed.param_value as gifname FROM gallery as g LEFT JOIN effects_user_dtl as ed ON g.username=ed.username AND g.effect_name=ed.effect_name AND ed.param_name='file1' WHERE ".$wherestr." AND g.effect_name <> '' ORDER BY ".$sortstr." LIMIT $eu, $limit";
//echo "alert($sql);";
//$fh=fopen("sqlOut.txt",'w');
//fwrite($fh,$sql);
//fclose($fh);
$row=$dbo->prepare($sql);
$row->execute();
$result=$row->fetchAll(PDO::FETCH_ASSOC);


if(($endrecord) < $nume ){$end="yes";}
else{$end="no";}

if(($endrecord) > $limit ){$startrecord="yes";}
else{$startrecord="no";}

$main = array('data'=>$result,'value'=>array("endrecord"=>"$endrecord","limit"=>"$limit","end"=>"$end","startrecord"=>"$startrecord","nume"=>"$nume"));
echo json_encode($main); 



////////////End of script /////////////////////////////////////////////////////////////////////////////////




?>