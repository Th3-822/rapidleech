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
		global $Referer;
		
		$id = substr( $link, 13, 6 );
				
		//$Url = parse_url( $link );
		//$id = basename($Url['path']);
		//preg_match('%/x7.to/(.+?)/%', $link, $id);
		//$id = $id[1];
		
		$getlink = "http://x7.to/james/ticket/dl/$id";
		
		$post = Array();
		$page = $this->GetPage( $getlink, 0, $post, $Referer );
			
		$cookie = GetCookies($page);
		
		$badJson = trim ( cut_str ( $page, '{', '}' ) );
		$badJson = "{". $badJson. "}";
		
				
		$raj = $this->convertToJson( $badJson );
		
		$json = str_replace( "'", "\"", $raj );
		$jsarray = json_decode($json, true);
				
		if (!$jsarray)
		{
			html_error("Cannot decode Json string!",0);
		}

		if ( $jsarray['err'] == "limit-dl" )
		{
			//html_error("Download limit exceeded.",0 );
			html_error( lang(111), 0 );
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
	
	private function DownloadPremium( $link )
	{
		html_error("Working with Premium Account is not coded. This plugin need to update.",0);
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
}	

/**************************************************\  
WRITTEN by rajmalhotra  26 Jan 2010
Fixed by rajmalhotra 28 Jan 2010 
\**************************************************/
?>