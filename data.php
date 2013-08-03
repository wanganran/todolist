<?php 
include_once("functions.php");
	$f=$_POST["all"];
	$s=$_POST["txttodo"];
	$t=$_POST["date"];
	if($f){
		if($f=="b")$f="";
		UpdateData($f);
		echo("ok");
	}
	else if(!$s)
		echo(GetAllData());
	else
	{
		echo(AddData($t,$s));
	} 
?>