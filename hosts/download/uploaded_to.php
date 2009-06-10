<?php //szalinski 09-May-09
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}
if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["uploaded_to"]["user"] && $premium_acc["uploaded_to"]["pass"]))
{
	$Url = parse_url('http://uploaded.to');
	$post = array();
	$post["email"] = $_GET["premium_user"] ? $_GET["premium_user"] : $premium_acc["uploaded_to"]["user"];
	$post["password"] = $_GET["premium_pass"] ? $_GET["premium_pass"] : $premium_acc["uploaded_to"]["pass"];
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/login", 0, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$cook = GetCookies($page);
	preg_match('/auth=(.*?);/', $cook, $cookie);
	$cookie = 'auth='.$cookie[1];
	$Url = parse_url($LINK);
	if ($Url['host'] == 'uploaded.to')
	{
		$LINK = str_replace(array('uploaded.to', 'file/'), array('ul.to', ''), $LINK);
		$Url = parse_url($LINK);
	}
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	preg_match('%ocation: (.+)\r\n%', $page, $redir);
	$Href = trim($redir[1]);
	$Url = parse_url($Href);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	preg_match('%ocation: (.+)\r\n%', $page, $redir);
	$Href = trim($redir[1]);
	$Url = parse_url($Href);
	$Url["path"] = str_replace('//', '/', $Url["path"]);
	$FileName = !$FileName ? basename($Url["path"]) : $FileName;
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
else
{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	//file_put_contents("uploaded_to_1.txt", $page);
	is_page($page);
	is_present($page, "Location: /?view=error_fileremoved", "File not found");
	is_present($page, "Location: /?view=error_traffic_exceeded_free", "Download limit exceeded");
	is_present($page, "http://images.uploaded.to/key.gif", "This file is password protected");

	if (preg_match('/(http.+dl\?id=[0-9a-zA-Z]+)/', $page, $dllink))
	{
		$dlink = $dllink[1];
	}
	else
	{
		html_error("Download link not found", 0);
	}
	$Url = parse_url($dlink);
	$FileName = "none";

	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
?>