<?php

if (!defined('RAPIDLEECH'))
{
    require_once("index.html");
    exit;
}

if (($_GET ["premium_acc"] == "on"  && $_GET ["premium_pass"]) || ($_GET ["premium_acc"] == "on" && $premium_acc ["letitbit"] ["pass"])) {

 $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, 0, 0, 0, $_GET["proxy"],$pauth);
    is_page($page);
    is_present($page, "The requested file was not found");
    is_present($page, "Gesuchte Datei wurde nicht gefunden", "The requested file was not found");
    is_present($page, "Запрашиваемый файл не найден", "The requested file was not found");

    $cookie=biscottiDiKaox($page);
    $PreForm = cut_str ( $page ,'password here:' ,'</form>' );

    $uid5 = cut_str($PreForm,'uid5" value="','"');
    $uid = cut_str($PreForm,'uid" value="','"');
    $name = cut_str($PreForm,'name="name" value="','"');
    $pin = cut_str($PreForm,'pin" value="','"');
    $realuid = cut_str($PreForm,'realuid" value="','"');
    $realname = cut_str($PreForm,'realname" value="','"');
    $host = cut_str($PreForm,'host" value="','"');
    $ssserver = cut_str($PreForm,'ssserver" value="','"');
    $sssize = cut_str($PreForm,'sssize" value="','"');

    $UrlAct="http://letitbit.net/sms/check2.php";
    $post['pass']=$_GET ["premium_pass"] ? $_GET ["premium_pass"] : $premium_acc ["letitbit"] ["pass"];
    $post['uid5']=$uid5;
    $post['uid']=$uid;
    $post['name']=$name;
    $post['pin']=$pin;
    $post['realuid']=$realuid;
    $post['realname']=$realname;
    $post['host']=$host;
    $post['ssserver']=$ssserver;
    $post['sssize']=$sssize;
    $post['optiondir']='';
    $Url=parse_url($UrlAct);

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
    is_page($page);
    $dlink=cutter($page,"Download Master","</table>",3);
    $dwnl=cutter($dlink,"<a href='","'",1);
    $Url = parse_url($dwnl);
    $FileName = basename($dwnl);
insert_location("index.php?filename=".urlencode($FileName)."&force_name=".urlencode($FileName)."&host=".$Url["host"]."&port=".$Url["port"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));


}else{
if ($_POST['step'] == 1) {

    $UrlAct="http://letitbit.net/download3.php";
    $post = unserialize(urldecode($_POST["post"]));
	$post['uid2']=$post['uid'];
    $post['cap']=$_POST["captcha"];
    $cookie = urldecode($_POST['cookie']);
    $Url = parse_url($UrlAct);
    $Referer = "http://letitbit.net/download4.php";

    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);

    is_page($page);

    if(preg_match('/http:\/\/\w{2}\.[^"\']+/', $page, $nextPageArray))
    {
        $nextPage = $nextPageArray[0];
		$Url=parse_url($nextPage);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $UrlAct, $cookie, 0, 0, $_GET["proxy"],$pauth);
		 $wait = cut_str ( $page ,'y = ' ,';' );
        $act2 = cut_str ( $page ,'window.location.href="' ,'"' );
        $Url=parse_url($act2);
		insert_timer($wait);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $act, $cookie, 0, 0, $_GET["proxy"],$pauth);
    }
    else
    {
        html_error("Could not find frame.", 0);
    }
    $snap = cut_str ( $page ,'id="links' ,'</div>' );
    $dwn= cut_str ( $snap ,'href="' ,'"' );
    $Url = parse_url($dwn);
    $FileName = basename($Url["path"]);
    
