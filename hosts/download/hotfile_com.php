<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class hotfile_com extends DownloadClass
{
	public function Download( $link )
	{
		global $premium_acc;
		if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["hotfile"] ["user"] && $premium_acc ["hotfile"] ["pass"]))
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
		
	    is_present($page,"File not found","File not found, the file is not present or bad link","0");
	    is_present($page,"due to copyright","This file is either removed due to copyright claim or is deleted by the uploader.","0");
	    is_present($page,"You are currently downloading","You are currently downloading. Only one connection with server allow for free users","0");
		
		preg_match_all( '/timerend=d\.getTime\(\)\+(\d+)/i', $page, $arraytime ); 
		$wtime = $arraytime[1][1]/1000;    
		if ( $wtime > 0 ) 
		{
			insert_timer( $wtime, "You reached your hourly traffic limit" ); 
		}
		
		$action = trim ( cut_str( $page, "action value=",">" ) );
		$tm = trim ( cut_str( $page, "tm value=",">" ) );
		$tmhash = trim ( cut_str( $page, "tmhash value=",">" ) );
		$wait = trim ( cut_str( $page,"wait value=",">" ) );
		$waithash = trim ( cut_str( $page, "waithash value=",">" ) );
		
		$post = array();
		$post["action"] = $action;
		$post["tm"] = $tm;
		$post["tmhash"] = $tmhash;
		$post["wait"] = $wait;
		$post["waithash"] = $waithash;
		insert_timer( $wait, "Waiting link timelock" );   
		
		$page = $this->GetPage( $link, 0, $post );
		
		preg_match( '/http:\/\/.+get\/[^\'"]+/i', $page, $loca );
		$Href = trim( $loca[0] );
		
		$page = $this->GetPage( $Href );
		
		preg_match('/Location: *(.+)/i', $page, $newredir );
		$Href = trim ( $newredir [1] );
		
		if ( strpos( $Href,"http://" ) === false ) 
		{ 
			html_error("Server problem. Please try again after", 0 );
		}
		
		$Url = parse_url( $Href );
		$FileName = basename($Url["path"]);
		$this->RedirectDownload( $Href, $FileName );
		exit ();
	}
	
	private function DownloadPremium($link)
	{
		global $premium_acc, $Referer;
		$loginUrl = "http://hotfile.com/login.php";
		$Referer1 = "http://hotfile.com/";
		
		$post = array();
		$post["returnto"] = "/";
		$post["user"] = $_REQUEST["premium_user"] ? trim( $_REQUEST["premium_user"] ) : $premium_acc["hotfile"]["user"];
		$post["pass"] = $_REQUEST["premium_pass"] ? trim( $_REQUEST["premium_pass"] ) : $premium_acc["hotfile"]["pass"];
		$page = $this->GetPage( $loginUrl, 0, $post, $Referer1 );
			
		$cookie = GetCookies( $page );
			
		$page = $this->GetPage( "http://hotfile.com/?lang=en", $cookie, 0, $Referer1 );
		
		$findpre = strpos( $page, 'Premium' );
		
		if( false === $findpre )
		{
			html_error( "Login Failed , Bad username/password combination.",0 );
		}
		
		$page = $this->GetPage( $link, $cookie, 0, $Referer );
		
		is_present( $page, "File not found", "File not found, the file is not present or bad link","0" );
		is_present( $page, "due to copyright","This file is either removed due to copyright claim or is deleted by the uploader.","0");
	
		preg_match( '/http:\/\/.+get\/[^\'"]+/i', $page, $loca );
		$Href = trim( $loca[0] );
		
		$page = $this->GetPage( $Href, $cookie, 0, $Referer );
		
		preg_match('/Location: *(.+)/i', $page, $newredir );
		$Href = trim ( $newredir [1] );
		
		//$Href = urldecode ( $Href );
		$Url = parse_url( $Href );
		$FileName = basename($Url["path"]);
		//$FileName = urldecode ( $FileName );
		//$FileName = str_replace ( " " , "_" , $FileName );
		$this->RedirectDownload( $Href, $FileName, $cookie, 0, $Referer );
		exit ();
	}
}	

/**********************************************************	
written by kaox 15-oct-2009
update by kaox 10-jan-2010

Fixed  downloading from free and premium account, Converted in OOPs format, removed un-neccesary code by Raj Malhotra on 27 Feb 2010
**********************************************************/
?>