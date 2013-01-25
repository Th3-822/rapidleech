<?php
if (!defined('RAPIDLEECH')) {
	require_once 'index.html';
	exit;
}

class uppit_com extends DownloadClass {
	
	public function Download($link) {
		global $premium_acc;
		
		if (!$_REQUEST['step']) {
			$this->cookie = array('lang' => 'english');
			$this->page = $this->GetPage($link, $this->cookie);
			if (preg_match('/Location: (https?:\/\/uppit\.com\/[^\r\n]+)/i', $this->page, $rd)) {
				$link = trim($rd[1]);
				$this->page = $this->GetPage($link, $this->cookie);
			}
			is_present($this->page, 'The file you were looking for could not be found, sorry for any inconvenience.');
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])||($premium_acc['uppit_com']['user'] && $premium_acc['uppit_com']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}
	
	private function Free() {
		
		if (!preg_match_all('/var count=(\d+)/i', $this->page, $w)) html_error('Error[Timer not found!]');
		$this->CountDown(array_rand($w[1]));
		$form = cut_str($this->page, '<form action="" name="pre" method="POST">', '</form>');
		if (!preg_match_all('/<input type="hidden" name="([^"]+)" value="([^"]+)?">/', $form, $one) || !preg_match_all('/<input type="submit" name="([^"]+)" id="btn_download" value="([^"]+)?">/', $form, $two)) html_error('Error[Post Data - FREE not found!]');
		$match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
		$post = array();
		foreach ($match as $k => $v) {
			$post[$k] = $v;
		}
		$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		if (!preg_match('/<a href="(https?:\/\/srv\d+\.uppcdn\.com\/dl\/[^\r\n\'"]+)"/', $page, $dl)) html_error('Error[Download Link - FREE not found!]');
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $this->cookie, 0, $this->link);
		exit;
	}
	
	private function Premium() {
		html_error('Error[Unsupported now!]');
	}
}

/*
 * Written by Tony Fauzi Wihana/Ruud v.Tony 22-01-2013
 */
?>
