<?php
 
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}


if($_POST["ffi"] == "ok"){
 $cookie=$_POST["cookie"];
$ttemp= $_POST["link"]  ;
$ddlreq= str_replace("type=na&esn=1","type=simple&esn=1&".$_POST["vimg"]."=".$_POST["captcha_check"],$ttemp);
$Url = parse_url($ddlreq);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], $loc, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_present($page, "show_captcha", $strerror = "The captha inserted is wrong", 0) ;
$Url = parse_url("http://ifile.it/dl");
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $loc, $cookie, 0, 0, $_GET["proxy"],$pauth);

$snap = cut_str ( $page ,'id="req_btn2' ,'>download' );
$Href  = cut_str ( $snap ,'href="' ,'"' );

$Href = $loc[1];
$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;

insert_location("$PHP_SELF?cookie=".urlencode($cook)."&filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

}else{
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
$cook2 = $temp[1];
$cookie = implode(';',$cook2)."; ".$cook;
$Referer = $LINK;
preg_match('/ocation: (.*)/',$page,$loc);
$loc = trim($loc[1]);
$Url = parse_url($loc);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
$__x_fsa = cut_str ( $page ,"var __x_fsa = '" ,"'" );
$__x_fs = cut_str ( $page ,"var __x_fs = '" ,"'" );
$__x_fs2 = cut_str ( $page ,'__x_fs + "' ,'"' );
$__x_c = cut_str ( $page ,"var __x_c = '" ,"'" );
/*
preg_match('/<input.*name="file_key".*value="(.*)"/',$page,$file_key);
$file_key = $file_key[1];
$dllink = "http://ifile.it/download:dl_request?it=".$file_key.",type=na,esn=1";
*/
$dllink = "http://ifile.it/download:dl_request?".$__x_fsa."&type=na&esn=1&".$__x_fs.$__x_fs2  ;

$Url = parse_url($dllink);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], $loc, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

if (strpos($page,'captcha":"none'))
{
    /*
$Url = parse_url($dllink);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], $loc, $cook, 0, 0, $_GET["proxy"],$pauth);
*/
$Url = parse_url($loc);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $loc, $cookie, 0, 0, $_GET["proxy"],$pauth);
$Referer = $loc;
/*
preg_match('/<a name="req_btn".*href="(.*)"/', $page, $loc);
$Href = $loc[1];
*/

$snap = cut_str ( $page ,'id="req_btn2' ,'>download' );
$Href  = cut_str ( $snap ,'href="' ,'"' );

$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;

insert_location("$PHP_SELF?cookie=".urlencode($cook)."&filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

} elseif (strpos($page,'captcha":"simple')){

$rnd='0.'.idnum(16);
$access_image_url='http://ifile.it/download:captcha?'.$rnd;
$Url = parse_url($access_image_url);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].'?'.$Url['query'], "http://ifile.it/dl", $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
	
       
        $headerend = strpos($page,"JFIF");
        $pass_img = substr($page,$headerend-6);
        $imgfile=$download_dir."ifile_captcha.jpg"; 
        if (file_exists($imgfile)){ unlink($imgfile);} 
		write_file($imgfile, $pass_img);
	
		
	print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"$imgfile\">$nn";
	print	"<input name=\"cookie\" value=\"$cookie\" type=\"hidden\">$nn";
	print	"<input name=\"ffi\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"link\" value=\"$dllink\" type=\"hidden\">$nn";
	print	"<input name=\"vimg\" value=\"$__x_c\" type=\"hidden\">$nn";	
	print	"<input name=\"captcha_check\" type=\"text\" >";
	print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";

    
    
}else{
html_error( "If you have already reattempted more times then this plugin not work , probably changes have been made on the page. Report it in the Rapidleech forum" , 0 );
}
}

function idnum ($ll){
$id = "";
for($i=0; $i<$ll; $i++)
$id .= floor(rand(0, 9));
return $id;
}


// written by kaox 24-may-2009
// update by kaox 15-nov-2009

?>