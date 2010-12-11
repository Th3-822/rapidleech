<?php

if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["hotfile"]["user"] && $premium_acc["hotfile"]["pass"]))
    {
    

	$in=parse_url("http://hotfile.com/login.php");
	$post=array();
	$post["returnto"]="/";
	$post["user"]=$_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc["hotfile"]["user"]  ;
	$post["pass"]=$_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc["hotfile"]["pass"];
	$page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), "http://hotfile.com/", 0, $post, 0, $_GET["proxy"],$pauth);    
	preg_match('/auth=\w{64}/i', $page, $ook);
	$cook = $ook[0];

	$in =parse_url("http://hotfile.com/?cookiecheck=1");
	$page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), "http://hotfile.com/", $cook, 0, 0, $_GET["proxy"],$pauth);    

	is_present($page,"Bad username", "Bad username/password combination.");
	is_present($page,"Upgrade to Premium", "Account has been found. Account no Premium");

	$findpre=strpos($page,'Premium until:');
	if(false===$findpre){
	html_error("Login Failed , Bad username/password combination.",0);
	}

	$Url =parse_url($Referer);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cook, 0, 0, $_GET["proxy"],$pauth); 
	is_present($page,"\r\nfailed\r\n","Your IP blocked by hotfile.com","0");
	is_present($page,"File not found","File not found, the file is not present or bad link","0");
	is_present($page,"due to copyright","This file is either removed due to copyright claim or is deleted by the uploader.","0");
	$findpre=strpos($page,'Set-Cookie: ip=');
	if(false===$findpre){
	html_error("Your IP blocked by hotfile.com",0);
	}


	preg_match('/^HTTP\/1\.0|1 ([0-9]+) .*/',$page,$status);
	if ($status[1] == 200) {
 	preg_match('/http:\/\/.+get\/[^\'"]+/i', $page, $loca);   
	$Href = rtrim($loca[0]);  
	} else{
	preg_match('/Location:.+?\\r/i', $page, $loca);
	$redir = rtrim($loca[0]);
	preg_match('/http:.+/i', $redir, $loca);
	$Href = rtrim($loca[0]);
	}
  
	$Url =parse_url($Href);
	$FileName = basename($Url["path"]);

insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) ."&force_name=".urlencode($FileName)."&host=" . $Url ["host"] . "&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&cookie=" . urlencode ( $cookie ) . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&method=POST&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . "&auth=" . $auth . ($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : "") );

    
}else{

$hf = $_POST['hf'];
if($hf == "ok"){
    $post=unserialize(urldecode($_POST['post']));
    $post["action"] = "checkcaptcha";
    $post["recaptcha_response_field"] = $_POST["captcha"];
    $Referer = $_POST["link"];
    

    $Url = parse_url($Referer);
    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
    is_page($page);

    
    preg_match('/\/\d+\/\w+\/\w+\/[^\'"]+/i', $page, $down);      
    $LINK="http://hotfile.com/get".$down[0];     
     if ($down[0]==""){
     $dsource = cut_str($page,'<h3','</h3');
     $ddw = cut_str($dsource,'href="','"');
     $LINK=$ddw;
     }
     
    if (!stristr($page,"REGULAR DOWNLOAD")){

     $Url =parse_url($LINK);
     $FileName = basename($Url["path"]);
     $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth); 
     preg_match('/Location: *(.+)/', $page, $redir);
     if (strpos($redir[1],"http://")===false) {html_error("Server problem. Please try again after",0);}
     $redirect=rtrim($redir[1]);
     $Url = parse_url($redirect);
     insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
	 exit;
     }
     
}
	if($hf == "ok"){echo  ("<center><font color=red><b>Wrong captcha .Please re-enter</b></font></center>");}  
    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
    
    is_present($page,"File not found","File not found, the file is not present or bad link","0");
    is_present($page,"due to copyright","This file is either removed due to copyright claim or is deleted by the uploader.","0");
    is_present($page,"You are currently downloading","You are currently downloading. Only one connection with server allow for free users","0");
    
   preg_match_all('/timerend=d\.getTime\(\)\+(\d+)/i', $page, $arraytime); 
  $wtime=$arraytime[1][1]/1000;    
      if ($wtime > 0 ) {
      $dowait = true;


	?>
<p><center><div id="dl"><h4>ERROR: Please enable JavaScript.</h4></div></center></p>
<form action="index.php" method="post">
<input type="hidden" name="link" value="<?php echo $LINK; ?>">
<script language="JavaScript">
var c = <?php echo $wtime; ?>;
fc();
function fc() {
	if(c>0) {
		document.getElementById("dl").innerHTML = "You reached your hourly traffic limit. Please wait <b>" + c + "</b> seconds...";
		c = c - 1;
		setTimeout("fc()", 1000);
		}
	else {
		document.getElementById("dl").style.display="none";
		void(document.forms[0].submit());
		}
	}
</script>
</form></body></html>
<?php
exit;


  } 
      $action=cut_str($page,"action value=",">");
      $tm=cut_str($page,"tm value=",">");
      $tmhash=cut_str($page,"tmhash value=",">");
      $wait=cut_str($page,"wait value=",">");
      $waithash=cut_str($page,"waithash value=",">");
      $post=array();
      $post["action"] =$action;
      $post["tm"] = $tm;
      $post["tmhash"] = $tmhash;
      $post["wait"] = $wait;
      $post["waithash"] = $waithash;
  
      $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, $post, 0, $_GET["proxy"],$pauth);  
      preg_match('/\/\d+\/\w+\/\w+\/[^\'"]+/i', $page, $down);      
      $LINK="http://hotfile.com/get".$down[0];
      
    
     if ($down[0]==""){
     $dsource = cut_str($page,'<h3','</h3');
     $ddw = cut_str($dsource,'href="','"');
     $LINK=$ddw;

     }
     
     
  if ($down[0]=="") {
        $nofinish=true;
        
        $Url=parse_url("http://api.recaptcha.net/noscript?k=6LfRJwkAAAAAAGmA3mAiAcAsRsWvfkBijaZWEvkD");
        
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
        is_page($page);
        is_present($page,"Expired session", "Expired session . Go to main page and reattempt", 0);
        
	if(preg_match('/Location: *(.+)/i', $page, $redir )){
	$newreca = trim( $redir[1] );

	$Url = parse_url( $newreca );
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);

		$ch = cut_str ( $page ,'src="image?c=' ,'"' );

	}
        if($ch){	
		$Url=parse_url("http://www.google.com/recaptcha/api/image?c=".$ch);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);

        $headerend = strpos($page,"\r\n\r\n");
        $pass_img = substr($page,$headerend+4);
        $imgfile = $options['download_dir']."hotfile_captcha.jpg";
        
        
       if (file_exists($imgfile)){ unlink($imgfile);} 
        write_file($imgfile, $pass_img);
        }else{
        html_error("Error get captcha", 0);
        }
        unset($post);
        $post['recaptcha_challenge_field']=$ch;
        
	$code = '<center>';
	$code.= "<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	$code.= "<h4>Type the two words:<br><img src=\"$imgfile\"> <br>here:<input name=\"captcha\" type=\"text\" >$nn";
	$code.= "<input name=\"link\" value=\"$Referer\" type=\"hidden\">$nn";
	$code.= '<input type="hidden" name="post" value="'.urlencode(serialize($post)).'">'.$nn;
	$code.= "<input name=\"hf\" value=\"ok\" type=\"hidden\">$nn";
	$code.= "<br><input name=\"Submit\" value=\"Submit\" type=\"submit\"></h4>";

	$code.= "</form></center>";
	$js_code = "".$nn;
	
	if (!$wait)
		{
		print $code.$nn.$nn.$js_code."$nn</body>$nn</html>";
		}
	else
		{
		insert_new_timer($wait, rawurlencode($code), "You will download as a Free User.", $js_code);
		}

}
if (!$nofinish){
	$Url = parse_url($LINK);
	$FileName = basename($Url["path"]);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth); 
	preg_match('/Location: *(.+)/i', $page, $redir);
	if (strpos($redir[1],"http://")===false) {html_error("Server problem. Please try again after",0);}
	$redirect=rtrim($redir[1]);
	$Url = parse_url($redirect);
    insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
  }
  

?>