<?php
{

	if (!defined('RAPIDLEECH'))
	{
	  require_once("index.html");
	  exit;
	}

	    $Url["path"] = str_replace("/video/","/download/",$Url["path"]);
	    $Url["path"] = str_replace("/audio/","/download/",$Url["path"]);

		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
		is_page($page);
		
		is_present($page,"file-404","File Not Found");
		$cookie=GetCookies($page);
		
		$post = array();
		$post["referer2"] = "";
		$post["download"] = 1;
		$post["imageField.x"] = rand(1,140);
		$post["imageField.y"] = rand(1,20);

		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, $post, 0, $_GET["proxy"],$pauth);
		is_page($page);
	    
	    $enclink= cut_str($page,"link_enc=new Array('","')");
	    $linkdown = preg_replace('/[,\']/i', '', $enclink);
	  
		if($linkdown){
			$Url = parse_url($linkdown);
		}else{html_error("Link not found",0);}
		insert_timer("60");
	    /*
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, $post, 0, $_GET["proxy"],$pauth);
		is_page($page);
	    
		if (preg_match('/Array\((.+)\);link/', $page, $jsaray)) {
			$linkenc = $jsaray[1];
			$zsharelink = preg_replace('/\'[, ]?[ ,]?/six', '', $linkenc);
			$Url = parse_url($zsharelink);
		}elseif(preg_match('/<param name="URL" value="(.+)?">/', $page, $audio)){
			$zsharelink =$audio[1]
			$Url = parse_url($zsharelink);
		}
	    */

	$FileName = basename($Url["path"]);
		
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

	// Update by kaox 12/09/2009}

?>