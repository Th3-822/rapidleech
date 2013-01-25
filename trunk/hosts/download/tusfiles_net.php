<?php
if (!defined('RAPIDLEECH')) {
	require_once 'index.html';
	exit;
}

class tusfiles_net extends DownloadClass {
	
	public function Download($link) {
		global $premium_acc;
		
		if (!$_REQUEST['step']) {
			$this->cookie = array('lang' => 'english');
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, '<b>File Not Found</b>');
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])||($premium_acc['tusfiles_net']['user'] && $premium_acc['tusfiles_net']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}
	
	private function Free() {
		
		$form = cut_str($this->page, '<Form name="F1" method="POST"', '</Form>');
		if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $match)) html_error('Error[Post Data - FREE not found!]');
		$match = array_combine($match[1], $match[2]);
		$post = array();
		foreach ($match as $k => $v) {
			$post[$k] = $v;
		}
		$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		if (!preg_match('/Location: (https?:\/\/[^\r\n]+)/i', $page, $dl)) html_error('Error[Download link - FREE not found!]');
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link);
	}
	
	private function Premium() {
		html_error('Error[Unsupported now!]');
	}
}

/*
 * Written by Tony Fauzi Wihana/Ruud v.Tony 22-01-2013
 */
?>
