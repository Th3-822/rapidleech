<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

$post = Array();

$FileName = !$FileName ? basename($Url["path"]) : $FileName;
$FileName = str_replace(".html", "", $FileName);

if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET["premium_pass"])) {
	$Url =  parse_url("http://freakshare.net/login.html");
	$post["user"] = $_GET["premium_user"];
	$post["pass"] = $_GET["premium_pass"];
	$post["submit"] = "Login";
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);

	$cookie = GetCookies($page);
	insert_timer(5, "Please wait while logging in");

	$Url =  parse_url($LINK);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	if (stristr($page, "Location:")) {
		$Href = trim(cut_str($page, "Location:","\n"));
		$Url =  parse_url($Href);
	} else {
		html_error ("Cannot get download link!", 0 );
	}

} else {
	$post["section"] = "waitingtime";
	$post["did"] = "2";

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	$cookie = GetCookies($page);
	$countdowntime = trim(cut_str($page, "var time = ",";"));

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);

	if (!stristr($page, "Location:")) {
		insert_timer(3, "");
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	}

	if (stristr($page, "Location:")) {
		$Href = trim(cut_str($page, "Location:","\n"));
		$Url =  parse_url($Href);
	} else {
		html_error ("Cannot get download link!", 0 );
	}

	$cookie .= "; " . GetCookies($page);

	insert_timer($countdowntime, "Please wait while getting file.");
}

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&port=".$Url["port"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($LINK)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));


// by plapla on Nov 25, 2009

?>
