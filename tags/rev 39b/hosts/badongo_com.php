<?php
 function biscotti($content) {
        is_page($content);
        preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
        foreach ($matches[0] as $coll) {
        $bis.=cut_str($coll,"Set-Cookie: ","; ")."; ";    
        }return $bis;}
if ($_POST["ss"]=="ok"){

$post["user_code"]=$_POST["usercode"];
	$post["cap_id"]=$_POST["cid"];
	$post["cap_secret"]=$_POST["cap_secret"];
	$cook=$_POST["cook"];
    $link=$_POST["link"];
    $url=parse_url($link);
	$page = geturl($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), $LINK, $cook, $post, 0, $_GET["proxy"], $pauth);
	is_page($page);
    $cook=biscotti($page);
	$locat=cut_str($page,'req.open("GET", "','status"');
	$url=$locat.'ifr?pr=1&zenc=';
	if (!strpos($locat,'badongo.com'))
	{
		echo "<center><b>Incorrect CAPTCHA Entered, try again<b></center><br>\n";
        exit;
	
	}
	

	$countDown=cut_str($page,'check_n =',';');
	$countDown = is_numeric($countDown) ? $countDown : 45;
	insert_timer($countDown, "Waiting link timelock.","",true);
	$Url=parse_url($url);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cook, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$Href = $locat.'loc?pr=1';
	$Url = parse_url($Href);
    $page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cook, 0, 0, $_GET["proxy"],$pauth);
    preg_match('/Location:.+?\\r/i', $page, $loca);
    $redir = rtrim($loca[0]);
    preg_match('/http:.+/i', $redir, $loca);
    
    $Url = parse_url($loca[0]);    
	$FileName = !$FileName ? basename($Url["path"]) : $FileName;

	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($link).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));



}else{
	
       
        
	$page=geturl($Url["host"], 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page,'been deleted','This file has been deleted');
	is_present($page,'id="fileError','This file has been deleted');
    $cook=biscotti($page);
	$cap_url=$LINK.'?rs=refreshImage&rst=&rsrnd='.$tmp;
	$Url2=parse_url($cap_url);
	$page = geturl($Url2["host"], defport($Url2), $Url2["path"].($Url2["query"] ? "?".$Url2["query"] : ""), $LINK, $cook, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$cid=cut_str($page,'"/ccaptcha.php?cid=','\"');
	$cap_secret=cut_str($page,'cap_secret value=','>');
	if (!is_numeric($cid) || !$cap_secret) html_error ('Error retrive link 2');
	$img_link="http://".$Url["host"].'/ccaptcha.php?cid='.$cid;
	
	$Url3 = parse_url($img_link);
		$page = geturl($Url3["host"], $Url3["port"] ? $Url3["port"] : 80, $Url3["path"].($Url3["query"] ? "?".$Url3["query"] : ""), $free_link, $cook, 0, 0, $_GET["proxy"],$pauth);
		
	$headerend = strpos($page,"\r\n\r\n");
	$pass_img = substr($page,$headerend+10);
    $imgfile=$download_dir."badongo_captcha.jpg";
    if (file_exists($imgfile)){
    unlink($imgfile) ;
    }
	write_file($imgfile, $pass_img);
	$link=str_replace('/file/','/cfile/',$LINK);
	

	
	print    "<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"$imgfile\">$nn";
	print	"<input name=\"usercode\" type=\"text\" >";
    print   "<input name=\"ss\" value=\"ok\" type=\"hidden\">$nn";
    print	"<input name=\"link\" value=\"$link\" type=\"hidden\">$nn";
	print   "<input type=hidden name=\"cap_secret\" value=".$cap_secret.">\n";
	print	"<input name=\"cook\" value=\"$cook\" type=\"hidden\">$nn";
	print   "<input type=hidden name=cid value=".$cid.">$nn";
    print    "<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";
	

}
/*
writted by kaox 06/05/09
*/

?>