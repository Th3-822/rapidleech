<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

$eg = $_POST['eg'];
if ($eg == 'ok') {
	$post = array();
	$post['captchacode'] = $_POST['captchacode'];
	$post['2ndpage'] = 1;
	$cook = trim($_POST['cookie']);
	$Referer = trim($_POST["referer"]);
	$Href = $_POST["act_url"];
	$Url = parse_url(trim($Href));
	//$DebugRequest = true;
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cook, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page,'Captcha number error or expired');
	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	$cook .= implode(';',$cookie);
	
	var_dump(nl2br(htmlentities($page)));exit;
} else {
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	if (stristr($page,'DL_FileNotFound')) {
		html_error("File not found",0);
	}
	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	$cook = implode(';',$cookie);
	preg_match('/<form name=myform action="(.*)"/',$page,$act_url);
	$act_url = $act_url[1];
	$img = 'http://www.egoshare.com/captcha.php';
	$Url = parse_url($img);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cook, 0, 0, $_GET["proxy"],$pauth);

	$headerend = strpos($page,"\r\n\r\n");
	$pass_img = substr($page,$headerend+4);
	write_file($download_dir."egoshare_captcha.jpg", $pass_img);
	$randnum = rand(10000, 100000);

	$img_data = explode("\r\n\r\n", $page);
	$header_img = $img_data[0];

	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	if ($cookie) {
		$cook .= ';'.implode(';',$cookie);
	}
	
	print 	"<form method=\"post\" action=\"$PHP_SELF\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"{$download_dir}egoshare_captcha.jpg?id=".$randnum."\" >$nn";
	print	"<input name=\"link\" value=\"$LINK\" type=\"hidden\">$nn";
	print	"<input name=\"referer\" value=\"$LINK\" type=\"hidden\">$nn";
	print	"<input name=\"act_url\" value=\"$act_url\" type=\"hidden\">$nn";
	print	"<input name=\"eg\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"cookie\" value=\"$cook\" type=\"hidden\">$nn";
	print	"<input name=\"captchacode\" type=\"text\" >";
	print	"<input name=\"submit\" value=\"Download\" type=\"submit\"></form>";
}

?>