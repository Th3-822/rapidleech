<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class d1fichier_com extends DownloadClass {
	private $page, $cookie = array('LG' => 'en'), $pA;
	public function Download($link) {
		$this->LnkRegexp = '@https?://(?:www\.)?((?:1fichier|alterupload|desfichiers|dfichiers|pjointe|tenvoi|dl4free)\.com|(?:cjoint|piecejointe)\.net|mesfichiers\.org|megadl\.fr)/\?([\w\-]+)@i';

		$link = preg_replace('@//([\w\-]{4,})\.((?:1fichier|alterupload|desfichiers|dfichiers|pjointe|tenvoi|dl4free)\.com|(?:cjoint|piecejointe)\.net|mesfichiers\.org|megadl\.fr)/$@i', '//$2/?$1', $link); // Let's support old links by now

		$link = parse_url($link);
		$link['scheme'] = 'https';
		$link = rebuild_url($link);

		if (!preg_match($this->LnkRegexp, $link, $fid)) html_error('Invalid link?.');
		$this->domain = $fid[1];
		$this->link = $Referer = $fid[0];
		$this->fid = $fid[2];

		$this->DLRegexp = '@https?://\w+-\w+\.((?:1fichier|alterupload|desfichiers|dfichiers|pjointe|tenvoi|dl4free)\.com|(?:cjoint|piecejointe)\.net|mesfichiers\.org|megadl\.fr)/(?:\w+'.preg_quote($this->fid).'|\w\d+)(/[^\s\'\"<>]*)?@i';

		if (empty($_POST['step'])) {
			$this->page = $this->GetPage($this->link, $this->cookie);
			is_present($this->page, 'The requested file has been deleted because was not downloaded within', 'File was Removed by Inactivity.');
			is_present($this->page, 'The requested file has been deleted following an abuse request', 'File was Removed due to Abuse.');
			is_present($this->page, 'The requested file could not be found', 'File not Found.');
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

	private function FreeDL() {
		$post = array('submit' => 'Download');
		$page = $this->GetPage($this->link, $this->cookie, $post);
		is_present($page, 'you can only download one file at a time');
		$this->cookie = GetCookiesArr($page, $this->cookie);

		if (preg_match($this->DLRegexp, $page, $dl)) return $this->RedirectDownload($dl[0], (empty($dl[2]) ? 'T8_1f_f2' : urldecode(parse_url($dl[0], PHP_URL_PATH))));

		is_present($page, 'you must wait between downloads', 'You must wait 15 minutes between downloads');

		if (!preg_match('@[\s,;]var\s+count\s*=\s*(\d+)\s*;@i', $page, $cD)) html_error('Countdown not found.');
		if ($cD[1] > 0) $this->CountDown($cD[1]);

		$post = array();
		$post['submit'] = cut_str($this->page, 'name="submit" value="', '"');
		$post['t'] = cut_str($this->page, 'name="t" value="', '"');
		if (empty($post['submit'])) $post['submit'] = 'Show the download link';
		if (empty($post['t'])) html_error('Form data not found.');

		$page = $this->GetPage($this->link, $this->cookie, $post);
		$this->cookie = GetCookiesArr($page, $this->cookie);

		if (!preg_match($this->DLRegexp, $page, $dl)) html_error('Download Link Not Found.');

		return $this->RedirectDownload($dl[0], (empty($dl[2]) ? 'T8_1f_f1' : urldecode(parse_url($dl[0], PHP_URL_PATH))));
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
		if (stripos($page, '/console/index.pl" class="premium"') === false) {
			$this->changeMesg(lang(300).'<br /><b>Account isn\\\'t premium</b><br />Using it as member.');
			$this->page = $this->GetPage($this->link, $this->cookie);
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
			return $this->FreeDL();
		}

		return $this->PremiumDL();
	}

	// As some users doesn't have support por https downloads, let's switch to http
	public function RedirectDownload($link, $FileName, $cookie = 0, $post = 0, $referer = 0, $force_name = 0, $auth = 0, $addon = array()) {
		$link = parse_url($link);
		$link['scheme'] = 'http';
		$link = rebuild_url($link);
		return parent::RedirectDownload($link, $FileName, $cookie, $post, $referer, $force_name, $auth, $addon);
	}
}

//[08-4-2014] Written by Th3-822.
//[18-4-2014] Fixed Link Regexp. - Th3-822
//[17-12-2014] Un-tested fixes for changes at the site. - Th3-822

?>