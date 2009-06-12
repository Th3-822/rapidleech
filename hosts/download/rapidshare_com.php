<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

if (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["rs_com"]["user"] && $premium_acc["rs_com"]["pass"]))
	{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	is_present($page,"The file could not be found.", "The file could not be found. Please check the download link.");
	is_present($page,"This limit is reached", "This file is neither allocated to a Premium Account, or a Collector's Account, and can therefore only be downloaded 10 times. This limit is reached.");
	is_present($page,"Due to a violation of our terms of use, the file has been removed from the server.");
	is_present($page,"This file is suspected to contain illegal content and has been blocked.");
	is_present($page,"The uploader has removed this file from the server.");
	is_present($page,"This file has been removed from the server, because the file has not been accessed in a long time.");
	is_present($page,"is momentarily not available", "This server is momentarily not available.  We are aware of this and are working to get this resolved.");
	is_present($page,"unavailable due to hardware-problems", "Server unavailable due to hardware-problems");

	$FileName = basename(trim(cut_str($page, '<form action="', '"')));
	!$FileName ? $FileName = basename($Url["path"]) : "";
	$auth = $_REQUEST["premium_user"] ? base64_encode($_REQUEST["premium_user"].":".$_REQUEST["premium_pass"]) : base64_encode($premium_acc["rs_com"]["user"].":".$premium_acc["rs_com"]["pass"]);

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth, $auth);
	is_page($page);
	is_present($page,"Account found, but password is incorrect");
	is_present($page,"Account not found");

	if (stristr($page, "Location:"))
		{
		$Href = trim(cut_str($page, "Location:","\n"));
		$Url =  parse_url($Href);

	 	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
		}
	else
		{
		html_error("Cannot use premium account", 0);
		}
	}
else
	{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	is_present($page,"Due to a violation of our terms of use, the file has been removed from the server.");
	is_present($page,"This limit is reached", "This file is neither allocated to a Premium Account, or a Collector's Account, and can therefore only be downloaded 10 times. This limit is reached.");
	is_present($page,"This file is suspected to contain illegal content and has been blocked.");
	is_present($page,"The file could not be found.", "The file could not be found. Please check the download link.");
	is_present($page,"The uploader has removed this file from the server.");
	is_present($page,"This file has been removed from the server, because the file has not been accessed in a long time.");
	is_present($page,"is momentarily not available", "This server is momentarily not available.  We are aware of this and are working to get this resolved.");
	is_present($page,"unavailable due to hardware-problems", "Server unavailable due to hardware-problems");
	is_present($page, "is already downloading a file","Your IP-address is already downloading a file, Please wait until the download is completed.");

	$post = array();
	$post["dl.start"] = "Free";

	$Href = trim(cut_str($page, '<form action="', '"'));
	$refimg = $Href;
	$Url = parse_url($Href);

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : "") ,$LINK , 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);

	is_present($page, "is not allowed to use the free-service anymore today","No more free downloads from this IP today");
	is_present($page,"This limit is reached", "This file is neither allocated to a Premium Account, or a Collector's Account, and can therefore only be downloaded 10 times. This limit is reached.");
	is_present($page, "This file exceeds your download-limit","Download limit exceeded");
	is_present($page, "is already downloading a file","Your IP-address is already downloading a file, Please wait until the download is completed.");

	if (stristr($page, "Would you like more?"))
		{
		$minutes = trim(cut_str($page, "Or try again in about ", "minutes"));
		if ($minutes)
			{
               $countdown2= $minutes*60;
			}
		else
			{
			html_error("Download limit exceeded.", 0);
			}
		}

	if(stristr($page, "Too many users downloading right now") || stristr($page, "Too many connections"))
		{
		html_error("Too many users downloading right now", 0);
		}

if($countdown2) {


 insert_timer($countdown2, "Plus Wait time for free user.");

      $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
    is_page($page);

    is_present($page,"Due to a violation of our terms of use, the file has been removed from the server.");
    is_present($page,"This limit is reached", "This file is neither allocated to a Premium Account, or a Collector's Account, and can therefore only be downloaded 10 times. This limit is reached.");
    is_present($page,"This file is suspected to contain illegal content and has been blocked.");
    is_present($page,"The file could not be found.", "The file could not be found. Please check the download link.");
    is_present($page,"The uploader has removed this file from the server.");
    is_present($page,"This file has been removed from the server, because the file has not been accessed in a long time.");
    is_present($page,"is momentarily not available", "This server is momentarily not available.  We are aware of this and are working to get this resolved.");
    is_present($page,"unavailable due to hardware-problems", "Server unavailable due to hardware-problems");
    is_present($page, "is already downloading a file","Your IP-address is already downloading a file, Please wait until the download is completed.");

    $post = array();
    $post["dl.start"] = "Free";

    $Href = trim(cut_str($page, '<form action="', '"'));
    $refimg = $Href;
    $Url = parse_url($Href);

    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : "") ,$LINK , 0, $post, 0, $_GET["proxy"],$pauth);
    is_page($page);

}


        $countDown = trim(cut_str($page, "var c=", ";"));

	preg_match('%<form name="dlf?".*</form>%s', $page, $form_content);
	$middle_str = str_replace("\\", "", preg_replace('/(\' *\+.*?(\r\n)*.*?\'|display:none;)/s', '', $form_content[0]));
	$code = '<center>'.trim($middle_str);
	$FileAddr = trim(cut_str($code, '<form name="dlf" action="', '"'));
    $FileName = basename($FileAddr);
	$Url = parse_url($FileAddr);

insert_timer($countDown, "Download-Ticket reserved.");
insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($Referer).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

	}
	// edited by kaox
?>