<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
//file_put_contents("free_1.txt", $page);
is_page($page);

if(preg_match('/Location: ([^\r\n]+)/', $page, $redir))
{
	$link = trim($redir[1]);
	$Url = parse_url($link);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"]."?".$Url["query"], 0, 0, 0, 0, $_GET["proxy"],$pauth);
	//file_put_contents("free_2.txt", $page);
	is_page($page);
}
is_present($page, "Fichier inexistant.", "The file could not be found. Please check the download link.");
is_present($page, "Le fichier demand&eacute; n'a pas &eacute;t&eacute; trouv&eacute;.", "The file could not be found. Please check the download link.");
is_present($page, "Erreur 404 - Document non trouv&eacute;", "The file could not be found. Please check the download link.");
is_present($page, "Appel incorrect.", "Incorrect link.");

preg_match('%<a style="text-decoration: underline" href="(.*)">T&eacute;l&eacute;charger ce fichier%', $page, $link);
if ($link)
{
	$Url = parse_url($link[1]);
	$FileName = basename($link[1]);
}
else
{
	html_error("Download link not found.", 0);
}

if (preg_match('/Set-Cookie: (.*); path/', $page, $cook))
{
	$cookie = $cook[1];
}
else
{
	html_error("Cookie not found.", 0);
}
insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>