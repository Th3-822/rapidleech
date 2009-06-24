<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }


//Use PREMIUM?
if (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["filefactory"]["user"] && $premium_acc["filefactory"]["pass"]))
{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page, 'File Not Found', 'Error - File was not found!');
	$post = array();
	$post['email'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc["filefactory"]["user"]  ;
	$post['password'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc["filefactory"]["pass"];
	$post['redirect'] = $LINK;
	$page = geturl("www.filefactory.com", 80, "/", 0, 0, $post, 0, $_GET["proxy"], $pauth);
	is_page($page);
	if (!preg_match('%(ff_membership=.+); expires%', $page, $lcook)) html_error('Error getting login-cookie', 0);
	if (!preg_match('%ocation: (.+)\r\n%', $page, $redir)) html_error('Error getting redirect', 0);
	$Url = parse_url($redir[1]);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $lcook[1], 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	if (!preg_match('%(http://dl\d{3}\.filefactory\.com/dlp/.+)">Download with FileFactory Premium%U', $page, $redir2)) html_error('Error getting redirect 2', 0);
	$Url = parse_url($redir2[1]);
	$FileName = basename($Url['path']);
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($lcook[1])."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".$redir2[1].($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
	//szal 18-jun-09
}
else
//Use FREE instead?
{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	preg_match('|id="freeBtnContainer">\n\t\t\t\t\t\t\t<form action="(.*)" method="post">|U', $page, $out);
	$one = str_replace('/dlf', 'http://www.filefactory.com/dlf', $out[1]);
	$Url = parse_url($one);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	preg_match('|href="(http.*)">Click here to begin your download</a>|u', $page, $link);
	$file = $link[1];
	$Href = $file;
	$Url = parse_url($Href);
	$wait = "60";
	insert_timer($wait, "Preparing Your File");
	$FileName = !$FileName ? basename($Url["path"]) : $FileName;

	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : ""));

/*filefactory download plugin by mrbrownee70, created june 22, 2009 */
}

//older free download below
/*if ($_GET["step"] == "1")
  {
 $cook = $_POST['cookie'];
  
  
  $post["captchaText"] = $_POST["captcha"];
  $post['captchaID'] = $_POST['captchaID'];
  
  $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cook, $post, 0, $_GET["proxy"],$pauth);
  is_page($page);
  //var_dump(nl2br(htmlentities($page)));exit;
  
	preg_match('/<a href="(.+?)" class="download">/', $page, $dllink);
	$Href = trim($dllink[1]);
	$Url = parse_url($Href);
  
  if (!is_array($Url))
    {
    html_error("Download link not found", 0);
    }
  
  $FileName = "attachment";
  
  insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($premium_cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".$_POST["link2"].($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
else
  {
  
  $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
  is_page($page);
  if (stristr($page,'ocation:')) {
	preg_match('/ocation: (.*)/',$page,$loc);
	$loc = "http://".$Url['host'].$loc[1];
	$Url = parse_url(trim($loc));
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$LINK = trim($loc);
  }
  is_present($page, "This file is no longer available");
  is_notpresent($page, "Download for free", "Download link not found");
  // Get first download link (download with free)
  preg_match('/<a class="download" href="(.*)">Download for free/',$page,$href);
  $Href = "http://".$Url['host'].$href[1];
  $Url = parse_url(trim($Href));
  $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, 0, 0, 0, $_GET["proxy"],$pauth);
  is_page($page);
  preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	$cook = implode(';',$cookie);
  // Find captcha image
  preg_match('/<img class="captchaImage" src="(.*)"/',$page,$capimg);
  if (!$capimg) html_error("Cannot find captcha image",0);
  $access_image_url = "http://".$Url['host'].$capimg[1];
  // Captcha ID
  preg_match('/<input id="captchaID" name="captchaID" type="hidden" value="(.*)"/',$page,$cID);
  $cID = $cID[1];
    
  print "<form name=\"dl\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\" method=\"post\">\n";
  print "<input type=\"hidden\" name=\"link\" value=\"".urlencode($Href)."\">\n<input type=\"hidden\" name=\"link2\" value=\"".urlencode($LINK)."\">\n<input type=\"hidden\" name=\"referer\" value=\"".urlencode($Href)."\">\n<input type=\"hidden\" name=\"f\" value=\"$f\">\n<input type=\"hidden\" name=\"h\" value=\"$h\">\n<input type=\"hidden\" name=\"b\" value=\"$b\">\n<input type=\"hidden\" name=\"step\" value=\"1\">\n";
  print "<input type=\"hidden\" name=\"comment\" id=\"comment\" value=\"".$_GET["comment"]."\">\n<input type=\"hidden\" name=\"email\" id=\"email\" value=\"".$_GET["email"]."\">\n<input type=\"hidden\" name=\"partSize\" id=\"partSize\" value=\"".$_GET["partSize"]."\">\n<input type=\"hidden\" name=\"method\" id=\"method\" value=\"".$_GET["method"]."\">\n";
  print "<input type=\"hidden\" name=\"proxy\" id=\"proxy\" value=\"".$_GET["proxy"]."\">\n<input type=\"hidden\" name=\"proxyuser\" id=\"proxyuser\" value=\"".$_GET["proxyuser"]."\">\n<input type=\"hidden\" name=\"proxypass\" id=\"proxypass\" value=\"".$_GET["proxypass"]."\">\n<input type=\"hidden\" name=\"path\" id=\"path\" value=\"".$_GET["path"]."\">\n";
  print "<input type='hidden' name='cookie' value='$cook' /><input type='hidden' name='captchaID' value='$cID' />";
  print "<h4>Enter <img src=\"$access_image_url\"> here: <input type=\"text\" name=\"captcha\" size=\"4\">&nbsp;&nbsp;<input type=\"submit\" onclick=\"return check()\" value=\"Download File\"></h4>\n";
  print "<script language=\"JavaScript\">".$nn."function check() {".$nn."var imagecode=document.dl.captcha.value;".$nn.'if (imagecode == "") { window.alert("You didn\'t enter the image verification code"); return false; }'.$nn.'else { return true; }'.$nn.'}'.$nn.'</script>'.$nn;
  print "</form>\n</body>\n</html>";
  }*/
?>