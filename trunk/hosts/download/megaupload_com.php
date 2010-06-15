<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}

class megaupload_com extends DownloadClass {
	public $cookie = "user=XTOKCFTTQUGFM50AQ9C6AAY1SDEH-34O; megauploadtoolbar_id=D910E987B19B436EBF452B3C0D503909; megauploadtoolbar_visible=yes; toolbar=1; MUTBI=E%3D3%2CP%3D3; v=1" ;
	public function Download($link) {
		global $premium_acc,$mu_cookie_user_value;
		$matches = "";
		$Url = parse_url(trim($link));
		if (preg_match ( "/f=(\w+)/", $Url ["query"], $matches )) {
			$page = $this->GetPage("http://www.megaupload.com/xml/folderfiles.php?folderid=" . $matches [1]);
			if (! preg_match_all ( "/url=\"(http[^\"]+)\"/", $page, $matches )) html_error ( 'link not found' );
			
			if (! is_file ( "audl.php" )) html_error ( 'audl.php not found' );
			echo "<form action=\"audl.php?GO=GO\" method=post>\n";
			echo "<input type=hidden name=links value='" . implode ( "\r\n", $matches [1] ) . "'>\n";
			foreach ( array ( "useproxy", "proxy", "proxyuser", "proxypass" ) as $v )
				echo "<input type=hidden name=$v value=" . $_GET [$v] . ">\n";
			echo "<script language=\"JavaScript\">void(document.forms[0].submit());</script>\n</form>\n";
			flush ();
			exit ();
		}
		if ($_GET ["step"] != "1") {
			list ( $link, $filepassword ) = explode ( "|", $link, 2 );
			$link = preg_replace ( "/\.com\/[a-z]{2}\//", ".com/", $link );
			$this->filepassword = trim($filepassword);
		}
		if (isset($_REQUEST['premium_acc'])) {
			if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
				($_REQUEST ["premium_acc"] == "on" && $premium_acc ["megaupload_com"] ["user"] && $premium_acc ["megaupload_com"] ["pass"] ||
				$_REQUEST ["mu_acc"] == "on" && $_REQUEST ["mu_cookie"]) || $_REQUEST ["mu_acc"] == "on" && $mu_cookie_user_value) {
				$this->DownloadPremium($link);
			} else {
				if ($_POST['step'] == 1) {
					return $this->DownloadFree($link);
				} else {
					return $this->EnterCaptchaCode($link);
				}
			}
		} else {
			if (isset($_POST['step'])) {
				if ($_POST['step'] == 1) {
					return $this->DownloadFree($link);
				} else {
					return $this->EnterCaptchaCode($link);
				}
			} else {
				return $this->EnterCaptchaCode($link);
			}
		}
		return "";
	}
	private function EnterCaptchaCode($link) {
		global $nn, $PHP_SELF, $options;
		$page = $this->GetPage($link,$this->cookie);
		
		if (stristr ( $page, "Location:" )) {
			$Referer = $link;
			$Href = trim ( cut_str ( $page, "ocation:", "\n" ) );
			
			$page = $this->GetPage($Href,$this->cookie,0,$Referer);			
			
			is_present ( $page, "All download slots assigned to your country", "All download slots assigned to your country are currently in use" );
			
			if (! stristr ( $page, "gencap.php?" )) {
				print "An error occured, see details below:<br>" . $nn . str_replace ( "<HEAD>", "<HEAD>$nn<base href=\"http://www.megaupload.com\">", $page );
				exit ();
			}
			$link = $Href;
		}
		
		is_present ( $page, 'The file you are trying to access is temporarily unavailable' );
		is_present ( $page, 'the link you have clicked is not available', 'Invalid link' );
		is_present ( $page, 'This file has expired due to inactivity' );
		
		$Href = $link;
		$Referer = $link;
		if (stristr ( $page, 'password protected' )) {
			print "<form name=\"dl\" action=\"$PHP_SELF\" method=\"post\">\n";
			print "<input type=\"hidden\" name=\"link\" value=\"" . urlencode ( $Href ) . "\">\n<input type=\"hidden\" name=\"referer\" value=\"" . urlencode ( $Referer ) . "\">\n<input type=\"hidden\" name=\"step\" value=\"1\">\n";
			print "<input type=\"hidden\" name=\"comment\" id=\"comment\" value=\"" . $_GET ["comment"] . "\">\n<input type=\"hidden\" name=\"email\" id=\"email\" value=\"" . $_GET ["email"] . "\">\n<input type=\"hidden\" name=\"partSize\" id=\"partSize\" value=\"" . $_GET ["partSize"] . "\">\n<input type=\"hidden\" name=\"method\" id=\"method\" value=\"" . $_GET ["method"] . "\">\n";
			print "<input type=\"hidden\" name=\"proxy\" id=\"proxy\" value=\"" . $_GET ["proxy"] . "\">\n<input type=\"hidden\" name=\"proxyuser\" id=\"proxyuser\" value=\"" . $_GET ["proxyuser"] . "\">\n<input type=\"hidden\" name=\"proxypass\" id=\"proxypass\" value=\"" . $_GET ["proxypass"] . "\">\n<input type=\"hidden\" name=\"path\" id=\"path\" value=\"" . $_GET ["path"] . "\">\n";
			print "<h4>Enter password here: <input type=\"text\" name=\"filepassword\" size=\"13\">&nbsp;&nbsp;<input type=\"submit\" onclick=\"return check()\" value=\"Download File\"></h4>\n";
			print "<script language=\"JavaScript\">" . $nn . "function check() {" . $nn . "var imagecode=document.dl.imagestring.value;" . $nn . 'if (imagecode == "") { window.alert("You didn\'t enter the image verification code"); return false; }' . $nn . 'else { return true; }' . $nn . '}' . $nn . '</script>' . $nn;
			print "</form>\n</body>\n</html>";
			exit ();
		}
		
		if (stristr ( $page, "?c=happyhour" )) {
			$tmp = "";
			preg_match ( '/<a href="(.*)" style="font-size:15px;"/', $page, $tmp );
				
			if (! $tmp [1]) 
			{
				// Going ahead because in Happy Hour megaupload need Captcha
				//html_error ( "Download link not found in happy hour" );
			}
			else
			{
				$Href = $tmp [1];
				$Url = parse_url ( html_entity_decode($Href, ENT_QUOTES, 'UTF-8') );
				if (! is_array ( $Url )) 
				{
					html_error ( "Download link not found", 0 );
				}
				$FileName = basename ( $Url ["path"] );
				$this->RedirectDownload($Href,$FileName,$this->cookie);
				exit ();
			}
		}
		
		if (! stristr ( $page, "id=\"captchaform" )) html_error ( "Image code not found", 0 );
	
		$Href = $link;
		$Referer = $link;
		$page = cut_str ( $page, 'id="captchaform">', '</FORM>' );
		$imagecode = cut_str ( $page, 'captchacode" value="', '"' );
		$megavar = cut_str ( $page, '<input type="hidden" name="megavar" value="', '">' );
		
		$access_image_url = cut_str ( $page, 'img src="', '"' );
		
		// Fetching megaupload captha image STARTED 
		$cap_img = $options['download_dir']."megaupload_captcha.gif";
		if ($fp = fopen($access_image_url, 'r')) 
		{
		   $content = '';
		   // keep reading until there's nothing left
		   while ($line = fgets($fp, 1024)) 
		   {
			  $content .= $line;
		   }
			 // Deleting old captcha image file
		   if ( file_exists( $cap_img ) )
		   {
				unlink( $cap_img );
		   }
			// Saving megaupload new captcha image
			$fpt = fopen($cap_img, "w");
			fwrite($fpt, $content);
			fclose($fpt);
		}
		// Fetching megaupload captha image ENDED 
		
		print "<form name=\"dl\" action=\"$PHP_SELF\" method=\"post\">\n";
		print "<input type=\"hidden\" name=\"link\" value=\"" . urlencode ( $Href ) . "\">\n<input type=\"hidden\" name=\"referer\" value=\"" . urlencode ( $Referer ) . "\">\n<input type=\"hidden\" name=\"imagecode\" value=\"$imagecode\">\n<input type=\"hidden\" name=\"megavar\" value=\"$megavar\">\n<input type=\"hidden\" name=\"step\" value=\"1\">\n";
		print "<input type=\"hidden\" name=\"comment\" id=\"comment\" value=\"" . $_GET ["comment"] . "\">\n<input type=\"hidden\" name=\"email\" id=\"email\" value=\"" . $_GET ["email"] . "\">\n<input type=\"hidden\" name=\"partSize\" id=\"partSize\" value=\"" . $_GET ["partSize"] . "\">\n<input type=\"hidden\" name=\"method\" id=\"method\" value=\"" . $_GET ["method"] . "\">\n";
		print "<input type=\"hidden\" name=\"proxy\" id=\"proxy\" value=\"" . $_GET ["proxy"] . "\">\n<input type=\"hidden\" name=\"proxyuser\" id=\"proxyuser\" value=\"" . $_GET ["proxyuser"] . "\">\n<input type=\"hidden\" name=\"proxypass\" id=\"proxypass\" value=\"" . $_GET ["proxypass"] . "\">\n<input type=\"hidden\" name=\"path\" id=\"path\" value=\"" . $_GET ["path"] . "\">\n";
		print "<h4>".lang(301)." <img src=\"$cap_img\" style='background-color: #FFF'> ".lang(302).": <input type=\"text\" name=\"imagestring\" size=\"3\">&nbsp;&nbsp;<input type=\"submit\" onclick=\"return check()\" value=\"".lang(303)."\"></h4>\n";
		print "<script language=\"JavaScript\">" . $nn . "function check() {" . $nn . "var imagecode=document.dl.imagestring.value;" . $nn . 'if (imagecode == "") { window.alert("You didn\'t enter the image verification code"); return false; }' . $nn . 'else { return true; }' . $nn . '}' . $nn . '</script>' . $nn;
		print "</form>\n</body>\n</html>";
	}
	private function DownloadFree($link) {
		global $Referer;
		if ($_GET ["step"] == "1") {
			$post ["captchacode"] = $_GET ["imagecode"];
			$post ["captcha"] = $_GET ["imagestring"];
			$post ["megavar"] = $_GET ["megavar"];
		} else
			$post ["filepassword"] = $this->filepassword;
		
		$page = $this->GetPage($link,$this->cookie,$post,$Referer);
		
		is_present ( $page, "The file you are trying to access is temporarily unavailable" );
		if (! stristr ( $page, "id=\"captchaform" )) {
			$countDown = trim ( cut_str ( $page, "count=", ";" ) );
			$countDown = (! is_numeric ( $countDown ) ? 26 : $countDown);
			
			$Href = cut_str ( $page, 'downloadlink"><a href="', '"' );
			$Url = parse_url ( html_entity_decode($Href, ENT_QUOTES, 'UTF-8') );
			if (! is_array ( $Url )) {
				html_error ( "Download link not found", 0 );
			}
			
			insert_timer ( $countDown, "The file is being prepared.", "", true );
			
			$FileName = basename ( $Url ["path"] );
			$this->RedirectDownload($Href,$FileName,$this->cookie);
			exit ();
		}
	}
	private function DownloadPremium($link) {
		global $Referer, $premium_acc, $mu_cookie_user_value;
		if ($_GET['step'] == 1) {
                $post ["filepassword"] = $_GET ['filepassword'];
                $this->GetPage($link,0,$post,$Referer);
        } else {
                $post = array ();
                $post ['login'] = 1;
                $post ['redir'] = 1;
                
                $post ["username"] = $_GET ["premium_user"] ? $_GET ["premium_user"] : $premium_acc ["megaupload_com"] ["user"];
                $post ["password"] = $_GET ["premium_pass"] ? $_GET ["premium_pass"] : $premium_acc ["megaupload_com"] ["pass"];
                $page = $this->GetPage('http://www.megaupload.com/?c=login',0,$post);
                
                $premium_cookie = trim ( cut_str ( $page, "Set-Cookie:", ";" ) );
                
                if ($mu_cookie_user_value) {
                        $premium_cookie = 'user=' . $mu_cookie_user_value;
                } elseif ($_GET ["mu_acc"] == "on" && $_GET ["mu_cookie"]) {
                        $premium_cookie = 'user=' . $_GET ["mu_cookie"];
                } elseif (! stristr ( $premium_cookie, "user" )) {
                        html_error ( "Cannot use premium account", 0 );
                }
                
                $page = $this->GetPage($link,$premium_cookie,$this->filepassword ? array (
                        "filepassword" => $this->filepassword 
                ) : 0);
                is_page ( $page );
                
                $Href = $link;
                $Referer = $link;
                if (stristr ( $page, 'password protected' )) {
                        html_error("You should insert link with format: http://www.megaupload.com/?d=xxxxxxxx|password");
                }
        }
        
        if (stristr ( $page, "Location:" )) {
                $Href = trim ( cut_str ( $page, "Location: ", "\n" ) );
                $Url = parse_url ( html_entity_decode($Href, ENT_QUOTES, 'UTF-8') );
                $FileName = basename ( $Url ["path"] );
                $this->RedirectDownload($Href,$FileName,$premium_cookie);
                
        } elseif ($page = cut_str ( $page, 'downloadlink">', '</div>' )) {
                $Href = cut_str ( $page, 'href="', '"' );
                $Referer = $link;
                $Url = parse_url ( html_entity_decode($Href, ENT_QUOTES, 'UTF-8') );
                $FileName = basename ( $Url ["path"] );
                $this->RedirectDownload($Href,$FileName,$premium_cookie);
        } else {
                html_error ( "Download link not found", 0 );
        }
	}
}

// Updated by rajmalhotra on 10 Jan 2010 MegaUpload captcha is downloaded on server, then display
// Fixed by rajmalhotra on 20 Jan 2010 Fixed for Download link not found in happy hour
// Updated 08-June-2010 for cookie encryption (szal)
?>
