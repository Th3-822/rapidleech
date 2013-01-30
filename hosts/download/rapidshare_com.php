<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class rapidshare_com extends DownloadClass {
	public $apiurl, $fileid, $filename;
	private $cookie;
	public function Download($link) {
		global $premium_acc, $Referer;
		$this->cookie = '';
		$this->apiurl = array('scheme' => 'http'); // Add 's' for https :D
		$this->apiurl['host'] = 'api.rapidshare.com';
		$this->apiurl['path'] = '/cgi-bin/rsapi.cgi';

		$url = parse_url($link);
		if (!preg_match('@^/files/(\d+)/([^\r\n\t\s\?\&<>/]+)@i', $url['path'], $m) && !empty($url['fragment']) && !preg_match('@!download\|(?:[^\|]+)\|(\d+)\|([^\|]+)@i', $url['fragment'], $m)) html_error('Cannot get fileid or filename. Check your link.');
		$Referer = "https://rapidshare.com/files/{$m[1]}/{$m[2]}";
		$this->fileid = $m[1];
		$this->filename = str_replace(array('?', '&'), '', basename(rawurldecode($m[2])));

		if (($_REQUEST['cookieuse'] == 'on' && preg_match('@enc\s?=\s?(\w+)@i', $_REQUEST['cookie'], $c)) || ($_REQUEST['premium_acc'] == 'on' && !empty($premium_acc['rapidshare_com']['cookie']))) {
			$this->cookie = (empty($c[1]) ? $premium_acc['rapidshare_com']['cookie'] : $c[1]);
			$this->CheckLogin();
		} elseif ($_REQUEST['premium_acc'] == 'on' && (($pA = (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']))) || (!empty($premium_acc['rapidshare_com']['user']) && !empty($premium_acc['rapidshare_com']['pass'])))) {
			$user = ($pA ? $_REQUEST['premium_user'] : $premium_acc['rapidshare_com']['user']);
			$pass = ($pA ? $_REQUEST['premium_pass'] : $premium_acc['rapidshare_com']['pass']);
			$this->CheckLogin(rawurlencode($user), rawurlencode($pass));
		} else $this->StartDL();
	}

	private function StartDL() {
		$this->apiurl['query'] = 'sub=download&try=1&fileid=' . $this->fileid . '&filename=' . rawurlencode($this->filename);
		if (!empty($this->cookie)) $this->apiurl['query'] .= '&cookie=' . rawurlencode($this->cookie);
		$page = $this->GetPage(rebuild_url($this->apiurl));

		$err1 = array('ERROR: File owner\'s public traffic exhausted.' => 'File owner\'s public traffic exhausted.', 'ERROR: Download permission denied by uploader.' => 'Download permission denied by uploader.', 'ERROR: Filename invalid.' => 'Filename invalid. Please check the download link.', 'ERROR: File ID invalid.' => 'File ID invalid. Please check the download link.', 'ERROR: Server under repair.' => 'Server under repair. Please try again later');
		$err2 = array('ERROR: File not found.' => 0, 'ERROR: File physically not found.' => 0, 'ERROR: File deleted R1.' => 1, 'ERROR: File deleted R2.' => 1, 'ERROR: File deleted R3.' => 2, 'ERROR: File deleted R5.' => 2, 'ERROR: File deleted R4.' => 3, 'ERROR: File deleted R8.' => 3, 'ERROR: File deleted R10.' => 4, 'ERROR: File deleted R11.' => 4, 'ERROR: File deleted R12.' => 4, 'ERROR: File deleted R13.' => 4, 'ERROR: File deleted R14.' => 4, 'ERROR: File deleted R15.' => 4, 'ERROR: This file is marked as illegal.' => 4, 'ERROR: raid error on server.' => 5, 'ERROR: File incomplete.' => 5);
		// R10=Game;R11=Movie/Video;R12=Music;R13=Software;R14=Image;R15=Literature 
		$err2_txt = array('This file was not found on our server.', 'The file was deleted by the owner or the administrators.', 'The file was deleted due to our inactivity-rule (no downloads).', 'The file is suspected to be contrary to our terms and conditions and has been locked up for clarification.', 'The file has been removed from the server due of infringement of the copyright-laws.', 'The file is corrupted or incomplete.');

		foreach ($err1 as $err => $errn) is_present($page, $err, $errn);
		foreach ($err2 as $err => $errn) is_present($page, $err, $err2_txt[$errn]);

		$firstline = trim(substr($page, strpos($page, "\r\n\r\n") + 4));
		if (strpos($firstline, "\n") !== false) $firstline = trim(substr($firstline, 0, strpos($firstline, "\n")));
		$data = explode(':', $firstline, 2);
		if ($data[0] == 'DL') {
			$details = explode(',', $data[1]);
			$this->apiurl['host'] = $details[0];
			//$file_md5 = $details[3];
			unset($details, $this->apiurl['query']);

			$post = array('sub' => 'download', 'directstart' => '1', 'fileid' => $this->fileid, 'filename' => urlencode($this->filename));
			if (!empty($this->cookie)) $post['cookie'] = urlencode($this->cookie);
			return $this->RedirectDownload(rebuild_url($this->apiurl), '[T8]rs_dl', 0, $post);
		} elseif ($data[0] == 'ERROR') html_error('Error: ' . htmlentities($data[1]));
		else html_error('Unknown reply while checking link.');
	}

	private function CheckLogin($user = '', $pass = '') {
		if (!empty($this->cookie)) {
			$this->apiurl['query'] = 'sub=getaccountdetails&cookie=' . rawurlencode($this->cookie);
			$page = $this->GetPage(rebuild_url($this->apiurl));
			$t1 = 'Cookie';$t2 = 'cookie';
		} elseif (!empty($user) && !empty($pass)) {
			$this->apiurl['query'] = "sub=getaccountdetails&withcookie=1&login=$user&password=$pass";
			$page = $this->GetPage(rebuild_url($this->apiurl));
			$t1 = 'Error';$t2 = 'login details';
		} else html_error('Login Failed. User/Password empty.');

		is_present($page, 'ERROR: IP blocked.', '[ERROR] Rapidshare has locked your IP. (Too many wrong login/cookie sended)');
		is_present($page, 'ERROR: Login failed. Login data invalid.',
			"[$t1] Invalid $t2.");
		is_present($page, 'ERROR: Login failed. Password incorrect or account not found.', "[$t1] Login failed. User/Password incorrect or could not be found.");
		is_present($page, 'ERROR: Login failed. Account not validated.', "[$t1] Login failed. Account not validated.");
		is_present($page, 'ERROR: Login failed. Account locked.', "[$t1] Login failed. Account locked.");
		is_present($page, 'ERROR: Login failed.', "[$t1] Login failed. Invalid $t2?");

		if (empty($this->cookie)) {
			$body = substr($page, strpos($page, "\r\n\r\n") + 4);
			if (!preg_match('@\Wcookie=(\w+)@i', $body, $cookie)) html_error('Cookie value not found.');
			$this->cookie = $cookie[1];
		}
		$this->StartDL();
	}
}

//[06-12-2012] Rewritten by Th3-822.

?>