insert_location("index.php?filename=".urlencode($FileName)."&force_name=".urlencode($FileName)."&host=".$Url["host"]."&port=".$Url["port"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

} else {
    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
    is_page($page);

    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, 0, 0, 0, $_GET["proxy"],$pauth);
    is_page($page);
    is_present($page, "The requested file was not found");
    is_present($page, "Gesuchte Datei wurde nicht gefunden", "The requested file was not found");
    is_present($page, "Запрашиваемый файл не найден", "The requested file was not found");

    $cookie=biscottiDiKaox($page);
    $FreeForm = cut_str ( $page ,'id="dvifree">' ,'</form>' );

	$act = "http://".cut_str ( $page ,'form action="http://' ,'"' );
    $uid = cutter($FreeForm,'name="uid" value="','"',2);
    $md5crypt = cut_str($FreeForm,'="md5crypt" value="','"');
   // $uid2 = cut_str($FreeForm,'name="uid2" value="','"');
    $uid5 = cut_str($FreeForm,'name="uid5" value="','"');
    $name = cut_str($FreeForm,'name="name" value="','"');
    $pin = cut_str($FreeForm,' name="pin" value="','"');
    $realuid = cut_str($FreeForm,'e="realuid" value="','"');
    $realname = cut_str($FreeForm,'="realname" value="','"');
    $host = cut_str($FreeForm,'name="host" value="','"');
    $ssserver = cut_str($FreeForm,'="ssserver" value="','"');
    $sssize = cut_str($FreeForm,'me="sssize" value="','"');


    $post['uid']=$uid;
    $post['md5crypt']=$md5crypt;
    $post['frameset']='Download file';
  //  $post['uid2']=$uid2;
    $post['uid5']=$uid5;
 //   $post['uid']=$uid2;
    $post['name']=$name;
    $post['pin']=$pin;
    $post['realuid']=$realuid;
    $post['realname']=$realname;
    $post['host']=$host;
    $post['ssserver']=$ssserver;
    $post['sssize']=$sssize;
    $post['optiondir']='';
    $post['fix']='1';

	$Url=parse_url($act);
 $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);
	$cookie.= " ".GetCookies($page);
	
if (stristr($page,"cap.php?"))
    {
    $imagecode = cut_str($page,"<img src='http://letitbit.net/cap.php?jpg=","'");
    $img = 'http://letitbit.net/cap.php?jpg='.$imagecode;
    $Url = parse_url($img);
    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, 0, 0, $_GET["proxy"],$pauth);

    $headerend = strpos($page,"\r\n\r\n");
    $pass_img = substr($page,$headerend+9);
    write_file($download_dir."letitbit_captcha.jpg", $pass_img);

    $code = '<form method="post" action="'.$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "").'">'.$nn;
    $code .= '<input type="hidden" name="link" value="'.urlencode($LINK).'">'.$nn;
    $code .= '<input type="hidden" name="post" value="'.urlencode(serialize($post)).'">'.$nn;
    $code .= '<input type="hidden" name="step" value="1">'.$nn;
    $code .= '<input type="hidden" name="cookie" value="'.urlencode($cookie).'">'.$nn;
    $code .= 'Please enter : <img src="'.$download_dir.'letitbit_captcha.jpg?'.rand(1,10000).'"><br><br>'.$nn;
    $code .= '<input type="text" name="captcha"> <input type="submit" value="Download">'.$nn;
    $code .= '</form>';
    echo $code;

    }
  else
    {
    html_error("Image code not found", 0);
    }

}
}
function biscottiDiKaox($content)
 {
     preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
     foreach ($matches[1] as $coll) {
     $bis0=split(";",$coll);
     $bis1=$bis0[0]."; ";
     $bis2=split("=",$bis1);
     $cek=" ".$bis2[0]."="; 
     if(strpos($bis1,"=deleted") || strpos($bis1,$cek.";")) {
     }else{
    if  (substr_count($bis,$cek)>0)
    {$patrn=" ".$bis2[0]."=[^ ]+";
    $bis=preg_replace("/$patrn/"," ".$bis1,$bis);     
    } else {$bis.=$bis1;}}}  
    $bis=str_replace("  "," ",$bis);     
    return rtrim($bis);
 }
 // tweaked cutstr with pluresearch functionality
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
 WRITTEN BY KAOX 05-dec-09
\*************************/
?>
