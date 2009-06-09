<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["megashares"]["user"] && $premium_acc["megashares"]["pass"]))
{
	$post = Array();
	$post["lc_email"] = (($_GET["premium_user"] && $_GET["premium_pass"]) ? $_GET["premium_user"] : $premium_acc["megashares"]["user"]);
	$post["lc_pin"] = (($_GET["premium_user"] && $_GET["premium_pass"]) ? $_GET["premium_pass"] : $premium_acc["megashares"]["pass"]);
	$post["lc_signin"] = "Sign-In";
	$Url = parse_url("http://www.megashares.com/");
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"], $pauth);
	is_page($page);
	is_present($page, "Failed login message:", "Wrong username or password");
	is_notpresent($page, "Set-Cookie: linkcard=", "Cannot use premium account");
	preg_match_all("/Set-Cookie: ([^\;]+)/", $page, $matches);
	$cookies = implode("; ", $matches[1]);
	$Url = parse_url($LINK);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	is_present($page, "This link requires a password to continue:", "This file is password protected");
	is_present($page, "Link was deleted as it was not downloaded", "File not found");
	is_present($page, "Invalid link", "File not found");
	is_present($page, "Link not found", "File not found");
	is_present($page, "Link was removed", "File not found");
	if (preg_match('%id="dlink".*href="(http://[^\"]+)%', $page, $href))
	{
		$Url = parse_url($href[1]);
		$FileName = basename($href[1]);
		insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookies)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&cookie=".urlencode($cookies)."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
	}
	else
	{
		html_error("Download link not found", 0);
	}
}
else
{
	$ms = $_POST['ms']; 
	if($ms == "ok"){
		$captcha = $_POST["captcha"];
		$cookies = $_POST["cookies"];
		$fquery = $_POST["fquery"];
		$randnum = $_POST["randnum"];
		$pass = $_POST["pass"];
		$mhost = $_POST["mhost"];
		$dllink = "http://".$_POST["dllink"];
		$rndtime = $_POST["rndtime"];
		$name = $_POST["name"];
		
		$final = "http://".$mhost.$fquery."&rs=check_passport_renewal&rsargs[]=".$captcha."&rsargs[]=".$randnum."&rsargs[]=".$pass."&rsargs[]=replace_sec_pprenewal&rsrnd=".$rndtime;

		$Url = parse_url($final);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"],$pauth);

		if(preg_match('/Thank you for reactivating your passport/', $page)){
			$Url = parse_url($dllink);
			$FileName = $name;
			
			insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&cookie=".urlencode($cookies)."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
		}else{
			html_error("An error occured", 0);
		}
	}else{
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
		is_page($page);
		preg_match_all('/Set-Cookie: *(.+);/', $page, $cook);
		$cookies = implode(';', $cook[1]);
		
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "index.php?".$Url["query"] : ""), $Referer, $cookies, 0, 0, $_GET["proxy"],$pauth);
		is_page($page);
		is_present($page, "This link requires a password to continue:", "This file is password protected");
		is_present($page, "Link was deleted as it was not downloaded", "File not found");
		is_present($page, "Invalid link", "File not found");
		is_present($page, "Link not found", "File not found");
		is_present($page, "Link was removed", "File not found");
		is_present($page,"This link's <u>filesize is larger</u> than what you have left on your Passport.");
		is_present($page, "You can download again when your download completes", "This IP address is already downloading a file");
		
		preg_match_all('/Set-Cookie: *(.+);/', $page, $cook);
		$cookies = implode(';', $cook[1]);
		
		preg_match('%id="dlink".*http://(.*?)"%', $page, $dllink);
		preg_match('%Filename:&nbsp;<strong>(.+?)</%', $page, $name);
		
		if(preg_match('/Your Passport needs to be reactivated/', $page)){
			preg_match('/var request_uri *= *"(.*)";/', $page, $fquery);
			preg_match('/src="index\.php\?secgfx=gfx&random_num=(.+?)"/', $page, $randnum);
			preg_match('/name="passport_num".*value="(.*?)"/', $page, $pass);
			
			$img = "http://".$Url["host"]."/index.php?secgfx=gfx&random_num=".$randnum[1];
			$mhost = $Url["host"];
			
			$Url = parse_url($img);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookies, 0, 0, $_GET["proxy"],$pauth);
			$headerend = strpos($page,"\r\n\r\n");
			$pass_img = substr($page,$headerend+4);
			write_file($download_dir."megashare_captcha.png", $pass_img);
			
			print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\" name=\"msform\">$nn";
			print	"<b>Enter the passport reactivation code in the graphic, then hit the 'Reactivate Passport' button.</b><br><br>$nn";
			print	"<img src=\"{$download_dir}megashare_captcha.png?id=".rand(10000, 100000)."\" >$nn";
			print	"<input name=\"link\" value=\"$LINK\" type=\"hidden\">$nn";
			print	"<input name=\"mhost\" value=\"$mhost\" type=\"hidden\">$nn";
			print	"<input name=\"fquery\" value=\"$fquery[1]\" type=\"hidden\">$nn";
			print	"<input name=\"randnum\" value=\"$randnum[1]\" type=\"hidden\">$nn";
			print	"<input name=\"pass\" value=\"$pass[1]\" type=\"hidden\">$nn";
			print	"<input name=\"cookies\" value=\"$cookies\" type=\"hidden\">$nn";
			print	"<input name=\"dllink\" value=\"$dllink[1]\" type=\"hidden\">$nn";
			print	"<input name=\"name\" value=\"$name[1]\" type=\"hidden\">$nn";
			print	"<input name=\"captcha\" type=\"text\" >$nn";
			print "<input type=\"hidden\" name=\"rndtime\">\n";
			print	"<input name=\"ms\" value=\"ok\" type=\"hidden\">$nn";
			print "<script language=\"JavaScript\">function check() { document.msform.rndtime.value=new Date().getTime(); return true; }</script>$nn";
			print	"<input name=\"Submit\" onclick=\"return check()\" value=\"Reactivate Passport\" type=\"submit\"></form>";
		}else{
			$dllink = "http://".$dllink[1];
			$Url = parse_url($dllink);
			$FileName = $name[1];
					
			insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&cookie=".urlencode($cookies)."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
		}
	}
}
?>