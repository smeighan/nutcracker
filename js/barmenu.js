<!--
var timeout         = 500;
var closetimer		= 0;
var ddmenuitem      = 0;

function NewURL(url,intype,id) {
	var myStr = document.forms[0].elements[2].value;
	var myName = document.forms[0].elements[2].name;
	var newurl = url + '?type='+intype+'&id='+id+'&frame_delay='+myStr;
	//alert(newurl);
	document.location.href=newurl;
}

// open hidden layer
function mopen(id)
{	
	// cancel close timer
	mcancelclosetime();

	// close old layer
	if(ddmenuitem) ddmenuitem.style.visibility = 'hidden';

	// get new layer and show it
	ddmenuitem = document.getElementById(id);
	ddmenuitem.style.visibility = 'visible';

}
// close showed layer
function mclose()
{
	if(ddmenuitem) ddmenuitem.style.visibility = 'hidden';
}

// go close timer
function mclosetime()
{
	closetimer = window.setTimeout(mclose, timeout);
}

// cancel close timer
function mcancelclosetime()
{
	if(closetimer)
	{
		window.clearTimeout(closetimer);
		closetimer = null;
	}
}

// close layer when click-out
document.onclick = mclose; 
// -->
