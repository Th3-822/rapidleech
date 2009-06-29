<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
$hotfile_username="hgemini";  // username
$hotfile_password="gemini";  // password

if ($hotfile_username & $hotfile_password){
$in=parse_url("http://hotfile.com/login.php");
$post=array();
$post["returnto"]="/";
$post["user"]=$hotfile_username;
$post["pass"]=$hotfile_password;
$page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), "http://hotfile.com/", 0, $post, 0, $_GET["proxy"],$pauth);	
preg_match('/auth=\w{64}/i', $page, $ook);
$cook=$ook[0];
if(!$cook){
html_error("Login Failed , Bad username/password combination.",0);
}
$Url =parse_url($Referer);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cook, 0, 0, $_GET["proxy"],$pauth); 
preg_match('/http:\/\/.+get[^"\']+/i', $page, $dwn);
$Url =parse_url($dwn[0]);   
$FileName = basename($Url["path"]);
insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
 }
else
  {
$hf = $_POST['hf'];
if($hf == "ok"){
	$post = array();
	$post["action"] = "checkcaptcha";
	$post["captchaid"] = $_POST["captchaid"];
	$post["hash1"] = $_POST["hash1"];
	$post["hash2"] = $_POST["hash2"];
	$post["captcha"] = $_POST["captcha"];
	$Referer = $_POST["link"];
	

	$Url = parse_url($Referer);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
    
     preg_match('/http:\/\/.+get[^\'"]+/i', $page, $down);
     $LINK=rtrim($down['0']);
     if ($LINK==""){html_error("You may forgot the security code or it might be wrong!",0);}
     $Url =parse_url($LINK);
     $FileName = basename($Url["path"]);
     $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth); 
     preg_match('/Location: *(.+)/', $page, $redir);
     if (strpos($redir[1],"http://")===false) {html_error("Server problem. Please try again after",0);}
     $redirect=rtrim($redir[1]);
     $Url = parse_url($redirect);
     insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}else{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
    is_present($page,"File not found","File not found, the file is not present or bad link","0");
    is_present($page,"You are currently downloading","You are currently downloading. Only one connection with server allow for free users","0");
   preg_match_all('/timerend=d\.getTime\(\)\+(\d+)/i', $page, $arraytime); 
  $wtime=$arraytime[1][1]/1000;	
      if ($wtime > 0 ) {
      $dowait = true;
  insert_timer($wtime, "You reached your hourly traffic limit"); 
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
      insert_timer($wait, "Waiting timelock");   
      $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, $post, 0, $_GET["proxy"],$pauth);  
      preg_match('/http:\/\/.+get[^\'"]+/i', $page, $down);
      $LINK=rtrim($down['0']);
  if ($LINK=="") {
        $nofinish=true;
        if(preg_match('/\/captcha\.php[^"\']+/i', $page, $img)){
		$imglink="http://hotfile.com".$img[0];	
		$Url = parse_url($imglink);
		$imgpage = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $free_link, $cookie, 0, 0, $_GET["proxy"],$pauth);
        $headerend = strpos($imgpage,"JFIF");
        $pass_img = substr($imgpage,$headerend-6);
        $imgfile=$download_dir."hotfile_captcha.jpg"; 
        if (file_exists($imgfile)){ unlink($imgfile);} 
		write_file($imgfile, $pass_img);
	    }else{
		html_error("Error get captcha", 0);
	    }
     
        $captchaid=cut_str($page,"captchaid value=",">");
		$hash1=cut_str($page,"hash1 value=",">");
		$hash2=cut_str($page,"hash2 value=",">");

	print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"$imgfile\">$nn";
	print	"<input name=\"hash1\" type=\"hidden\" value=\"$hash1\" />";
	print	"<input name=\"hash2\" type=\"hidden\" value=\"$hash2\" />";
	print	"<input name=\"captchaid\" type=\"hidden\" value=\"$captchaid\" />";
	print	"<input name=\"link\" value=\"$Referer\" type=\"hidden\">$nn";  
	print	"<input name=\"hf\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"captcha\" type=\"text\" >";
	print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";
}
if (!$nofinish){
  $Url =parse_url($LINK);
   $FileName = basename($Url["path"]);
   $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, 0, 0, 0, $_GET["proxy"],$pauth); 
     preg_match('/Location: *(.+)/i', $page, $redir);
     if (strpos($redir[1],"http://")===false) {html_error("Server problem. Please try again after",0);}
     $redirect=rtrim($redir[1]);
     $Url = parse_url($redirect);
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
  }
  }
/*************************\  
written by kaox 21/06/2009 rev.2
\*************************/
?>