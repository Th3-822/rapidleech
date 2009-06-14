<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

if($_POST["ffi"] == "ok"){
 $cookie=$_POST["cookie"];
$ddlreq= $_POST["link"].$_POST["captcha_check"].",0d149x=0";
$Url = parse_url($ddlreq);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], $loc, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_present($page, "show_captcha", $strerror = "The captha inserted is wrong", 0) ;
$Url = parse_url("http://ifile.it/dl");
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $loc, $cookie, 0, 0, $_GET["proxy"],$pauth);
preg_match('/<a name="req_btn".*href="(.*)"/', $page, $loc);
$Href = $loc[1];
$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;

insert_location("$PHP_SELF?cookie=".urlencode($cook)."&filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

}else{
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
$cookie = $temp[1];
$cook = implode(';',$cookie);
$Referer = $LINK;
preg_match('/ocation: (.*)/',$page,$loc);
$loc = trim($loc[1]);
$Url = parse_url($loc);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, $cook, 0, 0, $_GET["proxy"],$pauth);
preg_match('/<input.*name="file_key".*value="(.*)"/',$page,$file_key);
$file_key = $file_key[1];
$dllink = "http://ifile.it/download:dl_request?it=".$file_key.",type=na,esn=1";
$Url = parse_url($dllink);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], $loc, $cook, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

if (strpos($page,'"message":"ok"'))
{
$Url = parse_url($dllink);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], $loc, $cook, 0, 0, $_GET["proxy"],$pauth);
$Url = parse_url($loc);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $loc, $cook, 0, 0, $_GET["proxy"],$pauth);
$Referer = $loc;
preg_match('/<a name="req_btn".*href="(.*)"/', $page, $loc);
$Href = $loc[1];
$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;
echo " <b>Filename : $FileName </b></br>";

insert_location("$PHP_SELF?cookie=".urlencode($cook)."&filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

} elseif (strpos($page,'show_captcha')){
$rnd=mt_rand(10000000,99999999);
$rnd='0.'.$rnd.($rnd>>1);
$access_image_url='http://ifile.it/download:captcha?'.$rnd;
$Url = parse_url($access_image_url);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], "http://ifile.it/dl", $cook, 0, 0, $_GET["proxy"],$pauth);
        

        $headerend = strpos($page,"JFIF");
        $pass_img = substr($page,$headerend-6);
        $imgfile=$download_dir."ifile_captcha.jpg"; 
        if (file_exists($imgfile)){ unlink($imgfile);} 
		write_file($imgfile, $pass_img);
        $link ="http://ifile.it/download:dl_request?it=".$file_key.",type=simple,esn=0,0d149=";
		
	print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"$imgfile\">$nn";
	print	"<input name=\"cookie\" value=\"$cook\" type=\"hidden\">$nn";
	print	"<input name=\"ffi\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"link\" value=\"$link\" type=\"hidden\">$nn";	
	print	"<input name=\"captcha_check\" type=\"text\" >";
	print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";

    
    
}
}
// written by kaox 24/05/09
?>