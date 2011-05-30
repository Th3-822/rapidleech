<?php

if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

	
		if( $_POST['step'] == "1")
		{

		$post=unserialize(urldecode($_POST['post']));
		$post['recaptcha_response_field']=$_POST['captcha'];

		insert_timer(60);
	
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, 0, $post, 0, $_GET["proxy"],$pauth);
		is_page($page);
		is_present($page,"Wrong captcha", "Wrong captcha . Go to main page and reattempt", 0);
		is_present($page,"Expired session", "Expired session . Go to main page and reattempt", 0);
		
		$snap = cut_str ( $page ,'Filename:' ,'</table>' );
		$dwn = cut_str ($snap ,'href="' ,'"' );
		if (!$dwn) html_error( "Error getting download link" , 0 ); 
		$Url=parse_url($dwn);
		$FileName=basename($dwn);
	
	
		$loc = "$PHP_SELF?filename=" . urlencode ( $FileName ) .
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
		           ($pauth ? "&pauth=$pauth" : "");
		insert_location ( $loc );
	 
		}else{
				  
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
		is_page($page);
		is_present($page,"File Not Found", "File Not Found", 0);
		
		$id = cut_str($page,'name="id" value="','"');
		$fname = cut_str($page,'name="fname" value="','"');
		
		$post=array();
		$post['op']='download1';
		$post['usr_login']='';
		$post['id']=$id;
		$post['fname']=$fname;
		$post['referer']='';
		$post['method_free']=' Free Download ';
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, 0, $post, 0, $_GET["proxy"],$pauth);
		is_page($page);
		
		$rand = cut_str($page,'name="rand" value="','"');
		$referer = cut_str($page,'referer" value="','"');
		$down_direct = cut_str($page,'own_direct" value="','"');
		
		unset($post);
		$post['op']='download2';
		$post['id']=$id;
		$post['rand']=$rand;
		$post['referer']=$referer;
		$post['method_free']=' Free Download ';
		$post['method_premium']='';
		$post['down_direct']='1';
		
		$Url=parse_url("http://api.recaptcha.net/challenge?k=6LdzWwYAAAAAAAzlssDhsnar3eAdtMBuV21rqH2N");
		
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
		is_page($page);
		is_present($page,"Expired session", "Expired session . Go to main page and reattempt", 0);
		
		$cookie = GetCookies($page);
		$ch = cut_str ( $page ,"challenge : '" ,"'" );
		$Url=parse_url("http://api.recaptcha.net/image?c=".$ch);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
		$headerend = strpos($page,"\r\n\r\n");
		$pass_img = substr($page,$headerend+4);
		write_file($options['download_dir']."oron_captcha.jpg", $pass_img);
		
		$post['recaptcha_challenge_field']=$ch;

		$code = '<form method="post" action="'.$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "").'">'.$nn;
		$code .= '<input type="hidden" name="step" value="1">'.$nn;
		$code .= '<input type="hidden" name="post" value="'.urlencode(serialize($post)).'">'.$nn;
		$code .= '<input type="hidden" name="link" value="'.urlencode($LINK).'">'.$nn;
		$code .= '<input type="hidden" name="dwn" value="'.urlencode($dwn).'">'.$nn;
		$code .= 'Please enter : <img src="'.$options['download_dir'].'oron_captcha.jpg?'.rand(1,10000).'"><br><br>'.$nn;
		$code .= '<input type="text" name="captcha"> <input type="submit" value="Download">'.$nn;
		$code .= '</form>';
		echo ($code) ;


	    	 }
/************************************************\
 WRITTEN BY KAOX 03-oct-09
 UPDATE BY KAOX 06-oct-09 ADD SUPPORT TO CAPTCHA
\************************************************/
  
?>