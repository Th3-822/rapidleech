<?php

if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

if (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ($_REQUEST["premium_acc"] == "on" && $premium_acc["hotfile_com"]["user"] && $premium_acc["hotfile_com"]["pass"]))
{ 
	// ////////////////////////////////////////////////////////// START PREMIUM /////////////////////////////////////////////////////////////
	$Url = parse_url($LINK);
	$post = array();
	$post["returnto"] = "/";
	$post["user"] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc["hotfile_com"]["user"];
	$post["pass"] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc["hotfile_com"]["pass"];
	$auth = base64_encode($post["user"] . ":" . $post["pass"]);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"], $pauth, $auth);
	// echo "<pre>";var_dump(nl2br(htmlentities($page)));echo "</pre>";exit;
	if (!preg_match('|ocation: (.+)\r\n|i', $page, $loca))
	{
		if (!preg_match('|(http://hotfile\.com/get/\d+/\w+/\w+/.+)"\s|', $page, $loca))
		{
			html_error('No direct link found, please check your account details are correct');
		}
		else $newlink = $loca[1];
	}
	else $newlink = $loca[1];

	$redir = trim($newlink);
	$Url = parse_url($redir);
	$FileName = basename($Url["path"]);
	insert_location ("$PHP_SELF?filename=" . urlencode ($FileName) . "&force_name=" . urlencode($FileName) . "&host=" . $Url ["host"] . "&path=" . urlencode ($Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "")) . "&referer=" . urlencode ($Referer) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ($LINK) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ($_GET ["comment"]) : "") . "&auth=1" . ($pauth ? "&pauth=$pauth" : "") . (isset($_GET["audl"]) ? "&audl=doum" : ""));
	// ////////////////////////////////////////////////////////// END PREMIUM ///////////////////////////////////////////////////////////////
}
else
{
	$hf = $_POST['hf'];
	if ($hf == "ok")
	{
		@unlink(urldecode($_POST["delete"]));
		$post = unserialize(urldecode($_POST['post']));
		$post["action"] = "checkcaptcha";
		$post["recaptcha_response_field"] = $_POST["captcha"];
		$Referer = $_POST["link"];

		$Url = parse_url($Referer);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"], $pauth);
		is_page($page);

		preg_match('/\/\d+\/\w+\/\w+\/[^\'"]+/i', $page, $down);
		$LINK = "http://hotfile.com/get" . $down[0];
		if ($down[0] == "")
		{
			$dsource = cut_str($page, '<h3', '</h3');
			$ddw = cut_str($dsource, 'href="', '"');
			$LINK = $ddw;
		}

		if (!stristr($page, "REGULAR DOWNLOAD"))
		{
			$Url = parse_url($LINK);
			$FileName = basename($Url["path"]);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"], $pauth);
			preg_match('/Location: *(.+)/', $page, $redir);
			if (strpos($redir[1], "http://") === false)
			{
				html_error("Server problem. Please try again after", 0);
			}
			$redirect = rtrim($redir[1]);
			$Url = parse_url($redirect);
			insert_location("$PHP_SELF?filename=" . urlencode($FileName) . "&host=" . $Url["host"] . "&path=" . urlencode($Url["path"] . ($Url["query"] ? "?" . $Url["query"] : "")) . "&referer=" . urlencode($Referer) . "&email=" . ($_GET["domail"] ? $_GET["email"] : "") . "&partSize=" . ($_GET["split"] ? $_GET["partSize"] : "") . "&method=" . $_GET["method"] . "&proxy=" . ($_GET["useproxy"] ? $_GET["proxy"] : "") . "&saveto=" . $_GET["path"] . "&link=" . urlencode($LINK) . ($_GET["add_comment"] == "on" ? "&comment=" . urlencode($_GET["comment"]) : "") . "&auth=" . $auth . ($pauth ? "&pauth=$pauth" : "") . (isset($_GET["audl"]) ? "&audl=doum" : ""));
		}
	}
	if ($hf == "ok")
	{
		echo ("Wrong captcha .Please re-enter");
	}
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"], $pauth);

	is_present($page, "File not found", "File not found, the file is not present or bad link", "0");
	is_present($page, "due to copyright", "This file is either removed due to copyright claim or is deleted by the uploader.", "0");
	is_present($page, "You are currently downloading", "You are currently downloading. Only one connection with server allow for free users", "0");

	preg_match_all('/timerend=d\.getTime\(\)\+(\d+)/i', $page, $arraytime);
	$wtime = $arraytime[1][1] / 1000;
	if ($wtime > 0)
	{
		$dowait = true;
		insert_timer($wtime, "You reached your hourly traffic limit");
	}
	$action = cut_str($page, "action value=", ">");
	$tm = cut_str($page, "tm value=", ">");
	$tmhash = cut_str($page, "tmhash value=", ">");
	$wait = cut_str($page, "wait value=", ">");
	$waithash = cut_str($page, "waithash value=", ">");
	$post = array();
	$post["action"] = $action;
	$post["tm"] = $tm;
	$post["tmhash"] = $tmhash;
	$post["wait"] = $wait;
	$post["waithash"] = $waithash;
	insert_timer($wait, "Waiting timelock");
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, $post, 0, $_GET["proxy"], $pauth);
	preg_match('/\/\d+\/\w+\/\w+\/[^\'"]+/i', $page, $down);
	$LINK = "http://hotfile.com/get" . $down[0];

	if ($down[0] == "")
	{
		$dsource = cut_str($page, '<h3', '</h3');
		$ddw = cut_str($dsource, 'href="', '"');
		$LINK = $ddw;
	}

	if ($down[0] == "")
	{
		$nofinish = true;

		$Url = parse_url("http://api.recaptcha.net/noscript?k=6LfRJwkAAAAAAGmA3mAiAcAsRsWvfkBijaZWEvkD");

		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"], $pauth);
		is_page($page);
		is_present($page, "Expired session", "Expired session . Go to main page and reattempt", 0);

		$cookie = GetCookies($page);
		$ch = cut_str ($page , 'recaptcha_challenge_field" value="' , '"');

		if ($ch)
		{
			$Url = parse_url("http://api.recaptcha.net/image?c=" . $ch);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"], $pauth);
			$headerend = strpos($page, "\r\n\r\n");
			$pass_img = substr($page, $headerend + 4);
			if ($options["download_dir"])
			{
				$imgfile = $options["download_dir"] . "hotfile_captcha.jpg";
			}
			else
			{
				$imgfile = download_dir . "hotfile_captcha.jpg";
			}
			if (file_exists($imgfile))
			{
				unlink($imgfile);
			}
			write_file($imgfile, $pass_img);
		}
		else
		{
			html_error("Error get captcha", 0);
		}

		$captchaid = cut_str($page, "captchaid value=", ">");
		$hash1 = cut_str($page, "hash1 value=", ">");
		$hash2 = cut_str($page, "hash2 value=", ">");

		unset($post);
		$post['recaptcha_challenge_field'] = $ch;

		print "<form method=\"post\" action=\"" . $PHP_SELF . (isset($_GET["audl"]) ? "?audl=doum" : "") . "\">$nn";
		print "<h4>Enter <img src=\"$imgfile\"> here:</h4><input name=\"captcha\" type=\"text\" >$nn";
		print "<input name=\"link\" value=\"$Referer\" type=\"hidden\">$nn";
		print '<input type="hidden" name="post" value="' . urlencode(serialize($post)) . '">' . $nn;
		print "<input name=\"hf\" value=\"ok\" type=\"hidden\">$nn";
		print "<input type=\"hidden\" name=\"delete\"  value=\"" . urlencode($imgfile) . "\" >" . $nn;
		print "<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";
	}
	if (!$nofinish)
	{
		$Url = parse_url($LINK);
		$FileName = basename($Url["path"]);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"], $pauth);
		preg_match('/Location: *(.+)/i', $page, $redir);
		if (strpos($redir[1], "http://") === false)
		{
			html_error("Server problem. Please try again after", 0);
		}
		$redirect = rtrim($redir[1]);
		$Url = parse_url($redirect);
		insert_location("$PHP_SELF?filename=" . urlencode($FileName) . "&host=" . $Url["host"] . "&path=" . urlencode($Url["path"] . ($Url["query"] ? "?" . $Url["query"] : "")) . "&referer=" . urlencode($Referer) . "&email=" . ($_GET["domail"] ? $_GET["email"] : "") . "&partSize=" . ($_GET["split"] ? $_GET["partSize"] : "") . "&method=" . $_GET["method"] . "&proxy=" . ($_GET["useproxy"] ? $_GET["proxy"] : "") . "&saveto=" . $_GET["path"] . "&link=" . urlencode($LINK) . ($_GET["add_comment"] == "on" ? "&comment=" . urlencode($_GET["comment"]) : "") . "&auth=" . $auth . ($pauth ? "&pauth=$pauth" : "") . (isset($_GET["audl"]) ? "&audl=doum" : ""));
	}
}

/**
* ***************************\
* WRITTEN by kaox 15-oct-2009
* UPDATE by kaox 01-mar-2010
* \***************************
*/
//updated 05-jun-2010 for standard auth system (szal)
?>
