<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
$es = $_POST['es']; 
if($es == "ok"){
	$post = array();
	$post["id"] = $_POST["id"];
	$post["captcha"] = $_POST["captcha"];
	
	$cookie = $_POST["cookie"];
	$Referer = $_POST["referer"];
	$Href = $_POST["flink"];
	$FileName = urldecode($_POST["name"]);
	
	$Url = parse_url($Href);
	$FileName = !$FileName ? basename($Url["path"]) : $FileName;
	
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&cookie=".urlencode($cookie)."&post=".urlencode(serialize($post))."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&method=POST&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : ""));
}else{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	
	is_present($page,'File was deleted');
	is_present($page,'File not found');
	
	$name=cut_str($page,"<title>Download ",",");
	if(preg_match_all("/Set-Cookie: (([^=]+)=[^;]*;)/", $page, $matches))foreach($matches[2] as $k=>$v)$cookies[$v]=$matches[1][$k];
	
	if($count=(cut_str($page,"w='","'")))
	{
		if(!$Url["path"]=cut_str($page,"u='","'")) html_error('Error getting link');
		insert_timer($count, "Waiting link timelock","",true);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $LINK, $cookies, 0, 0, $_GET["proxy"],$pauth);
		if(preg_match_all("/Set-Cookie: (([^=]+)=[^;]*;)/", $page, $matches))foreach($matches[2] as $k=>$v)$cookies[$v]=$matches[1][$k];
	}
	
	if(!$new_url=cut_str($page,"document.location='","'")) 
	{
		is_present($page,'You have downloaded over 150MB during last hour.');
		html_error('Error getting link location');
	}
	$Url = parse_url($new_url);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	if(preg_match_all("/Set-Cookie: (([^=]+)=[^;]*;)/", $page, $matches))foreach($matches[2] as $k=>$v)$cookies[$v]=$matches[1][$k];
	
	$page = cut_str($page,"<div class=\"timer\">","</form>");
	if(!$Href=cut_str($page,'form action="','"')) html_error('Download link not found');
	$id = cut_str($page,'id" value="','"');
	
	if (!$src=cut_str($page,'src="','"'))
	{
		$post["captcha"] = "1";
		$post["id"] = $id;
		$cookie = implode("",$cookies);
		$Referer = $new_url;
		$Url = parse_url($Href);
		$FileName = $name;
		
		insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&cookie=".urlencode($cookie)."&post=".urlencode(serialize($post))."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&method=POST&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : ""));
		exit();
	}
	
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $src, $new_url, $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	list($header,$img)=explode("\r\n\r\n",$page,2);
	$img=preg_replace("%^\w*\r\n%","",$img);
	$cap_img=$download_dir."easyshare_captcha.jpg";
	if(!write_file($cap_img, $img))
	{
		unlink($cap_img);
		write_file($cap_img, $img);
	}
	if(preg_match_all("/Set-Cookie: (([^=]+)=[^;]*;)/", $header, $matches))foreach($matches[2] as $k=>$v)$cookies[$v]=$matches[1][$k];
	
	print "<form method=\"post\" action=\"$PHP_SELF\">$nn";
	print "<b>Please enter code:</b><br>$nn";
	print "<img src=\"$cap_img?".time()."\" >$nn";
	print "<input name=\"link\" value=\"$LINK\" type=\"hidden\">$nn";
	print "<input name=\"flink\" value=\"$Href\" type=\"hidden\">$nn";
	print "<input name=\"referer\" value=\"$new_url\" type=\"hidden\">$nn";
	print "<input type=hidden name=id value=$id>$nn";
	print "<input name=\"es\" value=\"ok\" type=\"hidden\">$nn";
	print "<input name=\"cookie\" value=\"".implode("",$cookies)."\" type=\"hidden\">$nn";
	print "<input name=\"name\" value=\"".urlencode($name)."\" type=\"hidden\">$nn";
	foreach(array("comment","email","partSize","method","proxy","proxyuser","proxypass","path") as $v) print "<input type=\"hidden\" name=\"$v\" value=\"".$_GET[$v]."\">\n";
	print "<input name=\"captcha\" type=\"text\" >";
	print "<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";
}
?>