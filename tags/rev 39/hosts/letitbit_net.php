<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

if ($_GET['step'] == 1) {

$post = Array();
$post["uid"] = $_POST['uid'];
$post["frameset"] = "Download+file";
$nextPage = $_POST['nextpage'];
$post["fix"] = "1";
$post['cap'] = $_POST['imagestring'];
$post['uid2'] = $_POST['uid2'];
$cookie = $_POST['cookie'];
$Url = parse_url($nextPage);
$Referer = urldecode($_GET['referer']);
// Reorganize cookie...
$cookies = explode(';',$cookie);
foreach ($cookies as $temp) {
	$temp2 = explode('=',$temp);
	$temp3[$temp2[0]] = $temp2[1];
}
$temp3['ref_full'] = $_GET['referer'];
$temp3['ref_site'] = $Url['host'];
$cookie = "";
foreach ($temp3 as $k=>$v) {
	$cookie .= $k."=".$v.";";
}
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
//preg_match('/Set-Cookie: ([^\r\n]+)/', $page, $cookies);
//file_put_contents("letitbit_2.txt", $page);

is_page($page);

if(preg_match('/<frame src="http:\/\/letitbit.net\/tmpl\/tmpl_frame_top\.php\?link=(.+?)" name="topFrame"/', $page, $nextPageArray))
{
	$Referer = $Referer;
	$nextPage = $nextPageArray[1];
}
else
{
	html_error("Could not find frame.", 0);
}
/*$Url = parse_url($nextPage);
//$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
var_dump(nl2br(htmlentities($page)));exit;
//file_put_contents("letitbit_3.txt", $page);
is_page($page);


if(preg_match('/<a .*href="(.+?)"/', $page, $nextPageArray))
{
	$Referer = $nextPage;
	$nextPage = $nextPageArray[1];
}
else
{
	html_error("Could not find download link.", 0);
}
$Url = parse_url($nextPage);

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
//file_put_contents("letitbit_4.txt", $page);

preg_match('/Location: ([^\r\n]+)/i', $page, $nextPageArray);*/
$Url = parse_url($nextPage);
$FileName = basename($Url["path"]);

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&port=".$Url["port"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
} else {

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
//file_put_contents("letitbit_1.txt", $page);
is_page($page);
is_present($page, "The requested file was not found");
is_present($page, "Gesuchte Datei wurde nicht gefunden", "The requested file was not found");
is_present($page, "Запрашиваемый файл не найден", "The requested file was not found");

preg_match_all('/Set-Cookie: ([^\r\n]+)/', $page, $cookies);
//one day... $cookie = Array();
foreach ($cookies[1] as $fullCookie)
{
	$cookieSplit = explode("; ", $fullCookie);
	//one day... $cookie[] = $cookieSplit[0];
	$cookie .= $cookieSplit[0]."; ";
}
$cookie = substr($cookie, 0, -2); //remove one day...

if(preg_match('/<form action="(.+?)" method="post" name="Premium" id="Premium">/', $page, $nextPageArray))
{
	$Referer = $LINK;
	$nextPage = $nextPageArray[1];
}
else
{
	html_error("Could not find download form.", 0);
}
preg_match('/\n<input type="hidden" name="uid" value="(.+?)" \/>/', $page, $uidArray);
preg_match('/<input type="hidden" name="uid2" value="(.+?)" \/>/', $page, $uid2Array);
$post = Array();
$post["uid"] = $uidArray[1];
$post["frameset"] = "Download file";
//$post["fix"] = "1";
$post['uid2'] = $uid2Array[2];
if (stristr($page,"cap.php?"))
    {
    $imagecode = cut_str($page,"<img src='http://letitbit.net/cap.php?jpg=","'");
    //$capcode = cut_str($page,'capgen.php?','"');
    //$megavar = cut_str($page, '<input type="hidden" name="megavar" value="', '">');
              
    //$access_image_url = $Url["scheme"]."://".$Url["host"]."/capgen.php?".$capcode;
	$access_image_url = 'http://letitbit.net/cap.php?jpg='.$imagecode;
             
    print "<form name=\"dl\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\" method=\"post\">\n";
?>
	<input type="hidden" name="uid" value="<?php echo $uidArray[1]; ?>" />
	<input type="hidden" name="uid2" value="<?php echo $uid2Array[1]; ?>" />
	<input type="hidden" name="nextpage" value="<?php echo $nextPage; ?>" />
	<input type="hidden" name="cookie" value="<?php echo $cookie; ?>" />
<?php
    print "<input type=\"hidden\" name=\"link\" value=\"".urlencode($LINK)."\">\n<input type=\"hidden\" name=\"referer\" value=\"".urlencode($Referer)."\">\n<input type=\"hidden\" name=\"fileid\" value=\"$fid\">\n<input type=\"hidden\" name=\"imagecode\" value=\"$imagecode\">\n<input type=\"hidden\" name=\"megavar\" value=\"$megavar\">\n<input type=\"hidden\" name=\"step\" value=\"1\">\n";
    print "<input type=\"hidden\" name=\"comment\" id=\"comment\" value=\"".$_GET["comment"]."\">\n<input type=\"hidden\" name=\"email\" id=\"email\" value=\"".$_GET["email"]."\">\n<input type=\"hidden\" name=\"partSize\" id=\"partSize\" value=\"".$_GET["partSize"]."\">\n<input type=\"hidden\" name=\"method\" id=\"method\" value=\"".$_GET["method"]."\">\n";
    print "<input type=\"hidden\" name=\"proxy\" id=\"proxy\" value=\"".$_GET["proxy"]."\">\n<input type=\"hidden\" name=\"proxyuser\" id=\"proxyuser\" value=\"".$_GET["proxyuser"]."\">\n<input type=\"hidden\" name=\"proxypass\" id=\"proxypass\" value=\"".$_GET["proxypass"]."\">\n<input type=\"hidden\" name=\"path\" id=\"path\" value=\"".$_GET["path"]."\">\n";
    print "<h4>Enter <img src=\"$access_image_url\"> here: <input type=\"text\" name=\"imagestring\" size=\"6\">&nbsp;&nbsp;<input type=\"submit\" onclick=\"return check()\" value=\"Download File\"></h4>\n";
    print "<script language=\"JavaScript\">".$nn."function check() {".$nn."var imagecode=document.dl.imagestring.value;".$nn.'if (imagecode == "") { window.alert("You didn\'t enter the image verification code"); return false; }'.$nn.'else { return true; }'.$nn.'}'.$nn.'</script>'.$nn;
    print "</form>\n</body>\n</html>";
    }
  else
    {
    html_error("Image code not found", 0);
    }

}
?>
