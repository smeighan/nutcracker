<?php
require_once('../conf/auth.php');
require_once('../conf/barmenu.php');
require_once ("gallery_submit.php");
require_once ("dbcontrol.php");
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
	myForm.action="gallery_page.php";
	myForm.CopyEffect.Value="XXXXX";
	myForm.onSubmit="";
	myForm.method="POST";
	myForm.submit();
	location.refresh();
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
			var cnt=0;
			var oldvarstring="XXXXXX";
			var str="<table class=Gallery><tr><th>Effect Details</th><th>Image</th><th>Effect Details</th><th>Image</th><th>Effect Details</th><th>Image</th><th>Effect Details</th><th>Image</th></tr>";
			for(i=0;i<myObject.data.length;i++)
			{
				varstring = myObject.data[i].username + "~" + myObject.data[i].effname;
				if (oldvarstring != varstring) 
				{
					if(cnt%8==0)
					{
						str = str + "</tr>"
					} else { 
						if(cnt%4==0)
						{
						trstr="</tr><tr class=alt>" 
						} else { 
						trstr="" 
						}
					}

						//alert(varstring);
						str = str + trstr + "<td>Class&nbsp;&nbsp;: " + myObject.data[i].effclass;
						str = str + "<br />User&nbsp;&nbsp;&nbsp;: " + myObject.data[i].username ;
						str = str + "<br />Member ID: " + myObject.data[i].member_id;
						str = str +  "<br />Effect&nbsp;: " + myObject.data[i].effname + "<br />";
						str = str + "<br /><input type=\"checkbox\" name=\"copyeffect[]\" class\"GalleryFormField\" value=\""+ varstring + "\"> Use effect";
						str = str + "<br /><input type=\"text\" name=\"" + varstring + "\" id=\"" + varstring + "\" class=\"GalleryFormField\">"; 
						str = str + "<br />Effect Name";
						str = str + "</td>";
						//	<scm> start
						/*$fpath2 =  "/nutcracker/effects/"  . myObject.data[i].fullpath;
						if(!file_exists($fpath)) $fpath2 = "/nutcracker/images/noThumb.gif";*/
						//	<scm> end
						str = str + "<td><img src=\"/nutcracker/effects/" + myObject.data[i].fullpath +"\"  height=\150\" width=\"75\"></td>";
						//str = str + "<td><img src=\"" . $fpath ."\"  height=\"100\" width=\"50\"></td>";
					cnt++;
				}
				oldvarstring=varstring;
			}
			var endrecord=myObject.value.endrecord 
			str = str + "<tr><td colspan=6>" + myObject.value.nume + " records found</td></tr>"
			myForm.st.value=endrecord;
			if(myObject.value.end =="yes")
			{
				document.getElementById("fwd").style.display='inline';
			}
			else{document.getElementById("fwd").style.display='none';
			}
			if(myObject.value.startrecord =="yes")
			{
				document.getElementById("back").style.display='inline';
			}
			else{document.getElementById("back").style.display='none';
			}
			str = str + "</table>" 
			document.getElementById("txtHint").innerHTML=str;
		}
	}
	var url="gallery_page_check.php";
	var myendrecord=myForm.st.value;
	var mylimit=myForm.mylimit.value;
	var mysort=myForm.mysort.value;
	var myfilter=myForm.myfilter.value;
	var filterusername=myForm.filterusername.value;
	var filtereffect=myForm.filtereffect.value;
	url=url+"?endrecord="+myendrecord;
	url=url+"&direction="+val;
	url=url+"&mylimit="+mylimit;
	url=url+"&mysort="+mysort;
	url=url+"&myfilter="+myfilter;
	url=url+"&filterusername="+filterusername;
	url=url+"&filtereffect="+filtereffect;
	url=url+"&sid="+Math.random();
	httpxml.onreadystatechange=stateChanged;
	httpxml.open("GET",url,true);
	httpxml.send(null);
	document.getElementById("txtHint").innerHTML="Please Wait....";
}
	</script>
	</head>
	<body onLoad="ajaxFunction('fw')";>
	<?php show_barmenu(); 
	$msg_str="";
	if (isset($_POST))
	{
		extract($_POST);
		if (isset($CopyEffect))
		{
			//require_once("../project/project_filer.php");
			//print_r($_POST);
			handleCopy($_POST);
			$msg_str="Effects Copied";
		}
	}
	echo $msg_str."<br />";
	?>
	<h2>Gallery View</h2>
	<form name="myForm" onSubmit="ajaxFunction(this.form); return false">
	<input type=hidden name=st value=0>
	<input type="hidden" name="CopyEffect" value="XXXX">
	<table>
	<tr><td># Recs to Show : <select id="mylimit" onChange="ajaxFunction('bk')">
	<option value="10">10</option>
	<option value="50">50</option>
	<option value="100">100</option>
	<option value="200">200</option>
	<option value="500">500</option>
	<option value="1000">1000</option>
	</select></td>
	<td>Sort by : <select id="mysort" onChange="ajaxFunction('bk')">
	<option value="1">user,effect</option>
	<option value="2">type, user, effect</option>
	<option value="3">effect</option>
	</select></td></tr>
	<tr><td>Filter by : <select id="myfilter" onChange="ajaxFunction('bk')">
	<option value="all">all</option>
	<?php show_current_effects(); ?>
	</select></td>
	</tr>
	<tr><td>Username <input type="text" id="filterusername" onChange="ajaxFunction('bk')"></td></tr>
	<tr><td>Effect <input type="text" id="filtereffect" onChange="ajaxFunction('bk')"></td></tr>
	<tr>
	<td><input type=button id="back" class="SubmitButton" value=Prev onClick="ajaxFunction('bk'); return false"></td>
	<td align=right><input type=button class="SubmitButton" value=Next id="fwd" onClick="ajaxFunction('fw');  return false"></td></tr>
	<tr>
	</tr>
	<tr><td colspan=2><div id="txtHint"><b>Records will be displayed here</b></div></td></tr>
	</table>
	<input type="button" class="SubmitButton" value="Copy Checked Effect(s)" onClick="copyEffects();">
	</form>
	</body>"
	</html>
	<?php
	
	function show_current_effects()
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
		$query = "select * from  effects_hdr where active='Y'
		order by effect_class";
		//echo "<pre>get_number_segments: query=$query</pre>\n";
		$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . 
		$query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
		//
		while ($row = mysql_fetch_assoc($result))
		{
			extract($row);
			printf("<option value=\"%s\">%s - %s</option>",$effect_class,$effect_class,$description);
		}
		return ;
	}

