<?php
require_once('../conf/auth.php');
require_once('../conf/barmenu.php');

?>

<!doctype html public "-//w3c//dtd html 3.2//en">
<html>
<head>
<link rel="stylesheet" type="text/css" href="../css/barmenu.css">
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
<link href="../css/ncFormDefault.css" rel="stylesheet" type="text/css" />
<title>Nutcracker - Gallery Browser</title>
<script language="javascript" src="../js/json2.js"></script>
<script type="text/javascript">

function filterChange()
{
	myObject.value.endrecord=0;
	ajaXFunction('fw');
}

function copyEffects()
{
	myForm.action="project.php";
	myForm.CopyEffect.Value="XXXXX";
	myForm.onSubmit="";
	myForm.method="POST";
	myForm.submit();
}

function ajaxFunction(val)
{

var httpxml;
try
  {
  // Firefox, Opera 8.0+, Safari
  httpxml=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    httpxml=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    try
      {
      httpxml=new ActiveXObject("Microsoft.XMLHTTP");
      }
    catch (e)
      {
      alert("Your browser does not support AJAX!");
      return false;
      }
    }
  }
function stateChanged() 
    {
    if(httpxml.readyState==4)
      {
var myObject = JSON.parse(httpxml.responseText);
var trstr="";
var varstring="";
var str="<table class=Gallery><tr><th>Effect Details</th><th>Image</th><th>Effect Details</th><th>Image</th><th>Effect Details</th><th>Image</th><th>Effect Details</th><th>Image</th></tr>";
for(i=0;i<myObject.data.length;i++)
{ 
	if(i%8==0) { str = str + "</tr>"
	} else { 
		if(i%4==0) { trstr="</tr><tr class=alt>" } else { trstr="" }
	}
	varstring = myObject.data[i].username + "~" + myObject.data[i].effname;
	str = str + trstr + "<td>Class&nbsp;&nbsp;: " + myObject.data[i].effclass;
	str = str + "<br />User&nbsp;&nbsp;&nbsp;: " + myObject.data[i].username ;
	str = str +  "<br />Effect&nbsp;: " + myObject.data[i].effname + "<br />";
	str = str + "<br /><input type=\"checkbox\" name=\"copyeffect[]\" class\"GalleryFormField\" value=\""+ varstring + "\"> Use effect";
	str = str + "<br /><input type=\"text\" name=\"" + varstring + "\" id=\"" + varstring + "\" class=\"GalleryFormField\">"; 
	str = str + "<br />Your Name";
	str = str + "</td>";
	str = str + "<td><img src=\"/nutcracker/effects/" + myObject.data[i].fullpath +"\"  height=\"100\" width=\"50\"></td>";

}

var endrecord=myObject.value.endrecord 
str = str + "<tr><td colspan=6>" + myObject.value.nume + " records found</td></tr>"

myForm.st.value=endrecord;
if(myObject.value.end =="yes"){ document.getElementById("fwd").style.display='inline';
}else{document.getElementById("fwd").style.display='none';}


if(myObject.value.startrecord =="yes"){ document.getElementById("back").style.display='inline';
}else{document.getElementById("back").style.display='none';}


str = str + "</table>" 
document.getElementById("txtHint").innerHTML=str;
    }
    }

var url="gallery_page_check.php";
var myendrecord=myForm.st.value;
var mylimit=myForm.mylimit.value;
var mysort=myForm.mysort.value;
var myfilter=myForm.myfilter.value;
url=url+"?endrecord="+myendrecord;
url=url+"&direction="+val;
url=url+"&mylimit="+mylimit;
url=url+"&mysort="+mysort;
url=url+"&myfilter="+myfilter;
url=url+"&sid="+Math.random();
httpxml.onreadystatechange=stateChanged;
httpxml.open("GET",url,true);
httpxml.send(null);
 document.getElementById("txtHint").innerHTML="Please Wait....";
}
</script>


</head>

<body onLoad="ajaxFunction('fw')";>

<?php show_barmenu(); ?>
<h2>Gallery View</h2>
<form name="myForm" onSubmit="ajaxFunction(this.form); return false">
<input type=hidden name=st value=0>
<input type="hidden" name="CopyEffect" value="XXXX">
<table>
<tr>
	<td class="normalText"># Recs to Show : <select id="mylimit" onChange="ajaxFunction('bk')">
		<option value="10">10</option>
		<option value="50">50</option>
		<option value="100">100</option>
		<option value="200">200</option>
		<option value="500">500</option>
		<option value="1000">1000</option>
	</select></td>
	<td class="normalText">Sort by : <select id="mysort" onChange="ajaxFunction('bk')">
		<option value="1">user,effect</option>
		<option value="2">type, user, effect</option>
		<option value="3">effect</option>
	</select></td>
	<td class="normalText">Filter by : <select id="myfilter" onChange="ajaxFunction('bk')">
		<option value="all">all</option>
		<option value="bars">bars - Horizontal Bars</option>
		<option value="butterfly">butterfly - Butterfly Wing</option>
		<option value="color_wash">color_wash - Color wash between start and end color</option>
		<option value="fire">fire - Burning Fire</option>
		<option value="garlands">garlands - Falling circles on your rgb device</option>
		<option value="gif">gif - Display animated gif's on your RGB device</option>
		<option value="layer">layer - Allows to effect files to be merged</option>
		<option value="life">life - Game of Life</option>
		<option value="meteors">meteors - Falling meteors on your RGB device</option>
		<option value="pictures">pictures - JPG, PNG or GIF image projected on your RGB device</option>
		<option value="snowflakes">snowflakes - Snowflakes on your RGB device</option>
		<option value="snowstorm">snowstorm - Snow blowing in a storm</option>
		<option value="spirals">spirals - Create spirals around your RGB device</option>
		<option value="text">text - Scrolling text around your RGB device</option>
		<option value="user_defined">user_defined - This effect class is for user defined functions</option>
	</select></td>
</tr>
<tr>
	<td><input type=button id="back" class="SubmitButton" value=Prev onClick="ajaxFunction('bk'); return false"></td>
	<td align=right><input type=button class="SubmitButton" value=Next id="fwd" onClick="ajaxFunction('fw');  return false"></td></tr>
<tr>
</tr>
<tr><td colspan=2><div id="txtHint"><b>Records will be displayed here</b></div></td></tr>
</table>
<input type="button" class="SubmitButton" value="Copy Checked Effect(s)" onClick="copyEffects();">
</form>
</body>
</html>
