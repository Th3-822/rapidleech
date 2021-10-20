<?php  
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}
class rapidvid_to extends DownloadClass
{
	public function Download($link)
	{
		if(self::CheckLink($link) == false)
		{
			html_error('Error: Download link not found!');
			return 0;
		}
		echo "<pre>";
		$html = $this->GetPage($link);
		$doc = new DOMDocument;
		@$doc->loadHTML($html);
		$e = $doc->getElementsByTagName("title");
		$title = $e[0]->textContent;
		$e = $doc->getElementsByTagName("source");
		$link = $e[0]->getAttribute('src');
		$FileName = basename(parse_url($title, PHP_URL_PATH));
		$this->RedirectDownload($link, $FileName);
		return 0;
	}
	private function CheckLink($l)
	{
		$e = explode("/", $l);
		if($e[3] == 'e')
		{
			return true;
		}
		return false;
	}
}
?>