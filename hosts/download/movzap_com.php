<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit;
}

class movzap_com extends DownloadClass {
	public function Download($link) {
		$page = $this->GetPage($link, 'lang=english');
		is_present($page, "<h2>File Not Found</h2>", 'Video not found or it was deleted.');
		is_present($page, "Video is not encoded yet.", 'Video is not encoded yet. Please wait some minutes.');
		is_present($page, "The file you were looking for could not be found");

		$page2 = cut_str($page, '<form name="F1" method="POST"', '</form>'); //Cutting page

		if (!preg_match('@name="id" value="([^"]+)"@i', $page2, $id)) html_error("Video ID not found.");

		$page = $this->GetPage("http://movzap.com/xml/{$id[1]}.xml", 'lang=english');
		if (!preg_match('@<title><!\[CDATA\[([^\[|\]]+)\]\]></title>@i', $page, $title)) html_error("Video Title not found.");

		$page = $this->GetPage("http://movzap.com/xml2/{$id[1]}.xml", 'lang=english');
		if (!preg_match('@<hd\.file><!\[CDATA\[([^\[|\]]+)\]\]></hd\.file>@i', $page, $encrypted))
			if (!preg_match('@<file><!\[CDATA\[([^\[|\]]+)\]\]></file>@i', $page, $encrypted)) html_error("Encrypted link not found.");
		$down = $this->decryptme(substr($encrypted[1], 0, -6)) or $down = $this->decryptme($encrypted[1]);
		if ($down === false) html_error("Cannot decrypt download link");

		if(!preg_match('@\.[^\.]+$@i', basename($down), $ext)) $ext = array('.mp4');
		$badchars = '<>:"/\\|?*\'@#+~{}[]^';
		$fname = str_replace(str_split($badchars), "_", trim($title[1])) . $ext[0];
		$this->RedirectDownload($down."?start=0", $fname, 0, 0, 0, $fname);
	}

	private function decryptme($encrypted) {
		$KEY = 'N%66=]H6';
		$method = MCRYPT_DES;
		$mode = MCRYPT_MODE_ECB;

		$iv_size = mcrypt_get_iv_size($method, $mode);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

		return $this->pkcs5_unpad(mcrypt_decrypt($method, $KEY, base64_decode($encrypted), $mode, $iv));
	}

	// Source: A comment @ http://php.net/manual/en/ref.mcrypt.php
	private function pkcs5_unpad($text) {
		$pad = ord($text{strlen($text)-1});
		if ($pad > strlen($text)) return false;
		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
		return substr($text, 0, -1 * $pad);
	}
}

//[06-3-2012]  Written by Th3-822.

?>