<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

$post = Array();

$FileName = !$FileName ? basename($Url["path"]) : $FileName;
$FileName = str_replace(".html", "", $FileName);

if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["freakshare_com"]["user"] && $premium_acc["freakshare_com"]["pass"]))
{
	$Url =  parse_url("http://freakshare.com/login.html");
	$post["user"] = ($_GET["premium_user"] ? $_GET["premium_user"] : $premium_acc["freakshare_com"]["user"]);
	$post["pass"] = ($_GET["premium_pass"] ? $_GET["premium_pass"] : $premium_acc["freakshare_com"]["pass"]);
	$post["submit"] = "Login";
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);

	is_notpresent($page, "Set-Cookie: login=", "Wrong Username or Password!");

	$cookie = GetCookies($page);

	$Url =  parse_url("http://freakshare.com/?language=EN");
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page,"Member (free)", "Accounttype: <b>Member (free)</b>", 0);
	insert_timer(5, "Please wait", true);

	$Url =  parse_url($LINK);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page,"Your Traffic is used up for today!");
	is_present($page,"This file does not exist!");

	if (stristr($page, "Location:")) {
		$Href = trim(cut_str($page, "Location:","\n"));
		$Url =  parse_url($Href);
	} else {
		html_error ("Cannot get download link!", 0 );
	}

} else {

if ($_GET ["step"] != "second") {
if ($_GET ["step"]!= "cu") {
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	$info = cut_str($page, '<h1 style="text-align:center;" class="box_heading">','</h1>');
	if(!$info){$info = cut_str($page, '<h1 class="box_heading" style="text-align:center;">','</h1>');}
	echo "<center><b>$info</b></center>";
	$cookie = GetCookies($page);
	is_present($page,"This file does not exist!");
	$countdowntime = trim(cut_str($page, "var time = ",";"));
	?>
<center><div id="cnt"><h4>ERROR: Please enable JavaScript.</h4></div></center>
<form action="<?php echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="link" value="<?php echo $LINK; ?>">
<input type="hidden" name="step" value="cu">
<input type="hidden" name="cookies" value="<?php echo $cookie; ?>">
<script language="JavaScript">
var cu = <?php echo $countdowntime; ?>;
fcu();
function fcu() {
	if(cu>0) {
		if(cu>60){dt ="<font color=red>You reached your traffic limit.</font>";}else{dt ="<font color=yellow>FreakShare Free User</font>";}
		document.getElementById("cnt").innerHTML = "<b>" + dt + "</b><br>Please wait <b>" + cu + "</b> seconds";
		cu = cu - 1;
		setTimeout("fcu()", 1000);
		}
	else {
		document.getElementById("cnt").style.display="none";
		void(document.forms[0].submit());
		}
	}
</script>
</form></body></html>
<?php
exit;
}else{
$cookie = $_GET ["cookies"];
}
	$post = Array();
	$post["section"] = 'benefit';
	$post["did"] = '0';
	$post["submit"] = 'Free Download';

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);

	$k = trim(cut_str($page, 'challenge?k=','"'));
	preg_match('%input type="hidden" value="(.*)" name="did"%U', $page, $dids);
	$did = $dids[1];

	$post["section"] = 'waitingtime';
	$post["submit"] = 'Download';
if($k){
//$k = "6Le1iwoAAAAAANwh1QwMQkA0eGvqbHkaKZO8FxFy";
	$post["did"] = $did;
	$post["recaptcha_response_field"] = 'manual_challenge';
if(!$options["download_dir"]){$options["download_dir"] = $download_dir;}
$arcapt = apireca($k, $options["download_dir"], $cookie, "freakshare");
$post['recaptcha_challenge_field'] = $arcapt[rcf];

	$code = '<center>';
	$code .= '<form id="regularForm" method="post" action="'.$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "").'">'.$nn;
	$code .= '<input type="hidden" name="step" value="second">'.$nn;
	$code .= '<input type="hidden" name="post" value="'.urlencode(serialize($post)).'">'.$nn;
	$code .= '<input type="hidden" name="link" value="'.urlencode($LINK).'">'.$nn;
	$code .= '<input type="hidden" name="cookie" value="'.urlencode($arcapt[cookie]).'">'.$nn;
	$code .= '<h4>Enter the code shown below<br><img src="'.$arcapt[capfile].'"><br>here: <input type="text" name="captcha"><br>'.$nn;
	$code .= '<input type="submit" value="Download">'.$nn;
	$code .= '</form></h4></center>';
	echo ($code) ;exit;
}

		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);

	$cookie .= "; " . GetCookies($page);

	if(preg_match('/Location: *(.+)/i', $page, $redir )){
	$dowlink = trim( $redir[1] );
	$Url = parse_url( $dowlink );
	}else{html_error( "Error getting download link" , 0 );}

}else{

	$post = array();
		$post = unserialize(urldecode($_POST['post']));
		$post['recaptcha_response_field'] = $_POST['captcha'];
		$cookie = urldecode($_POST["cookie"]);
		$LINK = urldecode($_POST[link]);
		$Url = parse_url( $LINK );
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, $post, 0, $_GET["proxy"],$pauth);

	is_present($page,"Captcha Incorrect!");
	$cookie .= "; " . GetCookies($page);

	if(preg_match('/Location: *(.+)/i', $page, $redir )){
	$dowlink = cut_str ($page ,"Location: ","\r");
	$Url = parse_url( $dowlink );
	}else{html_error( "Error getting download link" , 0 );}
}
}

	if (function_exists(encrypt) && $cookie!=""){$cookie=encrypt($cookie);}
insert_location("$PHP_SELF?filename=". urlencode($FileName)."&force_name=".urlencode($FileName)."&host=".$Url["host"]."&port=".$Url["port"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));


function apireca($k, $optionsd, $cookies, $sr){
		$Url = parse_url("http://api.recaptcha.net/challenge?k=$k");
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
		is_page($page);
	if(preg_match('/Location: *(.+)/i', $page, $redir )){
	$newreca = trim( $redir[1] );
	$Url = parse_url( $newreca );
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookies, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
		$rcf = cut_str ( $page ,"challenge:'" ,"'" );
		if(!$rcf){$rcf = cut_str ( $page ,"challenge : '" ,"'" );}
		$Url = parse_url("http://www.google.com/recaptcha/api/image?c=".$rcf);
	}else{
		$rcf = cut_str ( $page ,"challenge : '" ,"'" );	
		$cookie = GetCookies($page);
		$Url = parse_url("http://api.recaptcha.net/image?c=".$rcf);
		}
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
		$headerend = strpos($page,"\r\n\r\n");
		$cap_img = substr($page,$headerend+4);
		$capfile = $optionsd.$sr."_captcha.jpg";
		if (file_exists($capfile)){unlink($capfile);} 
		write_file($capfile, $cap_img);
if(!$rcf){html_error("Error getting captcha", 0);}
	$cookies .= "; " . $cookie;
$arcapt = array();
$arcapt[cookie] = $cookies;
$arcapt[rcf] = $rcf;
$arcapt[capfile] = $capfile;
return $arcapt;

}

?>