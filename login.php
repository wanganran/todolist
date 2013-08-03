<?php 
include_once("settings.php");

if(isset($_POST["pwd"])&&$_POST["pwd"]==$PWD){
	if($_POST["remember"]=="remember")setcookie("todopwd",$PWD,time()+3600*24*365*20);
	else setcookie("todopwd",$PWD);
	header("Location: index.php");
}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" " http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>ToDo List Config</title>
</head>
<body>
	<form method="post">
		<span>请输入密码：</span><input name="pwd" type="password"/>
		<input type="checkbox" name="remember" selected="selected" value="remember"/>记住我
		<input type="submit" value="确定"/>
	</form>
</body>