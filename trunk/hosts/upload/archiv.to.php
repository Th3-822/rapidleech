<table width=600 align=center> 
</td></tr> 
<tr><td align=center> 
<div id=login width=100% align=center></div> 
<?php
$continue_up=true;
if ($continue_up)
{
		$Url=parse_url('http://xyz.archiv.to:81/upload/frame');
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"], $pauth);
		$cookie=GetCookies($page);
		$post["UPLOAD_IDENTIFIER"] = cut_str($page,'id="UPLOAD_IDENTIFIER" value="','"'); 
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php	
		$url=parse_url('http://xyz.archiv.to:81/upload/frame');
		$post["data[Upload][mail]"] = "";
		$post["data[Upload][mail_friend]"] = "";
		$post["data[Upload][message]"] = "";

		$upfiles=upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://xyz.archiv.to:81/upload/frame?lang=de", $cookies, $post, $lfile, $lname, "data[Upload][file]");
		preg_match('#nextUrl":"http:\\\/\\\/archiv\.to\\\/info\\\/([a-z0-9]+)"#', $upfiles,$link);
		
		$Url = parse_url("http://archiv.to/info/".$link[1]."");
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"], $pauth);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
		$download_link = cut_str($page,'id="FileGET" value="','"'); 
		$delete_link = cut_str($page,'id="FileDEL" value="','"'); 
}

//Created by nastrove
?>