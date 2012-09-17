<?php
extract($_GET);
echo "<pre>";
print_r($_GET);
$maxString=0;
$maxPixel=0;
$fh=fopen($file,"r");
while (!feof($fh))
{
	$line = fgets($fh);
	$tok=preg_split("/ +/", $line);
	$cnt=count($tok);
	$l=strlen($line);
	$c= substr($line,0,1);
	if($tok[0]=='S' and $tok[2]=='P')
	{
		$s=$tok[1];	// 0 device name
		$p=$tok[3];// 2 pixel#	
		$rgb_array[$s][$p]=$cnt;
		if($s>$maxString) $maxString=$s;
		if($p>$maxPixel) $maxPixel=$p;
		if($s==1 and $p==1) $FirstCnt=$cnt;
	}
	//echo "$line,cnt=$cnt, $string,$pixel\n";
}
echo "<table border=1>";
echo "<tr><th></th>";
echo "<th colspan=\"$maxPixel\">PIXEL#</th>";
echo "</tr>";
echo "<tr><th></th>";
for($p=1;$p<=$maxPixel;$p++)
{
	printf("<th>%d</th>",$p);
}
echo "</tr>";
for($s=1;$s<=$maxString;$s++)
{
	printf ( "<tr><td>S %d</td>",$s);
	for($p=1;$p<=$maxPixel;$p++)
	{
		$cnt=$rgb_array[$s][$p];
		if($cnt==$FirstCnt) $color="lightgreen";
		else $color="pink";
		printf("<td bgcolor=\"$color\">%d</td>",$cnt);
	}
	echo "</tr>";
}
echo "</table>";
?>