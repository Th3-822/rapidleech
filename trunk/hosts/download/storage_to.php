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
		if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["storage"] ["user"] && $premium_acc ["storage"] ["pass"]))
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
		$this->DownloadLink( $link );
	}
	
	private function DownloadPremium($link)
	{
		global $premium_acc, $Referer;
		
		$loginUrl = "http://storage.to/login";
		$usrEmail = "";

		$post=array();
		$usrEmail = $_REQUEST ["premium_user"] ? $_REQUEST ["premium_user"] : $premium_acc ["storage"] ["user"];
		$post["email"] = $usrEmail;
		$post["password"] = $_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["storage"] ["pass"];
		$post[""] = "Login";
		$page = $this->GetPage( $loginUrl, 0, $post, $loginUrl );
		
		$cookie = GetCookies($page);
		
		if( !strpos( $page, "Login successful") )
		{
			html_error("Login Failed , Bad username/password combination.",0 );
		}
		
		$this->DownloadLink( $link, $cookie );
	}
	
	private function DownloadLink( $link, $cookie = 0 )
	{
		global $Referer;
		
		preg_match('%/get/(.+?)/%', $link, $id);
		$id = $id[1];
		$getlink = "http://www.storage.to/getlink/$id/";

		$page = $this->GetPage( $getlink, $cookie );
		
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

		if ($jsarray['state'] == "failed" && $jsarray['linkid'] == "" )
		{
			html_error("File not found. Kindly check the link",0 );
		}
		else if ($jsarray['state'] == "failed")
		{
			html_error("The download failed. Please try again later",0 );
		}
		else if ($jsarray['state'] == "wait")
		{
			html_error("Please wait. Free users may only download a few files per hour."); 
		}
		else if ($jsarray['state'] == "ok")
		{
			$count = $jsarray['countdown'];
			if ( $count > 0 )
			{
				insert_timer( $count, "Waiting link timelock", "", true );
			}
		}
						
		$Href = $jsarray['link'];		
		$FileName = basename( $Href );
		
		$this->RedirectDownload( $Href, $FileName, $cookie, 0, $Referer );
		exit ();
	}
}

// Created by rajmalhotra on 20 Jan 2010
// Updated by rajmalhotra on 24 Jan 2010 -> Added premium account support.
?>