<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

$username = "TheOnly92";
$password = "qnn1jWR7";

$action_url = 'http://forums.anime-eden.com/login.php';
$act = parse_url($action_url);
$post = array();
$post['vb_login_username'] = $username;
$post['vb_login_password'] = $password;
$post['login'] = 'Login';
$post['cookieuser'] = "";
$post['s'] = "";
$post['do'] = "login";
$post['vb_login_md5password'] = "";
$post['vb_login_md5password_utf'] = "";

$page = geturl($act["host"], $act["port"] ? $act["port"] : 80, $act["path"], $Referer, 0, $post, 0, $_GET["proxy"],$pauth);
$cookie = GetCookies($page);

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

$link = cut_str($page,'MB)</span><br /><a href="','">Click here to ');
$Url = parse_url ( $link );
//$link = $Url['scheme'].'://'.$username.':'.$password.'@'.$Url['host'].$Url['path'];

$auth = urlencode(base64_encode($username.':'.$password));

$FileName = !$FileName ? basename($Url["path"]) : $FileName;
insert_location("$PHP_SELF?filename=".urlencode($FileName)."&auth=".$auth."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));


?>