<?php

if (!defined('RAPIDLEECH'))
{
  require_once("index.html");
  exit;
  }
if ($_POST["step"]==1){
    
$LINK=$_POST["link"];
$cookie = $_POST['cookie'];
$FileName = $_POST['filename'];
$Url=parse_url($LINK);
	
insert_location("$PHP_SELF?filename=".urlencode($FileName)."&force_name=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&cookie=".urlencode($cookie)."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : ""));
    
}else{			
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
if (cutter($page,"Location: ","\r")) html_error( "Probably the link is typed incorrect or old , verify it in your browser." , 0 );
is_present($page,"file does not exist", "The requsted file does not exist on this server.", 0);
$cookie=GetCookies($page);
$FileName=cut_str($page,"Name: </strong>","</font>");
$ss = <<<HTML
<html>
<head>
<title>FormLogin</title>
</head>
<body bgcolor="#FFFFFF" text="#000000">
<form method="post" name="plink" action="$PHP_SELF">
<input id="link" name="link" type="hidden">
<input type="hidden" name="cookie" value="$cookie" >
<input type="hidden" name="step" value="1" > 
<input type="hidden" name="filename" value="$FileName">
</form>
HTML;

preg_match('/(var wannaplayagameofpong[\s\S]+?)function/i',$page,$scr)  ;
//$script1=cutter($page,'var','function',3);
$script1=$scr[1];
$var=trim(cutter($script1,'var','=',2));
$script1=str_replace($var,"encoded",$script1);
$script=$ss.'<script language="Javascript">'.$script1.'document.getElementById("link").value=encoded; document.plink.submit();</script>' ;
insert_timer("10", "Waiting link timelock.","",true);
echo ($script);
}

function cutter($str, $left, $right,$cont=1)
	{
    for($iii=1;$iii<=$cont;$iii++){
	$str = substr ( stristr ( $str, $left ), strlen ( $left ) );
	}
    $leftLen = strlen ( stristr ( $str, $right ) );
    $leftLen = $leftLen ? - ($leftLen) : strlen ( $str );
    $str = substr ( $str, 0, $leftLen );
    return $str;
}

/*************************\
 WRITTEN BY KAOX 03-oct-09
\*************************/

?>