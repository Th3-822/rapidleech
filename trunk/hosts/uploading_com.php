<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
//file_put_contents("uploading_1.txt", $page);
is_page($page);
is_present($page, "Sorry, the file requested by you does not exist on our servers.");

/*if (preg_match('/Set-Cookie: (.*); path/', $page, $coo))
{
	$cookie = $coo[1];
}
else
{
	html_error("Cookie not found");
}*/
$cookie = GetCookies($page);

$post = Array();
$post["free"] = 1;
$cookie = 'redirect=1';
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);
//file_put_contents("uploading_2.txt", $page);
is_page($page);

$countDown = trim(cut_str($page, "var c2168=", ";"));
$FileName = substr(basename($Url["path"]), 0, -5);
$post = Array();
$post["free"] = 1;
$post["x"] = 1;
$coo = "";
/*if (preg_match('/Set-Cookie: (.*); path/', $page, $coo))
{
	$cookie = $coo[1];
}
$cookie = GetCookies($page);
foreach ($cookie as $i=>$k) {
	$cookie[$i] = trim($k);
}
$cookie = implode(';',$cookie);*/
$temp = "";
preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
$cookie = $temp[1];
$cookie = implode(';',$cookie);
//echo $cookie;exit;
/*$code = '<form name="dlf" method="post" action="'.$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "").'">'.$nn;
$code .= '<input type="hidden" name="filename" value="'.urlencode($FileName).'">'.$nn;
$code .= '<input type="hidden" name="force_name" value="'.urlencode($FileName).'">'.$nn;
$code .= '<input type="hidden" name="link" value="'.urlencode($LINK).'">'.$nn;
$code .= '<input type="hidden" name="referer" value="'.urlencode($LINK).'">'.$nn;
$code .= '<input type="hidden" name="saveto" value="'.$_GET["path"].'">'.$nn;
$code .= '<input type="hidden" name="host" value="'.$Url["host"].'">'.$nn;
$code .= '<input type="hidden" name="path" value="'.urlencode($Url["path"]).'">'.$nn;
$code .= '<input type="hidden" name="cookie" value="'.urlencode($cookie).'">'.$nn;
$code .= '<input type="hidden" name="post" value="'.urlencode(serialize($post)).'">'.$nn;
$code .= '<input type="hidden" name="add_comment" value="'.$_GET["add_comment"].'">'.$nn;
$code .= ($_GET["add_comment"] == "on" ? '<input type="hidden" name="comment" value="'.urlencode($_GET["comment"]).'">'.$nn : "");
$code .= '<input type="hidden" name="domail" value="'.$_GET["domail"].'">'.$nn;
$code .= '<input type="hidden" name="email" value="'.($_GET["domail"] ? $_GET["email"] : "").'">'.$nn;
$code .= '<input type="hidden" name="split" value="'.$_GET["split"].'">'.$nn;
$code .= '<input type="hidden" name="partSize" value="'.($_GET["split"] ? $_GET["partSize"] : "").'">'.$nn;
$code .= '<input type="hidden" name="method" value="'.$_GET["method"].'">'.$nn;
$code .= ($_GET["useproxy"] ? '<input type="hidden" name="useproxy" value="'.$_GET["useproxy"].'">'.$nn : "");
$code .= '<input type="hidden" name="proxy" value="'.($_GET["useproxy"] ? $_GET["proxy"] : "").'">'.$nn;
$code .= ($pauth ? '<input type="hidden" name="pauth" value="'.$pauth.'">'.$nn : "");
$code .= '</form>';

insert_new_timer($countDown, rawurlencode($code), "Download-Ticket reserved.");*/
insert_timer($countDown,"Wait for your turn");
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);
$loc = "";
preg_match('/ocation: (.*)/',$page,$loc);
$Href = $loc[1];
$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;

insert_location("$PHP_SELF?cookie=".urlencode($cookie)."&filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>