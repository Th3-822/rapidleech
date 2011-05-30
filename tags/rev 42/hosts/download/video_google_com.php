<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class video_google_com extends DownloadClass
{
	public function Download( $link )
	{
		global $premium_acc;
		$this->DownloadFree($link);
	}

	private function DownloadFree($link)
	{
		$Referer = $link;
		$page = $this->GetPage($link);
		
		//is_present ( $page, "Due to a violation of our terms of use, the file has been removed from the server." );
	
		$downloadLink_1 = trim ( cut_str ( $page, 'If the download does not start automatically, right-click <a href=', '>' ) );
		
		$downloadLink_1 = urldecode( $downloadLink_1 ); 
		
		//$page = $this->GetPage($downloadLink_1, 0, 0, 0 );
		//preg_match('/Location: *(.+)/i', $page, $newredir );		
		
		$FileName = "";		
		$Href = trim ( $downloadLink_1 );
		//$Href = trim ( $newredir [1] );
		
		$FileName = trim ( cut_str ( $Href, 'title=', '&' ) );
				
		$this->RedirectDownload($Href,$FileName,0, 0,$Referer);
		exit ();
	}
}

// Created by rajmalhotra on 05 Jan 2010
?>