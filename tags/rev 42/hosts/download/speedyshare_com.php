<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class speedyshare_com extends DownloadClass
{
	public function Download( $link )
	{
		global $premium_acc;
		$this->DownloadFree($link);
	}

	private function DownloadFree($link)
	{
		$Referer = $link;
		$page = $this->GetPage( $link );

		is_present($page,"This file has been deleted");
		is_present($page,"The one-hour limit has been reached");
		is_present($page,"File not found. It has been either deleted, or it never existed at all.");
		
		$cookie = GetCookies( $page );
		
		$newurl = "http://www.speedyshare.com".trim( cut_str( $page,'resultlink><a href="','">' ) );
		$page = $this->GetPage( $newurl, $cookie, 0, $Referer );
		
		if(preg_match('/ocation: (.+)/', $page, $location))
		{
			$Href = trim( $location[1] );
			$Url = parse_url( $Href );
		}

		$FileName = urldecode ( basename($Url['path']) );
		$this->RedirectDownload($Href, $FileName, $cookie, 0, $Referer );
		exit ();
	}
}

// Created by rajmalhotra on 20 Jan 2010
?>