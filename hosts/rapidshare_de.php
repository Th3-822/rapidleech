<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["rs_de"]["user"] && $$premium_acc["rs_de"]["pass"]))
	{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	is_present($page,"File not found");
	is_present($page,"This file has been deleted");
	is_present($page,"Inactivity-timeout exceeded");
	is_present($page,"unavailable due to technical-maintenance", "Download-Server unavailable due to maintenance");
	is_present($page,"unavailable due to hardware-problems", "Server unavailable due to hardware-problems");
					
	$FileName = basename(trim(cut_str($page, 'name="uri" value="', '"')));
	!$FileName ? $FileName = basename($Url["path"]) : "";
	$auth = $_GET["premium_user"] ? base64_encode($_GET["premium_user"].":".$_GET["premium_pass"]) : base64_encode($premium_acc["rs_de"]["user"].":".$premium_acc["rs_de"]["pass"]);
					
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth, $auth);
	is_page($page);
					
	if (stristr($page,"Location:"))
		{
		$Href = trim(cut_str($page,"Location:","\n"));
		$Url = parse_url($Href);
						
		insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET['split'] ? $_GET['partSize'] : "")."&method=".$_GET['method']."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
		}
	else
		{
		html_error("Cannot use premium account");
		}
	}
else
	{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	is_present($page,"File not found");
	is_present($page,"This file has been deleted");
	is_present($page,"Inactivity-timeout exceeded");
	is_present($page,"unavailable due to technical-maintenance", "Download-Server unavailable due to maintenance");
	is_present($page,"unavailable due to hardware-problems", "Server unavailable due to hardware-problems");
					
	$post = array();
	$post["uri"] = $Url["path"];
	$post["dl.start"] = "Free";
					
	$Href = trim(cut_str($page, '<form action="', '"'));
	$Url = parse_url($Href);
					
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : "") ,$LINK , 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
					
	is_present($page, "is not allowed to use the free-service anymore today","No more free downloads from this IP today");
	is_present($page, "This file exceeds your download-limit","Download limit exceeded");
	is_present($page, "KB in one hour","Download limit exceeded");
	is_present($page, "is already downloading a file","Your IP-address is already downloading a file");
					
	if (stristr($page, "Want to download more?"))
		{
		$minutes = trim(cut_str($page, "Or wait", "minute"));
		if ($minutes)
			{
			html_error("Download limit exceeded. You have to wait ".$minutes." minutes until the next download.");
			}
		else
			{
			html_error("Download limit exceeded.");
			}
		}

	if(stristr($page, "Too many users downloading right now") || stristr($page, "Too many connections"))
		{
		html_error("Too many users are downloading right now");
		}
					
	$countDown = trim(cut_str($page, "var c =", ";"));				
	$code = urldecode(cut_str($page, "unescape('", "'"));
					
	if (!$code)
		{
		html_error('Error getting access code');
		}
					
	$access_image_url = trim(cut_str($code,'<img src="','">'));
					
	if (!$access_image_url)
		{
		html_error('Error getting access image url');
		}
					
	if ($images_via_php === true)
		{
		$code = str_replace($access_image_url, $PHP_SELF."?image=".urlencode(trim(cut_str($code, '<img src="', '">')))."&referer=".urlencode($Url["scheme"]."://".$Url["host"]."/"), $code);
		}
						
	$FileAddr = trim(cut_str($code, '<form name="dl" action="', '"'));
	$Href = parse_url($FileAddr);
	$FileName = basename($Href["path"]);
				
	if (!$FileAddr)
		{
		html_error("Error getting download link");
		}
	
	$code = str_replace($FileAddr, $PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : ""), $code);				
	$capthatag = cut_str($code,'here: <input','>');
	$capthatag = cut_str($capthatag,'name="','"');
					
	preg_match_all("/http:\/\/dl(.*).rapidshare.de\/(.*)".$FileName."/iU", $code, $matches);
					
	if (!$matches)
		{
		html_error("Error getting available server's list");
		}
					
	for ($i = 0; $i < count($matches[0]); $i++)
		{
		$Url = parse_url($matches[0][$i]);
		$code = str_replace("document.dl.action='".$matches[0][$i], "document.dl.host.value='".$Url["host"], $code);
		}
	
	$code = str_replace("</form>", $nn, $code);
	$code.= "<input type=\"hidden\" name=\"filename\" value=\"".urlencode($FileName)."\">$nn<input type=\"hidden\" name=\"link\" value=\"".urlencode($LINK)."\">$nn<input type=\"hidden\" name=\"referer\" value=\"".urlencode($Referer)."\">$nn<input type=\"hidden\" name=\"saveto\" value=\"".$_GET["path"]."\">$nn<input type=\"hidden\" name=\"host\" value=\"".$Href["host"]."\">$nn<input type=\"hidden\" name=\"path\" value=\"".urlencode($Href["path"])."\">$nn";
	$code.= ($_GET["add_comment"] == "on" ? "<input type=\"hidden\" name=\"comment\" value=\"".urlencode($_GET["comment"])."\">$nn" : "")."<input type=\"hidden\" name=\"email\" value=\"".($_GET["domail"] ? $_GET["email"] : "")."\">$nn<input type=\"hidden\" name=\"partSize\" value=\"".($_GET["split"] ? $_GET["partSize"] : "")."\">$nn";
	$code.= "<input type=\"hidden\" name=\"method\" value=\"".$_GET["method"]."\">$nn<input type=\"hidden\" name=\"proxy\" value=\"".($_GET["useproxy"] ? $_GET["proxy"] : "")."\">$nn".($pauth ? "<input type=\"hidden\" name=\"pauth\" value=\"".$pauth."\">$nn" : "");
	$code.= "</form>";
	$code = str_replace('type="submit"', 'type="submit" onclick="return check()"', $code);
	$js_code = "<script language=\"JavaScript\">".$nn."function check() {".$nn."var imagecode=document.dl.$capthatag.value;".$nn."var path=document.dl.path.value;".$nn;
	$js_code.= 'if (imagecode == "") { window.alert("You didn\'t enter the image verification code"); return false; }'.$nn.'else {'.$nn.'document.dl.path.value=path+escape("?'.$capthatag.'="+imagecode);'.$nn.'return true; }'.$nn.'}'.$nn.'</script>'.$nn;
	
	insert_new_timer($countDown, rawurlencode($code), "Download-Ticket reserved.", $js_code);
	}
?>