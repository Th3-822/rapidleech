<?php
/**********************torrific.com****************************\
torrific.com Download Plugin
Written by kaox
Re-Written by Raj Malhotra on 16 May 2010
Updated name btaccel.com to torrific.com by SaKIB on 24 August 2010
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
		$pos = strpos( $link, "step=1" );
		if( !( $pos === false ) )
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
		
		$cookies = $this->loginto( $this->torrific_login, $this->torrific_pass );
		
		$page = $this->GetPage( $link, $cookies ); 
		
		$frmfiles=cut_str($page,'<table id="files"','</table>');
		//preg_match_all('%http://.+/get\?[^\'"]+%i',$frmfiles,$files) ;
		//preg_match_all( '%http://u01\.btaccel\.com/[^\'"]+%i', $frmfiles, $files ) ;
		preg_match_all( '%http://u01\.btaccel\.com/[^\"]+%i', $frmfiles, $files ) ;
		
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
			
				$file = $tmp . "?step=1&cookies=".urlencode ( $cookies );
								
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
		$cookie = trim ( cut_str ( $link, "cookies=", "\r\n" ) );
		$cookie = urldecode( $cookie );

		$Href = "http" . trim ( cut_str ( $link, "http", "?" ) );
		$Url = parse_url( $Href );

		$FileName = "";         
		$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
		$FileName = urldecode( $FileName );
		$FileName = str_replace( "'", "_", $FileName ); 
		
		$this->RedirectDownload( $Href, $FileName, $cookie, 0, $link );
		exit ();
	}
			
	private function loginto( $usr, $pass )
	{       
		if ( empty($usr) || empty($pass) )
		{
				html_error("Login/Pass not inserted",0);
		}
		else
		{
				$Href = "http://www.torrific.com/login/";
				$referer = "http://www.torrific.com/";
				
				$post["email"]=$usr;
				$post["password"]=$pass;
				$page = $this->GetPage( $Href, 0, $post, $referer );
				$cookie = GetCookies($page);
										
				$page = $this->GetPage('http://www.torrific.com/home/', $cookie, 0, $referer);
				is_notpresent($page, 'logout', 'Error logging in - perhaps logins are incorrect');
				
				return $cookie;
		}
	}
}       
/**********************torrific.com****************************\
torrific.com Download Plugin
Written by kaox
Re-Written by Raj Malhotra on 16 May 2010
Updated name btaccel.com to torrific.com by SaKIB on 24 August 2010
\**********************torrific.com****************************/
?>