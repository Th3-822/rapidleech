<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 

$page=geturl($Url["host"],80,$Url["path"]);
is_page($page);
preg_match('/Set-Cookie: (.*);/', $page, $cook) ;
$cookie = $cook[1];
$dir = preg_replace("/pokaz/","pobierz",$Url["path"]);
$rf="http://".$Url["host"]. $Url["path"];
$page=geturl($Url["host"],80,$dir,$rf,$cookie);
is_page($page);
preg_match('/Set-Cookie: (.*);/', $page, $cook) ;
$cookie =$cookie."; ".$cook[1];
$rf="http://".$Url["host"].$dir;
$dir = preg_replace("/pobierz/","download",$dir);
$page=geturl($Url["host"],80,$dir,$rf,$cookie);
is_page($page);
if(preg_match("/ocation: (.+)/", $page, $location)){
$link = trim($location[1]);
$Url = parse_url($link);
}else{html_error("Plugin Outdated , Enter In http://www.rapidleech.com Forum for news",0);}
$FileName = basename($Url["path"]);
insert_location("$PHP_SELF?filename=".urlencode($FileName)."&force_name=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&post=".urlencode(serialize($post))."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : ""));
		
?>

