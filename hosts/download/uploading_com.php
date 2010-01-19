<?php

if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

if (($_GET ["premium_acc"] == "on" && $_GET ["premium_user"] && $_GET ["premium_pass"]) || ($_GET ["premium_acc"] == "on" && $premium_acc ["uploading"] ["user"] && $premium_acc ["uploading"] ["pass"])) 
{
$in=parse_url("http://uploading.com/general/login_form/");
$post=array();
$post["email"]= $_GET ["premium_user"] ? $_GET ["premium_user"] : $premium_acc ["uploading"] ["user"];
$post["password"]= $_GET ["premium_pass"] ? $_GET ["premium_pass"] : $premium_acc ["uploading"] ["pass"];
$page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), "http://uploading.com/login/", 0, $post, 0, $_GET["proxy"],$pauth);
$cookie=GetCookies($page);
if(strpos($cookie,"error=") != false){
html_error("Login Failed , Bad username/password combination.",0);
}
$in=parse_url("http://uploading.com/");
$page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), "http://uploading.com/login/", $cookie, 0, 0, $_GET["proxy"],$pauth);

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
$fileID=cut_str($page,"get_link', file_id: ",",");
$tmp = basename($Url["path"]);
$FileName=str_replace(".html","",$tmp);

$tid=str_replace(".","12",microtime(true));
$sUrl="http://uploading.com/files/get/?JsHttpRequest=".$tid."-xml";
$Url=parse_url($sUrl);

unset($post);
$post["file_id"]=$fileID;
$post["action"]="step_1";
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);

$tid=str_replace(".","12",microtime(true));
$sUrl="http://uploading.com/files/get/?JsHttpRequest=".$tid."-xml";
$Url=parse_url($sUrl);

unset($post);
$post["file_id"]=$fileID;
$post["action"]="step_2";
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);

$dUrl=str_replace("\\","",cut_str($page,'link": "','"'));
if ($dUrl=="") {html_error("Download url error , Please reattempt",0);
}
$Url=parse_url($dUrl);

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK)."&cookie=".urlencode($cookie).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}else
{
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
$fileID = trim ( cut_str($page,'file_id" value="','"') );
$action = trim ( cut_str($page,'action" value="','"') );
$Href_1 = trim ( cut_str($page,'form action="http://uploading.com/files','"') );
$Href_1 = "http://uploading.com/files".$Href_1;

$cookie = GetCookies($page);
$temp = cut_str($page,'<div class="c_1','</div>');
$FileName = trim(cut_str($temp,'<h2>','</h2>'));

is_page($page);
is_present($page, "Sorry, the file requested by you does not exist on our servers.");
is_present($page, "We are sorry, the file was removed either by its owner or due to the complaint received" );
if (stristr ( $page, "Sorry, you can download only one file per" ))
{
	$minutes = trim ( cut_str ( $page, "Sorry, you can download only one file per ", " minutes." ) );
	if ($minutes)
	{
		html_error ( "Download limit exceeded. Sorry, you can download only one file per <font color=black><span id='waitTime'>$minutes</span></font> minutes.Please try again later or acquire a premium membership.", 0 ); 
	}
	else
	{
		html_error ( "Download limit exceeded. Please try again later or acquire a premium membership.", 0 ); 
	}
}

$Url = parse_url( $Href_1 );
//$code=explode("/",$Url["path"]);
//$Url["path"]="/files/get/".$code[count($code)-2]."/";

$post = Array();
$post["action"] = $action;
$post["file_id"] = $fileID;
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);

is_page($page);

is_present($page, "Requested file not found");

/*
preg_match_all('/start_timer\((.*)\)/i', $page,$tm);
//$waitt=$tm[1][1];
//insert_timer($waitt+10); 
*/

preg_match_all('/start_timer\((\d+)\)/i', $page,$tm);
$count = trim ( $tm[1][0] );

insert_timer( $count, "Waiting link timelock", "", true );

$tid=str_replace(".","12",microtime(true));
$sUrl="http://uploading.com/files/get/?JsHttpRequest=".$tid."-xml";
$Url=parse_url($sUrl);

unset($post);
$post["file_id"]=$fileID;
$post["action"]="get_link";
$post["pass"]="";
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);

$dUrl=str_replace("\\","",cut_str($page,'answer": { "link": "','"'));

if ($dUrl=="") {html_error("Download url error , Please wait for some minute and reattempt",0);
}
$Url=parse_url($dUrl);

$loc = "$PHP_SELF?cookie=".urlencode($cookie)."&filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""); 
insert_location( $loc );

}
/**************************************************\  
WRITTEN by kaox 24-may-2009
UPDATE by kaox  29-nov-2009
UPDATE by rajmalhotra  20 Jan 2010
\**************************************************/
?>