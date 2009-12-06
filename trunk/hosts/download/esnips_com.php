<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class esnips_com extends DownloadClass
{
	public function Download( $link )
	{
		global $premium_acc;
		$this->DownloadFree($link);
	}

	private function DownloadFree($link)
	{
		global $nn, $PHP_SELF, $pauth;
		
		$Url = parse_url($link);
		$urlPathValue = $Url["path"];
		
		$page = $this->GetPage($link);
		is_page($page);

		$cookie = "";
		preg_match_all("/Set-Cookie: ([^;]+;)/", $page, $cook);
		$arraySize = count($cook);

		for ( $i=0;$i<$arraySize;$i++)
		{
			$cookie=$cookie.array_shift($cook[1]);
		}
		$fileNumber = trim(cut_str($urlPathValue, "/doc/", "/"));
	
		global $Referer;
		$Referer = $link;
		
		$Href = "http://www.esnips.com/nsdoc/".$fileNumber."/?action=forceDL";
		$FileName = "file";
		
		$this->RedirectDownload($Href,$FileName,$cookie, 0,$Referer);
		exit ();
	}
}

// Created by rajmalhotra on 04 Dec 09	
?>