<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

if($_POST["ffi"] == "ok")
{
	$cookie=$_POST["cookie"];
	$ddlreq= $_POST["link"].$_POST["captcha_check"];
	$Url = parse_url($ddlreq);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], 'http://ifile.it/dl', $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page, '"captcha":"retry"', $strerror = "The captcha inserted is wrong", 0) ;
	$Url = parse_url("http://ifile.it/dl");
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 'http://ifile.it/dl', $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	if (!preg_match('%href="(http://s\d+\.ifile\.it/.+/.+/\d+/.+\..{3})"%U', $page, $dlink)) html_error('Final Download Link Not Found!');
	$Href = $dlink[1];
	$Url = parse_url($Href);
	$FileName = !$FileName ? basename($Url["path"]) : $FileName;

	insert_location("$PHP_SELF?cookie=".urlencode($cook)."&filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

}
else
{
	/*if (!$_POST ["premium_acc"] == "on" || !$premium_acc ['ifile_it'] ['user'] || !$premium_acc ['ifile_it'] ['pass']) html_error('iFile.it Free Account Logins Required. Please set them in the config.php.');

	$page = sslcurl('https://secure.ifile.it/account:process_signin', array('usernameFld' => $premium_acc ['ifile_it'] ['user'], 'passwordFld' => $premium_acc ['ifile_it'] ['pass'], 'submitBtn' => 'continue'));
	$cook = GetCookies($page);
	if (!preg_match('%(http://ifile.it/\?timestamp=\d+)">you have successfully signed in%', $page, $redir)) html_error('Invalid Authentication');
	$Referer = $LINK;
	$loc = trim($redir[1]);
	$Url = parse_url($loc);
	*/
	$Url = parse_url($LINK);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, $cook, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$cook .= '; ' . GetCookies($page);
	$Url = parse_url($LINK);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, $cook, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	preg_match('%ocation: (.+)\r\n%', $page, $rdir);
	$Url = parse_url($rdir[1]);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, $cook, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	if (!preg_match('%x64=(\d+)&%U', $page, $file_key)) html_error('File Key Not Found!');
	$dllink = "http://ifile.it/download:dl_request?x64=".$file_key[1]."&type=na&esn=1";
	$Url = parse_url($dllink);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], $rdir[1], $cook, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	if (strpos($page,'"captcha":"none"'))
	{
		$Url = parse_url('http://ifile.it/dl');
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], 0, $cook, 0, 0, $_GET["proxy"],$pauth);
		is_page($page);
		if (!preg_match('%href="(http://s\d+\.ifile\.it/.+/.+/\d+/.+\..{3})"%U', $page, $dlink)) html_error('Final Download Link Not Found!');
		$Url = parse_url($dlink[1]);
		$FileName = basename($Url['path']);
		insert_location("$PHP_SELF?cookie=".urlencode($cook)."&filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
	}
	elseif (strpos($page,'"captcha":"simple"'))
	{
		$rnd=mt_rand(10000000,99999999);
		$rnd='0.'.$rnd.($rnd>>1);
		$access_image_url='http://ifile.it/download:captcha?'.$rnd;
		$Url = parse_url($access_image_url);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], "http://ifile.it/dl", $cook, 0, 0, $_GET["proxy"],$pauth);
		is_page($page);

		$headerend = strpos($page,"JFIF");
		$pass_img = substr($page,$headerend-6);
		$imgfile=$download_dir."ifile_captcha.jpg";
		if (file_exists($imgfile)) unlink($imgfile);
		write_file($imgfile, $pass_img);
		$link ="http://ifile.it/download:dl_request?x64=".$file_key[1]."&type=simple&esn=1&9c16d=";

		print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
		print	"<b>Please enter code:</b><br>$nn";
		print	"<img src=\"$imgfile\">$nn";
		print	"<input name=\"cookie\" value=\"$cook\" type=\"hidden\">$nn";
		print	"<input name=\"ffi\" value=\"ok\" type=\"hidden\">$nn";
		print	"<input name=\"link\" value=\"$link\" type=\"hidden\">$nn";
		print	"<input name=\"captcha_check\" type=\"text\" >";
		print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";
	}
}

function sslcurl ($link, $post = 0, $cookie = 0, $refer = 0)
{
	$mm = !empty($post) ? 1 : 0;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U;Windows NT 5.1; de;rv:1.8.0.1)\r\nGecko/20060111\r\nFirefox/1.5.0.1');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, $mm);
	curl_setopt($ch, CURLOPT_POSTFIELDS, formpostdata($post));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_REFERER, $refer);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie) ;
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	// curl_setopt ( $ch , CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	$contents .= curl_exec($ch);
	// $info = curl_getinfo($ch);
	// $stat = $info['http_code'];
	curl_close($ch);
	return $contents;
}
// written by kaox 24/05/09
//updated by szalinski 16-Sep-09
?>