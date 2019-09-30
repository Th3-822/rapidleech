<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class zippyshare_com extends DownloadClass {
	public $link;
	private $page, $cookie, $fid;
	public function Download($link) {
		$this->link = str_ireplace('http://', 'https://', $link);
		$this->cookie = array('ziplocale' => 'en');

		if (!preg_match('@https?://(?:[\w\-]+\.)*zippyshare\.com/\w/(\w+)@i', $this->link, $this->fid)) html_error('File ID not found at link. Invalid link?');
		$this->fid = $this->fid[1];

		if (!empty($_POST['step'])) switch ($_POST['step']) {
			case '1': return $this->CheckCaptcha();
			case '2': return $this->GetDecodedLink();
		}

		$this->page = $this->GetPage($this->link, $this->cookie);
		is_present($this->page, '>File does not exist on this server<', 'File does not exist.');
		is_present($this->page, '>File has expired and does not exist anymore on this server', 'File does not exist.');
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		//if (($pos = stripos($this->page, 'getElementById(\'dlbutton\').href')) !== false || ($pos = stripos($this->page, 'getElementById("dlbutton").href')) !== false) return $this->GetJSEncodedLink();
		if ($this->findJS()) return $this->GetJSEncodedLink();
		else return $this->GetCaptcha();
	}

	private function GetDecodedLink() {
		if (empty($_POST['dllink']) || ($dlink = parse_url($_POST['dllink'])) == false || empty($dlink['path'])) html_error('Empty decoded link field.');
		$this->cookie = urldecode($_POST['cookie']);

		$dlink = 'http://' . parse_url($this->link, PHP_URL_HOST) . $dlink['path'] . (!empty($dlink['query']) ? '?' . $dlink['query'] : '');
		$fname = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
		$this->RedirectDownload($dlink, $fname, $this->cookie);
	}

	private function findJS() {
		if (!preg_match_all('@<script(?:\s[^>]*)?>([^<]+)</script>@i', $this->page, $scripts)) html_error('No inline JS found at page.');
		foreach ($scripts[1] as $script) {
			if (preg_match('@\.getElementById\(\s*(?:(\'|\")(?i)(?:dlbutton|fimage)(?-i)\1|([\$_A-Za-z][\$\w]*))\)\.href\s*=\s*[\'\"](?i)(?:https?://(?:[\w\-]+\.)*zippyshare\.com)?/d/(?-i)'.$this->fid.'@', $script, $match)) {
				if (!empty($match[2])) $this->vname = $match[2];
				$this->script = $script;
				return true;
			}
			if (preg_match('@((?:[\$_A-Za-z][\$\w]*\.)*[\$_A-Za-z][\$\w]*)\s*\(\s*(?:(\'|\")(?:dlbutton|fimage)(?-i)\2|([\$_A-Za-z][\$\w]*))\s*,\s*[\'\"](?:https?://(?:[\w\-]+\.)*zippyshare\.com)?/d/(?-i)'.$this->fid.'@i', $script, $match)) {
				$this->fname = $match[1];
				if (!empty($match[3])) $this->vname = $match[3];
				$this->script = $script;
				return true;
			}
		}
		return false;
	}

	private function GetJSEncodedLink() {
		$this->script = rtrim(str_replace(array(').href', "'dlbutton'", '"dlbutton"', '    '), array(').value', "'T8_dllink'", '"T8_dllink"', "\t\t"), $this->script));
		if (!empty($this->fname)) $this->script = str_replace($this->fname, 'zsWriteLink', $this->script);
		if (empty($this->script)) html_error('Error while getting js code.');

		$T8 = '';
		if (preg_match_all('@getElementById[\s\t]*\([\s\t]*[\"\']([a-z][\w\.\-]*)[\"\'][\s\t]*\)@i', $this->script, $ids) && count($ids[0]) > 1) foreach (array_unique($ids[1]) as $id) {
			if ($id == 'T8_dllink') continue;
			if (!preg_match("@<([a-z][a-z\d]*)\s*(?:\w+\s*=\s*[\"\'][^\"\'<>]*[\"\']\s*)*(?:\s*id\s*=[\"\']{$id}[\"\']\s*)(?:\w+\s*=\s*[\"\'][^\"\'<>]*[\"\']\s*)*(?:(\s*>[^<>]*</\1)|(/)?[\s]*>)@i", $this->page, $tag)) break; // If it doesn't found the tag the decode will fail, im not sure if break or continue...
			$T8 .= (!empty($tag[2]) ? $tag[0].'>' : (empty($tag[3]) ? $tag[0].'</'.$tag[1].'>' : $tag[0]));
		}
		$this->script = preg_replace('@^\s*(?:function\s+[\$_A-Za-z][\$\w]*|(?:var\s+)?[\$_A-Za-z][\$\w]*\s*=\s*function)\s*\([^)]*\)\s*\{\s*[\$_A-Za-z][\$\w]*\.\w+\s*\(\s*[\'\"]\w+[\'\"]\s*\)\.value\s*=\s*[\'\"][^\'\"]?[\'\"]*\s*;\s*\};?@', '', $this->script, 1);
		if (preg_match('@^\s*function\s+([\$_A-Za-z][\$\w]*)\s*\(@i', $this->script, $funcName) || preg_match('@^\s*(?:var\s+)?([\$_A-Za-z][\$\w]*)\s*=\s*function\s*\(@i', $this->script, $funcName)) $this->script .= "\n\t\t{$funcName[1]}();";

		$data = $this->DefaultParamArr($this->link, $this->cookie);
		$data['step'] = '2';
		$data['dllink'] = '';
		echo "\n<div style='display:none;'>$T8</div>\n<form name='zs_dcode' action='{$GLOBALS['PHP_SELF']}' method='POST'><br />\n";
		foreach ($data as $name => $input) echo "<input type='hidden' name='$name' id='T8_$name' value='" . htmlspecialchars($input, ENT_QUOTES) . "' />\n";
		echo("</form>\n<span id='T8_emsg' class='htmlerror' style='text-align:center;display:none;'></span>\n<noscript><span class='htmlerror'><b>Sorry, this code needs JavaScript enabled to work.</b></span></noscript>\n<script type='text/javascript'>/* <![CDATA[ Th3-822 */\n\tvar T8 = true;\n\tfunction zsWriteLink(a, b) {\n\t\tdocument.getElementById(a).value = b;\n\t}\n\ttry {{$this->script}\n\t} catch(e) {\n\t\t$('#T8_emsg').html('<b>Cannot decode link: ['+e.name+'] '+e.message+'</b>').show();\n\t\tT8 = false;\n\t}\n\tif (T8) window.setTimeout(\"$('form[name=zs_dcode]').submit();\", 300); // 300 µs to make sure that the value was decoded and added.\n/* ]]> */</script>\n\n</body>\n</html>");
		exit;
	}

	private function GetCaptcha() {
		if (!preg_match('@/d/\w+/(\d+)/[^\r\n\t\'\"<>\;]+@i', $this->page, $dlpath)) html_error('Download Link Not Found.');
		if (!preg_match('@\'sitekey\'\s*:\s*[\'\"]([\w\.\-]+)[\'\"]@i', $this->page, $cpid)) html_error('reCAPTCHA2 Not Found.');
		//if (!preg_match('@\Wshortencode[\s\t]*:[\s\t]*\'?(\d+)\'?@i', $this->page, $short)) html_error('Captcha Data Not Found.');

		$data = $this->DefaultParamArr($this->link, $this->cookie);
		$data['step'] = '1';
		$data['dlpath'] = $dlpath[0];
		$data['shortencode'] = $dlpath[1];

		$this->reCAPTCHAv2($cpid[1], $data);
	}

	// Special Function Called by verifyReCaptchav2 When Captcha Is Incorrect, To Allow Retry. - Required
	protected function retryReCaptchav2() {
		$data = $this->DefaultParamArr($this->link, $this->cookie);
		$data['step'] = '1';
		$data['shortencode'] = (!empty($_POST['shortencode']) ? $_POST['shortencode'] : '');
		$data['dlpath'] = (!empty($_POST['dlpath']) ? $_POST['dlpath'] : '');

		return $this->reCAPTCHAv2($_POST['recaptcha2_public_key'], $data);
	}

	private function CheckCaptcha() {
		$host = 'https://' . parse_url($this->link, PHP_URL_HOST);
		$this->cookie = urldecode($_POST['cookie']);

		$post = array();
		$post['response'] = $this->verifyReCaptchav2(true);
		$post['shortencode'] = urlencode($_POST['shortencode']);

		$page = $this->GetPage($host . '/rest/captcha/test', $this->cookie, $post, $this->link . "\r\nX-Requested-With: XMLHttpRequest");
		$body = strtolower(trim(substr($page, strpos($page, "\r\n\r\n"))));

		if ($body == 'false') html_error('Error: Wrong CAPTCHA Entered.');
		elseif ($body != 'true') html_error('Unknown Reply from Server.');

		$dlink = $host . $_POST['dlpath'];
		$fname = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
		$this->RedirectDownload($dlink, $fname, $this->cookie);
	}
}

// [24-11-2012]  Written by Th3-822. (Only for rev43 :D)
// [05-2-2013]  Added support for links that need user-side decoding of the link. - Th3-822
// [04-3-2013]  Fixed File doesn't exists error msg... - Th3-822
// [25-3-2014]  Fixed link decoder function. - Th3-822
// [11-1-2015] Fixed link regexp. (Happy new year) - Th3-822
// [07-3-2015]  Added Support for v2 reCAPTCHAs. - Th3-822
// [13-3-2015]  Quick fix to decoder function. - Th3-822
// [24-6-2018] Switched to https & small changes. - Th3-822