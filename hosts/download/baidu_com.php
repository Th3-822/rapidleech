<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class baidu_com extends DownloadClass {
	private $userAgent = 'netdisk;5.4.9;PC;PC-Windows;6.1.7601;WindowsBaiduYunGuanJia';
	public function Download($link) {
		if (!preg_match('@(https?://(?:pan|yun)\.baidu\.com)/(?:s/\w+|(?:(?:wap/)?share|wap)/link\?\w+=\w+(?:&\w+=\w+)*)@i', $link, $_link)) html_error('Invalid link?.');
		$link = $GLOBALS['Referer'] = str_ireplace(array('https://', '/wap/link', '/wap/'), array('http://', '/share/link', '/'), $_link[0]);
		if (function_exists('headers2request')) $GLOBALS['Referer'] .= "\r\nUser-Agent: {$this->userAgent}"; // Changed User-Agent to "increase" download speed
		$host = $_link[1];
		$cookie = isset($_POST['step']) && !empty($_POST['cookie']) ? StrToCookies(decrypt(urldecode($_POST['cookie']))) : array();

		$page = $this->GetPage($link, $cookie);
		$cookie = GetCookiesArr($page, $cookie);

		// Baidu doesn't reply /s/ links without a valid BAIDUID cookie :S
		if (substr($page, 9, 3) == '403') {
			$page = $this->GetPage($link, $cookie);
			$cookie = GetCookiesArr($page, $cookie);
		}

		is_present($page, '啊哦，你所访问的页面不存在了', 'File Doesn\'t Exist');

		if (!preg_match('@yunData\.setData\((\{.+?\})\);@s', $page, $data)) html_error('yunData.setData Not Found.');
		$data = preg_replace('@([:,\[\]{}])(\d{11,})([:,\[\]{}])@', '$1"$2"$3', $data[1]); // Avoid Issues With Long IDs
		$yunData = $this->json2array($data, 'Error while parsing yunData');

		$post = array('encrypt' => 0, 'product' => 'share', 'uk' => $yunData['uk'], 'primaryid' => $yunData['shareid'], 'fid_list' => "%5B{$yunData['file_list']['list'][0]['fs_id']}%5D");
		if (!empty($_POST['step']) && $_POST['step'] == '1') {
			$_POST['step'] = false;
			if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
			if (empty($_POST['vcode_str'])) html_error('Empty Captcha Challenge.');
			$post['vcode_input'] = urlencode($_POST['captcha']);
			$post['vcode_str'] = urlencode($_POST['vcode_str']);
		}

		$json = $this->GetPage("$host/api/sharedownload?app_id={$yunData['file_list']['list'][0]['app_id']}&bdstoken={$yunData['bdstoken']}&channel=chunlei&clienttype=0&sign={$yunData['sign']}&timestamp={$yunData['timestamp']}&web=1", $cookie, $post, $GLOBALS['Referer'] . "\r\nX-Requested-With: XMLHttpRequest");
		$data = $this->json2array($json, 'Error getting download info');

		if (!empty($data['errno'])) {
			if ($data['errno'] == -20) {
				$captcha = $this->json2array($this->GetPage("{$host}/api/getcaptcha?app_id={$yunData['file_list']['list'][0]['app_id']}&bdstoken={$yunData['bdstoken']}&channel=chunlei&clienttype=0&prod=share&web=1", $cookie, $post, $GLOBALS['Referer'] . "\r\nX-Requested-With: XMLHttpRequest"), 'Error getting captcha data');
				if (!empty($captcha['errno'])) html_error("Unknown captcha error [{$captcha['errno']}]");

				$data = $this->DefaultParamArr($link, $cookie, 1, 1);
				$data['step'] = 1;
				$data['vcode_str'] = $captcha['vcode_str'];
				list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage($captcha['vcode_img']), 2);
				if (substr($headers, 9, 3) != '200') html_error('Error downloading CAPTCHA img.');
				$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/jpg');
				return $this->EnterCaptcha("data:$mimetype;base64,".base64_encode($imgBody), $data);
			}
			html_error("Unknown download-info error [{$data['errno']}]");
		}

		if (empty($data['list'][0]['dlink'])) html_error('Redirect-Link Not Found.');
		if (empty($cookie['pcsett'])) {
			// Extra Cookie (Needed to Download)
			$plantCookie = $this->GetPage('http://pcs.baidu.com/rest/2.0/pcs/file?method=plantcookie&type=ett', $cookie);
			$cookie = GetCookiesArr($plantCookie, $cookie);
			if (empty($cookie['pcsett'])) {
				textarea($plantCookie);
				html_error('Failed to get Anti-HotLink Cookie.');
			}
		}

		$page = $this->GetPage($data['list'][0]['dlink'], $cookie);
		if (!preg_match('@https?://(?:[\w\-]+\.)+baidupcs(?:\.[\w\-]+)*\.com/file/[^\s\"\'<>]+@i', $page, $DL)) html_error('Download-Link Not Found.');

		return $this->RedirectDownload($DL[0], 'pan_baidu_com_placeholder');
	}
}

// [26-8-2016] Written by Th3-822.
// [30-8-2016] Switched links to http to help decrease connection errors & Changed User-Agent for "better" download speed. - Th3-822
// [18-4-2017] Fixed. - Th3-822