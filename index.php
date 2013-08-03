<?php 
include_once("settings.php");
if(isset($_COOKIE["todopwd"])){
$p=$_COOKIE["todopwd"];
if($p!=$PWD)
header("Location: login.php");
}
else{
header("Location: login.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" " http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php if(isset($TITLE))echo($TITLE);else echo("PHP ToDo List");?></title>
<link rel="stylesheet" type="text/css" href="style.css" />

</head>

<body>
<div class="container">
<img src="working.png" width=1px height=1px></img>
<img src="tick.png" width=1px height=1px></img>
<img src="stop.png" width=1px height=1px></img>
<img src="pause.png" width=1px height=1px></img>
<img src="emergence.png" width=1px height=1px></img>
<div class="header">
<h1><?php if(isset($TITLE))echo($TITLE);else echo("PHP ToDo List");?></h1>
<form id="frm" onsubmit="AddToDo();return false;"><input type="text"
	name="txttodo" id="txttodo"></input>
<input type="submit" id="btnadd" value="发布" alt="发布"></input><!-- &nbsp;<a href="config.php">设置</a>-->
<p style="margin-left:0;margin-right:20px;">Filter：<span style="cursor:hand" id="filter"></span><a href="javascript:DeleteShown()">Delete all displayed items below</a></p>
</form>
</div>
<div class="content" id="content">
<div class="wait" id="wait">Wait...</div>

<div class="table" id="tb">

</div>
</div>
<div class="footer">
	<p>Developed by Anran.</p>
</div>
</div>
<script type="text/javascript" language="javascript">
var data=new Array();
var img=new Array(<?php echo($FILTERS);?>);
var syncing=false;
var filters=new Array();
var filterdata=0;
var sorted=1;
window.onload = function(){
    if(getCookie("todofilter")==null)
    {
		filterdata=Math.pow(2,img.length)-1;
		setCookie("todofilter",filterdata,24*365*20);
	}
    else
    	filterdata=parseInt(getCookie("todofilter"));
	xmlhttpPost("data.php", "", updatepage);
	for (i = 0; i < img.length; i++){ 
		filters[i] = (filterdata%2==1);
		filterdata=Math.floor(filterdata/2);
	}
	var dom = document.getElementById("filter");
	dom.innerHTML = "";
	for (i = 0; i < img.length; i++) {
		dom.innerHTML += '<span style="background-color:'+(filters[i]?'#ff8204':'white')+';color:'+(filters[i]?'white':'#555')+'" onclick="javascript:filter(' + i + ',this)">' + img[i].text + '</span>&nbsp;';
	}
}
function getCookie( name ) { 
    var start = document.cookie.indexOf( name + "=" ); 
var len = start + name.length + 1; 
    if ( ( !start ) && ( name != document.cookie.substring( 0, name.length ) ) ) { 
        return null; 
    } 
if ( start == -1 ) return null; 
    var end = document.cookie.indexOf( ';', len ); 
    if ( end == -1 ) end = document.cookie.length; 
    return unescape( document.cookie.substring( len, end ) ); 
} 
 
function setCookie( name, value, expires, path, domain, secure ) { 
    var today = new Date(); 
    today.setTime( today.getTime() ); 
    if ( expires ) { 
        expires = expires * 1000 * 60 * 60 * 24; 
    } 
    var expires_date = new Date( today.getTime() + (expires) ); 
    document.cookie = name+'='+escape( value ) + 
        ( ( expires ) ? ';expires='+expires_date.toGMTString() : '' ) + //expires.toGMTString() 
        ( ( path ) ? ';path=' + path : '' ) + 
        ( ( domain ) ? ';domain=' + domain : '' ) + 
        ( ( secure ) ? ';secure' : '' ); 
} 
function filter(st,dom)
{
	if (st!=null) {
		filters[st] = !filters[st];
		dom.style.color = filters[st] ? "white" : "#555";
		dom.style.backgroundColor = filters[st] ? "#ff8204" : "white";
		filterdata=0;
		for(i=0;i<filters.length;i++)
			filterdata+=Math.pow(2,i)*(filters[i]?1:0);
		setCookie("todofilter",filterdata,24*365*20);
	}
	for (i = 0; i < data.length; i++) {
		if (filters[data[i].status]) 
			document.getElementById("tr" + i).style.display = "table-row";
		else 
			document.getElementById("tr" + i).style.display = "none";
	}
}
function xmlhttpPost(strURL,data,onreceived) { 
	var xmlHttpReq = false; 
	var self = this; 
	if (window.XMLHttpRequest) {  
	    self.xmlHttpReq = new XMLHttpRequest(); 
	} 
	else if (window.ActiveXObject) { 
	    self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP"); 
	} 
	self.xmlHttpReq.open('POST', strURL, true);//Post方式请求数据 
	self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); 
	self.xmlHttpReq.onreadystatechange = function() { 
	if (self.xmlHttpReq.readyState == 3){
		document.getElementById("wait").innerHTML="正在解析数据...";
	}
	if (self.xmlHttpReq.readyState == 4&&self.xmlHttpReq.status==200) {//已经收到数据 
	    if(onreceived)onreceived(self.xmlHttpReq.responseText); 
		document.getElementById("wait").style.display="none";
		syncing=false;
	}
	} 
	document.getElementById("wait").style.display="block";
	document.getElementById("wait").innerHTML="正在请求数据...";
	syncing=true;
	self.xmlHttpReq.send(data); 
	} 
function getquerystring() { 
	var form = document.forms['frm']; 
	var word = form.txttodo.value;
	qstr = 'txttodo=' + escape(word); 
	qstr+="&date="+new Date().getTime();
	return qstr; 
} 
function updatepage(str){

	var html = "";
	var strstr;
	if(str=="ok")str=null;
	if (str != null) {
		strstr = str.split("{N}");
		if (str=="") 
			strstr.length--;
	}
	else 
		strstr = {
			length: data.length
		};
	html+='<tr><th width="40px"><a href="javascript:if(!syncing)sort(1)">状态</a></th><th width="40px"><a href="javascript:if(!syncing)sort(2)">紧急!</a></th><th><span>计划</span></th><th width="150px"><a href="javascript:if(!syncing)sort(0)">时间</a></th><th></th></tr>'
	for (i = 0; i < strstr.length; i++) {
		if (str != null) {
			var ss = strstr[i].split("|||");
			data[i] = {
				time: new Date(parseInt(ss[0])),
				emergence: parseInt(ss[2]) == 1,
				status: parseInt(ss[1]),
				text: ss[3]
			};
		}
		html += '<tr id="tr'+i+'"><td width="40px"><a href="';
		html += 'javascript:if(!syncing)changestatus(' + i + ')';
		html += '"><img src="' + img[data[i].status].file + '" width="32px" height="32px"';
		html += ' alt="' + img[data[i].status].text + '"/></a></td><td width="40px">';
		html+='<a href="javascript:if(!syncing)changeemergence('+i+')"><img src="'+(data[i].emergence?"emergence.png":"notemergence.png")+'" width="32px" height="32px"/></a></td><td>';
		if(data[i].emergence)html+='<b style="color:red;">';
		html += unescape(data[i].text);
		if(data[i].emergence)html+="</b>";
		html+='</td><td width="150px" style="color:#555;text-align:right">';
		str2="";
		var t=new Date().getTime()/1000;
		var t2=data[i].time.getTime()/1000;
		var dt=t-t2;
		if (dt / 3600 / 24 > 1) {
			str2 += Math.floor(dt / 3600 / 24) + " day ";
			dt %= 3600 * 24;
		}
		if(dt/3600>1)
		{
			str2 += Math.floor(dt / 3600) + " hr ";
			dt %= 3600;
		}
		if(dt/60>1)
		{
			str2 += Math.floor(dt / 60) + " min ";
			dt %= 3600;
		}
		if(str2=="")str2="seconds ";
		str2+="ago";
		html+=str2+'</td><td width="30px"><span><a href="javascript:if(!syncing)DeleteToDo('+i+')"><img src="cross.jpg" width="24px" height="24px"/></a></span></td></tr>'
		
	}
	document.getElementById("tb").innerHTML = '<table cellspacing="0" cellpadding="7px" width="100%" id="tb">'+html+'</table>';
	filter();
}
function sort(b)
{
	if (b==0) {
		if (sorted == 1) {
			for (i = 0; i < data.length; i++) 
				for (j = i + 1; j < data.length; j++) 
					if (data[i].time.getTime() > data[j].time.getTime()) {
						var dt = data[i];
						data[i] = data[j];
						data[j] = dt;
					}
		}
		else 
			for (i = 0; i < data.length; i++) 
				for (j = i + 1; j < data.length; j++) 
					if (data[i].time.getTime() < data[j].time.getTime()) {
						var dt = data[i];
						data[i] = data[j];
						data[j] = dt;
					}
	}
	if (b==1) {
		if (sorted == 2) {
			for (i = 0; i < data.length; i++) 
				for (j = i + 1; j < data.length; j++) 
					if (data[i].status < data[j].status) {
						var dt = data[i];
						data[i] = data[j];
						data[j] = dt;
					}
		}
		else 
			for (i = 0; i < data.length; i++) 
				for (j = i + 1; j < data.length; j++) 
					if (data[i].status > data[j].status) {
						var dt = data[i];
						data[i] = data[j];
						data[j] = dt;
					}
	}
	if (b==2) {
		if (sorted == 3) {
			for (i = 0; i < data.length; i++) 
				for (j = i + 1; j < data.length; j++) 
					if (data[i].emergence==false&&data[j].emergence==true) {
						var dt = data[i];
						data[i] = data[j];
						data[j] = dt;
					}
		}
		else 
			for (i = 0; i < data.length; i++) 
				for (j = i + 1; j < data.length; j++) 
					if (data[i].emergence==true&& data[j].emergence==false) {
						var dt = data[i];
						data[i] = data[j];
						data[j] = dt;
					}
	}
	if (b==0) {
		if (sorted == 1) 
			sorted = 0;
		else 
			sorted = 1;
	}
	if (b==1) {
		if (sorted == 2) 
			sorted = 0;
		else 
			sorted = 2;
	}
	if(b==2){
		if (sorted == 3) 
			sorted = 0;
		else 
			sorted = 3;
	}
	xmlhttpPost("data.php",getquerystring2(),updatepage);
}
function changestatus(i){
	data[i].status = (data[i].status + 1) % img.length;
	xmlhttpPost("data.php", getquerystring2(),updatepage);
}
function changeemergence(i){
	data[i].emergence=!data[i].emergence;
	xmlhttpPost("data.php",getquerystring2(),updatepage);
}
function getquerystring2()
{
	var str="all=";
	var str2=new Array();
	for(i=0;i<data.length;i++)
		str2[i]=data[i].time.getTime()+"|||"+data[i].status+"|||"+(data[i].emergence?"1":"0")+"|||"+data[i].text;
	if(str2.length==0)str+="b";
	else
		str+=escape(str2.join("{N}"));
	return str;
}
function AddToDo()
{	
	xmlhttpPost("data.php",getquerystring(),updatepage);
	document.getElementById("txttodo").value="";
}
function DeleteToDo(i){
	if (!confirm("确实要删除吗？")) 
		return;
	for (j = i; j < data.length - 1; j++) 
		data[j] = data[j + 1];
	data.length--;
	xmlhttpPost("data.php", getquerystring2(),updatepage);
}
function DeleteShown()
{
	if(!confirm("Really delete them?"))
		return;
	for(i=0;i<data.length;i++)
		if(filters[data[i].status])
		{
			for (j = i; j < data.length - 1; j++) 
				data[j] = data[j + 1];
			data.length--;i--;
		}
	xmlhttpPost("data.php", getquerystring2(),updatepage);
}
</script>
</body>

</html>
