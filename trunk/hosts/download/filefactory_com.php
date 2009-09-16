	<?php

	if (!defined('RAPIDLEECH'))
	  {
	  require_once("index.html");
	  exit;
	  }


	//Use PREMIUM?
	if (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["filefactory"]["user"] && $premium_acc["filefactory"]["pass"]))
	{
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
		is_page($page);
		is_present($page, 'File Not Found', 'Error - File was not found!');
		$post = array();
		$post['email'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc["filefactory"]["user"]  ;
		$post['password'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc["filefactory"]["pass"];
		$post['redirect'] = $LINK;
		$page = geturl("www.filefactory.com", 80, "/", 0, 0, $post, 0, $_GET["proxy"], $pauth);
		is_page($page);
		if (!preg_match('%(ff_membership=.+); expires%', $page, $lcook)) html_error('Error getting login-cookie', 0);
		if (!preg_match('%ocation: (.+)\r\n%', $page, $redir)) html_error('Error getting redirect', 0);
		$Url = parse_url($redir[1]);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $lcook[1], 0, 0, $_GET["proxy"],$pauth);
		is_page($page);
		if (!preg_match('%(http://dl\d{3}\.filefactory\.com/dlp/.+)">Download with FileFactory Premium%U', $page, $redir2)) html_error('Error getting redirect 2', 0);
		$Url = parse_url($redir2[1]);
		$FileName = basename($Url['path']);
		insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($lcook[1])."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".$redir2[1].($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

	}
	else
	//Use FREE instead?
	{
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"], $pauth);
		is_page($page);
		$btn=cut_str($page,'class="basicBtn">','</div>');
		$out=cut_str($btn,'href="','"');

		$one = str_replace('/dlf', 'http://www.filefactory.com/dlf', $out);
		$Url = parse_url($one);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"], $pauth);
		is_page($page);
		preg_match('/http:\/\/.+filefactory\.com\/dl\/f\/[^\'"]+/i', $page, $link);
		$file = $link[0];
		$Href = $file;
		$Url = parse_url($Href);
		$wait = "40";
		insert_timer($wait, "Preparing Your File");
		$FileName = !$FileName ? basename($Url["path"]) : $FileName;
	     $fritz="$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "");
		insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : ""));

	/*
	szal 18-jun-09
	filefactory download plugin by mrbrownee70, created june 22, 2009 
	update by kaox 12-sep-2009 (free member)
	*/
	}

	?>
