<?php

if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

### Your Authentication Info ### - To use sharebase premium: Uncomment the line below and affix your own email and passhash to the values below, as shown by example. Change your email like shown (make sure to use '@' symbol), and change the hash to your pass hash (see your browser cookies to get the value of memp)
$sbemail = '';
$sbpass = '';



//Use PREMIUM?
if ($sbemail && $sbpass)
{
	$posturl = 'http://sharebase.to/members/';
	$postfields = array();
	$postfields['lmail'] = $sbemail;
	$postfields['lpass'] = $sbpass;
	$postfields['104'] = 'Login Now !';
	$Url = parse_url($posturl);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""),  0, 0, $postfields, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$PHPSID = cut_str($page, 'Set-Cookie: PHPSESSID=', ';');
	$memm = 'memm=' . cut_str($page, 'Set-Cookie: memm=', ';');
	$memp = 'memp=' . cut_str($page, 'Set-Cookie: memp=', ';');
	$sharebase_cookie = $memm .'; ' . $memp;
	$post = array();
	$post['asi'] = $PHPSID;
	$post["$PHPSID"] = 'Download Now !';
	$sharebase_cookie_phpsid = "PHPSESSID=$PHPSID; " . $sharebase_cookie;
	$Url = parse_url($LINK);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $sharebase_cookie_phpsid, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	if (!$redir = trim(cut_str($page, 'Location: ', "\n"))) html_error("Download locator not found", 0);
	$Url = parse_url($redir);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $sharebase_cookie_phpsid, 0, 0, $_GET["proxy"],$pauth);	
	is_page($page);
	if (!$redir = trim(cut_str($page, 'Location: ', "\n"))) html_error("Final download link not found", 0);
	$flink = 'http://' . $Url['host'] . $redir;
	$Url = parse_url($flink);
	insert_location("$PHP_SELF?filename=".urlencode(basename($Url['path']))."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($sharebase_cookie_phpsid)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".$LINK.($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
//Use FREE instead?
else
{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page, "The download is deleted or the Download-Link is wrong.");

	if ($PHPSID = cut_str($page, 'Set-Cookie: PHPSESSID=', ';'))
	{
		$sharebase_cookie_phpsid = "PHPSESSID=$PHPSID;";
	}
	else
	{
		html_error("Cookie not found", 0);
	}
	$post = array();
	$post['asi'] = $PHPSID;
	$post["$PHPSID"] = 'Download Now !';
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $sharebase_cookie_phpsid, $post, 0, $_GET["proxy"],$pauth);	
	is_page($page);
	if (preg_match('%you still must wait\s(.+)\s!%i', $page, $time)) html_error('Service says you must wait ' . $time[1], 0);
	if (!$redir = trim(cut_str($page, 'Location: ', "\n"))) html_error("Download locator not found", 0);
	$Url = parse_url($redir);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, 0, 0, 0, $_GET["proxy"],$pauth);	
	is_page($page);
	if (!$redir = trim(cut_str($page, 'Location: ', "\n"))) html_error("Final download link not found", 0);
	$flink = 'http://' . $Url['host'] . $redir;
	$Url = parse_url($flink);
	insert_location("$PHP_SELF?filename=".urlencode(basename($Url['path']))."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($sharebase_cookie_phpsid)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".$LINK.($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
?>