<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class storage_to extends DownloadClass
{
	public function Download( $link )
	{
		global $premium_acc;
		$this->DownloadFree($link);
	}

	private function DownloadFree($link)
	{
		$Referer = $link;
		preg_match('%/get/(.+?)/%', $link, $id);
		$id = $id[1];
		$getlink = "http://www.storage.to/getlink/$id/";

		$page = $this->GetPage( $getlink );
		
		preg_match('/Object\((.+?)\)/', $page, $json);
		if (!$json[1])
		{
			html_error("No Json string returned! Seems downloading system has changed. This plugin need to update.",0);
		}
		
		$json = str_replace("'", "\"", $json[1]);
		$jsarray = json_decode($json, true);
		if (!$jsarray)
		{
			html_error("Cannot decode Json string!",0);
		}

		if ($jsarray['state'] == "failed")
		{
			html_error("The download failed. Please try again later",0);
		}
		else if ($jsarray['state'] == "wait")
		{
			html_error("Please wait. Free users may only download a few files per hour."); 
		}
		else if ($jsarray['state'] == "ok")
		{
			insert_timer( $jsarray['countdown'], "Waiting link timelock", "", true );
		}
		
		$FileName = basename($jsarray['link']);
		
		$Href = $jsarray['link'];
		$this->RedirectDownload($Href,$FileName,0, 0,$Referer);
		exit ();
	}
}

// Created by rajmalhotra on 20 Jan 2010
?>