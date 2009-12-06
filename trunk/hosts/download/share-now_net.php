<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	
	is_page($page);
	//is_present($page, "Location: /errortracking.php?msg=File not found", "File not found");
	
	$cookie = "";
	preg_match_all("/Set-Cookie: ([^;]+;)/", $page, $cook);
	$arraySize = count($cook);
	
	for ( $i=0;$i<$arraySize;$i++)
	{
		$cookie=$cookie.array_shift($cook[1]);
	}
	
	$timeLock = trim(cut_str($page, "var wait = ", ";"));
	$downloadLink = trim(cut_str($page, "form name=\"download\" method=\"post\" action=\"", "\""));
	$downloadId = trim(cut_str($page, "input type=\"hidden\" name=\"download\" value=\"", "\""));
	$submitValue = trim(cut_str($page, "input type=\"submit\" name=\"Submit\" value=\"", "\""));

	$countDown = $timeLock;
	insert_timer($countDown, "Waiting link timelock");
	
	$post = Array();
	$post["download"] = $downloadId;;
	$post["Submit"] = $submitValue;
	$Url = parse_url($downloadLink);
	
	$FileName = "fileName";
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($LINK)."&cookie=".urlencode($cookie)."&post=".urlencode(serialize($post))."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
	
	// Created by rajmalhotra 06 Dec 2009
?>