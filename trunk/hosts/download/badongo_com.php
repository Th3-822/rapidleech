<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit();
}

class badongo_com extends DownloadClass {
	private $page, $cookie, $step;
	public function Download($link) {
		global $premium_acc, $Referer;
		$link = $Referer = str_ireplace(array('://badongo.com/', '.com/audio/'), array('://www.badongo.com/','.com/file/'), $link);
		$Referer = str_ireplace('/cfile/', '/file/', $Referer);
		$this->step = empty($_REQUEST['step']) ? false : $_REQUEST['step'];

		if ($this->step != '1') {
			$this->page = $this->BD_cURL($link, 'badongoL=en');
			$this->cookie = GetCookiesArr($this->page);
			$this->cookie['badongoL'] = 'en';

			is_present($this->page, "The file is scheduled for deletion and may be <b>permanently removed</b> at any time");
			is_present($this->page, "This file has been removed due to copyright infrigment");
			is_present($this->page, "This file has been deleted because it has been inactive for over 30 days");
			is_present($this->page, 'Somebody from your IP address has not been obeying our rules', 'Badongo has banned this IP...');
		}

		if ($this->step == 1) {
			return $this->FreeDL($link);
		} else {
			return $this->Prepare($link);
		}
	}

	private function Prepare($link) {
		if (!$purl = cut_str($this->page, "var gURL = '", "';")) html_error("gURL not found.");
		$page = $this->BD_cURL("$purl?rs=refreshImage&rst=&rsrnd=".jstime(), $this->cookie);

		$data = $this->DefaultParamArr(cut_str($page, 'action=\"', '\"'), $this->cookie, $link);
		$data['step'] = 1;
		if (!$data['link']) html_error("Error getting captcha data [url].");
		if (!$data['bd_cid'] = cut_str($page, 'name=cap_id value=', '>')) html_error("Error getting captcha data [id].");
		if (!$data['bd_ckey'] = cut_str($page, 'name=cap_secret value=', '>')) html_error("Error getting captcha data [key].");
		if (!$imgurl = cut_str($page, 'src=\"', '\"')) html_error("Error getting captcha data [imgurl].");
		if (!strstr($imgurl, '://')) $imgurl = 'http://www.badongo.com'.$imgurl;

		//Download captcha img.
		$page = $this->BD_cURL($imgurl, $this->cookie);
		$capt_img = substr($page, strpos($page, "\r\n\r\n") + 4);
		$imgfile = DOWNLOAD_DIR . "badongo_captcha.jpg";

		if (file_exists($imgfile)) unlink($imgfile);
		if (!write_file($imgfile, $capt_img)) html_error("Error getting CAPTCHA image.", 0);

		$this->EnterCaptcha($imgfile.'?'.time(), $data);
		exit;
	}

