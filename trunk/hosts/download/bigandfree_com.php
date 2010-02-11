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
		if ( ($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["bigandfree_com"] ["user"] && $premium_acc ["bigandfree_com"] ["pass"] ) )
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
		
		$page = $this->GetPage($link, $cookie, $post, $Referer );
		is_present ( $page, "Performing scheduled network maintenance" );
		
		preg_match('/Location: *(.+)/i', $page, $newredir );		
		
		$FileName = "";		
		$Href = trim ( $newredir [1] );
		$Url = parse_url ( $Href );
		$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
		
		$this->RedirectDownload($Href,$FileName,0, 0,$Referer);
		exit ();
	}
	
	private function DownloadPremium( $link )
	{
		global $premium_acc, $Referer;
		$loginURL = "http://www.bigandfree.com/members";
		$post = array();
		$usrId = $_REQUEST ["premium_user"] ? $_REQUEST ["premium_user"] : $premium_acc ["bigandfree_com"] ["user"];
		$post["uname"] = $usrId;
		$post["pwd"] = $_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["bigandfree_com"] ["pass"];
		$post["login"] = "Click here to login";
		$page = $this->GetPage( $loginURL, 0, $post, $loginURL );
		
		if ( strpos( $page , "Account not found." ) )
		{
			html_error("Login Failed , Bad username/password combination.",0 );
		}
		
		$cookie = GetCookies( $page );
		$page = $this->GetPage( $link, $cookie );
		
		$current = trim ( cut_str ( $page, 'current" value="', '">' ) );
		$limitReached = trim ( cut_str ( $page, 'limit_reached" value="', '">' ) );
		$download_now = trim ( cut_str ( $page, 'download_now" value="', '">' ) );
		
		$post = array();
		$post["current"] = $current;
		$post["limit_reached"] = $limitReached;
		$post["download_now"] = $download_now;
		$page = $this->GetPage( $link, $cookie, $post );
		
		preg_match('/Location: *(.+)/i', $page, $newredir );		
		
		$FileName = "";		
		$Href = trim ( $newredir [1] );
		$Url = parse_url ( $Href );
		$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
		
		$this->RedirectDownload( $Href, $FileName, $cookie, 0, $Referer );
		exit ();
	}
}

// Created by rajmalhotra on 04 Dec 09	
// Updated by rajmalhotra on 14 Dec 09 for adding server maintaince error message	
// Updated by rajmalhotra on 12 Feb 2010 => Added support for downloading from PREMIUM ACCOUNTS
?>