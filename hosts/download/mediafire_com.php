<?php

if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}
if($_POST["passfile"]){
$trynumber=$_POST["trynumber"]; ;    
$post=array();
$post["downloadp"]=$_POST["downloadp"];
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
is_page($page);
}else{
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
preg_match('/Location:.*error/i', $page) ? html_error("Invalid File", 0) : '';
if(preg_match('/Location: (.*)/i', $page, $redir))
{
	$Href = trim($redir[1]);
	$Url = parse_url($Href);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
}
}
$cookie = GetCookies($page);

if(preg_match('/cu\([^qk](.*?)\);/', $page, $values))
{
	$values = str_replace("'", '', $values[1]);
	$value = explode(',', $values);
	$qk = $value[0];
	$pk = $value[1];
	$r = $value[2];
}
/*if(preg_match('/cu\((.*?)\);/', $page, $values)){
$value = preg_split('/\',?\'?/', $values[1], -1, PREG_SPLIT_NO_EMPTY);
$qk = $value[0];
$pk = $value[1];
$r = $value[2];
}*/
else{
echo("<div style=\"text-align: center\"><br><br>");
$trynumber ++;
if($trynumber>1){
echo ("<div style=\"text-align: center\">The file password entered not match, please correct the error</div>");
}else{
echo ("<div style=\"text-align: center\">The file is password protect, please enter the password</div>");     
} 
$code = '<div style="text-align: center"><form method="post" action="'.$PHP_SELF.'">'.$nn;
$code .= '<input type="text" name="downloadp"> <input type="submit" value="Send password">'.$nn;
$code .= '<input type="hidden" name="trynumber" value="'.$trynumber.'">'.$nn;
$code .= '<input type="hidden" name="passfile" value="true">'.$nn;
$code .= '<input type="hidden" name="link" value="'.urlencode($LINK).'">'.$nn;
$code .= '</form></div>';
echo $code;
die;
}

$Href = "http://www.mediafire.com/dynamic/download.php?qk=$value[0]&pk=$value[1]&r=$value[2]";
$Url = parse_url($Href);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

$mL = cut_str($page, "var mL='", "';");
$mH = cut_str($page, "var mH='", "';");
$mY = cut_str($page, "var mY='", "';");
preg_match('%http://"\+mL\+\'/\'\s\+(.+)\+\s\'g/%', $page, $parts);

$temps = explode("+",$parts[1]);
foreach ($temps as $temp)
{
	if (empty($temp)) continue;
	preg_match('/'.trim($temp).' ?= ?\'(.*?)\';/', $page, $temp2);
	$mpath1.= $temp2[1];
}

$Href = 'http://'.$mL.'/'.$mpath1.'g/'.$mH.'/'.$mY;
$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;
insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

// edited by mrbrownee70
//updated by szalinski 15-Sep-09
//update by kaox 01-oct-09  - support for password protected file
//update by szalinski 03-nov-09 (mediafire layout change)

?>