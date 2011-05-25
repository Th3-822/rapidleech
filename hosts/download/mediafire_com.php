<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

function mf_str_conv($str_or)
{
	$str_or = stripslashes($str_or);
	if (!preg_match("/unescape\(\W([0-9a-f]+)\W\);\w+=([0-9]+);[^\^]+\)([0-9\^]+)?\)\);eval/", $str_or, $match)) return $str_or;
	$match[3] = $match[3] ? $match[3] : "";
	for ($i = 0; $i < $match[2]; $i++){
		$c = HexDec(substr($match[1], $i*2, 2));
		eval ("\$c = \$c".$match[3].";");
		$str_re .= chr($c);
	}
	$str_re = str_replace($match[0], stripslashes($str_re), $str_or);
	if (preg_match("/unescape\(\W([0-9a-f]+)\W\);\w+=([0-9]+);[^\^]+\)([0-9\^]+)?\)\);eval/", $str_re, $dummy))
		$str_re = mf_str_conv($str_re);
	return $str_re;
}

$Url["path"] = str_replace("download.php", "", $Url["path"]);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0,  $_GET["password"] ? array("downloadp" => $_GET["password"]) : 0, 0, $_GET["proxy"],$pauth);
is_page($page);
$cookie = cut_str($page, 'Set-Cookie: ',' ');

if ($location = trim(cut_str($page, "Location: ", "\n"))){
	if (strstr($location, "http://download")){
		$Href = $location;
		$Url = parse_url($Href);
		$FileName = !$FileName ? basename($Url["path"]) : $FileName;
		
		insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
		die;
	}
	if (strstr($location, "error.php?")) html_error('Invalid or Deleted File');
	$page = geturl($Url["host"], defport($Url), $location, $cookie, 0, 0, 0, $_GET["proxy"],$pauth);
	if ($tmp = cut_str($page, 'Set-Cookie: ',' ')) $cookie = $tmp;
	$Referer = "http://www.mediafire.com$location";
}
else $Referer = $LINK;

if (stristr ( $page, "Eo();  dh('')" )) {
	print "<form name=\"dl\" action=\"$PHP_SELF\" method=\"post\">\n";
	print "<input type=\"hidden\" name=\"link\" value=\"" . urlencode ( $LINK ) . "\">\n<input type=\"hidden\" name=\"referer\" value=\"" . urlencode ( $Referer ) . "\">";
	print "<input type=\"hidden\" name=\"comment\" id=\"comment\" value=\"" . $_GET ["comment"] . "\">\n<input type=\"hidden\" name=\"email\" id=\"email\" value=\"" . $_GET ["email"] . "\">\n<input type=\"hidden\" name=\"partSize\" id=\"partSize\" value=\"" . $_GET ["partSize"] . "\">\n<input type=\"hidden\" name=\"method\" id=\"method\" value=\"" . $_GET ["method"] . "\">\n";
	print "<input type=\"hidden\" name=\"proxy\" id=\"proxy\" value=\"" . $_GET ["proxy"] . "\">\n<input type=\"hidden\" name=\"proxyuser\" id=\"proxyuser\" value=\"" . $_GET ["proxyuser"] . "\">\n<input type=\"hidden\" name=\"proxypass\" id=\"proxypass\" value=\"" . $_GET ["proxypass"] . "\">\n<input type=\"hidden\" name=\"path\" id=\"path\" value=\"" . $_GET ["path"] . "\">\n";
	print "<h4>Enter password here: <input type=\"text\" name=\"password\" size=\"13\">&nbsp;&nbsp;<input type=\"submit\" onclick=\"return check()\" value=\"Download File\"></h4>\n";
	print "<script language=\"JavaScript\">" . $nn . "function check() {" . $nn . "var imagecode=document.dl.imagestring.value;" . $nn . 'if (imagecode == "") { window.alert("You didn\'t enter the image verification code"); return false; }' . $nn . 'else { return true; }' . $nn . '}' . $nn . '</script>' . $nn;
	print "</form>\n</body>\n</html>";
	exit ();
}

$pages = explode("eval(", $page);
$page = "";
foreach ($pages as $v) $page .= mf_str_conv($v."eval");
if (!preg_match("/=\W?(\w+)[\('\"]+(\w{11,})[,'\"]+([0-9a-f]{33,})/i", $page, $eb)) html_error('Error 1.1');
if (!$pageb = cut_str($page, $eb[1].'(', ';}}')) html_error('Error 1.2');
if (!preg_match("|getElementById\(.([0-9a-f]{32}).|", $pageb, $match)) html_error('Error 1.3');
if (!$pKr = cut_str($page, "pKr='", "'")) html_error('Error 1.4');

$page = geturl($Url["host"], 80, "/dynamic/download.php?qk=".$eb[2]."&pk1=".$eb[3]."&r=$pKr", $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

$pages = explode("eval(", $page);
$page = "";
foreach ($pages as $v) $page .= mf_str_conv($v."eval");
if (!$tmp = cut_str($page, $match[1], '</xmp>')) html_error('Error 2.0');
if (!preg_match("|http:[^\"']+(.\+(\w+)\+.)[^\"']+|", stripslashes(str_replace(" ", "", $tmp)), $link_match)) html_error('Error get download link');
if (!preg_match("/".$link_match[2]."=.([\w]+)/", $page, $match)){
	$pagea = stripslashes(str_replace(" ", "", $pages[0]));
	if (!preg_match("/".$link_match[2]."=.([\w]+)/", $pagea, $match)) html_error('Error 2.5');
}

$Href = str_replace($link_match[1], $match[1], $link_match[0]);
$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

// written by okoze
?>