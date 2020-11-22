var M_aid = 0;
var M_userid = "";
var M_ugradeid = 0;

var chatupd = new Array();
var openTabs = new Array();
var lastMsg = new Array();

function aCount(arr)
{
	var r = 0;
	for(var i in arr)
	{
		r++;
	}

	return r;
}

function closeAllTabs()
{
	for(var i in openTabs)
	{
		cDelete(openTabs[i]);
	}
}

function HideAllTabs()
{
	for(var i in openTabs)
	{
		cBoxHide(openTabs[i]);
	}
}

function SendWEnter(event, aid)
{
	//alert("CALLED");
	
	if(event == 13)
	{
		chatSend(aid);
		return false;
	}
	return true;
}

function checkChatUpdates(times)
{
	var http = new XMLHttpRequest();
	var url = "http://panel.qualitygunz.net/ajax/chat.php?action=5&userinfo=" + M_aid;

	http.open("GET", url , true);
	http.onreadystatechange = function()
	{
		if(http.readyState == 4 && http.status == 200)
		{
			var response = http.responseText;
			//alert(response);
	
			response = JSON.parse(response);
			if(response.errors != "none")
			{
				alert("Chat Update Check Thread Error:\n\n" + response.errors);
				return;
			}
	
			for(var i in response.LastRec)
			{
				if(lastMsg['' + response.LastRec[i][1] + ''] != response.LastRec[i][3])
				{
					lastMsg['' + response.LastRec[i][1] + ''] = response.LastRec[i][3];
					if(times != 0)
					{
						var found = false;
						for(var a in openTabs)
						{
							if(openTabs[a] == response.LastRec[i][1])
							{
								found = true;
							}
						}
						if(found == false)
						{
							openTab(response.LastRec[i][1], response.LastRec[i][2], 0);
						}
					}
				}
			}
		}
	}

	http.send(null);
}

function onlineThreading(aid, userid, ugradeid, times)
{
	//alert(aid + "\n" + userid + "\n" + ugradeid);

	M_aid = aid;
	M_userid = userid;
	M_ugradeid = ugradeid;

	checkChatUpdates(aid, userid, ugradeid, times);
	setTimeout("onlineThreading(" + aid + ", '" + userid + "', " + ugradeid +", " + ++times + ")", 10000);

	var http = new XMLHttpRequest();
	var url = "http://panel.qualitygunz.net/ajax/chat.php?action=0&userinfo=" + aid + " || " + userid + " || " + ugradeid;

	http.open("GET", url , true);
	http.onreadystatechange = function()
	{
		if(http.readyState == 4 && http.status == 200)
		{
			var response = http.responseText;
			//alert(response);

			response = JSON.parse(response);
			if(response.errors != "none")
			{
				alert("Chat Online Thread Error:\n\n" + response.errors);
				return;
			}
		}
	}

	http.send(null);
}

function openChatList(aid)
{
	//alert('called');
	closeAllTabs();

	var http = new XMLHttpRequest();
	var url = "http://panel.qualitygunz.net/ajax/chat.php?action=4&userinfo=" + M_aid;

	http.open("GET", url , true);
	http.onreadystatechange = function()
	{
		if(http.readyState == 4 && http.status == 200)
		{
			var response = http.responseText;
			//alert(response);

			response = JSON.parse(response);
			if(response.errors != "none")
			{
				alert("Chat Online Thread Error:\n\n" + response.errors);
				return;
			}

			var cont = "<table id=\"chat_list_table\">";
			for(var i in response.Users)
			{
				cont += "<tr class=\"cl\" onclick=\"openTab('" + response.Users[i][0] + "', '" + response.Users[i][1] + "', '" + response.Users[i][2] + "');\">";
				cont += "<td>" + response.Users[i][1] + "</td>";
				cont += "</tr>";
			}
			cont += "</table>";
			//alert(cont);

			var clist = document.getElementById("chat_list");
			clist.style.visibility = "visible";
			clist.innerHTML = cont;
		}
	}

	http.send(null);
}

