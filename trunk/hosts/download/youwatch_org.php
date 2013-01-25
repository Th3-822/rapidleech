<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class youwatch_org extends DownloadClass {
	public function Download($link) {
		global $premium_acc;
		$cookie = array('lang' => 'english');

		$page = $this->GetPage($link, $cookie);
		is_present($page, "The file you were looking for could not be found");

		$page2 = cut_str($page, 'Form method="POST" action=\'', '</form>'); //Cutting page
		$post = array();
		$post['op'] = cut_str($page2, 'name="op" value="', '"');
		$post['usr_login'] = '';
		$post['id'] = cut_str($page2, 'name="id" value="', '"');
		$FileName = $post['fname'] = cut_str($page2, 'name="fname" value="', '"');
		$post['referer'] = '';
		$post['hash'] = cut_str($page2, 'name="hash" value="', '"');
		$post['method_free'] = cut_str($page2, 'name="method_free" value="', '"');

		if (!preg_match('@<span id="countdown_str">[^<|>]+<span[^>]*>(\d+)</span>[^<|>]+</span>@i', $page2, $count)) $count = array(1=>30);
		if ($count[1] > 0) $this->CountDown($count[1]);

		$page = $this->GetPage($link, $cookie, $post);
		is_present($page, ">Skipped countdown", "Error: Skipped countdown?.");

		if (!preg_match('@eval\s*\(\s*function\s*\(p,a,c,k,e,d\)\s*\{[^\}]+\}\s*\(\s*\'([^\r|\n]*)\'\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*\'([^\']+)\'\.split\([\'|\"](.)[\'|\"]\)\)\)@', $page, $js)) html_error('Error: Embed code not found.');
		$embed = $this->JSun_packer($js[1], $js[2], $js[3], $js[4], $js[5]);

		if (!preg_match('@file\s*:\s*"(https?://([^/|\"]+\.)?youwatch\.org(:\d+)?/[^\"]+)"@i', $embed, $dlink)) html_error('Error: Download link not found.');

		$_FileName = basename(parse_url($dlink[1], PHP_URL_PATH));
		if (!preg_match('@\.[^\.]+$@i', $_FileName, $ext)) $ext = array('.mp4');

		if (empty($FileName)) $FileName = $_FileName;
		else $FileName .= $ext[0];

		$FileName = urldecode($FileName);
		$this->RedirectDownload($dlink[1], $FileName);
	}

	private function JSun_packer($p,$a,$c,$k,$er) {
		$k = explode($er, $k);
		while ($c--) if($k[$c]) $p = preg_replace('@\b'.base_convert($c, 10, $a).'\b@', $k[$c], $p);
		return $p;
	}

}

// [20-6-2012]  Written by Th3-822. (XFS, XFS everywhere. :D)
// This site have a ripped template :D

?>