<?php

if (!defined('RAPIDLEECH'))
{
  require_once("index.html");
  exit;
}

class zshare_net extends DownloadClass
{
	private $page;
	private $cookie;

	public function Download($link)
	{
		global $premium_acc;
		if (($_POST ["premium_acc"] == "on" && $_POST ["premium_user"] && $_POST ["premium_pass"]) || ($_POST ["premium_acc"] == "on" && $premium_acc ["zshare"]))
		{
			$this->DownloadPremium($link);
		}
		else
		{
			$this->DownloadFree($link);
		}
	}

	private function DownloadFree($link)
	{
		$this->page = $this->GetPage($link, 0, array('referer2'=> '', 'download' => '1', 'imageField.x' => rand(0, 50), 'imageField.y' => rand(0,30)));
		$this->cookie = GetCookies($this->page);
		if ( !preg_match('/Array\((.+)\);.*link/', $this->page, $jsaray) ) html_error('Final link not found');
		$linkenc = $jsaray[1];
		$zsharelink = preg_replace('/\'[, ]?[ ,]?/six', '', $linkenc);
		$this->RedirectDownload($zsharelink, basename($zsharelink), $this->cookie);
	}
}
?>