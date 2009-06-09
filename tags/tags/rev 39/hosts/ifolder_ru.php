<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

$ifol = $_POST['ifol'];

if($ifol == "ok"){
	
	$post = array();
	$post["confirmed_number"] = $_POST["confirmed_number"];
	$post["session"] = $_POST["session"];
	$post["action"] = $_POST["action"];
	$mlink = $_POST["mlink"];
	
	
	$Url = parse_url($mlink);
	
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	if(preg_match('/ocation: ?(.+)/', $page, $loc)){
		$Url = parse_url($loc[1]);
	}else{
		html_error("Error[getRedirect2]", 0);
	}
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	if(preg_match('/document\.location=\'(.+?)\'/', $page, $final)){
		$Url = parse_url($final[1]);
		$FileName = !$FileName ? basename($Url["path"]) : $FileName;
	}else{
		html_error("Error[getFinalLink]", 0);
	}
	  
	 	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($loc[1])."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

}else{
 
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	if(preg_match('/Файл помещен в архив/', $page)){
		html_error("Files placed in the archives!", 0);
	}
	if(preg_match('/удален !!!/', $page)){
		html_error("File deleted", 0);
	}
	
	if(preg_match('/href="(.+?)".+сюда/', $page, $adver)){
		$Url = parse_url($adver[1]);
	}else{
		html_error("Error[getAd.Link]", 0);
	}
	
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	if(preg_match_all('%(http://.+cgi/g\.cgi\?p=.+?)>%', $page, $ad)){
		$Url = parse_url($ad[1][0]);
	}else{
		html_error("Ad-URL not found.", 0);
	}
	
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	if(preg_match('/ocation: ?(.+)/', $page, $loc)){
		$Url = parse_url($loc[1]);
	}else{
		html_error("Error[getRedirect]", 0);
	}
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	

	
	if(preg_match('%src="(/ints/frame/\?session=.+)"%', $page, $sess)){
		$mlink = "http://ints.ifolder.ru".$sess[1];
		$Url = parse_url($mlink);
	}else{
		html_error("Error[getSessLink]", 0);
	}
	
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	if(preg_match('/var delay = (.+);/', $page, $count)){
		$countnum = $count[1];
		insert_timer($countnum, "Waiting link timelock");
	}else{
		html_error("Error[getCountNum]", 0);
	}
	
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	
		
	if(preg_match('%<img src="(/random/images/\?session=.+)"%', $page, $img)){
		$img_link = "http://ints.ifolder.ru".$img[1];
		preg_match('/name="session" value=(.+)>/', $page, $ses);
		$session = $ses[1];
	}else{
		html_error("Error[getImg]", 0);
	}
	
	print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"$img_link\">$nn";
	print	"<input name=\"link\" value=\"http://ifolder.ru\" type=\"hidden\">$nn";
	print	"<input name=\"mlink\" value=\"$mlink\" type=\"hidden\">$nn";
	print	"<input type=hidden name=action value=1>$nn";
	print	"<input name=\"ifol\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"session\" value=\"$session\" type=\"hidden\">$nn";
	print	"<input name=\"confirmed_number\" type=\"text\" >";
	print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";
}
?>