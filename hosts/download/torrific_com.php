<?php
/**********************torrific.com****************************\
torrific.com Download Plugin
Written by Raj Malhotra on 16 May 2010
Fixed by Raj Malhotra on 19 Oct 2010
Updated by Raj Malhotra on 07 Nov 2010
Updated by Raj Malhotra on 08 Nov 2010
\**********************torrific.com****************************/

/******** If you don't have torrific login, then you can use this account ********\
Email : mssakib11@gmail.com
Password : qwert
Put this account in accounts.php
\******** If you don't have torrific login, then you can use this account ********/


if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class torrific_com extends DownloadClass
{
	public function Download( $link )
	{
		global $premium_acc;
		
		//$isPresent = strpos( $link, "http://torrific.com/dl/" );
		$isPresent = strpos( $link, "step=1" );
		if( !( $isPresent === false ) )
		{
			$this->DownloadFree( $link );
		}
		elseif ( ( $_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"] ) ||
			( $_REQUEST ["premium_acc"] == "on" && $premium_acc ["torrific_com"] ["user"] && $premium_acc ["torrific_com"] ["pass"] ) )	
		{
			$this->displayLinks( $link );
		}
		else
		{
			$this->downloadNotSupported( $link );
		}
	}
	
	private function displayLinks( $link )
	{
		$GetUser = parse_url( $link );

		$user = $GetUser['user'];
		$password = $GetUser['pass'];
				
		if ( isset ( $user ) && isset ( $pass ) )
		{
			$link = $GetUser['scheme'] . "://" . $GetUser['host'] .$GetUser['path'];
		}
		else
		{
			global $premium_acc;
				
			$user = $_REQUEST ["premium_user"] ? $_REQUEST ["premium_user"] : $premium_acc ["torrific_com"] ["user"];
			$password = $_REQUEST ["premium_pass"] ? $_REQUEST ["premium_pass"] : $premium_acc ["torrific_com"] ["pass"];
		}
		
		// login
		$cookies = $this->login( $user, $password );
		// end login
				
		$page = $this->GetPage( $link, $cookies );
		$new_cookie = GetCookies( $page );
		$cookies = $cookies . "; " . $new_cookie;

		if ( !( strpos( $page, 'torrent has not yet been queued' ) === false ) )
		{
			// Torrent is not fetched. Fetching it first.
			echo "Torrent is not fetched. Fetching it first.";
			$Href = "http://torrific.com/queue/";
			
			$queueForm  = trim ( cut_str ( $page , 'action="/queue/"', '</form>' ) );
			$csrfmiddlewaretoken  = trim ( cut_str ( $queueForm , "csrfmiddlewaretoken' value='", "'" ) );
			$url  = trim ( cut_str ( $queueForm , 'url" value="', '"' ) );
			
			$post = array();
			$post['csrfmiddlewaretoken'] = $csrfmiddlewaretoken;
			$post['url'] = $url;
			$post[''] = "fetch";
						
			$page = $this->GetPage( $Href, $cookies, $post );
			
			if ( preg_match('/Location: *(.+)/i', $page, $newredir ) )
			{
				$Href = trim ( $newredir [1] );
				$page = $this->GetPage( $Href, $cookies );
			}
		}
		
		is_present( $page, 'problem occurred', 'Sorry, a problem occurred. The original source of the torrent file cannot be loaded. Please find another source for this torrent, or try again later if you think this might just be a temporary problem.' );
		is_present( $page, 'torrent was removed', 'Sorry, this torrent was removed.' );
		is_present( $page, 'torrent is downloading', 'Your torrent is downloading. Please wait till it get finished.' );
		is_present( $page, 'torrent was added to the queue', 'Sorry, this torrent was in queue, please wait till it gets completed.' );
		
		if ( strpos( $page, '<table id="files"' ) === false )
		{
			html_error ("Unable to fetch the download links. Please check your download link!", 0 );
		}
		
		$frmfiles = cut_str( $page,'<table id="files"', '</table>' );
		//preg_match_all('%http://.+/get\?[^\'"]+%i',$frmfiles,$files) ;
		//preg_match_all( '%http://u01\.btaccel\.com/[^\'"]+%i', $frmfiles, $files );
		preg_match_all( '%http://u01\.btaccel\.com/[^\"]+%i', $frmfiles, $files ) ;
		preg_match_all( '%\/dl\/[^\"]+%i', $frmfiles, $filesNew ) ;
		
		$cc=1 ;
		echo "\n";
?>              
<table border="1" align="center" cellspacing="5" cellpadding="5">
        <form id="tavola">
<?php
        
		foreach( $files[0] as $tmp )
		{
				$FileName = "";         
				$Url = parse_url ( $tmp );
				$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
				$FileName = urldecode( $FileName );
			
				//$file = $tmp . "?step=1&cookies=".urlencode ( $cookies );
				$file = $tmp . "?isHostChanged=true&step=1&cookies=".urlencode ( $cookies );
				$file = str_replace( 'btaccel' , 'torrific', $file );
				//$file = "http://torrific.com" . $tmp;
					
				echo "<tr><td><input type=checkbox id=cs$cc ></td><td><input type='hidden' id=\"lin$cc\" value=\"$file\" ></td><td id=link$cc >$FileName</td></tr>";
				$cc++;
		}
		
		foreach( $filesNew[0] as $tmp )
		{
				$FileName = "";         
				$Url = parse_url ( $tmp );
				$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
				$FileName = urldecode( $FileName );
			
				//$file = $tmp . "?step=1&cookies=".urlencode ( $cookies );
				$file = "http://torrific.com" . $tmp . "?isHostChanged=false&step=1&cookies=" . urlencode ( $cookies );
					
				echo "<tr><td><input type=checkbox id=cs$cc ></td><td><input type='hidden' id=\"lin$cc\" value=\"$file\" ></td><td id=link$cc >$FileName</td></tr>";
				$cc++;
		}
		
?>
                <tr>
					<td>
						<input type='checkbox' name='checkall' id='checkall' onclick='checkedAll();' /> all
					</td>
					<td>
					</td>
					<td align=center>
						<input type=button onclick='selt(<?php echo $cc-1 ?>)' value='Step1 select and click' />
					</td>
                </tr>
        </form>
</table>
<script language="javascript" type="text/javascript">
    function selt(cc)
    {
        var pp;
        var lks="";    
        var ck;
        var tmp;
        var tmp2;
                
		var totalCheckedLinks = 0;
        for( pp=1; pp<=cc; pp++ )
		{
			ck = document.getElementById("cs"+ pp );
			if (ck.checked) 
			{
					totalCheckedLinks++;
					tmp = document.getElementById("lin"+ pp );
					tmp2 = tmp.value.replace(/%/g,"%25");
					lks = lks + tmp2 + "\r\n";
			}
        }
                
		if ( totalCheckedLinks == 1 )
		{
				// only one link to download, so rl can download it.
				document.write('<form id="downloadFormId" name="downloadForm" action=index.php method=post >');
				document.write('<Input type=Hidden name=link value= "' + lks + '" >');  
				// document.write('<center><Input type=submit name=submitButton value="Step2 click for download"></center>');   
				document.write('</form>');
		}
		else
		{
				// morethan one link to download, so autodownloader will download it.
				document.write('<form id="downloadFormId" name="downloadForm" action=audl.php?GO=GO method=post >');
				document.write('<Input type=Hidden name=links value= "' + lks + '" >'); 
				// document.write('<center><Input type=submit name=submit value="Step2 click for send selected to Autodownloader"></center>');  
				document.write('</form>');
		}
		
		document.getElementById( "downloadFormId" ).submit();
                
    }
        
	checked=false;
	function checkedAll (frm1) 
	{
		var aa= document.getElementById('tavola');
		if (checked == false)
		{
			checked = true
		}
		else
		{
		  checked = false
		}

		for (var i =0; i < aa.elements.length; i++) 
		{
			aa.elements[i].checked = checked;
		}
    }
</script>
        
<?php   

		flush();
		exit ();
	}
        
	private function DownloadFree( $link )
	{
		$cookie = trim ( cut_str ( $link, "cookies=", "\r\n" ) );
		$cookie = urldecode( $cookie );

		$Href = "http" . trim ( cut_str ( $link, "http", "?" ) );
		$isHostChanged = trim ( cut_str ( $link, "isHostChanged=", "&" ) );
		
		if( $isHostChanged == true )
		{
			$Href = str_replace( 'torrific', 'btaccel' , $Href );
		}
		else
		{
			//$page = $this->GetPage( $link );
			$page = $this->GetPage( $Href, $cookie );
			
			if ( preg_match('/Location: *(.+)/i', $page, $newredir ) )
			{
				$Href = trim ( $newredir [1] );
			}
			else
			{
				html_error ("Cannot get download link!", 0 );
			}
		}
					
		$Url = parse_url( $Href );

		$FileName = "";         
		$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
		$FileName = urldecode( $FileName );
		$FileName = str_replace( "'", "_", $FileName ); 
		
		$this->RedirectDownload( $Href, $FileName, $cookie );
		exit ();
	}
	
	private function downloadNotSupported( $link )
	{
		html_error( "Login/Pass not inserted. To download files you must be a member or premium user.", 0 );
	}
	
	private function login( $user, $password )
	{
		if ( empty($user) || empty($password) )
		{
			html_error("Login/Pass not inserted",0);
		}
		else
		{
			$loginURL = "http://torrific.com/login/";
			$Referer = $loginURL;
						
			$page = $this->GetPage( $loginURL );
			$cook = GetCookies( $page );
			
			$csrfmiddlewaretoken = trim ( cut_str( $page, "csrfmiddlewaretoken' value='", "'" ) );
				
			$post["csrfmiddlewaretoken"] = $csrfmiddlewaretoken;
			$post["email"] = $user;
			$post["password"] = $password;
			$post["next"] = "/";
		
			$page = $this->GetPage( $loginURL, $cook, $post, $Referer );
			$cookie = GetCookies( $page );
			
			is_present( $page, 'login failed', 'Login Failed, please try again!' );

			$page = $this->GetPage( 'http://torrific.com/home/', $cookie, 0, $Referer );
			is_notpresent($page, 'logout', 'Error logging in - perhaps logins are incorrect');
			
			return $cookie;
		}
	}
}

/**********************torrific.com****************************\
torrific.com Download Plugin
Written by Raj Malhotra on 16 May 2010
Fixed by Raj Malhotra on 19 Oct 2010
Updated by Raj Malhotra on 07 Nov 2010
Updated by Raj Malhotra on 08 Nov 2010
\**********************torrific.com****************************/
?>