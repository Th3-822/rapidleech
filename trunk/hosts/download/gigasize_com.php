<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["gigasize"]["user"] && $premium_acc["gigasize"]["pass"]))
{
	$post = Array();
	$post["uname"] = ($_GET["premium_user"] ? $_GET["premium_user"] : $premium_acc["gigasize"]["user"]);
	$post["passwd"] = ($_GET["premium_pass"] ? $_GET["premium_pass"] : $premium_acc["gigasize"]["pass"]);
	$post["d"] = "Login";
	$post["login"] = "1";
	$cookie = "Cookieuser[lng]=en";
	$Url = parse_url("http://www.gigasize.com/login.php");
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, $post, 0, $_GET["proxy"],$pauth);
	//file_put_contents("gigasize_1.txt", $page);
	is_page($page);
	is_present($page, "index.php?badlogin=1", "Invalid username or password");

	/*preg_match_all('/Set-Cookie: GigSizeCookieJar=([^;]+)/', $page, $matches);
	foreach ($matches[1] as $match) // workaround for multiple GigSizeCookieJar
	{
		$cookiejar = $match;
	}
	preg_match('/Set-Cookie: Cookieuser\[user\]=([^;]+)/', $page, $match);
	$cookieuser = $match[1];
	preg_match('/Set-Cookie: Cookieuser\[pass\]=([^;]+)/', $page, $match);
	$cookiepass = $match[1];
	preg_match('/Set-Cookie: PHPSESSID=([^;]+)/', $page, $match);
	$cookiesession = $match[1];
	$cookie = "Cookieuser[lng]=en; PHPSESSID=".$cookiesession."; GigSizeCookieJar=".$cookiejar."; Cookieuser[user]=".$cookieuser."; Cookieuser[pass]=".$cookiepass;*/
	$cookie = GetCookies($page);

	$Url = parse_url($LINK);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
	//file_put_contents("gigasize_2.txt", $page);
	is_page($page);
	is_present($page, "The file you are looking for is not available", "The file has been deleted");
	$Url = parse_url("http://www.gigasize.com/form.php");
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	$temp = "";
	preg_match('/<b>To download with a download accelerator\/manager copy &amp; paste the following link<\/b>: <a style="width:480px" href=".*">(.*)<\/a><\/div>/',$page,$temp);
	$Referer = "http://www.gigasize.com/form.php";
	$Url = parse_url($temp[1]);
	/*

	preg_match('/Set-Cookie: GigSizeCookieJar=([^;]+)/', $page, $match);
	$cookiejar = $match[1];
	$cookie = "Cookieuser[lng]=en; PHPSESSID=".$cookiesession."; GigSizeCookieJar=".$cookiejar."; Cookieuser[user]=".$cookieuser."; Cookieuser[pass]=".$cookiepass;*/
/*
	$Url = parse_url("http://www.gigasize.com/form.php");
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	//file_put_contents("gigasize_3.txt", $page);
	is_page($page);

	if (preg_match('/<a href=".*">([^<]+)/', $page, $match))
	{
		$Url = parse_url($match[1]);
		$Referer = "http://www.gigasize.com/form.php";
	}
	else
	{
		html_error("Download link not found", 0);
	}
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	//file_put_contents("gigasize_4.txt", $page);
	is_page($page);
	if (preg_match('/Location: ([^\r\n]+)/', $page, $match))
	{
		$Url = parse_url($match[1]);
		$FileName = "attachment";
		insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
	}
	else
	{
		html_error("Redirect not found", 0);
	}*/
	$FileName = "attachment";
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
else
{
$giga = $_POST['giga']; 
if($giga == "ok"){
	$post = array();
	$post["txtNumber"] = $_POST["txtNumber"];
	$cookie = $_POST["cookie"];
	$Referer = $_POST["link"];
	$Href = $_POST["flink"];
	
	$Url = parse_url($Href);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	/*
	if(preg_match('/document\.counter\.d2\.value=\'(.*)\'/', $page, $count)){
		$countnum = $count[1];
		insert_timer($countnum, "Waiting link timelock");
	}else{
		echo 'Error[getCountNum]';
		die;
	}
	*/
	preg_match('%(/getcgi\.php\?.*?)"%', $page, $dllink);
	$final = "http://www.gigasize.com".$dllink[1];
	$Url = parse_url($final);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	preg_match('/ocation: *(.+)/', $page, $redir);
	$Url = parse_url(trim($redir[1]));
	$FileName = basename($Url["path"]);

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));

}else{
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	preg_match_all('/Set-Cookie: *(.+);/', $page, $cook);
	$cookie = implode(';', $cook[1]);
	
	print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"http://www.gigasize.com/randomImage.php\" >$nn";
	print	"<input name=\"link\" value=\"$LINK\" type=\"hidden\">$nn";
	print	"<input name=\"flink\" value=\"http://www.gigasize.com/formdownload.php\" type=\"hidden\">$nn";
	print	"<input name=\"giga\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"cookie\" value=\"$cookie\" type=\"hidden\">$nn";
	print	"<input name=\"txtNumber\" type=\"text\" >";
	print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";
}
}
?>