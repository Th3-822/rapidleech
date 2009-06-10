<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
  $tu = $_POST['tu'];
 
if($tu == "1"){
	$post = array();
	$post["private_key"] = $_POST["private_key"];
	$post["waited"] = $_POST["waited"];
	$post["pass_test"] = $_POST["pass_test"];
	$post["pass1"] = $_POST["pass1"];
	$post["hncaptcha"] = $_POST["hncaptcha"];
	$post["public_key"] = $_POST["public_key"];
	$cookie = $_POST["cookie"];
	$Href = $_POST["link"];
	$Url = parse_url($Href);
	
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	
	preg_match('/<code>.+<a *href="(.+?)">/', $page, $flink);
	$Href = $flink[1];
	$Url = parse_url($Href);
	$FileName = basename($Url["path"]);
	

	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"])."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

}else{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page, "This file has been password protected by the uploader.", "This file is password protected");

if(preg_match('/Set-Cookie: *(PHP.+?);/', $page, $cook)){
	$cookie = $cook[1];
}else{
	html_error("Cookie not found.", 0);
}
	insert_timer(30, "Waiting link timelock");
	
	preg_match('/name="waited".+value="(.+?)">/', $page, $wait);
	preg_match('/name="pass_test".+value="(.+?)">/', $page, $test);
	preg_match('/name="pass1".+value="(.+?)">/', $page, $pass);
	preg_match('/name="hncaptcha".+value="(.+?)">/', $page, $hn);
	preg_match('/name="public_key".+value="(.+?)">/', $page, $public);
	preg_match('/<img *class="captchapict" *src="(.+?)"/', $page, $imagecode);
	$mlink = "http://".$Url["host"].$Url["path"];
	
	print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\" enctype=\"multipart/form-data\">$nn";
	print	"Enter <img src=\"$imagecode[1]\"> here$nn";
	print	"<input name=\"private_key\" type=\"text\">$nn";
	print	"<input name=\"waited\" value=\"$wait[1]\" type=\"hidden\">$nn";
	print	"<input name=\"pass_test\" value=\"$test[1]\" type=\"hidden\">$nn";
	print	"<input name=\"pass1\" value=\"$pass[1]\" type=\"hidden\">$nn";
	print	"<input name=\"hncaptcha\" value=\"$hn[1]\" type=\"hidden\">$nn";
	print	"<input name=\"public_key\" value=\"$public[1]\" type=\"hidden\">$nn";
	print	"<input name=\"link\" value=\"$mlink\" type=\"hidden\">$nn";
	print	"<input name=\"tu\" value=\"1\" type=\"hidden\">$nn";
	print	"<input name=\"cookie\" value=\"$cookie\" type=\"hidden\">$nn";
	print	"<input type=\"hidden\" name=\"domail\" value=\"".$_GET["domail"]."\">$nn";
	print	"<input type=\"hidden\" name=\"email\" value=\"".($_GET["domail"] ? $_GET["email"] : "")."\">$nn";
	print	"<input type=\"hidden\" name=\"split\" value=\"".$_GET["split"]."\">$nn";
	print	"<input type=\"hidden\" name=\"partSize\" value=\"".($_GET["split"] ? $_GET["partSize"] : "")."\">$nn";
	print	"<input type=\"hidden\" name=\"method\" value=\"".$_GET["method"]."\">$nn";
	print	($_GET["useproxy"] ? "<input type=\"hidden\" name=\"useproxy\" value=\"".$_GET["useproxy"]."\">$nn" : "");
	print	"<input type=\"hidden\" name=\"proxy\" value=\"".($_GET["useproxy"] ? $_GET["proxy"] : "")."\">$nn";
	print	"<input type=\"hidden\" name=\"path\" value=\"".$_GET["path"]."\">$nn";
	print	"<input type=\"hidden\" name=\"add_comment\" value=\"".$_GET["add_comment"]."\">$nn";
	print	($_GET["add_comment"] == "on" ? "<input type=\"hidden\" name=\"comment\" value=\"".urlencode($_GET["comment"])."\">$nn" : "");
	print	($pauth ? "<input type=\"hidden\" name=\"pauth\" value=\"".$pauth."\">$nn" : "");
	print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\">$nn";
	print	"</form>$nn";
}
?>