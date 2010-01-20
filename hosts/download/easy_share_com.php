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
		
		insert_timer( $count, "Waiting link timelock", "", true );
		
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
		
		$this->RedirectDownload( $Href, $FileName, $cookies, $post, $Referer, $pauth );
		exit ();
		
		/*
	$es = $_POST ['es'];
	if ($es == "ok") {    
		$post = array ();
		$post ["captcha"] = $_POST ["captcha"];
		$post ["id"] = $_POST ["id"];
		$cookie = $_POST ["cookie"];
		$Referer = $_POST ["referer"];
		$FileName = urldecode ( $_POST ["name"] );       
		$Url = parse_url ($_POST ["link"] );
	
        insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] . "&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&cookie=" . urlencode ( $cookie ) . "&post=" . urlencode ( serialize ( $post ) ) . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&method=POST&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . "&auth=" . $auth . ($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : "") );
	    
        } else {
		$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), 0, 0, 0, 0, $_GET ["proxy"], $pauth );
        $cookies=biscottiDiKaox($page);
		is_present ( $page, 'File was deleted' );
		is_present ( $page, 'File not found' );
        is_present ( $page, 'You have downloaded over 150MB during last hour.' );  
		$name = cut_str ( $page, "<title>Download ", "," );
		$count = (cut_str ( $page, "w='", "'" ));
		insert_timer ( $count, "Waiting link timelock", "", true );
        if ( $src = cut_str ( $page, "u='", "'" )){
        $Url=parse_url("http://".$Url["host"].$src);
        $page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"], $LINK, $cookies, 0, 0, $_GET ["proxy"], $pauth );
        is_page ( $page );
        }
        $LINK=cut_str($page,'post" action="','"');
        $id=cut_str($page,'"id" value="','"'); 
        $imgpath=cut_str($page,'<img src="','"');
        if (!$imgpath) html_error ( 'Error getting link' );   
        $imgurl="http://".$Url["host"].$imgpath;
        $Url=parse_url($imgurl);
        $page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"], $LINK, $cookies, 0, 0, $_GET ["proxy"], $pauth );
        is_page ( $page );
        
    
        $cook=GetCookies($page);
        $cookies.="; ".$cook;
		
		
        $headerend = strpos($page,"JFIF");
        $pass_img = substr($page,$headerend-6);
        $imgfile=$download_dir."easyshare_captcha.jpg"; 
        if (file_exists($imgfile)){ unlink($imgfile);} 
	    write_file($imgfile, $pass_img);

        print "<form method=\"post\" action=\"$PHP_SELF\">$nn";
        print "<b>Please enter code:</b><br>$nn";
        print "<img src=\"$imgfile?" . time () . "\" >$nn";
        print "<input name=\"link\" value=\"$LINK\" type=\"hidden\">$nn";
        print "<input name=\"referer\" value=\"$Referer\" type=\"hidden\">$nn";
        print "<input type=hidden name=id value=$id>$nn";
        print "<input name=\"es\" value=\"ok\" type=\"hidden\">$nn";
        print "<input name=\"cookie\" value=\"$cookies\" type=\"hidden\">$nn";
        print "<input name=\"name\" value=\"" . urlencode ( $name ) . "\" type=\"hidden\">$nn";
        print "<input name=\"captcha\" type=\"text\" >";
        print "<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";        
		}
		*/
	}
	
	private function DownloadPremium( $link )
	{
		global $premium_acc, $pauth;
		$Referer = $link;
		
		// login 
		$login = "http://www.easy-share.com/accounts/login";
		
		$post ["login"] = $_REQUEST ["premium_user"] ? $_REQUEST ["premium_user"] : $premium_acc ["easyshare"] ["user"];
		$post ["password"] = $_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["easyshare"] ["pass"];
		$post ["remember"] = "1";
		
		$page = $this->GetPage( $login, 0, $post, "http://www.easy-share.com/", $pauth );
		
		$cook = getcookies ( $page );
		// end login 
		
		is_notpresent ( $cook, "PREMIUMSTATUS", "Login failed<br>Wrong login/password?" );
		
		$Url = parse_url ( $link );
		$page = $this->GetPage( $login, $cook, 0, 0, $pauth );
			
		$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
		is_present ( $page, 'File was deleted' );
		is_present ( $page, 'File not found' );
		preg_match ( '/Location:.+?\\r/i', $page, $loca );
		$redir = rtrim ( $loca [0] );
		preg_match ( '/http:.+/i', $redir, $loca );
		$Href = trim ( $loca [0] );
		
		$cookie = $cook . "; " . $this->BiscottiDiKaox ( $page );
	
		$this->RedirectDownload( $Href, $FileName, $cookie, 0, $Referer, $pauth );
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
    // fixed by kaox 04/07/2009
	// FIXED and re-written by rajmalhotra on 10 Jan 2010
?>