<?
// Baking Addon
function generate($len){
	$conso = Array("b","c","d","f","g","h","j","k","l","m","n","p","r","s","t","v","w","x","y","z");
	$vocal = Array("a","e","i","o","u");
	$password = '';
	
	for($i=0; $i < $len/2; $i++)
	{
		$c = ceil(rand()) % 20;
		$v = ceil(rand()) % 5;
		$password .= $conso[$c].$vocal[$v];
	}

	return $password;
}
//End Addon
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://x7.to/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	
	$upserv = cut_str($page,"var uploadServer = '" ,"'");
	
	$upost['Filename'] = $lname;
	$upost['Upload'] = 'Submit Query';
	
	$code = generate(7);
	
	$url=parse_url($upserv."upload?admincode=".$code);
?>
<script>document.getElementById('info').style.display='none';</script>
<?
	$upagent = "Shockwave Flash";
	$upfiles = upfile( $url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $upost, $lfile, $lname, "Filedata", "", 0, 0, $upagent );
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	$ddl=cut_str($upfiles,'close' ,',');
	$access_pass = $code;
	$download_link= "http://x7.to/".ltrim($ddl);
	
/**************************************************\  
Made by Baking 19/12/2009 07:39
Upgraded by Baking 25/12/2009 12:30
Fixed by Raj Malhotra 16 May 2010 => Fixed Couldn't connect to Shockwave Flash at port 80
\**************************************************/
?>