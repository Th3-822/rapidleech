<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
$fg = $_POST['fg']; 
if($fg == "ok"){
	$post = array();
	$post["act"] = $_POST["act"];
	$post["id"] = $_POST["id"];
	$post["fname"] = $_POST["fname"];
	$post["rand"] = $_POST["rand"];
	$post["sc"] = $_POST["sc"];
	$post["code"] = $_POST["code"];
	$Referer = $_POST["link"];
	$FileName = $_POST["fname"];
	
	$Href = "http://filego.net/";
	$Url = parse_url($Href);
	
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&post=".urlencode(serialize($post))."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&method=".$_GET["method"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}else{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	preg_match('/name="act" value="(.*)">/i', $page, $act);
	preg_match('/name="id" value="(.*)">/i', $page, $id);
	preg_match('/name="fname" value="(.*)">/i', $page, $fname);
	preg_match('/name="rand" value="(.*)">/i', $page, $rand);
	preg_match('/name="sc" value="(.*)">/i', $page, $sc);
	preg_match('%<img src="(http://filego.net/captchas/.*?)">%i', $page, $img);
	
	print 	"<center><form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"$img[1]\" >$nn";
	print	"<input name=\"link\" value=\"$LINK\" type=\"hidden\">$nn";
	print	"<input name=\"fname\" value=\"$fname[1]\" type=\"hidden\">$nn";
	print	"<input type=hidden name=id value=$id[1]>$nn";
	print	"<input name=\"fg\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"act\" value=\"$act[1]\" type=\"hidden\">$nn";
	print	"<input name=\"rand\" value=\"$rand[1]\" type=\"hidden\">$nn";
	print	"<input name=\"sc\" value=\"$sc[1]\" type=\"hidden\">$nn";
	print	"<input name=\"code\" type=\"text\" >";
	print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form></center>";
}
?>