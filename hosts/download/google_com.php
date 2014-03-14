<?php

if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class google_com extends DownloadClass {
	public $fNames = array('odt' => 'OpenDocument Text', 'docx' => 'Microsoft Word', 'rtf' => 'Rich Text Format', 'txt' => 'Plain Text', 'pdf' => 'PDF Document', 'zip' => 'Zipped html Document', 'pptx' => 'Microsoft PowerPoint', 'ods' => 'OpenDocument Spreadsheet', 'xlsx' => 'Microsoft Excel'), $dFormats = array('odt', 'docx', 'rtf', 'txt', 'pdf', 'zip'), $pFormats = array('pptx', 'pdf'), $sFormats = array(13 => 'ods', 420 => 'xlsx', 12 => 'pdf');
	public function Download($link) {
		if (!preg_match('@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})@i', $link, $this->ID)) html_error('File/Folder ID not found at link.');
		$this->ID = $this->ID[1];

		// Use /open link for check if ID exists and also get it's type.
		$page = $this->GetPage('https://drive.google.com/open?id='.$this->ID);
		if (substr($page, 9, 3) == '404') html_error('File/Folder doesn\'t exists.');
		if (substr($page, 9, 2) != '30' || !preg_match('@\nLocation: https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(\w+)[\?/]@i', $page, $type)) html_error('Cannot find /open redirect.');

		switch (strtolower($type[1])) {
			case 'file': $this->File();break;
			case 'folder': case 'folderview': $this->Folder();break;
			case 'document': $this->Document();break;
			case 'presentation': $this->Presentation();break;
			case 'spreadsheet': $this->Spreadsheet();break;
			default: html_error('Unknown /open redirect.');break;
		}
	}

	private function isPrivate($page) {
		if (substr($page, 9, 2) == '30') is_present($page, 'Location: https://www.google.com/accounts/ServiceLogin', 'Private File/Folder.');
		else is_present($page, "0; url='https://www.google.com/accounts/ServiceLogin", 'Private File/Folder.');
	}

	private function File() {
		$page = $this->GetPage('https://drive.google.com/uc?export=download&confirm=T822&id='.$this->ID, "download_warning_13058876669334088843_{$this->ID}=T822");
		$this->isPrivate($page);
		if (substr($page, 9, 2) != '30' || !preg_match('@\nLocation: (https?://(?:[\w\-]+\.)*googleusercontent\.com/[^\r\n]+)@i', $page, $dl)) html_error('File\'s download-link not found.');
		$this->RedirectDownload($dl[1], 'fGoogle');
	}

	private function Folder() {
		if (isset($_GET['audl'])) html_error('Cannot check folder in audl.');
		$page = $this->GetPage('https://drive.google.com/folderview?id='.$this->ID);
		$this->isPrivate($page);
		if (!preg_match_all('@id=["\']entry-([\w\-]{28,})["\']@i', $page, $ids)) html_error('Empty folder?');
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
		$this->RedirectDownload("$url/export?format=$format", 'dGoogle');
	}

	private function Presentation() {
		$url = 'https://docs.google.com/presentation/d/'.$this->ID;
		$page = $this->GetPage("$url/edit");
		$this->isPrivate($page);
		if (empty($_GET['T8']['format']) && !isset($_GET['audl'])) $this->formatSelector(2);
		$format = (!empty($_GET['T8']['format']) && in_array($_GET['T8']['format'], $this->pFormats)) ? $_GET['T8']['format'] : reset($this->pFormats);
		$this->RedirectDownload("$url/export/$format", 'pGoogle');
	}

	private function Spreadsheet() {
		$url = 'https://docs.google.com/spreadsheet';
		$page = $this->GetPage("$url/ccc?key=".$this->ID);
		$this->isPrivate($page);
		$cookie = GetCookiesArr($page);

		if (substr($page, 9, 2) != '30' || !preg_match('@\nLocation: (https?://(?:[\w\-]+\.)*google\.com/[^\r\n]+)@i', $page, $redir)) html_error("Redirect 1 not found");
		$page = $this->GetPage($redir[1], $cookie);
		$cookie = GetCookiesArr($page, $cookie);
		if (empty($cookie['PREF'])) html_error("Cookie 'PREF' not found");

		$page = $this->GetPage("$url/ccc?key=".$this->ID, $cookie);

		if (empty($_GET['T8']['format']) && !isset($_GET['audl'])) $this->formatSelector(3);
		if (empty($_GET['T8']['format']) || ($fmcmd = array_search($_GET['T8']['format'], $this->sFormats)) === false) {
			reset($this->sFormats);
			$fmcmd = key($this->sFormats);
		}
		if (($cmdUrl = cut_str($page, '/fm?id=', '"')) == false || !preg_match('@[\w\-\.]+@i', $cmdUrl, $cmdId)) html_error('Download ID not found.');
		$this->RedirectDownload("$url/fm?id={$cmdId[0]}&fmcmd=$fmcmd", 'sGoogle', $cookie);
	}

	private function formatSelector($type = 1) {
		switch ($type) {
			case 1: $tName = 'Document';$formats = $this->dFormats;break;
			case 2: $tName = 'Presentation';$formats = $this->pFormats;break;
			case 3: $tName = 'Spreadsheet';$formats = $this->sFormats;break;
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

	public function CheckBack($headers) {
		if (stripos($headers, "\nTransfer-Encoding: chunked") !== false) {
			global $fp, $sFilters;
			if (empty($fp) || !is_resource($fp)) html_error('Error: Your rapidleech copy is outdated and it doesn\'t support functions required by this plugin.');
			if (!in_array('dechunk', stream_get_filters())) html_error('Error: dechunk filter not available, cannot download chunked document/presentation.');
			if (empty($sFilters)) $sFilters = array();
			if (empty($sFilters['dechunk'])) $sFilters['dechunk'] = stream_filter_append($fp, 'dechunk', STREAM_FILTER_READ);
		}
	}
}

?>