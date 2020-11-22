function sValSet(oname, val)
{
	var linkdiv = document.getElementById(oname);
	linkdiv.value=val;
}

function sAjax(autoid, inputid, selectid)
{
	var search = document.getElementById(inputid).value;
	if(search == null || search == "")
	{
		document.getElementById(autoid).style.visibility="hidden";
		return;
	}

	if(selectid > 0 && selectid < 4)
	{
		var type = selectid;
	}
	else
	{
		var type = document.getElementById(selectid).value;
	}
	

	var http = new XMLHttpRequest();

	var url = "/ajax/uget.php?uid=" + search + "&type=" + type;
	//alert(url);

	http.open("GET", url , true);

	http.onreadystatechange = function()
	{
		//alert("1" + "\n" + http.readyState + "\n" + http.status + "\n" + url);
		if(http.readyState == 4 && http.status == 200)
		{
			var response = http.responseText;
			response = JSON.parse(response);
			
			var linkdiv = document.getElementById(autoid);

			var i=0;
			var inStr = '<table id="acTable">';
			for(i=0; i<response.res.length; i++)
			{
				inStr = inStr + '<tr><td class="ac" onclick="sValSet(\'' + inputid+ '\', \'' + response.res[i] + '\'); sClean(\'' + autoid + '\')">' + response.res[i] + '</td></tr>';
			}
			inStr = inStr + '</table>';

			linkdiv.innerHTML = inStr;
			linkdiv.style.visibility="visible";
			//alert(response.res[0]);
		}
	};
	http.send(null);
}


function getElementsByClassName(className)
 {
  var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
  var allElements = document.getElementsByTagName("*");
  var results = [];

  var element;
  for (var i = 0; (element = allElements[i]) != null; i++)
  {
	   var elementClass = element.className;
	   if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
	   results.push(element);
  }

  return results;
 }

function gradeTableQuery(className, idQueryID)
{
	 var ids = getElementsByClassName(className);
	 var idStr = "";

	 for(var i=0; i<ids.length; i++)
	 {
	  if(i != 0)
	  {
	   idStr += "&";
	  }

	  idStr += 'actives[]=' + ids[i].checked;
	 }

	 var qID = document.getElementById(idQueryID);

	 return idStr + qID.value;
}

function wordwrap( str, width, brk, cut ) {
 
    brk = brk || '\n';
    width = width || 75;
    cut = cut || false;
 
    if (!str) { return str; }
 
    var regex = '.{1,' +width+ '}(\\s|$)' + (cut ? '|.{' +width+ '}|.+$' : '|\\S+?(\\s|$)');
 
    return str.match( RegExp(regex, 'g') ).join( brk );
 
}

var ReportID = 0;

function ShowResponseReport(idd,ResponseMsg,TitleOf,DateSe,Sender)
{
	//alert(idd);
	if(ReportID == idd)
	{
		document.getElementById("Responser").innerHTML="";
		document.getElementById("Responser").style.visibility='hidden';
		ReportID = 0;
	}
	else
	{
		var StringTable = '';
		StringTable +=	'<table border="1" class="LogsTable">';
		StringTable +=	'<tr>';
		StringTable +=	'<td>UserID(Name)</td>';
		StringTable +=	'<td>Response</td>';
		StringTable +=	'<td>Date</td>';
		StringTable +=	'</tr>';
		
		StringTable +=	'<tr>';
		StringTable +=	'<td>'+Sender+'</td>';
		StringTable +=	'<td>'+wordwrap(ResponseMsg, 50, '<br/>\n')+'</td>';
		StringTable +=	'<td>'+DateSe+'</td>';
		StringTable +=	'</tr>';
		StringTable +=	'</table>';
		
		document.getElementById("Responser").style.visibility='visible';
		document.getElementById("Responser").innerHTML="<br /><p style='font-size: 18px;color: blue;'>"+TitleOf+":</p> <br />"+StringTable;
		ReportID = idd;
	}
	//$('#'+idd).animate({visibility: "visible", opacity: 100}, 500, function() { alert("done"); });
	//document.getElementById("Responser").style.visibility='visible';
	
}

function Change(url, type, div)
{
	url = '/pages/getP.php?year='+url+'&type='+type;
	//alert("URL: "+url);
	//alert("TYPE: "+type);
	//alert("DIV: "+div);
	
	if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				document.getElementById(div).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET",url,true);
		xmlhttp.send();
}

function reason1(){
	var a = prompt("Please Enter The Reason" , "");

	if (a != "" && a != null)
	{
		var finala = "";
		var i = 0;

		for(i=0; i<a.length; i++)
		{
			finala = finala + "&#" + a.charCodeAt(i);
		}
		return a;
	}
	else
	{
		return false;
	}
}

function enterReason(num)
{
	var a = prompt("Please Enter The Reason" , "");

	if (a != "" && a != null)
	{
		var finala = "";
		var i = 0;

		for(i=0; i<a.length; i++)
		{
			finala = finala + "&#" + a.charCodeAt(i);
		}

		document.forms[num].reason.value = finala;
		return true;
	}
	else
	{
	  return false;
	}
}

function jQPrintRes(url, type1, type2, rs) {

	if(rs == 1) {
		$(function () {
			$("#reason_form_url").val(url);
			$("#reason_form_type1").val(type1);
			$("#reason_form_type2").val(type2);
			$("#reason_form_rs").val(rs);
			$("#reason_form").dialog("open");
		});
	}
	else
	{
		printResultsN(url, type1, type2, rs, null);
	}
}

function printResultsN(url, type1, type2, rs, reas)
{
	
	//type1 = Page;
	//type2 = form;
	var id = type2;
	var bool = true;
	if(rs == 1)
	{
		type2 = type2+"&reason="+reas;
	}
	
	if(bool)
	{
		url = "inclusion/act.php?"+url+"&type1="+type1+"&type2="+type2;
		//alert(url);
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				document.getElementById("results"+id).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET",url,true);
		xmlhttp.send();
	}
}

function printResults(url, type1, type2, rs)
{
	
	//type1 = Page;
	//type2 = form;
	var id = type2;
	var bool = true;
	if(rs == 1)
	{
		//alert("Needed Reason!");
		var check = reason1();
		if(!check)
		{
			//document.getElementById("results"+type2).innerHTML="Enter Reason!";
			bool = false
		}
		else
		{
			type2 = type2+"&reason="+check;
			//alert("asd");
			//alert(check);
		}
	}
	
	if(bool)
	{
		url = "inclusion/act.php?"+url+"&type1="+type1+"&type2="+type2;
		//alert(url);
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				document.getElementById("results"+id).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET",url,true);
		xmlhttp.send();
	}
}

function openpic(pic)
{
window.open(pic , "" , "width=1280 , height=1024");
}

function sClean(oname)
{
	var linkdiv = document.getElementById(oname);
	linkdiv.innerHTML="";
	linkdiv.style.visibility="hidden";
}