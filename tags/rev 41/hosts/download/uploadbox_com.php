<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

if ($_POST['ub'] == 'ok') {
	$post = array();
	$post['enter'] = $_POST['enter'];
	$post['code'] = $_POST['code'];
	$post['go'] = 'yes';
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"],$pauth);
	$cookie = GetCookies($page);
	if (!stristr($page,"If downloading hasn't started automatically within 2 seconds, please")) html_error("Download link not found");
	preg_match('/<a href="(.*)">click here/',$page,$Href);
	if (!$Href) html_error("Captcha wrong or download link not found");
	$Referer = $LINK;
	$Url = parse_url($Href[1]);
	$FileName = !$FileName ? basename($Url["path"]) : $FileName;
	
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&cookie=".urlencode($cookie)."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
} else {
	$post['free'] = 'yes';
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page, "The limit of traffic for your country is exceeded.");
	$cookies = GetCookies($page);
	preg_match('/ type it in:<img src="(.+?)"/', $page, $imglink);
	preg_match('/<input type="hidden" name="code" value="(.*)"/',$page,$code);
	$code = $code[1];
	$img ='http://'.$Url["host"].$imglink[1];
	$Url = parse_url($img);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, 0, 0, $_GET["proxy"],$pauth);
	$headerend = strpos($page,"\r\n\r\n");
	$pass_img = substr($page,$headerend+4);
	$pngstart = strpos($pass_img,"PNG");
	$pass_img = substr($pass_img,$pngstart-1);
	write_file($download_dir."uploadbox_captcha.png", $pass_img);
	$randnum = rand(10000, 100000);
	
	$img_data = explode("\r\n\r\n", $page);
	$header_img = $img_data[0];
	
	

	print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"{$download_dir}uploadbox_captcha.png?id=".$randnum."\" >$nn";
	print	"<input name=\"link\" value=\"$LINK\" type=\"hidden\">$nn";
	print	"<input type='hidden' name='code' value=$code>$nn";
	print	"<input name=\"ub\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"cookie\" value=\"$cookie\" type=\"hidden\">$nn";
	print	"<input name=\"name\" value=\"$name[1]\" type=\"hidden\">$nn";
	print	"<input name=\"enter\" type=\"text\" >";
	print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";
}