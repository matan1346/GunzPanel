var _online_timeout_handle = null;
var _chat_timeout_handle = null;
var _users_timeout_handle = null;
var _unread_timeout_handle = null;

var _chat_last_msg_request = 0;
var _chat_update_progress = false;

var _open_wysiwyg = false;

var _mouse_x;
var _mouse_y;
var _sub_menu_msg_id;

var _UGradeID=0;

function isHeb(str) {
    if(!str)
	return false;

    for (var i = 0, n = str.length; i < n; i++) {
        if (str.charCodeAt( i ) > 255) { return true; }
    }
    return false;
}


function onlineThread(user)
{
	var http = new XMLHttpRequest();
	url = "ajax/chat/online_thread.php?user="+user;
	http.open("GET", url , true);
	
	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status == 200)
		{
			_online_timeout_handle = setTimeout("onlineThread('"+user+"');", 10000);
		}
	}
	
	http.send(null);

	//alert(http.responseText);
}

function countUnread()
{
	var http = new XMLHttpRequest();
	url = "ajax/chat/get_msg_count.php";
	http.open("GET", url , true);
	
	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status == 200)
		{
			if(http.responseText != "false")
			{
				var json = JSON.parse(http.responseText);
				if(json.Count > 0)
				{
					$("#chat_count").html("("+json.Count+")");
				}
				else
				{
					$("#chat_count").html("");
				}
			}
			else
			{
				$("#chat_count").html("");
			}
			_unread_timeout_handle = setTimeout("countUnread();", 10000);
		}
	}
	
	http.send(null);
}

function postToChat(msg, sub)
{
	var txt;
	if(_open_wysiwyg)
	{
		txt = tinyMCE.get('chat_input_msg').getContent({format : 'bbcode'});
	}
	else
	{
		txt = msg.value;
	}

	if(txt.length < 2 || txt == "Loading..")
	{
		return;
	}

	var ob = msg;
	msg = txt;
	
	ob.value = "Loading..";
	ob.disabled = true;
	sub.disabled = true;

	var http = new XMLHttpRequest();
	url = "ajax/chat/send_msg.php";
	params = "msg="+escape(msg);

	http.open("POST", url, true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status == 200) {
			//alert(http.responseText);
			resetMsgTimeout();
			ob.value = "";
			ob.disabled = false;
			sub.disabled = false;
		}
	}

	http.send(params);
}

function loadMsg(lastMsg)
{
	var startLoad = lastMsg;

	if(_chat_update_progress)
		return null;

	var http = new XMLHttpRequest();
	url = "ajax/chat/get_msg.php?time="+lastMsg;
	http.open("GET", url , true);

	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status == 200)
		{
			if(http.responseText != "false")
			{
				if(http.responseText[0] != '[')
				{
					alert("Chat Error:\r\n" + http.responseText);
					return null;
				}

				var json = JSON.parse(http.responseText);
				for(var i=0; i<json.length; i++)
				{
					//alert(json[i].ID);
					var container = document.createElement('div');
					container.setAttribute('class', 'msg_holder');
					container.setAttribute('id', 'msg_'+json[i].ID);
					
					if(_UGradeID == 256)
						container.setAttribute('ondblclick', 'openSubMenu('+json[i].ID+');');
					
					var title = document.createElement('div');
					title.setAttribute('class', 'msg_title');
	
					var sender = document.createElement('div');
					sender.setAttribute('class', 'msg_sender');
					sender.innerHTML = json[i].User+":";
	
					var time_e = document.createElement('div');
					time_e.setAttribute('class', 'msg_time');
					time_e.innerHTML = json[i].Time;

					if(!json[i].Msg)
						json[i].Msg = "";
					json[i].Msg = unescape(json[i].Msg);


					var align = "left";
					if(isHeb(json[i].Msg[0]))
					{
						align = "right";
					}

					var content = document.createElement('div');
					content.setAttribute('class', "msg_content_" + align + "");

					var msgSpan = document.createElement('span');
					msgSpan.innerHTML = json[i].Msg;
	
	
					content.appendChild(msgSpan);
					title.appendChild(sender);
					title.appendChild(time_e);
					container.appendChild(title);
					container.appendChild(content);
	
					document.getElementById("chat_box").appendChild(container);
	
					lastMsg = json[i].ID;
				}

				if(startLoad != 0)
					scrollDown();
				else
					startDown();
			}
			_chat_last_msg_request = lastMsg;
			_chat_timeout_handle  = setTimeout("loadMsg("+lastMsg+");", 2000);
			_chat_update_progress = false;
		}
	}
	_chat_update_progress = true;
	http.send(null);
}

