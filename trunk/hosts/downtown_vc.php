<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
  
	$vc = $_POST['vc'];
  if($vc == "ok"){
	$post = array();
	$post["code"] = $_POST["code"];
	$post["sc"] = $_POST["sc"];
	$post["rand"] = $_POST["rand"];
	$post["fname"] = $_POST["fname"];
	$post["id"] = $_POST["id"];
	$post["act"] = $_POST["act"];
	$Referer = $_POST["link"];
	$host = "http://downtown.vc/";
	$Url = parse_url($host);
	$FileName = $_POST["fname"];

  insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&post=".urlencode(serialize($post))."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".$_POST["link"].($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

}else{
 
 $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
  is_page($page);

  insert_timer(20, "Waiting link timelock");
  
if(preg_match('%(http://downtown\.vc/captchas/.+?)"%', $page, $img)){
	$img_link = $img[1];
}else{
	html_error("Error[getImagecode]", 0);
}
preg_match('/name="act" value="(.+)">/', $page, $actdl);
preg_match('/name="id" value="(.+)">/', $page, $iddl);
preg_match('/name="fname" value="(.+)">/', $page, $namedl);
preg_match('/name="rand" value="(.+)">/', $page, $randdl);
preg_match('/name="sc" value="(.+)">/', $page, $scdl);

$mlink = "http://".$Url["host"].$Url["path"];

	print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"$img_link\">$nn";
	print	"<input type=\"text\" name=\"code\">$nn";
	print	"<input name=\"link\" value=\"$mlink\" type=\"hidden\">$nn";
	print	"<input name=\"vc\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"act\" value=\"$actdl[1]\" type=\"hidden\">$nn";
	print	"<input name=\"id\" value=\"$iddl[1]\" type=\"hidden\">$nn";
	print	"<input name=\"fname\" value=\"$namedl[1]\" type=\"hidden\">$nn";
	print	"<input name=\"rand\" value=\"$randdl[1]\" type=\"hidden\">$nn";
	print	"<input name=\"sc\" value=\"$scdl[1]\" type=\"hidden\">$nn";
	print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";
}
?>