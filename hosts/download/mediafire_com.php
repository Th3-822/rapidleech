<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

function str_conv($str_or)
{
	if (!preg_match("/unescape\([^\)]([^\)]+)[^\)]\);\w+=([0-9]+);[^\{^]+charCodeAt\(.\)([0-9\^]+)?/", $str_or, $match)) return $str_or;
	$str_de = urldecode($match[1]);
	$match[3] = $match[3] ? $match[3] : "";
	for ($i = 0; $i < $match[2]; $i++){
		$c = ord(substr($str_de, $i, 1));
		eval ("\$c = \$c".$match[3].";");
		$str_re .= chr($c);
	}
	$str_re = str_replace($match[0], $str_re, $str_or);
	if (preg_match("/unescape\([^\)]([^\)]+)[^\)]\).+charCodeAt\(.\)([0-9\^]+)/", $str_re, $dummy))
		$str_re = str_conv($str_re);
	return $str_re;
}

$Url["path"] = str_replace("download.php", "", $Url["path"]);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0,  $_GET["password"] ? array("downloadp" => $_GET["password"]) : 0, 0, $_GET["proxy"],$pauth);
is_page($page);
$cookie = cut_str($page, 'Set-Cookie: ',' ');

if (strstr($LINK,"?sharekey=")){
	preg_match ("/src=\"([^\"]+=_shared)\"/", $page, $matches);
	if (!$matches[1]) html_error('Error');
	$page = geturl($Url["host"], 80, $matches[1], 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	preg_match_all("/=Array\((\'\w{10,12})\'/i",$page,$matches);
	if (count($matches[1]) == 0)  html_error('Error get link');
	$Href=str_replace("'","http://www.mediafire.com/?",$matches[1]);
	if (!is_file("audl.php")) html_error('audl.php not found');
	echo "<form action=\"audl.php?GO=GO\" method=post>\n";
	echo "<input type=hidden name=links value='".implode("\r\n",$Href)."'>\n";
	foreach (array ("useproxy","proxy","proxyuser","proxypass") as $v)
		echo "<input type=hidden name=$v value=".$_GET[$v].">\n";
	echo "<script language=\"JavaScript\">void(document.forms[0].submit());</script>\n</form>\n";
	flush();
	exit();
}

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

list ($pagea, $pageb) = explode("default:DoShow", $page, 2);
$pageb = str_conv($pageb);
$pageb = stripslashes(str_replace(" ", "", $pageb));

if (!preg_match("/=\W?(\w+)[\('\"]+(\w+)[,'\"]+(\w+)([,'\"]+(\w+))?['\"]+\)/", $pageb, $eb)) html_error('Error 1');
$pages = explode("function", $pagea);
foreach ($pages as $v){
	$v = str_conv($v);
	if (strstr($v, $eb[1]."(")) break;
}
if (!preg_match("|getElementById\(.([0-9a-f]{32}).|", $v, $match)) html_error('Error 2');
if (!$eb[5]){
	$pages = explode("\n", $pagea);
	preg_match("|\w+=\W?(\w+)|", str_replace(" ", "", array_pop($pages)), $match_b);
	$eb[5] = $match_b[1];
}

$page = geturl($Url["host"], 80, "/dynamic/download.php?qk=".$eb[2]."&pk1=".$eb[3]."&r=".$eb[5], $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

$page = str_conv($page);
$page = stripslashes(str_replace(" ", "", $page));
$v = cut_str($page, $match[1],'}');
if (!preg_match("|http:[^\"']+(.\+(\w+)\+.)[^\"']+|", $v, $link_match)) html_error('Error get download link');
if (!preg_match("/".$link_match[2]."=.([\w]+)/", $page, $match)) html_error('Error 3');
$Href = str_replace($link_match[1], $match[1], $link_match[0]);
$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

// written by okoze
?>