function loadOnline()
{
	var http = new XMLHttpRequest();
	url = "ajax/chat/get_online.php";
	http.open("GET", url , true);

	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status == 200)
		{
			if(http.responseText != "false")
			{
				var json = JSON.parse(http.responseText);

				var userList = document.getElementById("online_users");
                
                //Remove All of accounts that are not on the list			
				while(userList.firstChild)
				{
                    			var flag = true;
                   			for(var i=0; i<json.length && flag; i++)
				    	{
				        	if(userList.firstChild.id == "chat_user_online_" + json[i] + "")
                        			{
                            				flag == false;
                            				break;
                        			}
                    			}
                    
                    			if(flag)
                    			{
					    userList.removeChild(userList.firstChild);
                    			}
				}

				for(var i=0; i<json.length; i++)
				{
				    if(!document.getElementById("chat_user_online_" + json[i] + "") || typeof(document.getElementById("chat_user_online_" + json[i] + "")) == 'undefined')
                    		    {
    					var user = document.createElement('div');
    					user.setAttribute('class', 'chat_user_online');
    					user.setAttribute('id', "chat_user_online_" + json[i] + "");
    					user.innerHTML = json[i];
                        
    					userList.appendChild(user);
                    		    }	
				}
			}
			_users_timeout_handle = setTimeout("loadOnline();", 5000);
		}
	}

	http.send(null);
}

function openAdvancedEdit()
{
	_open_wysiwyg = !_open_wysiwyg;
	var chat_msg = document.getElementById("chat_msg");
	var chat_box = document.getElementById("chat_input_msg");
	var chat_sub = document.getElementById("chat_input_submit");

	if(!chat_box)
	{
		alert("chat_input_msg was not found");
		return false;
	}

	if(!chat_msg)
	{
		alert("chat_msg was not found");
		return false;
	}	

	if(!chat_sub)
	{
		alert("chat_input_submit was not found");
		return false;
	}

	if(!_open_wysiwyg && tinyMCE.getInstanceById('chat_input_msg'))
	{
		tinyMCE.execCommand('mceFocus', false, 'chat_input_msg');                    
		tinyMCE.execCommand('mceRemoveControl', false, 'chat_input_msg');
	}

	chat_msg.removeChild(chat_sub);
	chat_msg.removeChild(chat_box);
	var new_box;

	if(_open_wysiwyg)
	{
		new_box = document.createElement("textarea");
		new_box.setAttribute("class", "wysiwyg");
		
	}
	else
	{
		new_box = document.createElement("input");
		new_box.setAttribute("class", "input");
	}

	new_box.setAttribute("id", "chat_input_msg");
	chat_msg.appendChild(new_box);

	if(_open_wysiwyg)
	{
		tinyMCE.execCommand('mceAddControl', false, 'chat_input_msg');
	}

	chat_msg.appendChild(chat_sub);
}


function resetMsgTimeout()
{
	clearTimeout(_chat_timeout_handle);
	loadMsg(_chat_last_msg_request);
}

function scrollDown()
{
	var bottomPos = document.getElementById("chat_box").scrollHeight;
	$("#chat_box").animate({scrollTop: bottomPos}, 1000);

	//document.getElementById("chat_box").scrollTop = document.getElementById("chat_box").scrollHeight;
}

