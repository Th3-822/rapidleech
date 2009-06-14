<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
				
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$cookie=biscotti($page);
	is_present($page,"File Not Found");
	
	if(preg_match('%FileSend -(\r|\n)*(.*)(\r|\n)*</title>%i', $page, $fname))
		{
		$FileName = $fname[2];
		}
		
	preg_match('/action="(.*dl\.php\?.*?)"/i', $page, $loc);
	preg_match_all('/\w{40,44}/i', $page, $comb);
	$act=cut_str($loc[0],'action="','"');
	$Href = $act;
	$Url = parse_url($Href);
	$FileName = !$FileName ? basename($Url["path"]) : $FileName;
	$post = array();
	$post[$comb[0][0]] = $comb[0][1];

insert_location("$PHP_SELF?filename=none&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($LINK)."&post=".urlencode(serialize($post))."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : ""));
	
	 function biscotti($content) {
        is_page($content);
        preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
        foreach ($matches[0] as $coll) {
        $bis.=cut_str($coll,"Set-Cookie: ","; ")."; ";    
        }return $bis;}
 // written by kaox 10/05/09
?>