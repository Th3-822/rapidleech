<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class google_com extends DownloadClass {
	public $fNames = array('odt' => 'OpenDocument Text', 'docx' => 'Microsoft Word', 'rtf' => 'Rich Text Format', 'txt' => 'Plain Text', 'pdf' => 'PDF Document', 'epub' => 'EPUB Publication', 'zip' => 'Zipped html Document', 'pptx' => 'Microsoft PowerPoint', 'ods' => 'OpenDocument Spreadsheet', 'xlsx' => 'Microsoft Excel'), $dFormats = array('odt', 'docx', 'rtf', 'txt', 'pdf', 'epub', 'zip'), $pFormats = array('pptx', 'pdf'), $ssFormats = array('ods', 'xlsx', 'pdf', 'zip'), $sFormats = array(13 => 'ods', 420 => 'xlsx', 12 => 'pdf');
	public function Download($link) {
		$this->checkBug78902($link);
		if (!preg_match('@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|(?:a/[\w\-\.]+/)?uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation|spreadsheets)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=|drive/folders/)([\w\-]{28,})@i', $link, $this->ID)) html_error('File/Folder ID not found at link.');
		$this->ID = $this->ID[1];

		// Use /open link for check if ID exists and also get it's type.
		$page = $this->GetPage('https://drive.google.com/open?id='.$this->ID);
		if (substr($page, 9, 3) == '404') html_error('File/Folder doesn\'t exists.');
		if (substr($page, 9, 1) != '3' || !preg_match('@\nLocation: https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:drive/)?(\w+)[\?/]@i', $page, $type)) html_error('Cannot find /open redirect.');

		switch (strtolower($type[1])) {
			case 'file': $this->File();break;
			case 'folder': case 'folders': case 'folderview': $this->Folder();break;
			case 'document': $this->Document();break;
			case 'presentation': $this->Presentation();break;
			case 'spreadsheets': $this->Spreadsheets();break; // New spreadsheets
			case 'spreadsheet': $this->Spreadsheet();break;
			default: html_error('Unknown /open redirect.');break;
		}
	}

	private function checkBug78902($link = '') {
		// >= 7.4, >=7.3.11 & >= 7.2.24
		if (substr(PHP_OS, 0, 3) == 'WIN' || version_compare(PHP_VERSION, '7.2.24', '<') || version_compare(PHP_VERSION, '7.3.11', '<')) return;

		if (empty($link)) echo('<div id="mesg" width="100%" align="center"></div><br />');
		$this->changeMesg('Warning: Running on PHP v' . PHP_VERSION . '<br />Downloads with this PHP release may be affected by the bug <a href="https://bugs.php.net/bug.php?id=78902">#78902</a> and will leak RAM until exhausted.<br />File may stop at a random size (up to ~1 GB).');

		if (!empty($_GET['method']) && $_GET['method'] == '78902') return;
		if (!empty($link)) {
			$form = $this->DefaultParamArr($link);
			$form['method'] = '78902';

			echo "\n<form name='f78902' action='{$_SERVER['SCRIPT_NAME']}' method='POST'>\n";
			foreach ($form as $name => $input) echo "\t<input type='hidden' name='$name' id='$name' value='" . htmlspecialchars($input, ENT_QUOTES) . "' />\n";
			echo "\t<div><br /><input type='submit' value='Continue' /></div></form>\n";
			include(TEMPLATE_DIR.'footer.php');
			exit();
		}
	}

	private function isPrivate($page) {
		if (substr($page, 9, 2) == '30') is_present($page, 'Location: https://www.google.com/accounts/ServiceLogin', 'Private File/Folder.');
		else is_present($page, "0; url='https://www.google.com/accounts/ServiceLogin", 'Private File/Folder.');
	}

	private function File() {
		$page = $this->GetPage('https://drive.google.com/uc?export=download&confirm=T822&id='.$this->ID, "download_warning_13058876669334088843_{$this->ID}=T822");
		$this->isPrivate($page);
		if (substr($page, 9, 1) != '3' || !preg_match('@\nLocation: (https?://(?:[\w\-]+\.)*googleusercontent\.com/[^\r\n]+)@i', $page, $dl)) html_error('File\'s download-link not found.');
		$this->RedirectDownload($dl[1], 'fGoogle', 0, 0, 'https://drive.google.com/file/d/'.$this->ID);
	}

	private function Folder() {
		if (isset($_GET['audl'])) html_error('Cannot check folder in audl.');
		$page = $this->GetPage('https://drive.google.com/drive/folders/'.$this->ID);
		$this->isPrivate($page);
		if (!preg_match_all('@\\\x5b\\\x22([\w\-]{28,})\\\x22,@i', $page, $ids) && !preg_match_all('@\[(\\\x22)([\w\-]{28,})\1,\[\1[\w\-]{28,}\1\]\\\n,\1(?>.*?\1),\1(?!application\\\/vnd\.google-apps\.folder)@i', $page, $ids, PREG_SET_ORDER)) html_error('Empty folder?');
		$ids = $ids[1];
		$links = array();
		foreach ($ids as $id) $links[] = "https://drive.google.com/uc?id=$id&export=download";
		$this->moveToAutoDownloader($links);
	}

	private function Document() {
		$url = 'https://docs.google.com/document/d/'.$this->ID;
		$page = $this->GetPage("$url/edit");
		$this->isPrivate($page);
		if (empty($_GET['T8']['format']) && !isset($_GET['audl'])) $this->formatSelector(1);
		$format = (!empty($_GET['T8']['format']) && in_array($_GET['T8']['format'], $this->dFormats)) ? $_GET['T8']['format'] : reset($this->dFormats);
		$this->RedirectDownload("$url/export?format=$format", 'dGoogle', 0, 0, $url);
	}

	private function Presentation() {
		$url = 'https://docs.google.com/presentation/d/'.$this->ID;
		$page = $this->GetPage("$url/edit");
		$this->isPrivate($page);
		if (empty($_GET['T8']['format']) && !isset($_GET['audl'])) $this->formatSelector(2);
		$format = (!empty($_GET['T8']['format']) && in_array($_GET['T8']['format'], $this->pFormats)) ? $_GET['T8']['format'] : reset($this->pFormats);
		$this->RedirectDownload("$url/export/$format", 'pGoogle', 0, 0, $url);
	}

	private function Spreadsheets() {
		$url = 'https://docs.google.com/spreadsheets/d/'.$this->ID;
		$page = $this->GetPage("$url/edit");
		$this->isPrivate($page);
		if (empty($_GET['T8']['format']) && !isset($_GET['audl'])) $this->formatSelector(3);
		$format = (!empty($_GET['T8']['format']) && in_array($_GET['T8']['format'], $this->ssFormats)) ? $_GET['T8']['format'] : reset($this->ssFormats);
		$this->RedirectDownload("$url/export/$format", 'ssGoogle', 0, 0, $url);
	}

	private function Spreadsheet() {
		$url = 'https://docs.google.com/spreadsheet';
		$page = $this->GetPage("$url/ccc?key=".$this->ID);
		$this->isPrivate($page);
		$cookie = GetCookiesArr($page);

		if (substr($page, 9, 1) != '3' || !preg_match('@\nLocation: (https?://(?:[\w\-]+\.)*google\.com/[^\r\n]+)@i', $page, $redir)) html_error("Redirect 1 not found");
		$page = $this->GetPage($redir[1], $cookie);
		$cookie = GetCookiesArr($page, $cookie);
		if (empty($cookie['PREF'])) html_error("Cookie 'PREF' not found");

		$page = $this->GetPage("$url/ccc?key=".$this->ID, $cookie);

		if (empty($_GET['T8']['format']) && !isset($_GET['audl'])) $this->formatSelector(4);
		if (empty($_GET['T8']['format']) || ($fmcmd = array_search($_GET['T8']['format'], $this->sFormats)) === false) {
			reset($this->sFormats);
			$fmcmd = key($this->sFormats);
		}
		if (($cmdUrl = cut_str($page, '/fm?id=', '"')) == false || !preg_match('@[\w\-\.]+@i', $cmdUrl, $cmdId)) html_error('Download ID not found.');
		$this->RedirectDownload("$url/fm?id={$cmdId[0]}&fmcmd=$fmcmd", 'sGoogle', $cookie, 0, "$url/ccc?key=".$this->ID);
	}

	private function formatSelector($type = 1) {
		switch ($type) {
			case 1: $tName = 'Document';$formats = $this->dFormats;break;
			case 2: $tName = 'Presentation';$formats = $this->pFormats;break;
			case 3: $tName = 'Spreadsheets';$formats = $this->ssFormats;break;
			case 4: $tName = 'Spreadsheet';$formats = $this->sFormats;break;
			default: html_error('formatSelector: Unknown type.');
		}
		if (count($formats) == 1) return $_GET['T8'] = array('format' => reset($formats));
		echo "\n<br /><br /><h3 style='text-align: center;'>$tName format selector.</h4>";
		echo "\n<center><form name='GD_FS' action='{$GLOBALS['PHP_SELF']}' method='POST'>\n";
		echo "<select name='T8[format]' id='GD_ext'>\n";
		foreach ($formats as $ext) echo "<option value='$ext'>".(!empty($this->fNames[$ext]) ? $this->fNames[$ext]." (.$ext)" : ".$ext")."</option>\n";
		echo "</select>\n";
		$data = $this->DefaultParamArr('https://drive.google.com/open?id='.$this->ID);
		foreach ($data as $n => $v) echo("<input type='hidden' name='$n' id='FS_$n' value='$v' />\n");
		echo "<input type='submit' name='Th3-822' value='".lang(209)."' />\n";
		echo "</form></center>\n</body>\n</html>";
		exit;
	}

	// Add a Range header for get the filesize on chunked file downloads
	public function RedirectDownload($link, $FileName = 0, $cookie = 0, $post = 0, $referer = 0, $force_name = 0, $auth = 0, $addon = array()) {
		$referer .= "\r\nRange: bytes=0-";
		return parent::RedirectDownload($link, $FileName, $cookie, $post, $referer, $force_name, $auth, $addon);
	}

	public function CheckBack(&$headers) {
		$this->checkBug78902();
		if (substr($headers, 9, 3) == '416') html_error('[google_com.php] Plugin needs a fix, \'Range\' method is not working.');
		if (stripos($headers, "\nTransfer-Encoding: chunked") !== false) {
			global $fp, $sFilters;
			if (empty($fp) || !is_resource($fp)) html_error('Error: Your rapidleech copy is outdated and it doesn\'t support functions required by this plugin.');
			if (!in_array('dechunk', stream_get_filters())) html_error('Error: dechunk filter not available, cannot download chunked file.');
			if (!isset($sFilters) || !is_array($sFilters)) $sFilters = array();
			if (empty($sFilters['dechunk'])) $sFilters['dechunk'] = stream_filter_append($fp, 'dechunk', STREAM_FILTER_READ);
			if (!$sFilters['dechunk']) html_error('Error: Unknown error while initializing dechunk filter, cannot download chunked file.');
			// Little hack to get the filesize.
			$headers = preg_replace('@\nContent-Range\: bytes 0-\d+/@i', "\nContent-Length: ", $headers, 1);
		}
	}
}

// [11-2-2014]  Written by Th3-822.
// [23-12-2014]  Added support for new spreadsheets format/urls & Some workarounds to get filesize on chunked downloads... - Th3-822
// [30-4-2018]  Fixed Folders. - Th3-822