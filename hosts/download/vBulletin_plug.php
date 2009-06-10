<?php
if (!defined('RAPIDLEECH'))
  {
  	require_once("index.html");
 	exit;
  }
if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["vBulletin_acc"]["user"] && $premium_acc["vBulletin_acc"]["pass"]))
  {
	$post = Array();
	$post["vb_login_username"] = ($_GET["premium_user"] ? $_GET["premium_user"] : $premium_acc["vBulletin_acc"]["user"]);
	$post["vb_login_password"] = ($_GET["premium_pass"] ? $_GET["premium_pass"] : $premium_acc["vBulletin_acc"]["pass"]);
	$post["do"] = "login";
	
	$last_part = strrchr($LINK, '/');
	$index_url = explode($last_part, $LINK);
	$index_url = $index_url[0].'/';
	
	$Url = parse_url($index_url);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	
	if(preg_match('/form action="(.*?)".*md5hash/i', $page, $login_file))
	  {
	  	$login_url = $index_url.'/'.$login_file[1];
	  }
	else
	  {
		html_error("Login page not found.", 0);		
	  }
		
	$Url = parse_url($login_url);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page, "You have entered an invalid username or password.");
	
	if(preg_match_all('/Set-Cookie: *(.*?);/i', $page, $cook))
	  {
		$cookie = implode(';', $cook[1]);
		$Url = parse_url($LINK);
		
		insert_location("$PHP_SELF?filename=".$Url["host"]."_attachment&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
	  }
	else
	  {
		html_error("Cookie not found.", 0);		
	  }
  }
else
  {
	html_error("Set Your Forum Account in config.php.", 0);
  }
?>