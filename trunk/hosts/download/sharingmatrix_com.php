<?php

if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

  
if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["sharingmatrix"]["user"] && $premium_acc["sharingmatrix"]["pass"]))
	{
	echo ('<b><center style="color: #FF0000"><span style="background-color: #F8F8B6">Use premium mode</span></center></b><br>');
	$lg='http://sharingmatrix.com/ajax_scripts/login.php?email='.$premium_acc["sharingmatrix"]["user"].'&password='.$premium_acc["sharingmatrix"]["pass"].'&remember_me=false';
	$Url=parse_url($lg);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://sharingmatrix.com/login", 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$cookie = "PHPSESSID=".cut_str ( $page ,'Set-Cookie: PHPSESSID=' ,';' );
	 $snap = cut_str ( $page ,"\r\n\r\n" ,"\r\n\r\n" );
	 $st=explode("\r\n",$snap);
     $st= join("",$st);
	if( $st !== "110" ){
	html_error( "Bad username/password combination" , 0 );
	}
    $Url=parse_url($LINK);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$snap = cut_str ( $page ,"sUrl='http://" ,"'" );
	is_present($page,"File has been deleted", "File has been deleted", 0);
	if($snap) html_error("download link not foun , please verify the link in your browser" , 0 );
	
	$FileName = cut_str ( $page ,'link_name=' ,'&' );
	$Url=parse_url("http://".$snap);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$sv = cut_str ( $page ,'serv:"' ,'"' );
	$hs = cut_str ( $page ,'hash:"' ,'"' );
	$dwn=$sv."/download/".$hs."/0/";
	$Url=parse_url($dwn);
	
	$loc = "$PHP_SELF?filename=" . urlencode ( $FileName ) . 
		"&force_name=".urlencode($FileName) .
		"&host=" . $Url ["host"] . 
		"&port=" . $Url ["port"] . 
		"&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . 
		"&referer=" . urlencode ( $Referer ) . 
		"&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . 
		"&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . 
		"&method=" . $_GET ["method"] . 
		"&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . 
		"&saveto=" . $_GET ["path"] . 
		"&link=" . urlencode ( $LINK ) . 
		           ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") .
		            $auth . 
		           ($pauth ? "&pauth=$pauth" : "") . 
		"&cookie=" . urlencode($cookie) ;
	insert_location ( $loc );
	
	 }
	else
	{
	echo ('<b><center style="color: #00FF00"><span style="background-color: #F8F8B6">Use guest mode</span></center></b><br>');
	if( $_POST['step'] == "1")
	{
		
	$Url=parse_url("http://sharingmatrix.com/ajax_scripts/verifier.php");
	$post=array();
	$post["?&code"]= $_POST["captcha"];
    $cookie=urldecode($_POST["cookie"]); 
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$snap = substr ( stristr ( $page, "\r\n\r\n" ), strlen ( "\r\n\r\n" ) );
	$st=explode("\r\n",$snap);
    $st= join("",$st);
	if( $st !== "510" ){
	html_error( "Captcha incorrect! Return to main and reattempt" , 0 );
	}
	$Url=parse_url("http://sharingmatrix.com/ajax_scripts/dl.php");
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	preg_match('/(\d{7})/i', $page, $mh);
	$did=$mh[0];
	$id=$_POST["id"];
	$FileName = urldecode($_POST["filename"]);
	
    
	$Url=parse_url("http://sharingmatrix.com/ajax_scripts/_get.php?link_id=".$id."&link_name=".$FileName."&dl_id=".$did."&password=");
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);	
	$sv = cut_str ( $page ,'serv:"' ,'"' );
	$hs = cut_str ( $page ,'hash:"' ,'"' );
	$dwn=$sv."/download/".$hs."/".$did."/";
	$Url=parse_url($dwn);
		$loc = "$PHP_SELF?filename=" . urlencode ( $FileName ) . 
		"&force_name=".urlencode($FileName) .
		"&host=" . $Url ["host"] . 
		"&port=" . $Url ["port"] . 
		"&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . 
		"&referer=" . urlencode ( $Referer ) . 
		"&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . 
		"&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . 
		"&method=" . $_GET ["method"] . 
		"&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . 
		"&saveto=" . $_GET ["path"] . 
		"&link=" . urlencode ( $LINK ) . 
		           ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") .
		            $auth . 
		           ($pauth ? "&pauth=$pauth" : "") . 
		"&cookie=" . urlencode($cookie) ;
	insert_location ( $loc );
	
	}else{
	
	
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
    is_present($page,"File has been deleted", "File has been deleted", 0);
	is_present($page,"already downloading file", "You are already downloading file. Only premium users can download several files at the same time.", 0);
    if($snap) html_error("download link not foun , please verify the link in your browser" , 0 );
	
	$cookie = GetCk($page);
	preg_match('/\/file\/(\d+)/i', $LINK, $mh);
	$id = $mh[1];
	$req="http://sharingmatrix.com/ajax_scripts/download.php?type_membership=free&link_id=".$id ;
	$Url=parse_url($req);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$link_name = cut_str ( $page ,"link_name = '" ,"'" );
	$img_url="http://sharingmatrix.com/include/crypt/cryptographp.inc.php?cfg=0&sn=PHPSESSID&";
	$cookie2 = $cookie."; cryptcookietest=1";
	$Url=parse_url($img_url);	
	insert_timer(5);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie2, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$headerend = strpos($page,"\r\n\r\n");
	$pass_img = substr($page,$headerend+9);
	write_file($options['download_dir']."sharingmatrix_captcha.jpg", $pass_img);
	
	 			$code = '<div align=center ><form method="post" action="'.$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "").'">'.$nn;
	 			$code .= '<input type="hidden" name="step" value="1">'.$nn;
	 			$code .= '<input type="hidden" name="link" value="'.urlencode($LINK).'">'.$nn;
				$code .= '<input type="hidden" name="id" value="'.$id.'">'.$nn;
				$code .= '<input type="hidden" name="filename" value="'.urlencode($link_name).'">'.$nn;
				$code .= '<input type="hidden" name="cookie" value="'.urlencode($cookie).'">'.$nn;
	 			$code .= 'Please enter : <img src="'.$options['download_dir'].'sharingmatrix_captcha.jpg?'.rand(1,10000).'"><br><br>'.$nn;
	 			$code .= '<input type="text" name="captcha"> <input type="submit" value="Download">'.$nn;
	 			$code .= '</form></div>';
                echo ($code);
	    	 }
	}
  function GetCk($content)
{
	$parthead = preg_replace('/Set-Cookie: .+deleted.+|Set-Cookie: .+=;.+/i', '', $content);
	preg_match_all('/Set-Cookie: (.*);/U',$parthead,$temp);
	$cookie = $temp[1];
	$cook = implode('; ',$cookie);
	return $cook;
}

/*************************\
 WRITTEN BY KAOX 08-oct-09
\*************************/
?>