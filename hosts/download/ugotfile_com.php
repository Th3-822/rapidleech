<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}



if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET["premium_pass"]) || ($_GET["cookieuse"] == "on" && $_GET["cookie"]) || ($_GET["premium_acc"] == "on" && $premium_acc["ugotfile_com"]["user"] && $premium_acc["ugotfile_com"]["pass"])) {
	if ($_GET["cookieuse"] == "on" && $_GET["cookie"]) {
		$cookie = $_GET["cookie"];
	} else {
		$Url =  parse_url("http://ugotfile.com/user/login");
		$post["ugfLoginUserName"] = $_GET ["premium_user"] ? $_GET ["premium_user"] : $premium_acc ["ugotfile_com"] ["user"];
		$post["ugfLoginPassword"] = $_GET ["premium_pass"] ? $_GET ["premium_pass"] : $premium_acc ["ugotfile_com"] ["pass"];
		$post["ugfLoginRememberMe"] = "on";
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"],$pauth);
		is_page($page);

		preg_match_all ( '/Set-Cookie: (.*)(;|\r\n) expires=/U', $page, $temp );
		$cookie = $temp [1][0];
	}

	$Url =  parse_url($LINK);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);

	if (stristr($page, "Location:")) {
	$dlink = trim(cut_str($page, "Location:","\n"));
	$Url =  parse_url($dlink);
	$FileName = basename($Url["path"]);
	} else {
		html_error ("Cannot get download link!", 0 );
	}
	if (function_exists(encrypt) && $cookie!=""){$cookie=encrypt($cookie);}

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&port=".$Url["port"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($LINK)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));


}else{


if ($_GET ["step"] != "second") {

$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
	is_present($page,"filename mismatched or file does not exist", "FileId and filename mismatched or file does not exist!");
	is_present($page,"You are trying to download file larger than", "Only premium members may download file larger than 400MB.");
	is_present($page,"existing download session", "You have an existing download session.<br>As a free member you can download only 1 file at a time.");

	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = implode(';',$temp[1]);
	$cookie = urlencode($cookie);
	$wait = cut_str($page,"countdown({seconds: ",",");

        echo "<center>$nn";
        echo "<form method=\"post\" action=\"$PHP_SELF\">$nn";
	echo "<input type=hidden name=step value=second>\n";
	echo "<input type=hidden name=wait value=$wait>\n";
        echo "<b>Please enter code:</b><br>$nn";
        echo "<img src=\"http://ugotfile.com/captcha?" . rand() . "\" ><br>$nn";
        echo "<input name=\"link\" value=\"$LINK\" type=\"hidden\">$nn";
        echo "<input name=\"referer\" value=\"$Referer\" type=\"hidden\">$nn";
        echo "<input name=\"cookie\" value=\"$cookie\" type=\"hidden\">$nn";
        echo "<input name=\"captcha\" type=\"text\" >";
        echo "<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form></center>";

}else{

	$post = array();
	$post['ugfCaptchaKey'] = $_POST['captcha'];
	$post["referer"] = $_POST["referer"];
	$wait = $_POST["wait"];
	$cookie = urldecode($_POST["cookie"]);
	$lica = "http://ugotfile.com/captcha?key=#".$post['ugfCaptchaKey']."";

	$Url = parse_url($lica);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, array(captchacode=>$_POST['captchacode']), 0, $_GET["proxy"],$pauth);
	is_page($page);

	is_present($page,'Session Expired!');
	is_present($page,'invalid key');
	insert_timer($wait, "Getting link.");

	$Href ="http://ugotfile.com/file/get-file";
	$Url = parse_url($Href);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cookie, array(captchacode=>$_POST['captchacode']), 0, $_GET["proxy"],$pauth);
	is_page($page);
	preg_match('/http:\/\/.+ugotfile\.com\/d\/[^\'"]+/i', $page, $down);   
	$Url=parse_url($down[0]);

	$FileName = basename($Url["path"]);
	if (function_exists(encrypt) && $cookie!=""){$cookie=encrypt($cookie);}

insert_location("$PHP_SELF?cookie=".urlencode($cookie)."&filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

}
}

//by RD27
?>