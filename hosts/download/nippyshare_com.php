<?php
if (!defined('RAPIDLEECH')) {
	require_once 'index.html';
	exit;
}

class nippyshare_com extends DownloadClass {

	public function Download($link) {
		
		echo 'Nippyshare.com Download Plugin by <b>The Devil</b><br>';
		$this->page = $this->GetPage($link);
		$devil = preg_match('@nippyshare.com/d/[^\r\n\s\t<>\'\"]+@',$this->page,$dlink);
		if($devil==0){
			html_error('Unable to Download');
		}
		$dloc = 'https://'.$dlink[0];
		preg_match('@<li>Name:.(.*)@',$this->page,$fnames);
		$fname = cut_str($fnames[0],'<li>Name: ','</li>');
		$cookie = GetCookiesArr($this->page);
		$this->RedirectDownload($dloc,$fname,$cookie);
		
	}

}

// Written by The Devil

?>
