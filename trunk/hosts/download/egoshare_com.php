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
	$cookie = trim($_POST['cookie']);
	$Referer = trim($_POST["referer"]);
	$Href = $_POST["act_url"];
	$Url = parse_url(trim($Href));
	//$DebugRequest = true;
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page,'Captcha number error or expired return to main and reattempt');
	
    $click = 'king_last_click='.cut_str ( $page ,'king_last_click=' ,';' );
	$cookie= preg_replace("/king_last_click=\w+/i",$click,$cookie) ;
	
	$wait = cut_str ( $page ,"var timeout='" ,"'" );
	insert_timer($wait, "<b>Timer :</b>");
	$snap = cut_str ( $page ,'loadfilelink.decode("' ,'"' );
	if ($dlink=base64_decode($snap)){
	$Url=parse_url($dlink);
	}else{html_error( "Error decoding link" , 0 );	}
	
	$FileName = cut_str ( $dlink ,'name=' ,' ' );
	
	$loc = "{$_SERVER['PHP_SELF']}?filename=" . urlencode ( $FileName ) . 
		"&host=" . $Url ["host"] . 
		"&port=" . $Url ["port"] . 
		"&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . 
		"&referer=" . urlencode ( $Referer ) . 
		"&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . 
		"&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . 
		"&method=" . $_GET ["method"] . 
		"&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . 
		"&saveto=" . $_GET ["path"] . 
		"&link=" . urlencode ( $link ) . 
		           ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") .
		            $auth . 
		           ($pauth ? "&pauth=$pauth" : "") . 
		"&cookie=" . urlencode($cookie) .
		"&post=" . urlencode ( serialize ( $post ) );
	insert_location ( $loc );
	
} else {
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	if (stristr($page,'DL_FileNotFound')) {
		html_error("File not found",0);
	}
	$cookie = GetCookies($page);
	preg_match('/<form name=myform action="(.*)"/',$page,$act_url);
	$act_url = $act_url[1];
	$img = 'http://www.egoshare.com/captcha.php';
	$Url = parse_url($img);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, 0, 0, $_GET["proxy"],$pauth);

	$headerend = strpos($page,"\r\n\r\n");
	$pass_img = substr($page,$headerend+4);
	write_file($download_dir."egoshare_captcha.jpg", $pass_img);
	$randnum = rand(10000, 100000);

	$img_data = explode("\r\n\r\n", $page);
	$header_img = $img_data[0];

	

	print 	"<form method=\"post\" action=\"$PHP_SELF\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"{$download_dir}egoshare_captcha.jpg?id=".$randnum."\" >$nn";
	print	"<input name=\"link\" value=\"$LINK\" type=\"hidden\">$nn";
	print	"<input name=\"referer\" value=\"$LINK\" type=\"hidden\">$nn";
	print	"<input name=\"act_url\" value=\"$act_url\" type=\"hidden\">$nn";
	print	"<input name=\"eg\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"cookie\" value=\"$cookie\" type=\"hidden\">$nn";
	print	"<input name=\"captchacode\" type=\"text\" >";
	print	"<input name=\"submit\" value=\"Download\" type=\"submit\"></form>";
}
/*
WRITTEN by kaox 24/09/2009
*/

?>