<?php

if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

if($_POST["step"]){
$cookie=$_POST["cookie"];
$post["recaptcha_challenge_field"]=$_POST["ch"];
$post['recaptcha_response_field']=urlencode($_POST['captcha']);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);
is_page($page);

}

if($_POST["passfile"])
{
	$trynumber=$_POST["trynumber"]; ;
	$post=array();
	$post["downloadp"]=$_POST["downloadp"];
    $dlelement=$_POST["dlelement"];
    $cookie=$_POST["cookie"];
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
}else
{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$dlelement = cut_str($page, "var io=document.getElementById('", "')");
	preg_match('/Location:.*error/i', $page) ? html_error("Invalid File", 0) : '';
	if(preg_match('/Location: (.*)/i', $page, $redir))
	{
		$Href = trim($redir[1]);
		$Url = parse_url($Href);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
		is_page($page);
	}
	$cookie = GetCookies($page);
}

if( strpos( $page ,"GetCaptcha('")!== false ){
$Url=parse_url("http://api.recaptcha.net/challenge?k=6LextQUAAAAAALlQv0DSHOYxqF3DftRZxA5yebEe");
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
		is_page($page);
		is_present($page,"Expired session", "Expired session . Go to main page and reattempt", 0);
		
		$cook = GetCookies($page);
		$ch = cut_str ( $page ,"challenge : '" ,"'" );
		$Url=parse_url("http://api.recaptcha.net/image?c=".$ch);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cook, 0, 0, $_GET["proxy"],$pauth);
		$headerend = strpos($page,"\r\n\r\n");
		$pass_img = substr($page,$headerend+4);
		$imgfile=$options['download_dir']."mediafire_captcha.jpg";
		
		if (file_exists($imgfile)){ unlink($imgfile);} 
        write_file($imgfile, $pass_img);

		$post['recaptcha_challenge_field']=$ch;

		$code = '<form method="post" action="'.$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "").'">'.$nn;
		$code .= '<input type="hidden" name="step" value="1">'.$nn;
		$code .= '<input type="hidden" name="dlelement" value="'.$dlelement.'">'.$nn;
		$code .= '<input type="hidden" name="link" value="'.urlencode($LINK).'">'.$nn;
		$code .= '<input type="hidden" name="ch" value="'.$ch.'">'.$nn;
		$code .= '<input type="hidden" name="cookie" value="'.$cookie.'">'.$nn;
		$code .= 'Please enter : <img src="'.$imgfile.'?'.rand(1,10000).'"><br><br>'.$nn;
		$code .= '<input type="text" name="captcha"> <input type="submit" value="Download">'.$nn;
		$code .= '</form>';
		echo ($code) ;
		die;

}


if( strpos( $page ,"dh('')")!== false ){
	echo("<div style=\"text-align: center\"><br><br>");
	$trynumber ++;
	if($trynumber>1)
	{
		echo ("<div style=\"text-align: center\">The file password entered not match, please correct the error</div>");
	}else
	{
		echo ("<div style=\"text-align: center\">The file is password protect, please enter the password</div>");
	}
	$code = '<div style="text-align: center"><form method="post" action="'.$PHP_SELF.'">'.$nn;
	$code .= '<input type="text" name="downloadp"> <input type="submit" value="Send password">'.$nn;
	$code .= '<input type="hidden" name="trynumber" value="'.$trynumber.'">'.$nn;
	$code .= '<input type="hidden" name="passfile" value="true">'.$nn;
    $code .= '<input type="hidden" name="cookie" value="'.$cookie.'">'.$nn;
    $code .= '<input type="hidden" name="dlelement" value="'.$dlelement.'">'.$nn;
	$code .= '<input type="hidden" name="link" value="'.urlencode($LINK).'">'.$nn;
	$code .= '</form></div>';
	echo $code;
	die;
}
if(preg_match('/cu\([^qk](.*?)\);/', $page, $values))
{
	$values = str_replace("'", '', $values[1]);
	$value = explode(',', $values);
	$qk = $value[0];
	$pk = $value[1];
	$r = $value[2];
}
else
{
html_error( "Download values not found" , 0 );
}

$Href = "http://www.mediafire.com/dynamic/download.php?qk=$value[0]&pk=$value[1]&r=$value[2]";
$Url = parse_url($Href);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

$mL = cut_str($page, "var mL='", "';");
$mH = cut_str($page, "var mH='", "';");
$mY = cut_str($page, "var mY='", "';");
preg_match('%eLink=parent.document.getElementById\(\'' . $dlelement . '\'\).*http://"\+mL\+\'/\'\s\+(.+)\+\s\'g/%U', $page, $parts);

$temps = explode("+",$parts[1]);
foreach ($temps as $temp)
{
	if (empty($temp)) continue;
	preg_match('/'.trim($temp).' ?= ?\'(.*?)\';/', $page, $temp2);
	$mpath1.= $temp2[1];
}

$Href = 'http://'.$mL.'/'.$mpath1.'g/'.$mH.'/'.$mY;
$Url = parse_url($Href);
$FileName = !$FileName ? basename($Url["path"]) : $FileName;
insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($LINK)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&cookie=".urlencode($cookie)."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

// edited by mrbrownee70
//updated by szalinski 15-Sep-09
//update by szalinski 03-nov-09 (mediafire layout change)
//update by szalinski 14-nov-09 (mediafire code change)
//update by kaox 10-jan-2010  - (fix password protected file and inserted support for captcha)

?>