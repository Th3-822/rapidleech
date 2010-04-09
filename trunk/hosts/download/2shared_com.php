<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}

class d2shared_com extends DownloadClass 
{
	public function Download( $link ) 
	{
		$page = $this->GetPage($link);
		is_present( $page, "file link that you requested is not valid", "The file link that you requested is not valid. Please contact link publisher or try to make a search", "0" );
		
		$this->getCountDown($page);
		$loc = "";
		$FileName = "";
		
		// Retrieve location
		preg_match ( '/location = "(.*)";/', $page, $loc );
		$Href = $loc [1];
		$Url = parse_url ( $Href );
		$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
		$this->RedirectDownload( $Href, $FileName );
	}
	
	private function getCountDown($page) 
	{
		if (preg_match ( '/var c = ([0-9])*;/', $page, $count ) ) 
		{
			$countDown = $count [1];
			$this->CountDown($countDown);
		}
	}
}

/********************************************************
Fixed by Raj Malhotra on 10 April 2010 => Fix Reloading to main page when link does not exists.
*********************************************************/
?>