 <?php
$content = $_GET['content'];
$fieldname = $_GET['fieldname'];
echo stripslashes(strip_tags($_GET['content'],"<br><p><img><a><br /><strong><em>"));

$update1 = "INSERT INTO paceReport `$fieldname` VALUES $content";
//$result = mysql_query($update1)
//    or die;
echo $update1 . "<br />";
?> 