function startDown()
{
	document.getElementById("chat_box").scrollTop = document.getElementById("chat_box").scrollHeight;
}

function openSubMenu(msg)
{
	_sub_menu_msg_id = msg;
	var sub_menu = document.getElementById("sub_menu");
	if(!sub_menu)
	{
		sub_menu = document.createElement("div");
		sub_menu.setAttribute("id", "sub_menu");

		var sub_table = document.createElement("table");
		sub_table.setAttribute("id", "sub_table");

		var sub_rows = new Array();
		var sub_cols = new Array();

		sub_rows[0] = document.createElement("tr");
		sub_cols[0] = document.createElement("td");
		sub_cols[0].innerHTML = "Close";
		sub_cols[0].setAttribute("onclick", "closeSubMenu();");

		sub_rows[1] = document.createElement("tr");
		sub_cols[1] = document.createElement("td");
		sub_cols[1].innerHTML = "Delete";
		sub_cols[1].setAttribute("onclick", "deleteMsg();closeSubMenu();");

		sub_rows[2] = document.createElement("tr");
		sub_cols[2] = document.createElement("td");
		sub_cols[2].innerHTML = "Modify";
		sub_cols[2].setAttribute("onclick", "modifyMsg();closeSubMenu();");

		//sub_rows[0].appendChild(sub_cols[0]);
		//sub_table.appendChild(sub_rows[0]);

		for(var i=0; i<sub_rows.length; i++)
		{
			sub_rows[i].appendChild(sub_cols[i]);
			sub_table.appendChild(sub_rows[i]);
		}

		sub_menu.appendChild(sub_table);
		document.body.appendChild(sub_menu);
	}

	sub_menu.style.left = _mouse_x;
	sub_menu.style.top = _mouse_y;
	

	//alert("called "+msg+"\r\n"+_mouse_x+", "+_mouse_y);
}

function closeSubMenu()
{
	var sub_menu = document.getElementById("sub_menu");
	if(sub_menu)
	{
		document.body.removeChild(sub_menu);
	}
}

function deleteMsg()
{
	var http = new XMLHttpRequest();
	url = "ajax/chat/del_msg.php?msg="+_sub_menu_msg_id;
	http.open("GET", url , true);

	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status == 200)
		{
			if(http.responseText != "OK")
			{
				alert(http.responseText);
			}
		}
	}
	http.send();

	var msg = document.getElementById("msg_"+_sub_menu_msg_id);
	var container = document.getElementById("chat_box");
	if(!msg)
	{
		return false;
	}

	container.removeChild(msg);
}

function modifyMsg()
{
	var http = new XMLHttpRequest();

	url = "ajax/chat/mod_msg.php?msg="+_sub_menu_msg_id;

	var new_msg = prompt("New Msg", "");
	if(!new_msg || new_msg == "")
	{
		return false;
	}
	var params = "newMsg="+encodeURI(new_msg);

	http.open("POST", url , true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.setRequestHeader("Content-length", params.length);
	http.setRequestHeader("Connection", "close");

	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status == 200)
		{
			if(http.responseText != "OK")
			{
				alert(http.responseText);
			}
			else
			{
				resetMsgTimeout();
				var container = document.getElementById("chat_box");
				while(container.firstChild)
				{
					container.removeChild(container.firstChild);
				}

				_chat_update_progress = false;
				loadMsg(0);
			}
		}
	}
	http.send(params);
}

//Set the button to be pressed on enter
$(function() {
	$("#chat_input_msg").keypress(function (e) {
		if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
			$('.submit').click();
			return false;
		} else {
			return true;
		}
	});
});


//Mouse pos
document.onmousemove = getCursorXY;
document.captureEvents(Event.MOUSEMOVE);
function getCursorXY(e) {
	_mouse_x = (window.Event) ? e.pageX : event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
	_mouse_y = (window.Event) ? e.pageY : event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
}