	private function FreeDL($link) {
		if (empty($_POST['captcha'])) html_error("You didn't enter the image verification code.");
		if (empty($_POST['bd_cid']) || empty($_POST['bd_ckey']) || empty($_POST['cookie'])) html_error("Error: Invalid captcha data.");

		$post = array();
		$post['user_code'] = $_POST['captcha'];
		$post['cap_id'] = $_POST['bd_cid'];
		$post['cap_secret'] = $_POST['bd_ckey'];

		$this->cookie = StrToCookies(urldecode($_POST['cookie']));
		$this->page = $this->BD_cURL($link, $this->cookie, $post);
		if (preg_match('@^HTTP/1\.[0-1] 302 @', $this->page)) html_error("Error: Wrong Captcha Entered.");
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		if (!preg_match("@window\.getFileLinkInitOpt = \{[^,|\}]+,'h':'([^']+)'\};@i", $this->page, $h)) html_error("Error: Cannot get 'h' value.");
		if (!preg_match("@\(window\.getFileLinkInitOpt\) \{ z = '([^']{11,})'; \}@i", $this->page, $z)) html_error("Error: Cannot get 'z' value.");

		$load = cut_str($this->page, "Event.observe(window, 'load', function(e) {", "}");
		$post = array();
		$post['id'] = cut_str($load, "window.getFileLinkId = '", "';");
		$post['type'] = cut_str($load, "window.getFileLinkType = '", "';");
		$post['ext'] = cut_str($load, "window.getFileLinkPart = '", "';");
		$post['f'] = 'download:init';
		$post['z'] = urlencode($z[1]);
		$post['h'] = urlencode($h[1]);

		if (preg_match('@window\.[a-z|0-9]+ = (\d+);@', $this->page, $CD) && $CD[1] > 0) $this->CountDown($CD[1]);

		$posturl = 'http://www.badongo.com/ajax/prototype/ajax_api_filetemplate.php';
		$ref = $link."\r\nX-Requested-With: XMLHttpRequest";
		$page = $this->BD_cURL($posturl, $this->cookie, $post, $ref);

		$post['f'] = 'download:check';
		$post['z'] = urlencode(cut_str($page, "z': '", "'"));
		$post['h'] = urlencode(cut_str($page, "h': '", "'"));
		$post['t'] = urlencode(cut_str($page, "t': '", "'"));

		if (preg_match("@window\['\w+'\] = \"(\d+)\";@i", $page, $CD) && $CD[1] > 0) $this->CountDown($CD[1]+10);

		$page = $this->BD_cURL($posturl, $this->cookie, $post, $ref);
		if (preg_match('@window\.[a-z|0-9]+ = (\d+);@', $page, $CD) && $CD[1] > 0) {
			$this->CountDown($CD[1]+5);
			$page = $this->BD_cURL($posturl, $this->cookie, $post, $ref);
		}
		is_notpresent($page, "window.getFileLinkCanDownload", "Error: Countdown bypassed?");

		$rsargs = array();
		$rsargs['?'] = 0;
		$rsargs['gflZone'] = 'yellow';
		$rsargs['z'] = cut_str($page, "z': '", "'");
		$rsargs['h'] = cut_str($page, "h': '", "'");
		$rsargs['t'] = cut_str($page, "t': '", "'");
		$rsargs['type'] = $post['type'];
		$rsargs['id'] = $post['id'];
		$rsargs['ext'] = $post['ext'];

		$query = '';
		foreach ($rsargs as $val) $query .= 'rsargs[]='.urlencode($val).'&';
		$query = substr($query, 0, -1);

		$type = $post['type'] == 'vid' ? 'Vid' : 'File';
		$page = $this->BD_cURL($link."?rs=get{$type}Link&rst=&rsrnd=".jstime()."&$query", $this->cookie, $post);

		if (!preg_match("@\\\'(http://www\.badongo\.com/(?:f|v)d/[^\\\|\\']+)\\\'@i", $page, $dl)) html_error("Error: Download url not found.");
		if (!preg_match('@window\.location\.href = dlUrl \+ "([^"]+)" \+ val;@i', $this->page, $dl2)) html_error("Error: Download url not found 2.");
		$dlurl = $ref = $dl[1].$dl2[1].'?zenc=';

		for ($i = 1; $i <= 4; $i++) {
			$page = $this->BD_cURL($dlurl, $this->cookie);
			if (preg_match('/The link timed out/', $page)) {
				sleep(5);
				$page = $this->BD_cURL($dlurl, $this->cookie);
			}
			if (!preg_match('/The link timed out/', $page)) break;
		}

		if (!preg_match("@window\.location\.href = '([^']+)';@i", $page, $dlurl)) html_error("Error: Download url not found 3.");
		if (!strstr($dlurl[1], '://')) $dlurl = 'http://www.badongo.com'.$dlurl[1];
		else $dlurl = $dlurl[1];

		for ($i = 1; $i <= 4; $i++) {
			$page = $this->BD_cURL($dlurl, $this->cookie);
			if (preg_match('/The link timed out/', $page)) {
				sleep(5);
				$page = $this->BD_cURL($dlurl, $this->cookie);
			}
			if (!preg_match('/The link timed out/', $page)) break;
		}
		if (!preg_match('@Location: (https?://[^\r|\n]+)@i', $page, $dllink)) html_error("Error: Download url not found 4.");

		$dllink = str_replace('.0\\\?', '?', $dllink[1]);

		$url = parse_url($dllink);
		$FileName = urldecode(basename($url["path"]));
		$this->RedirectDownload($dllink, $FileName, $this->cookie, 0, $ref);
	}

	private function BDefuscate($content) {
		if (!preg_match_all('@eval\(\(function\(([\w|\,]+)\).*\)\)\("([^"]+)"\).*\)\("(.+)"\)\)?@i', $content, $js)) return $content;
		$defuscated = array();
		for ($i = 0; $i < count($js[0]); $i++) {
			$str = '';
			$script = array_combine(explode(',', $js[1][$i]), explode('","', $js[3][$i]));
			foreach (explode('+', urldecode($js[2][$i])) as $key)
				$str .= urldecode($script[$key]);
			$defuscated[] = $str;
		}

		$content = str_replace($js[0], $defuscated, $content);
		return $content;
	}

	private function BD_cURL($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0) {
		$page = cURL($link, $cookie, $post, $referer, $auth);
		$page = $this->BDefuscate($page);

		return $page;
	}

	public function CheckBack($headers) {
		is_present($headers, '403 Forbidden', 'You can only connect once per ip.');
		is_present($headers, '410 Gone', 'Download link has expired.');
	}

}

//[25-1-2012]  Written by Th3-822. (Free Download only)

?>