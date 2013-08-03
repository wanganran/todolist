<?php 
function GetAllData()
{
	$f=fopen("list.dat","r");
	$s="";
	while(!feof($f)){
		$s=$s.fgets($f);
	}
	fclose($f);
	return $s;
}
function AddData($date,$ss)
{
	$f=fopen("list.dat","r");
	$s="";
	while(!feof($f)){
		$s=$s.fgets($f);
	}
	fclose($f);
	$f=fopen("list.dat","w");
	if($s=="")
		$s=$date."|||0|||0|||".htmlspecialchars($ss,ENT_COMPAT,"UTF-8").$s;
	else
		$s=$date."|||0|||0|||".htmlspecialchars($ss,ENT_COMPAT,"UTF-8")."{N}".$s;
	fwrite($f,$s);
	fclose($f);
	return $s;
}
function UpdateData($s)
{

	$f=fopen("list.dat","w");
	fwrite($f,$s);
	fclose($f);
	return $s;
}

?>