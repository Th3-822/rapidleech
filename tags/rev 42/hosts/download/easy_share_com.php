<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class easy_share_com extends DownloadClass
{
	public function Download( $link )
	{
		global $premium_acc;
		if ( ( $_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"] ) ||
			( $_REQUEST ["premium_acc"] == "on" && $premium_acc ["easyshare"] ["user"] && $premium_acc ["easyshare"] ["pass"] ) )
		{
			$this->DownloadPremium( $link );
		}
		else
		{
			$this->DownloadFree( $link );
		}
	}
		
	private function DownloadFree( $link )
	{
		global $pauth;
		$Referer = $link;
		
		$page = $this->GetPage( $link, 0, 0, 0, $pauth );
		
		$cookies = $this->biscottiDiKaox( $page );
		is_present ( $page, 'File was deleted' );
		is_present ( $page, 'File not found' );
        is_present ( $page, 'You have downloaded over 150MB during last hour.' );  
		$FileName = trim ( cut_str ( $page, "<title>Download ", "," ) );
				
		$div = trim ( cut_str ( $page, '<div id="block-captcha">', "</div>" ) );
		$count = trim ( cut_str ( $div, "w='", "'" ) );
		
		insert_timer( $count, "Waiting link timelock");
		
		if ( $src = trim ( cut_str ( $page, "u='", "'" ) ) )
		{
			$Url = parse_url( $link );
			$Href = "http://".$Url["host"].$src;
			$page = $this->GetPage( $Href, $cookies, 0, $Referer, $pauth );
        }
		
        $Href = trim ( cut_str($page,'post" action="','"') );
        $id = trim ( cut_str($page,'"id" value="','"') ); 
		
		$post = array ();
		$post ["captcha"] = "";
		$post ["id"] = $id;
		
		$this->RedirectDownload( $Href, $FileName, $cookies, $post, $Referer, 0, $pauth );
		exit ();
	}
	
	private function DownloadPremium( $link )
	{
		global $premium_acc, $pauth, $Referer;
		
		// Getting file name
		$page = $this->GetPage( $link, 0, 0, 0, $pauth );
		is_present ( $page, 'File was deleted' );
		is_present ( $page, 'File not found' );
        $FileName = trim ( cut_str ( $page, "<title>Download ", "," ) );
		// Getting file name end
		
		// login 
		$login = "http://www.easy-share.com/accounts/login";
		
		$post ["login"] = $_REQUEST ["premium_user"] ? $_REQUEST ["premium_user"] : $premium_acc ["easyshare"] ["user"];
		$post ["password"] = $_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["easyshare"] ["pass"];
		$post ["remember"] = "1";
			
		$page = $this->GetPage( $login, 0, $post, "http://www.easy-share.com/", $pauth );
		
		$cook = GetCookies( $page );
		// end login 
		
		is_notpresent ( $cook, "PREMIUM", "Login failed<br>Wrong login/password?" );
		
		$page = $this->GetPage( $link, $cook, 0, 0, $pauth );
		is_present ( $page, 'File was deleted' );
		is_present ( $page, 'File not found' );
		
		if ( !isset($FileName) || $FileName == "" )
		{
			$Url = parse_url ( $link );
			$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
		}
		
		preg_match ( '/Location:.+?\\r/i', $page, $loca );
		$redir = rtrim ( $loca [0] );
		preg_match ( '/http:.+/i', $redir, $loca );
		$Href = trim ( $loca [0] );
		
		$cookie = $cook . "; " . $this->BiscottiDiKaox ( $page );
	
		$this->RedirectDownload( $Href, $FileName, $cookie, 0, $Referer, 0, $pauth );
		exit ();
	}
	
	private function biscottiDiKaox( $content )
	{
		preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
		foreach ( $matches[1] as $coll ) 
		{
			$bis0=split(";",$coll);
			$bis1=$bis0[0]."; ";
			$bis2=split("=",$bis1);
			$cek=" ".$bis2[0]."="; 
			if( strpos( $bis1,"=deleted" ) || strpos( $bis1,$cek.";" ) ) 
			{
			}
			else
			{
				if ( substr_count( $bis,$cek ) > 0 )
				{
					$patrn=" ".$bis2[0]."=[^ ]+";
					$bis=preg_replace("/$patrn/"," ".$bis1,$bis);     
				} 
				else 
				{
					$bis.=$bis1;
				}
			}
		}  
		
		$bis=str_replace("  "," ",$bis);     
		return rtrim($bis);
	}
}

/**************************************************\  
FIXED by kaox 04/07/2009
FIXED and RE-WRITTEN by rajmalhotra on 10 Jan 2010
FIXED by rajmalhotra on 12 Feb 2010 => FIXED downloading from Premium Account
\**************************************************/	

?>