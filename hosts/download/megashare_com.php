<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["megashare"]["user"] && $premium_acc["megashare"]["pass"]))
{
	$Url = parse_url("http://www.megashare.com/login.php");
	$post = Array();
	$post["loginid"] = $_GET["premium_user"] ? $_GET["premium_user"] : $premium_acc["megashare"]["user"];
	$post["passwd"] = $_GET["premium_pass"] ? $_GET["premium_pass"] : $premium_acc["megashare"]["pass"];
	$post["yes"] = "Click Here To Login";
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, $post, 0, $_GET["proxy"],$pauth);
	//file_put_contents("megashare_1.txt", $page);
	is_page($page);
	is_present($page, "Invalid Username or Password, Try Again.");
	$post = Array();
	$post["PremDz"] = "PREMIUM";
	//one day... $cookie = Array();
	preg_match_all('/Set-Cookie: ([^\r\n]+)/', $page, $cookies);
	foreach ($cookies[1] AS $cookieFull)
	{
		$cookieSplit = explode("; ", $cookieFull);
		// one day... $cookie[] = $cookieSplit[0];
		$cookie .= $cookieSplit[0]."; ";
	}
	$cookie = substr($cookie, 0, -2); // remove one day...
	$Url = parse_url($LINK);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);
	//file_put_contents("megashare_2.txt", $page);
	is_page($page);
	is_present($page, "This File has been DELETED.");
	is_present($page, "This File is Password Protected.");
	$post = Array();
	$post["yes"] = "Click Here to Download";
	preg_match('/<input type="hidden" name="id" value="([0-9]+)">/', $page, $idArray);
	$post["id"] = $idArray[1];
	preg_match('/<input type="hidden" name="time_diff" value="([0-9]+)">/', $page, $timeArray);
	$post["time_diff"] = $timeArray[1];
	//preg_match('/<input type="hidden" name="req_auth" value="(.*)">/', $page, $authArray);
	//$post["req_auth"] = $authArray[1];
	$post["req_auth"] = "n";
	/* one day...
	foreach ($cookie AS $cookie1)
	{
		$cookie2 .= $cookie1."; ";
	}
	$cookie = substr($cookie2, 0, -2);*/

	insert_location("$PHP_SELF?filename=none&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($LINK)."&post=".urlencode(serialize($post))."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
else
{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	preg_match('/ocation: (.*)/',$page,$loc);
	$loc = trim($loc[1]);
	$Url = parse_url($loc);
	$post = Array();
	$post["FreeDz"] = "FREE";
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, 0, $post, 0, $_GET["proxy"],$pauth);
	//file_put_contents("megashare_1.txt", $page);
	is_page($page);
	is_present($page, "This File has been DELETED.");
	is_present($page, "This File is Password Protected.");
	$post = Array();
	$post["yesss"] = "Download";
	preg_match('/<input type="hidden" name="id" value="([0-9]+)">/', $page, $idArray);
	$post["id"] = $idArray[1];
	preg_match('/<input type="hidden" name="time_diff" value="([0-9]+)">/', $page, $timeArray);
	$post["time_diff"] = $timeArray[1];
	//preg_match('/<input type="hidden" name="req_auth" value="(.*)">/', $page, $authArray);
	//$post["req_auth"] = $authArray[1];
	$post["req_auth"] = "n";
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $loc, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	preg_match('/ocation: (.*)/',$page,$loc);
	$loc = trim("http://".$Url['host'].'/'.$loc[1]);
	$Url = parse_url($loc);
	$FileName = basename($Url['path']);

	insert_location("$PHP_SELF?filename=".$FileName."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($LINK)."&post=".urlencode(serialize($post))."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
?>