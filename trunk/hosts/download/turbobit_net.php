<?php

if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

	
if (($_GET["premium_acc"] == "on" && $_GET["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["turbobit"]["pass"]))
{
    $page = geturl("turbobit.net",80, "/", 0, 0, 0, 0, $_GET["proxy"],$pauth);
    preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
    $cookie = $temp[1][2]."; ".$temp[1][1];  
    $lang=parse_url("http://turbobit.net/en");
    $page = geturl($lang["host"], $lang["port"] ? $lang["port"] : 80, $lang["path"].($lang["query"] ? "?".$lang["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
	$post = Array();
	$post["code"] = ($_GET["premium_pass"] ? $_GET["premium_pass"] : $premium_acc["turbobit"]["pass"]);
	$Url = parse_url("http://turbobit.net/payments/getaccess/"); 
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, $post, 0, $_GET["proxy"],$pauth);
	//file_put_contents("turbobit_1.txt", $page);
	is_page($page);
	is_notpresent($page, "Turbo-access granted", "Invalid password");

	$Url = parse_url($LINK);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, 0, 0, $_GET["proxy"],$pauth);
	//file_put_contents("turbobit_2.txt", $page);
	is_page($page);
	//is_present($page, "The file you are looking for is not available", "The file has been deleted");
	
	$dsrg=cut_str($page,'<div class="download-file">','Download file');
	$durl=cut_str($dsrg,'href="','"');
    
	$Url = parse_url("http://turbobit.net".$durl);
    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, 0, 0, $_GET["proxy"],$pauth);
    $locat=cut_str ($page ,"Location: ","\r"); 
	$FileName=cut_str($locat,'name=','&'); 
	$Url=parse_url($locat);
	
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
else
{
$tbit = $_POST['tbit']; 
if($tbit == "ok"){
	$post = array();
	$post["captcha_response"] = $_POST["captcha_response"];
	$cookie = $_POST["cookie"];
	$Referer = $_POST["referer"];
    $link= $_POST["link"];  

	
	
	$Url = parse_url($link);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
    $wtime=cut_str($page,'limit: ',','); 
    if (!$wtime) html_error ("The captcha code inserted not match",0); 
	insert_timer($wtime, "<b>Timer :</b>");
    $Url["path"]=str_replace("free","timeout",$Url["path"]);

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	preg_match('/http:\/\/[^"\']+/i', $page, $go);
	$Url = parse_url(trim($go[0]));
	$FileName=cut_str($go[0],'name=','&');

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

}else{
     $page = geturl("turbobit.net",80, "/", 0, 0, 0, 0, $_GET["proxy"],$pauth);
    preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
    $cookie = $temp[1][2]."; ".$temp[1][1];
    $lang=parse_url("http://turbobit.net/en");
    $page = geturl($lang["host"], $lang["port"] ? $lang["port"] : 80, $lang["path"].($lang["query"] ? "?".$lang["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
    $temp= str_replace("/","/download/free/",$Url["path"]);
    $tmp =explode(".",$temp);
    $Url["path"]=$tmp[0];
    $link=  "http://".$Url["host"].$Url["path"];
	$referer="http://".$Url["host"].$temp;
    
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
    if (strpos($page,"The limit of connection was succeeded for your IP")){
            $wtime=cut_str($page,'limit: ',',');
        
            insert_timer($wtime, "<b>The limit of connection was succeeded for your IP Wait:</b>");  
    }
	$img_link=cut_str($page,'Captcha" src="','"');
	
	
	 
	  if ($img_link){
          
      // $capimg = $PHP_SELF."?image=".urlencode($img_link)."&referer=".urlencode($referer)."&cookie=".urlencode($cookie);       
        $Url = parse_url($img_link); 
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
        $headerend = strpos($page,"PNG");
        $imgfile = substr($page,$headerend-1);
        $capimg= $download_dir."turbobit_captcha.png" ;
      
        if (file_exists($capimg)) unlink($capimg) ;
        
        write_file($capimg, $imgfile);
        $capimg .= "?id=".rand(10000, 100000) ;
          
      }else{
	  html_error("Link image not found",0);
	  }
	
 


	print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"$capimg\">$nn";
	print	"<input name=\"referer\" value=\"$referer\" type=\"hidden\">$nn";
    print    "<input name=\"link\" value=\"$link\" type=\"hidden\">$nn"; 
	print	"<input name=\"cookie\" value=\"$cookie\" type=\"hidden\">$nn";
	print	"<input name=\"tbit\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"captcha_response\" type=\"text\" >";
	print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";
}
}

/*************************\  
written by kaox 18/09/2009
update by kaox 31-dec-2009
\*************************/
 
?>