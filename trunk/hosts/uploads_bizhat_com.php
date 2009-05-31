<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

if ($_GET["step"] == "1")
{
	$cookie = urldecode(trim($_GET["cookie"]));
	$post = unserialize(stripslashes(urldecode(trim($_GET["post"]))));
	$post["captcha"] = $_GET["captcha"];
	$post["download"] = "<< Download Now >>";

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	//file_put_contents("bizhat_3.txt", $page);
	is_page($page);
	is_present($page, "Access code wrong");

	if (preg_match("/<a href='(.*)'>Click here to download/", $page, $redir))
	{
		$Url = parse_url($redir[1]);
		$FileName = basename($Url["path"]);
		insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($LINK)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
	}
	else
	{
		html_error("Download link not found", 0);
	}
}
else
{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	//file_put_contents("bizhat_1.txt", $page);
	is_page($page);
	is_present($page, "File not found.");
	is_present($page, "Invalid id.");

	if (preg_match('/<input type="hidden" name="id" value="(.*)">/', $page, $fileid))
	{
		$post = Array();
		$post["id"] = $fileid[1];
	}
	else
	{
		html_error("Download form not found", 0);
	}

	preg_match('/Set-Cookie: PHPSESSID=(.*); /', $page, $coo);
	$cookie = "PHPSESSID=".$coo[1];

	$Urli = parse_url("http://uploads.bizhat.com/captcha.php");
	$page = geturl($Urli["host"], $Urli["port"] ? $Urli["port"] : 80, $Urli["path"].($Urli["query"] ? "?".$Urli["query"] : ""), $LINK, $cookie, 0, 0, $_GET["proxy"],$pauth);
	//file_put_contents("bizhat_2.txt", $page);
	$headerend = strpos($page, "\r\n\r\n");
	$pass_img = substr($page, $headerend + 4);
	write_file($download_dir."bizhat_com_captcha.jpg", $pass_img);

	$code = '<form method="post" action="'.$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "").'">'.$nn;
	//$code .= '<input type="hidden" name="filename" value="none">'.$nn;
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
	$code .= '<input type="hidden" name="step" value="1">'.$nn;
	$code .= 'Please enter : <img src="'.$download_dir.'bizhat_com_captcha.jpg?'.rand(1,10000).'"><br><br>'.$nn;
	$code .= '<input type="text" name="captcha"> <input type="submit" value="Download">'.$nn;
	$code .= '</form>';
	echo $code;
}
?>