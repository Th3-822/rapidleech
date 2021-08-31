<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class d1fichier_com extends DownloadClass {
	private $lpass, $page, $cookie = array('LG' => 'en'), $pA;
	public function Download($link) {
		$this->LnkRegexp = '@https?://(?:www\.)?((?:1fichier|alterupload|desfichiers|dfichiers|pjointe|tenvoi|dl4free)\.com|(?:cjoint|piecejointe)\.net|mesfichiers\.org|megadl\.fr)/\?([\w\-]+)@i';

		$link = explode('|', str_ireplace('%7C', '|', $link), 2);
		if (count($link) > 1) $this->lpass = rawurldecode($link[1]);

		$link = preg_replace('@//([\w\-]{4,})\.((?:1fichier|alterupload|desfichiers|dfichiers|pjointe|tenvoi|dl4free)\.com|(?:cjoint|piecejointe)\.net|mesfichiers\.org|megadl\.fr)/[^\r\n\t\'\"<>]*$@i', '//$2/?$1', $link[0]); // Let's support old links by now

		$link = parse_url($link);
		$link['scheme'] = 'https';
		$link = rebuild_url($link);

		if (!preg_match($this->LnkRegexp, $link, $fid)) html_error('Invalid link?.');
		$this->domain = $fid[1];
		$this->link = $GLOBALS['Referer'] = $fid[0];
		$this->fid = $fid[2];

		$this->DLRegexp = '@https?://\w+-\w+\.((?:1fichier|alterupload|desfichiers|dfichiers|pjointe|tenvoi|dl4free)\.com|(?:cjoint|piecejointe)\.net|mesfichiers\.org|megadl\.fr)/(?:\w+'.preg_quote($this->fid).'|\w\d+)(/[^\s\'\"<>]*)?@i';

		if (empty($_POST['step'])) {
			$this->page = $this->GetPage($this->link, $this->cookie);
			is_present($this->page, 'The requested file has been deleted because was not downloaded within', 'File was Removed by Inactivity.');
			is_present($this->page, 'The requested file has been deleted following an abuse request', 'File was Removed due to Abuse.');
			is_present($this->page, 'The requested file could not be found', 'File not Found.');
			$this->CheckPass($this->page);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);

			if (preg_match($this->DLRegexp, $this->page, $dl)) return $this->RedirectDownload($dl[0], (empty($dl[2]) ? 'T8_1f_d1' : urldecode(parse_url($dl[0], PHP_URL_PATH))));
		}

		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($GLOBALS['premium_acc']['1fichier_com']['user']) && !empty($GLOBALS['premium_acc']['1fichier_com']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['1fichier_com']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['1fichier_com']['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
			return $this->Login($user, $pass);
		} else return $this->FreeDL();
	}

	private function FreeDL($waited = 0) {
		$post = (empty($this->lpass) ? array('submit' => 'Download') : array('pass' => urlencode($this->lpass)));
		$post['adz'] = cut_str($this->page, 'name="adz" value="', '"');

		$page = $this->GetPage($this->link, $this->cookie, $post);
		is_present($page, 'you can only download one file at a time');
		$this->cookie = GetCookiesArr($page, $this->cookie);

		is_present($page, 'The link your requested is expired','The download link is expired, please try again.');
		
		if (preg_match($this->DLRegexp, $page, $dl)) return $this->RedirectDownload($dl[0], (empty($dl[2]) ? ($waited ? 'T8_1f_f1' : 'T8_1f_f2') : urldecode(parse_url($dl[0], PHP_URL_PATH))));

		if (preg_match('@\(\'\.clock\'\),\s*(\d+)\s*\*\s*60,\s*{\s*clockFace:@i', $page, $cD) && $cD[1] > 0) {
			if ($cD[1] > 1) {
				$data = $this->DefaultParamArr($this->link . (!empty($this->lpass) ? "|{$this->lpass}" : ''));
				return $this->JSCountdown($cD[1] * 60, $data);
			} else $this->CountDown($cD[1] * 60);
		} else $this->CheckPass($page, true);

		if ($waited > 1) html_error('Too many countdown retries, check concurrent downloads.');
		return $this->FreeDL($waited + 1);
	}

	private function PremiumDL() {
		$page = $this->GetPage($this->link, $this->cookie);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		is_present($page, 'Premium status should not be used on professional services. Use download credits.'); // IP Ban?
		if (!preg_match($this->DLRegexp, $page, $dl)) html_error('Download-Link Not Found.');

		return $this->RedirectDownload($dl[0], (empty($dl[2]) ? 'T8_1f_pr' : urldecode(parse_url($dl[0], PHP_URL_PATH))));
	}

	private function Login($user, $pass) {
		$purl = 'https://'.$this->domain.'/';

		$post = array();
		$post['mail'] = urlencode($user);
		$post['pass'] = urlencode($pass);
		$post['lt'] = 'on';
		$post['restrict'] = 'on';
		$post['valider'] = 'Send';

		$page = $this->GetPage($purl.'login.pl', $this->cookie, $post, $purl);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		is_present($page, 'Invalid email address');
		is_present($page, 'Invalid username or password', 'Login Failed: Email/Password incorrect.');

		$page = $this->GetPage($purl, $this->cookie, 0, $purl.'login.pl');
		is_notpresent($page, 'logout.pl">Logout', 'Login Error.');
		if (stripos($page, 'https://img.1fichier.com/icons/etoile.png') === false) {
			$this->changeMesg(lang(300).'<br /><b>Account isn\\\'t premium</b><br />Using it as member.');
			$this->page = $this->GetPage($this->link, $this->cookie);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			return $this->FreeDL();
		}

		return $this->PremiumDL();
	}

	// As some users doesn't have support por https downloads, let's switch to http
	public function RedirectDownload($link, $FileName = 0, $cookie = 0, $post = 0, $referer = 0, $force_name = 0, $auth = 0, $addon = array()) {
		$link = parse_url($link);
		$link['scheme'] = 'http';
		$link = rebuild_url($link);
		return parent::RedirectDownload($link, $FileName, $cookie, $post, $referer, $force_name, $auth, $addon);
	}

	private function CheckPass($page, $dl = false) {
		if (stripos($page, 'The owner of this file has chosen to protect access with a password.') !== false || stripos($page, 'warn">Bad password<') !== false) {
			if (empty($this->lpass)) html_error('File is password protected, please send the password in this format: link|pass');
			else if ($dl) html_error('The link\'s password you have sent is not valid.');
		}
	}
}

//[08-4-2014] Written by Th3-822.
//[18-4-2014] Fixed Link Regexp. - Th3-822
//[17-12-2014] Un-tested fixes for changes at the site. - Th3-822
//[14-10-2018] fixed countdown and form data. - miyuru
//[31-8-2021] Added link password support (FreeDL only at the moment). - Th3-822