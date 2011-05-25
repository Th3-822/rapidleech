<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class furk_net extends DownloadClass
{
	public function Download( $link )
	{
		global $premium_acc;
		$this->DownloadFree($link);
	}

	private function DownloadFree($link)
	{
		global $nn, $PHP_SELF, $pauth;
		
		$LINK =  str_replace("https", "http", $LINK);
		$page = $this->GetPage($link);
		is_page($page);
		
		preg_match('/action="([^\"]*)"/i', $page, $redir);
		$Href = trim($redir[1]);
		
		$random_furk = cut_str($page,'<input type="hidden" name="rand" value="','" />');
		$countd = cut_str($page,'startFreeDownload(',');');
	
		insert_timer($countd, "Waiting link timelock");
	
		$post = Array();
		$post["rand"] = $random_furk;
		$Url = parse_url($Href);
		
		$FileName = !$FileName ? basename($Url["path"]) : $FileName;
	
		$this->RedirectDownload($Href,$FileName,0, $post);
		exit ();
	}
}
// Created by rajmalhotra on 05 Dec 09
// Fixed countdown by Th3-822 on 31 Dec 10
?>