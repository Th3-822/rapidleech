<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class x7_to extends DownloadClass 
{
	public function Download($link) 
	{
		global $premium_acc;
		if ( ($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["x7"] ["user"] && $premium_acc ["x7"] ["pass"] ) )
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
		$this->DownloadLink( $link );
	}
	
	private function DownloadPremium( $link )
	{
		global $premium_acc, $Referer;
		
		$loginUrl = "http://x7.to/james/login";
		$usrId = "";

		$post=array();
		$usrId = $_REQUEST ["premium_user"] ? $_REQUEST ["premium_user"] : $premium_acc ["x7"] ["user"];
		$post["id"] = $usrId;
		$post["pw"] = $_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["x7"] ["pass"];
		$page = $this->GetPage( $loginUrl, 0, $post, $loginUrl );
		
		$cookie = $this->GetX7Cookies($page);
		
		$badJson = trim ( cut_str ( $page, '{', '}' ) );
		$badJson = "{". $badJson. "}";
			
		$loginResponseJson = $this->convertToJson( $badJson );
		
		$json = str_replace( "'", "\"", $loginResponseJson );
		$jsarray = json_decode($json, true);
				
		if ( ( !$jsarray ) || ( $jsarray['succ'] != "true" ) )
		{
			html_error("Login Failed , Bad username/password combination.",0 );
		}

		$this->DownloadLink( $link, $cookie );		
	}
	
	private function DownloadLink( $link, $cookie = 0 )	
	{
		global $Referer;
		
		$id = substr( $link, 13, 6 );
			
		$getlink = "http://x7.to/james/ticket/dl/$id";
			
		$post = Array();
		$page = $this->GetPage( $getlink, $cookie, $post, $Referer );
		
		if( !$cookie )
		{
			$cookie = GetCookies($page);
		}		
		
		$badJson = trim ( cut_str ( $page, '{', '}' ) );
		$badJson = "{". $badJson. "}";
		
		$raj = $this->convertToJson( $badJson );
		
		$json = str_replace( "'", "\"", $raj );
		$jsarray = json_decode($json, true);
				
		if (!$jsarray)
		{
			html_error("Cannot decode Json string!",0);
		}

		if ( ( $jsarray['err'] == "limit-dl" ) || ( $jsarray['err'] == "limit-parallel" ) )
		{
			//html_error("Download limit exceeded.",0 );
			html_error( lang(111), 0 );
		}
		else if ( $jsarray['err'] )
		{
			html_error( $jsarray['err'], 0 );
		}
		else if ( $jsarray['type'] == "download" )
		{
			$Url = parse_url( $jsarray['url'] );
			$downloadId = basename($Url['path']);
			
			if ( ( $jsarray['url'] == "" ) || ( $downloadId == "dl" ) )
			{
				html_error("File not found. Kindly check the link",0 );
			}
			else
			{
				$count = $jsarray['wait'];
				if ( $count > 0 )
				{
					insert_timer( $count, "Waiting link timelock", "", true );
				}
			}
		}
				
		$Href = trim( $jsarray['url'] );
		$FileName = $id;
		
		$this->RedirectDownload($Href, $FileName, $cookie);
		exit ();
	}
	
	private function convertToJson( $badJson )
	{
		$badJson = str_replace( "{", "", $badJson );
		
		$pieces = explode( ",", $badJson );

		$json = "{";
		$isModified = false;
		foreach( $pieces as $key => $value )
		{		
			if ( $isModified )
			{
				$json = $json.",";
			}
			
			$piece = explode(":", $value);
			
			$json = $json."'".$piece[0]."'";
			for ( $i = 1; $i<count( $piece ); $i++ )
			{
				$json = $json.":".$piece[$i];
			} 
						
			$isModified = true;
		}
		
		return $json;
	}
	
	private function GetX7Cookies( $content ) 
	{
		// The U option will make sure that it matches the first character
		// So that it won't grab other information about cookie such as expire, domain and etc
		preg_match_all ( '/Set-Cookie: (.*)(;|\r\n)/U', $content, $temp );
	
		$cookie = $temp [1];
					
		$cookieMap = array();
		foreach( $cookie as $key => $value )
		{
			$explodedArray = explode( "=", $value, 2 );
			$cookieMap[ $explodedArray[0] ] = $explodedArray[1]; 
		}
		
		$x7cookie = array();
		foreach( $cookieMap as $key => $value )
		{
			array_push( $x7cookie,$key."=".$value );
		}
		
		$cook = implode ( '; ', $x7cookie );

		return $cook;
	}
}	

/**************************************************\  
WRITTEN by rajmalhotra  26 Jan 2010
Fixed by rajmalhotra 28 Jan 2010 
Added premium account support by rajmalhotra 31 Jan 2010 
\**************************************************/
?>