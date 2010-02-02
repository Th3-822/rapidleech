<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class uploaded_to extends DownloadClass 
{
	public function Download($link) 
	{
		global $premium_acc;
		
		if ( ($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["uploaded_to"] ["user"] && $premium_acc ["uploaded_to"] ["pass"] ) )
		{
			$this->DownloadPremium($link);
		}
		else
		{
			$this->DownloadFree($link);
		}
	}
	
	private function DownloadFree( $link )
	{
		$page = $this->GetPage( $link );
		
		is_present($page, "Location: /?view=error_fileremoved", "File not found");
		is_present($page, "Location: /?view=error_traffic_exceeded_free", "Download limit exceeded");
		is_present($page, "http://images.uploaded.to/key.gif", "This file is password protected");

		if (preg_match('/(http.+dl\?id=[0-9a-zA-Z]+)/', $page, $dllink))
		{
			$dlink = trim ( $dllink[1] );
		}
		else
		{
			html_error("Download link not found", 0);
		}
		
		$FileName = "none";
		
		$this->RedirectDownload( $dlink, $FileName );
		exit ();	
	}
	
	private function DownloadPremium( $link )
	{
		global $premium_acc, $Referer;
			
		$post = array();
		$post["email"] = $_REQUEST["premium_user"] ? $_REQUEST["premium_user"] : $premium_acc["uploaded_to"]["user"];
		$post["password"] = $_REQUEST["premium_pass"] ? $_REQUEST["premium_pass"] : $premium_acc["uploaded_to"]["pass"];
		
		$page = $this->GetPage( 'http://uploaded.to/login', 0, $post );
		
		if( strpos($page,"Login failed") )
		{
			html_error("Login Failed , Bad username/password combination.",0);
		}
		
		$cook = GetCookies($page);
		
		$newHref = "http://uploaded.to/file/bvzd23/?redirect";
		$post = array();
		$page = $this->GetPage( $newHref, $cook, $post, $Referer );
			
		preg_match ( '/Location: (.*)/', $page, $newredir );
		$Href = trim ( $newredir [1] );
		$Url = parse_url($Href);
		$FileName = !$FileName ? basename($Url["path"]) : $FileName;
		
		$this->RedirectDownload( $Href, $FileName, $cook );
		exit ();	
	}
}	

/**************************************************\  
Fixed premium account support and Updated by rajmalhotra 31 Jan 2010
Simplified downloading from premium account by rajmalhotra 02 Feb 2010
\**************************************************/
?>