<?php
/**********************torrific.com****************************\
torrific.com Download Plugin
Written by kaox
Re-Written by Raj Malhotra on 16 May 2010
Fixed by Raj Malhotra on 19 Oct 2010
\**********************torrific.com****************************/

if (! defined ( 'RAPIDLEECH' ))
{
    require_once ("index.html");
    exit ();
}

class torrific_com extends DownloadClass
{
	####### Free Account Info. ###########
	private $torrific_login = "mssakib11@gmail.com";                      //  Set your username
	private $torrific_pass = "qwert";                                     //  Set your password
	##############################

	public function Download( $link )
	{		
		$isPresent = strpos( $link, "http://torrific.com/dl/" );
		if( !( $isPresent === false ) )
		{
			$this->DownloadFree( $link );
		}
		else
		{
			$this->displayLinks($link);
		}
	}

	private function displayLinks( $link )
	{
		$GetUser = parse_url( $link );

		$user = $GetUser['user'];
		$pass = $GetUser['pass'];
				
		if ( isset ( $user ) && isset ( $pass ) )
		{
			$this->torrific_login = trim( $user );
			$this->torrific_pass = trim( $pass );
			
			$link = $GetUser['scheme'] . "://" . $GetUser['host'] .$GetUser['path'];
		}
		
		$cookies = $this->login( $this->torrific_login, $this->torrific_pass );
		
		$page = $this->GetPage( $link, $cookies );
		is_present( $page, 'problem occurred', 'Sorry, a problem occurred. The original source of the torrent file cannot be loaded. Please find another source for this torrent, or try again later if you think this might just be a temporary problem.' );
				
		$frmfiles = cut_str( $page,'<table id="files"', '</table>' );
		//preg_match_all('%http://.+/get\?[^\'"]+%i',$frmfiles,$files) ;
		//preg_match_all( '%http://u01\.btaccel\.com/[^\'"]+%i', $frmfiles, $files ) ;
		//preg_match_all( '%http://u01\.btaccel\.com/[^\"]+%i', $frmfiles, $files ) ;
		preg_match_all( '%\/dl\/[^\"]+%i', $frmfiles, $files ) ;
		
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
				$file = "http://torrific.com" . $tmp;
					
				echo "<tr><td><input type=checkbox id=cs$cc ></td><td><input type=\"hidden\" id=\"lin$cc\" value=\"$file\" ></td><td id=link$cc >$FileName</td></tr>";
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
		$page = $this->GetPage( $link );
		
		if ( preg_match('/Location: *(.+)/i', $page, $newredir ) )
		{
			$Href = trim ( $newredir [1] );
		}
		else
		{
			html_error ("Cannot get download link!", 0 );
		}
			
		$Url = parse_url( $Href );

		$FileName = "";         
		$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
		$FileName = urldecode( $FileName );
		$FileName = str_replace( "'", "_", $FileName ); 
		
		$this->RedirectDownload( $Href, $FileName );
		exit ();
		
		/*
		//$cookie = trim ( cut_str ( $link, "cookies=", "\r\n" ) );
		//$cookie = urldecode( $cookie );

		//$Href = "http" . trim ( cut_str ( $link, "http", "?" ) );
		*/
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
Written by kaox
Re-Written by Raj Malhotra on 16 May 2010
Fixed by Raj Malhotra on 19 Oct 2010
\**********************torrific.com****************************/
?>