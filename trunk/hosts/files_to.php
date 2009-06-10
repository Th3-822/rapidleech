<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
$ft = $_POST['ft']; 
if($ft == "ok"){
	$post = array();
	$post["txt_ccode"] = $_POST["txt_ccode"];
	$cookie = $_POST["cookie"];
	$Referer = $_POST["flink"];
	$Href = $_POST["flink"];
	
	$Url = parse_url($Href);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	
	preg_match('%(http://.*/dl/.*?)"%', $page, $final);
	$Href = $final[1];
	$Url = parse_url($Href);
	$FileName = !$FileName ? basename($Url["path"]) : $FileName;
	
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&cookie=".urlencode($cookie)."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}else{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	preg_match_all('/Set-Cookie: *(.+);/', $page, $cook);
	$cookie = implode(';', $cook[1]);
	
	preg_match('/action="(.*?)"/', $page, $sess);
	$flink = $LINK.$sess[1];
	
	preg_match('%(http://.*PHPSESSID=.*?)"%', $page, $imglink);
	$img = $imglink[1];
	$Url = parse_url($img);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, 0, 0, $_GET["proxy"],$pauth);
	
	$headerend = strpos($page,"\r\n\r\n");
	$pass_img = substr($page,$headerend+4);
	write_file($download_dir."files_to_captcha.jpg", $pass_img);
	$randnum = rand(10000, 100000);
	
	
	print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"{$download_dir}files_to_captcha.jpg?id=".$randnum."\" >$nn";
	print	"<input name=\"link\" value=\"$LINK\" type=\"hidden\">$nn";
	print	"<input name=\"flink\" value=\"$flink\" type=\"hidden\">$nn";
	print	"<input name=\"ft\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"cookie\" value=\"$cookie\" type=\"hidden\">$nn";
	print	"<input name=\"txt_ccode\" type=\"text\" >";
	print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";
}
?>