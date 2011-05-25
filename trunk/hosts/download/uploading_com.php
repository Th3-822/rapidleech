<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class uploading_com extends DownloadClass 
{
	public function Download($link) 
	{
		global $premium_acc;
		if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["uploading"] ["user"] && $premium_acc ["uploading"] ["pass"]))
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
		$Referer = $link;
		$page = $this->GetPage($link);

		$fileID = trim ( cut_str($page,'file_id" value="','"') );
		$code = trim ( cut_str($page,'code" value="','"') );	
		$action = trim ( cut_str($page,'action" value="','"') );
		$Href_1 = trim ( cut_str($page,'form action="http://uploading.com/files','"') );
		$Href_1 = "http://uploading.com/files".$Href_1;
		$cookie = GetCookies($page);
		$temp = cut_str($page,'<div class="c_1','</div>');
		$FileName = trim(cut_str($temp,'<h2>','</h2>'));
		is_present($page, 'Sorry, the file requested by you does not exist on our servers','Download link not found');
		is_present($page, 'We are sorry, the file was removed either by its owner','Download link not found' );
		if (stristr ( $page, "Sorry, you can download only one file per" ))
		{
			$minutes = trim ( cut_str ( $page, "Sorry, you can download only one file per ", " minutes." ) );
			if ($minutes)
			{
				html_error ( "Download limit exceeded. Sorry, you can download only one file per <font color=black><span id='waitTime'>$minutes</span></font> minutes.Please try again later or acquire a premium membership.", 0 ); 
			}
			else
			{
				html_error ( "Download limit exceeded. Please try again later or acquire a premium membership.", 0 ); 
			}
		}
		$post = Array();
		$post["action"] = $action;
		$post["file_id"] = $fileID;
		$post["code"] = $code;
		$page = $this->GetPage( $Href_1, $cookie, $post, $Referer );
		is_present($page, "Requested file not found");
		preg_match_all('/start_timer\((\d+)\)/i', $page,$tm);
		$count = trim ( $tm[1][0] );
		insert_timer( $count, "Waiting link timelock", "", true );
		$tid=str_replace(".","12",microtime(true));
		$sUrl="http://uploading.com/files/get/?JsHttpRequest=".$tid."-xml";
		unset($post);
		$post["file_id"]=$fileID;
		$post["code"] = $code;
		$post["action"]="get_link";
		$post["pass"]="";
		$page = $this->GetPage( $sUrl, $cookie, $post, $Referer );
                if(strpos ( $page ,'You still need to wait for the download start' )){
                html_error("You still need to wait for the download start. Please wait for some minute and reattempt", 0);
                }
                $dUrl=str_replace("\\","",cut_str($page,'answer":{"link":"','"'));
		if ($dUrl=="") {
			html_error("Download url error , Please wait for some minute and reattempt",0);
		}
		$this->RedirectDownload( $dUrl, $FileName, $cookie, 0, $Referer );
		exit ();		
	}
	
	private function DownloadPremium( $link )
	{
		global $premium_acc, $Referer;
		
		$tid=str_replace(".","12",microtime(true));
		$loginUrl = "http://uploading.com/general/login_form/?JsHttpRequest=".$tid."-xml";
		$usrEmail = "";
		$post=array();
		$usrEmail = $_GET ["premium_user"] ? $_GET ["premium_user"] : $premium_acc ["uploading"] ["user"];
		$post["email"] = $usrEmail;
		$post["password"]= $_GET ["premium_pass"] ? $_GET ["premium_pass"] : $premium_acc ["uploading"] ["pass"];
		$page = $this->GetPage($loginUrl, 0, $post, 'http://uploading.com/login/' );

		$cookie=GetCookies($page);
		if(strpos($cookie,"error=") != false)
		{
			html_error("Login Failed , Bad username/password combination.",0);
		}
		$page = $this->GetPage( $link, $cookie, 0, $Referer );
                is_present($page, 'Sorry, the file requested by you does not exist on our servers','Download link not found');
		is_present($page, 'We are sorry, the file was removed either by its owner','Download link not found' );
		$code = trim( cut_str( $page, 'code: "', '",' ) );		
		$Url = parse_url( $link );
		$tmp = basename($Url["path"]);
		$FileName = str_replace(".html","",$tmp);
		$tid = str_replace(".","12",microtime(true));
		$sUrl = "http://uploading.com/files/get/?JsHttpRequest=".$tid."-xml";
		unset($post);
		$post["code"] = $code;
		$post["action"] = "get_link";
		$page = $this->GetPage( $sUrl, $cookie, $post, $Referer );
                $dUrl=str_replace("\\","",cut_str($page,'answer":{"link":"','"'));
		if ( $dUrl=="" ) 
		{ 
			html_error("Download url error , Please reattempt",0);
		}
		$this->RedirectDownload( $dUrl, $FileName, $cookie, 0, $Referer );
		exit ();
	}
}	

/**************************************************\  
WRITTEN by kaox 24-may-2009
UPDATE by kaox  29-nov-2009
UPDATE by rajmalhotra  20 Jan 2010
UPDATE by rajmalhotra Fix for downloading from Premium Accounts 23 Jan 2010 and converted in OOP's format
Fixed by rajmalhotra Fix for downloading from Free and Premium Accounts 07 Feb 2010. Basically fix changes due to change in Site
Fixed by VinhNhaTrang 27-10-2010
\**************************************************/
?>