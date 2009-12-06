<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class bigandfree_com extends DownloadClass
{
	public function Download( $link )
	{
		global $premium_acc;
		$this->DownloadFree($link);
	}

	private function DownloadFree($link)
	{
		global $nn, $PHP_SELF, $pauth;
		$page = $this->GetPage($link);
		
		//is_present ( $page, "Due to a violation of our terms of use, the file has been removed from the server." );

		$cookie = "";
		preg_match_all("/Set-Cookie: ([^;]+;)/", $page, $cook);
		$arraySize = count($cook);

		for ( $i=0;$i<$arraySize;$i++)
		{
			$cookie=$cookie.array_shift($cook[1]);
		}
		
		$post = array ();
		$post ["chosen_free"] = "Basic Download";

		global $Referer;
		$Referer = $link;
		$page = $this->GetPage($link,$cookie,$post,$Referer);

		$count = trim ( cut_str ( $page, "var x = ", ";" ) );
		
		$current = trim ( cut_str ( $page, '<input type="hidden" name="current" value="', '">' ) );
		$limitReached = trim ( cut_str ( $page, '<input type="hidden" name="limit_reached" value="', '">' ) );
		
		insert_timer( $count, "Waiting link timelock");
		
		$post = array ();
		$post ["current"] = $current;
		$post ["limit_reached"] = $limitReached;
		$post ["download_now"] = "Click here to download";
				
		$FileName = "filename";
		$this->RedirectDownload($link,$FileName,$cookie, $post,$Referer);
		exit ();
	}
}

// Created by rajmalhotra on 04 Dec 09	
?>