function chatUpdate(aid)
{
	//alert('called chatupdate');

	var http = new XMLHttpRequest();
	var url = "http://panel.qualitygunz.net/ajax/chat.php?action=2&userinfo=" + M_aid + "&sendinfo=" + aid;
	
	var oldcont = document.getElementById("chat_box_cont_" + aid).innerHTML;

	http.open("GET", url , true);
	http.onreadystatechange = function()
	{
		if(http.readyState == 4 && http.status == 200)
		{
			var response = http.responseText;
			//alert(response);

			response = JSON.parse(response);
			if(response.errors != "none")
			{
				alert("Chat Online Thread Error:\n\n" + response.errors);
				return;
			}

			var MsgText = "";
			for(var i in response.Messages)
			{
				if(response.Messages[i][0] == 0)
				{
					MsgText += "<div style=\"padding-bottom:4px;\">" + response.Messages[i][2] + ":<br /> " + response.Messages[i][3] + "</div>";
				}
 				else
				{
					MsgText += "<div style=\"padding-bottom:4px;\">" + response.Messages[i][2] + ":<br /> " + response.Messages[i][3] + "</div>";
				}
			}

			if(oldcont != MsgText)
			{
				document.getElementById("chat_box_cont_" + aid).innerHTML = MsgText;
				document.getElementById("chat_box_cont_" + aid).scrollTop = document.getElementById("chat_box_cont_" + aid).scrollHeight;
			}
		}
	}
	http.send(null);
}

function chatUpdateManager(aid)
{
	chatUpdate(aid);
	chatupd['' + aid + ''] = setTimeout("chatUpdateManager(" + aid + ")", 5000);
}

function openTab(aid, name, ugrade)
{
	if(document.getElementById("chat_div_" + aid) != null)
	{
		document.getElementById("chat_list").style.visibility = "hidden";
		return;
	}
	//alert("a");

	var cont = "";
	cont += '<div class="chat_div" id="chat_div_' + aid + '">';
	cont += '<div class="chat_bar">';
	cont += '<div id="chat_bar_open">';
	cont += '<div class="exit_t2" onclick="cDelete(\'' + aid + '\')">X</div>';
	cont += '<div id="chat_bar_text" onclick="cBoxOpen(\'' + aid + '\')">';
	cont += name;
	cont += '</span>';
	cont += '</div>';
	cont += '</div>';

	cont += '<div class="chat_box" id="chat_box_' + aid + '">';
	cont += '<div class="chat_box_top">';
	cont += '<span class="exit_t" onclick="cBoxHide(\'' + aid + '\')">X</span>';
	cont += name;
	cont += '</div>';

	cont += '<div class="chat_box_cont" id="chat_box_cont_' + aid + '">';
	cont += '</div>';

	cont += '<div class="chat_box_message">';
	cont += '<input type="text" value="Message.." class="chat_message" id="chat_message_' + aid + '" onkeypress="return SendWEnter(event.keyCode, \'' + aid + '\')" onfocus="if(this.value == \'Message..\'){this.value=\'\';}" onblur="if(this.value == \'\'){this.value=\'Message..\';}">';
	cont += '<input type="submit" value="Send" class="chat_send" onclick="chatSend(\'' + aid + '\');">';
	cont += '</div>';
	cont += '</div>';
	cont += '</div>';

	//alert("b");

	var cbars = document.getElementById("chat_bars");
	//alert(cbars.innerHTML);
	cbars.innerHTML += cont;

	document.getElementById("chat_box_" + aid).style.visibility = "visible";
	document.getElementById("chat_list").style.visibility = "hidden";

	openTabs[aCount(openTabs)] = aid;

	chatUpdateManager(aid);
}

function chatSend(aid)
{
	//alert('called chatsend');

	var http = new XMLHttpRequest();
	var message = document.getElementById("chat_message_" + aid).value;
	document.getElementById("chat_message_" + aid).value="";

	var url = "http://panel.qualitygunz.net/ajax/chat.php?action=1&userinfo=" + M_aid + " || " + M_userid + " || " + M_ugradeid + "&sendinfo=" + aid + " || " + message;

	http.open("GET", url , false);
	http.send(null);

	if(http.readyState == 4 && http.status == 200)
	{
		var response = http.responseText;
		//alert(response);

		response = JSON.parse(response);
		if(response.errors != "none")
		{
			alert("Chat Online Thread Error:\n\n" + response.errors);
			return;
		}

		chatUpdate(aid);
	}
	
}

function cBoxHide(aid)
{
	document.getElementById("chat_box_" + aid).style.visibility = "hidden";
}

function cBoxOpen(aid)
{
	HideAllTabs();
	document.getElementById("chat_box_" + aid).style.visibility = "visible";
}

function cDelete(aid)
{
	var mainob = document.getElementById("chat_bars");
	var delob = document.getElementById("chat_div_" + aid);

	mainob.removeChild(delob);
	clearTimeout(chatupd["" + aid + ""]);

	var found = false;
	for(var i in openTabs)
	{
		if(found == true)
		{
			openTabs[i-1] = openTabs[i];
			openTabs[i] = null;
		}
		if(openTabs[i] == aid)
		{
			found = true;
		}
	}
}