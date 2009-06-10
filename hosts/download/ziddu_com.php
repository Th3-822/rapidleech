<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}
$zd = $_POST['zd'];
if ($zd == "ok") {
	$post = Array();
	$post["fid"] = $_POST['fid'];
	$post["tid"] = $_POST['tid'];
	$post["securitycode"] = $_POST['securitycode'];
	$post["fname"] = $_POST['fname'];
	$post["securecode"] = $_POST['securecode'];
	$post["Keyword"] = 'Ok';
	$post["submit"] = 'Download';
	$cookie = $_POST['cookie'];
	$Referer = $_POST["referer"];
	$Href = $_POST["flink"];
	$FileName = $_POST["name"];
	$Url = parse_url($Href);
	

	/*$FileName = $fname[1];
	$Referer = $LINK;
	$Url = parse_url("http://www.ziddu.com/downloadfile.php");*/

	//$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	//var_dump($page);exit;
	//file_put_contents("ziddu_4.txt", $page);
	/*is_page($page);
	
	if (preg_match('/Location: ([^\r\n]+)/', $page, $nextPage))
	{
		if (strpos(strtolower($nextPage[1]), "http:") !== false)
		{
			$Url = parse_url($nextPage[1]);
		}
		else
		{
			$Url = parse_url("http://www.ziddu.com".$nextPage[1]);
		}
	}
	else
	{
		html_error("Redirect not found", 0);
	}

	$cookie = $cookieARPT."; ".$cookiePHPSESSID."; popcount=1";*/

	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&cookie=".urlencode($cookie)."&post=".urlencode(serialize($post))."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
} else {
	if (strpos(strtolower($LINK), "ziddu.com/downloadlink.php?uid="))
	{
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
		//file_put_contents("ziddu_1.txt", $page);
		is_page($page);
		
		if (preg_match('/<a href="(.*)" class="download">/', $page, $nextPage))
		{
			$LINK = "http://www.ziddu.com".$nextPage[1];
			$Url = parse_url($LINK);
		}
		else
		{
			html_error("Download page not found", 0);
		}
	}
	if (strpos(strtolower($LINK), "ziddu.com/download.php?uid="))
	{
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
		//file_put_contents("ziddu_2.txt", $page);
		is_page($page);
		is_present($page, "Location: /errortracking.php?msg=File not found", "File not found");
		
		if (preg_match('/<a href="(.*)" class="link2">/', $page, $nextPage))
		{
			$LINK = "http://www.ziddu.com".$nextPage[1];
			$Url = parse_url($LINK);
		}
		else
		{
			html_error("Download page not found", 0);
		}
	}
	if (strpos(strtolower($Url["path"]), "/download/") !== false)
	{
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
		preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
		$cookie = $temp[1];
		$cook = implode(';',$cookie);
		$Referer = $LINK;
		preg_match('/ocation: (.*)/',$page,$temp);
		$Url["path"] = str_replace("/download/", "/downloadfile/", $Url["path"]);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cook, 0, 0, $_GET["proxy"],$pauth);
		preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
		$cookie = $temp[1];
		$cook .= ';'.implode(';',$cookie);
		$Url["path"] = str_replace("/downloadfile/", "/download/", $Url["path"]);
	}

	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cook, 0, 0, $_GET["proxy"],$pauth);
	//file_put_contents("ziddu_3.txt", $page);
	is_page($page);
	is_present($page, "Location: /errortracking.php?msg=File not found", "File not found");
	$Url['host'] = 'downloads.ziddu.com';
	$Url["path"] = str_replace("/download/", "/downloadfile/", $Url["path"]);
	$post['mmemid'] = 0;
	$post['mname'] = "";
	$post['lang'] = 'english';
	$post['Submit'] = 'Download';
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cook, $post, 0, $_GET["proxy"],$pauth);
	$referer = 'http://'.$Url['host'].$Url['path'];
	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	$cook .= ';'.implode(';',$cookie);

	/*preg_match('/Set-Cookie: ARPT=(.*); /', $page, $coo);
	$cookieARPT = "ARPT=".$coo[1];
	preg_match('/Set-Cookie: PHPSESSID=(.*); /', $page, $coo);
	$cookiePHPSESSID = "PHPSESSID=".$coo[1];
	$cookie = $cookieARPT."; ".$cookiePHPSESSID;*/
	preg_match('/id="securefrm" action="(.*)"/U', $page, $flink);
	preg_match('/id="fid" value="(.*)">/', $page, $fid);
	preg_match('/id="tid" value="(.*)">/', $page, $tid);
	preg_match('/id="fname" value="(.*)">/', $page, $fname);
	preg_match('/id="securecode" value="(.*)">/', $page, $securecode);
	preg_match('/name="submit" * value="(.*)">/', $page, $submit);
	preg_match('/name="Keyword"  value="(.*)"/', $page, $keyword);
	$flink = 'http://'.$Url["host"].$flink[1];
	$name = $fname[1];
	
	preg_match('/<img src="(.+?)" align="absmiddle" id="image" name="image"/', $page, $imglink);
	$img ='http://'.$Url["host"].$imglink[1];
	
	preg_match('/name="id" *value="(.+)">/', $page, $id);
	
	$Url = parse_url($img);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cook, 0, 0, $_GET["proxy"],$pauth);
	
	$headerend = strpos($page,"\r\n\r\n");
	$pass_img = substr($page,$headerend+4);
	write_file($download_dir."ziddu_captcha.jpg", $pass_img);
	$randnum = rand(10000, 100000);
	
	$img_data = explode("\r\n\r\n", $page);
	$header_img = $img_data[0];
	
	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	$cook .= ';'.implode(';',$cookie);
	
	/*preg_match('/Set-Cookie: *(PHPSESSID=.*?);/', $page, $zdid);
	$cookie = $cookie.';'.$zdid[1];*/
	
	print 	"<form method=\"post\" action=\"$PHP_SELF\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"{$download_dir}ziddu_captcha.jpg?id=".$randnum."\" >$nn";
	print	"<input name=\"link\" value=\"$LINK\" type=\"hidden\">$nn";
	print	"<input name=\"referer\" value=\"$referer\" type=\"hidden\">$nn";
	print	"<input name=\"flink\" value=\"$flink\" type=\"hidden\">$nn";
	print	"<input type=hidden name=id value=$id[1]>$nn";
	print	"<input name=\"zd\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"cookie\" value=\"$cook\" type=\"hidden\">$nn";
	print	"<input name=\"name\" value=\"$name\" type=\"hidden\">$nn";
	print	"<input name=\"fid\" value=\"$fid[1]\" type=\"hidden\">$nn";
	print	"<input name=\"tid\" value=\"$tid[1]\" type=\"hidden\">$nn";
	print	"<input name=\"fname\" value=\"$fname[1]\" type=\"hidden\">$nn";
	print	"<input name=\"securecode\" value=\"$securecode[1]\" type=\"hidden\">$nn";
	print	"<input name=\"keyword\" value=\"$keyword[1]\" type=\"hidden\">$nn";
	print	"<input name=\"securitycode\" type=\"text\" >";
	print	"<input name=\"submit\" value=\"Download\" type=\"submit\"></form>";
	
}
?>