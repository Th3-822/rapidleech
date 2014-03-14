<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class zippyshare_com extends DownloadClass {
	public $link;
	private $page, $cookie;
	public function Download($link) {
		$this->link = $link;
		$this->cookie = array('ziplocale' => 'en');

		if (!empty($_POST['step'])) switch ($_POST['step']) {
			case '1': return $this->CheckCaptcha();
			case '2': return $this->GetDecodedLink();
		}

		$this->page = $this->GetPage($this->link, $this->cookie);
		is_present($this->page, '>File does not exist on this server<', 'File does not exist.');
		is_present($this->page, '>File has expired and does not exist anymore on this server', 'File does not exist.');
		$this->cookie = GetCookiesArr($this->page, $this->cookie);

		if (($pos = stripos($this->page, 'getElementById(\'dlbutton\').href')) !== false || ($pos = stripos($this->page, 'getElementById("dlbutton").href')) !== false) return $this->GetJSEncodedLink($pos);
		else return $this->GetCaptcha();
	}

	private function GetDecodedLink() {
		if (empty($_POST['dllink']) || ($dlink = parse_url($_POST['dllink'])) == false || empty($dlink['path'])) html_error('Empty decoded link field.');
		$this->cookie = urldecode($_POST['cookie']);

		$dlink = 'http://' . parse_url($this->link, PHP_URL_HOST) . $dlink['path'] . (!empty($dlink['query']) ? '?' . $dlink['query'] : '');
		$fname = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
		$this->RedirectDownload($dlink, $fname, $this->cookie);
	}

	private function GetJSEncodedLink($pos) {
		global $PHP_SELF;
		$script = substr(strstr(substr($this->page, strripos(substr($this->page, 0, $pos), '<script ')), '>'), 1);
		$script = rtrim(str_replace(array(').href', "'dlbutton'", '"dlbutton"', '    '), array(').value', "'T8_dllink'", '"T8_dllink"', "\t\t"), substr($script, 0, strpos($script, '</script>'))));
		if (empty($script)) html_error('Error while getting js code.');
		$T8 = '';
		if (preg_match_all('@getElementById[\s\t]*\([\s\t]*[\"\']([a-z][\w\.\-]*)[\"\'][\s\t]*\)@i', $script, $ids) && count($ids[0]) > 1) foreach (array_unique($ids[1]) as $id) {
			if ($id == 'T8_dllink') continue;
			if (!preg_match("@<([a-z][a-z\d]*)\s*(?:\w+\s*=\s*[\"\'][^\"\'<>]*[\"\']\s*)*(?:\s*id\s*=[\"\']{$id}[\"\']\s*)(?:\w+\s*=\s*[\"\'][^\"\'<>]*[\"\']\s*)*(?:(\s*>[^<>]*</\1)|(/)?[\s]*>)@i", $this->page, $tag)) break; // If it doesn't found the tag the decode will fail, im not sure if break or continue...
			$T8 .= (!empty($tag[2]) ? $tag[0].'>' : (empty($tag[3]) ? $tag[0].'</'.$tag[1].'>' : $tag[0]));
		}
		if (preg_match('@^\s*function\s+([\$_A-Za-z][\$\w]*)\s*\(@i', $script, $funcName)) $script .= "\n\t\t{$funcName[1]}();";

		$data = $this->DefaultParamArr($this->link, $this->cookie);
		$data['step'] = '2';
		$data['dllink'] = '';
		echo "\n<div style='display:none;'>$T8</div>\n<form name='zs_dcode' action='$PHP_SELF' method='POST'><br />\n";
		foreach ($data as $name => $input) echo "<input type='hidden' name='$name' id='T8_$name' value='$input' />\n";
		echo("</form>\n<span id='T8_emsg' class='htmlerror' style='text-align:center;display:none;'></span>\n<noscript><span class='htmlerror'><b>Sorry, this code needs JavaScript enabled to work.</b></span></noscript>\n<script type='text/javascript'>/* <![CDATA[ */\n\tvar T8 = true;\n\ttry {{$script}\n\t} catch(e) {\n\t\t$('#T8_emsg').html('<b>Cannot decode link: ['+e.name+'] '+e.message+'</b>').show();\n\t\tT8 = false;\n\t}\n\tif (T8) window.setTimeout(\"$('form[name=zs_dcode]').submit();\", 300); // 300 µs to make sure that the value was decoded and added.\n/* ]]> */</script>\n\n</body>\n</html>");
		exit;
	}

	private function GetCaptcha() {
		if (!preg_match('@/d/\d+/(\d+)/[^\r\n\t\'\"<>\;]+@i', $this->page, $dlpath)) html_error('Download Link Not Found.');
		if (!preg_match('@Recaptcha\.create[\s\t]*\([\s\t]*\"([\w\-]+)\"@i', $this->page, $cpid)) html_error('reCAPTCHA Not Found.');
		//if (!preg_match('@\Wshortencode[\s\t]*:[\s\t]*\'?(\d+)\'?@i', $this->page, $short)) html_error('Captcha Data Not Found.');

		$data = $this->DefaultParamArr($this->link, $this->cookie);
		$data['step'] = '1';
		$data['dlpath'] = urlencode($dlpath[0]);
		$data['shortencode'] = urlencode($dlpath[1]);

		$this->reCAPTCHA($cpid[1], $data);
	}

	private function CheckCaptcha() {
		if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
		$host = 'http://' . parse_url($this->link, PHP_URL_HOST);
		$this->cookie = urldecode($_POST['cookie']);

		$post = array();
		$post['challenge'] = $_POST['recaptcha_challenge_field'];
		$post['response'] = $_POST['recaptcha_response_field'];
		$post['shortencode'] = $_POST['shortencode'];

		$page = $this->GetPage($host . '/rest/captcha/test', $this->cookie, $post, $this->link . "\r\nX-Requested-With: XMLHttpRequest");
		$body = strtolower(trim(substr($page, strpos($page, "\r\n\r\n"))));

		if ($body == 'false') html_error('Error: Wrong CAPTCHA Entered.');
		elseif ($body != 'true') html_error('Unknown Reply from Server.');

		$dlink = $host . urldecode($_POST['dlpath']);
		$fname = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
		$this->RedirectDownload($dlink, $fname, $this->cookie);
	}
}

// [24-11-2012]  Written by Th3-822. (Only for rev43 :D)
// [05-2-2013]  Added support for links that need user-side decoding of the link. - Th3-822
// [04-3-2013]  Fixed File doesn't exists error msg... - Th3-822
// [07-3-2014]  Fixed link decoder function. - Th3-822

?>