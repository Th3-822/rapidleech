<?php

if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit();
}

class zippyshare_com extends DownloadClass 
{
    public function Download($link) 
	{
		global $options;
		
		$page = $this->GetPage($link);
		$FileName = trim(cut_str($page, 'addthis:title="','"'));
		if (empty($FileName))
		$FileName = trim(cut_str($page, '<title>Zippyshare.com - ','</title>'));
		
		if (preg_match("/url: '([^']+)', seed: (\d+)}/i", $page))
		{
			if ($_POST["step"] == "1") {
				$this->DownloadRar($link);
			} else {
				$this->Retrieve($link, $FileName);
			}
		}
		else
		{
			$this->DownloadFree($link, $page);
		}
	}

	private function Retrieve($link, $FileName) 
	{
		global $options;
		
		$page = $this->GetPage($link);
		is_present($page, "File does not exist on this server", "File does not exist on this server");
		$cookies = GetCookies($page);
		
		preg_match('/shortencode: \'(.+)\'/', $page, $shortencode);
		preg_match('/document\.location = \'\/d\/(.+)\';/', $page, $server);
		$srvid = cut_str($link,'http://','.zippyshare.com');
		
		$captchach = cut_str($page,'Recaptcha.create("','",');
		$captcha = "http://www.google.com/recaptcha/api/challenge?k=" . $captchach . "&ajax=1&cachestop=0.".rand()."&lang=de";
		$page = $this->GetPage($captcha, 0, 0, $link);
		$CookiesCaptcha = GetCookies($page);
		$ch = cut_str($page,'challenge : \'','\',');
		
		if ($ch) 
		{
			$img = "http://www.google.com/recaptcha/api/image?c=".$ch;
			$page = $this->GetPage($img);
			$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
			if($options["download_dir"])
			{
				$imgfile = $options["download_dir"]."zippyshare.jpg";
			} else {
				$imgfile = DOWNLOAD_DIR."zippyshare.jpg";
			}
			if (file_exists($imgfile)) {
				unlink($imgfile);
			}
			write_file($imgfile, $capt_img);
			} else {
				html_error("Error getting CAPTCHA image.", 0);
		}
		
		$data = array();
		$data["step"] = "1";
		$data["link"] = urlencode($link);
		$data["cookies"] = $cookies;
		$data["filename"] = $FileName;
		$data["recaptcha_challenge_field"] = $ch;
		$data["shortencode"] = $shortencode[1];
		$data["server"] = $server[1];
		$data["srvid"] = $srvid;
		$this->EnterCaptcha($imgfile, $data, 13);
		exit();
	}
	
	private function DownloadRar($link)
	{
	global $options;
		$imgfile = $options["download_dir"]."zippyshare.jpg";
		if (file_exists($imgfile))
		{
			unlink ($imgfile);
		}
		$FileName = $_POST["filename"];
		$cookies = $_POST["cookies"];
		$server = $_POST["server"];
		$srvid = $_POST["srvid"];
		$post = array();
		$post["challenge"] = $_POST["recaptcha_challenge_field"];
		$post["response"] = $_POST["captcha"];
		$post["shortencode"] = $_POST["shortencode"];
		$URL = "http://" . $srvid . ".zippyshare.com/rest/captcha/test";
		$page = $this->GetPage($URL, 0, $post, $link);
		if (!preg_match('#true#', $page)){
				html_error("Not true!");
		}
		else
		{
		preg_match('/Set-Cookie: JSESSIONID=(.+);/', $page, $cookie2);
		$cookie = explode("; ", $cookies);
		$cookies = $cookie[1] . "; JSESSIONID=" . $cookie2[1];
		}
		$dlink = "http://" . $srvid . ".zippyshare.com/d/".$server."";
		
		$this->RedirectDownload(trim($dlink), $FileName, $cookies, 0, $link, $FileName);
		exit();
	}

	private function DownloadFree($link, $page)
	{
	$cookies = GetCookies($page);
	is_present($page, "File does not exist on this server", "File does not exist on this server");
	$replace = array("%20", "%28", "%29", "%26", "%5B", "%5D", "%27", "%2C", "%5b", "%5d", "%2c",);
	$replacewith = array(" ", "(", ")", "&", "[", "]", "'", ",", "[", "]", ",");
	
		if (preg_match_all("#var (\w) = (\d+);#", $page, $temp))
		{
			$a=$temp[2][0];
			$b=$temp[2][1];
			$a=floor($temp[2][0]/3);
			$dlink=str_replace("/v/", "/d/", $link);
			$dlink=str_replace("file.html", $a+$temp[2][0]%$temp[2][1], $dlink);
			if (!preg_match('#\(\'dlbutton\'\).href.*"([^"]+)".+"([^"]+)";#', $page, $temp)){
				html_error("Error 0x01:Plugin is out of date");
			}
			$dlink.=$temp[2];
			//html_error($dlink);
		} else if (preg_match("/url: '([^']+)', seed: (\d+)}/i", $page, $L)) {
			$dlink = $L[1] . "&time=" . $L[2]; //src= return 6 * param1 % 78678623;
		} else if (preg_match("/var a = ([0-9]+)%([0-9]+);\s+var b = ([0-9]+)%([0-9]+);\s+.+\/(.+)\";/", $page, $L)) {
			$dlink=str_replace("/v/", "/d/", $link);
			$dlink=str_replace("file.html", (($L[1] % $L[2]) * ($L[3] % $L[4])) + 19 . "/" . $L[5], $dlink);
		} else if (preg_match('/\/([0-9]+)\/"\+\(([0-9]+)\%([0-9]+) \+ ([0-9]+)\%([0-9]+)\)\+"\/(.+)";/', $page, $L)) {
			$server = cut_str($link,'http://','.zippyshare.com');
			$dlink = "http://" . $server . ".zippyshare.com/d/" . $L[1] . "/" . (($L[2]%$L[3])+($L[4]%$L[5])) . "/" . $L[6];
			$FileName = str_replace($replace, $replacewith, $L[6]);
		} else if (preg_match('/var.+= ([0-9]+) (.+) ([0-9]+);\s+var.+[a-z] (.+) ([0-9]+);\s+var.+[a-z] (.+) ([0-9]+);\s+.+\/d\/([0-9]+)\/.+\/(.+)";/', $page, $L)) {
			$server = cut_str($link,'http://','.zippyshare.com');
			$n = $L[1] + $L[3];
			$b = $n - $L[5];
			$z = $b - $L[7];
			$dlink = "http://" . $server . ".zippyshare.com/d/" . $L[8] . "/" . $z . "/" . $L[9];
			$FileName = str_replace($replace, $replacewith, $L[9]);
		} else {
			html_error("Error 0x02: Plugin is out of date");
		}
	$Url=parse_url($dlink);
	if (!$FileName) $FileName=basename($Url['path']);
	$FileName = str_replace($replace, $replacewith, $FileName);
	$this->RedirectDownload(trim($dlink), $FileName, $cookies, 0, $link, $FileName);
	}
}

/*
 * By vdhdevil Jan-12-2010
 * Updated March-8-2011
 * Fixed July-17-2011 by defport
 * Credit to  Th3-822, motor
 */
?>