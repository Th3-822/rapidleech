<?php
/**********************mediafire.com****************************\
mediafire.com Download Plugin
WRITTEN by Raj Malhotra on 1 Jan 2011
\**********************mediafire.com****************************/

if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class mediafire_com extends DownloadClass
{
	public function Download( $link )
	{
		global $premium_acc;
		
		$link = str_replace( "download.php", "", $link );
		
		if ( $_POST['step'] == 1 )
		{
			// password protected file
			$file_password = $_POST['password'];
		}

		$this->checkLink( $link, $file_password );
		
		if ( ( $_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"] ) ||
			( $_REQUEST ["premium_acc"] == "on" && $premium_acc ["mediafire_com"] ["user"] && $premium_acc ["mediafire_com"] ["pass"] ) )
		{
			$this->downloadPremium( $link, $file_password );
		}
		else
		{
			$this->downloadFree( $link, $file_password );
		}
	}
	
	private function checkLink( $link, $file_password )
	{
		$page = $this->GetPage( $link );
		$cookie = GetCookies( $page );
		
		if ( ( ! $file_password ) && ( stristr ( $page, "Eo();  dh('')" ) ) )
		{
			global $Referer, $nn;
			print "<div align='center'><b>File is password protected</b></div> $nn";
			print "<form name='dl' action=\"$PHP_SELF\" method='post'> $nn";
			print "<input type='hidden' name='link' value='" . urlencode ( $link ) . "'> $nn";
			print "<input type='hidden' name='step' value='1'> $nn";
			print "<input type='hidden' name='referer' value='" . urlencode ( $Referer ) . "'>";
			print "<input type='hidden' name='comment' id='comment' value='" . $_GET ["comment"] . "'>$nn";
			print "<input type='hidden' name='email' id='email' value='" . $_GET ["email"] . "'>$nn";
			print "<input type='hidden' name='partSize' id='partSize' value='" . $_GET ["partSize"] . "'>$nn";
			print "<input type='hidden' name='method' id='method' value='" . $_GET ["method"] . "'>$nn";
			print "<input type='hidden' name='proxy' id='proxy' value='" . $_GET ["proxy"] . "'>$nn";
			print "<input type='hidden' name='proxyuser' id='proxyuser' value='" . $_GET ["proxyuser"] . "'>$nn";
			print "<input type='hidden' name='proxypass' id='proxypass' value='" . $_GET ["proxypass"] . "'>$nn"; 
			print "<input type='hidden' name='path' id='path' value='" . $_GET ["path"] . "'>$nn";
			print "<h4>Enter password here: <input type='text' name='password' size='13'>&nbsp;&nbsp;";
			print "<input type='submit' onclick='return check()' value='Download File'></h4>$nn";
			print "<script language='JavaScript'>" . $nn . "function check() {" . $nn . "var imagecode=document.dl.imagestring.value;" . $nn . 'if (imagecode == "") { window.alert("You didn\'t enter the image verification code"); return false; }' . $nn . 'else { return true; }' . $nn . '}' . $nn . '</script>' . $nn;
			print "</form>$nn";
			print "</body>$nn";
			print "</html>";
			exit ();
		}
		
		if ( preg_match('/Location: *(.+)/i', $page, $newredir ) )
		{
			$Href = trim ( $newredir [1] );
			if ( !( strpos( $Href, "error.php" ) === false ) )
			{
				html_error('Invalid or Deleted File');
				return;
			}
		}
		else
		{
			// checking is it a mediafire folder
			$this->isMediaFireFolder( $page, $cookie );
		}
	}
	
	// Checking is it a mediafire folder or not? If yes, then moving it to auto downloader
	private function isMediaFireFolder( $page, $cookie )
	{
		$data = htmlspecialchars_decode( $page );
		$pattern = '/LoadJS\("((.+?)myfiles\.php(.+?))"/';
		if ( preg_match( $pattern , $page, $sharekey ) )
		{
			$sharekey = $sharekey[1];
			$getlinksharekey = "http://www.mediafire.com$sharekey";
			$page = $this->GetPage( $getlinksharekey, $cookie );
			if ( preg_match_all("/es\[[0-9]+\]=Array\('.*?','.*?',.*?,'(.*?)','.*?','(.*?)','.*?','(.*?)','(.*?)','.*?','.*?','.*?','.*?','.*?','.*?','.*?'\);/",$page ,$es))
			{
				$link_stack = array();
				foreach ( $es[1] as $code )
				{
					$link_in_folder = "http://www.mediafire.com/?$code";
					array_push( $link_stack, $link_in_folder);
				}
				$link_stack_length = count( $link_stack );
				// Removing last element as it is some kind of hash
				unset( $link_stack[$link_stack_length-1] );
				
				$this->moveToAutoDownloader( $link_stack );
			}
		}
	}

	private function downloadPremium( $link, $file_password )
	{
		html_error ("Premium download supported not added yet, ask developer to add this support!", 0 );
	}
	
	private function downloadFree( $link, $file_password )
	{
		$post = 0;
		if ( $file_password )
		{
			// password protected file
			$post = array();
			$post['downloadp'] = $file_password;
		}

		$page = $this->GetPage( $link, 0, $post );
		$cookie = GetCookies( $page );
		
		if ( preg_match('/Location: *(.+)/i', $page, $newredir ) )
		{
			$Href = trim ( $newredir [1] );
			if ( !( strpos( $Href, "error.php" ) === false ) )
			{
				html_error('Invalid or Deleted File');
				return;
			}
		}
		
		list ($pagea, $pageb) = explode("default:DoShow", $page, 2);
		$pageb = $this->str_conv($pageb);
		$pageb = stripslashes(str_replace(" ", "", $pageb));

		if (!preg_match("/=\W?(\w+)[\('\"]+(\w+)[,'\"]+(\w+)([,'\"]+(\w+))?['\"]+\)/", $pageb, $eb))
			html_error('Error 1');
		
		$pages = explode("function", $pagea);
		foreach ( $pages as $v )
		{
			$v = $this->str_conv($v);
			if ( strstr($v, $eb[1]."(") ) 
				break;
		}
		
		if (!preg_match("|getElementById\(.([0-9a-f]{32}).|", $v, $match)) 
			html_error('Error 2');
			
		if (!$eb[5])
		{
			$pages = explode("\n", $pagea);
			preg_match("|\w+=\W?(\w+)|", str_replace(" ", "", array_pop($pages)), $match_b);
			$eb[5] = $match_b[1];
		}

		$Href = "http://www.mediafire.com/dynamic/download.php?qk=" . $eb[2] . "&pk1=" . $eb[3] . "&r=" . $eb[5];
		$page = $this->GetPage( $Href, $cookie );
		$page = $this->str_conv($page);
		$page = stripslashes(str_replace(" ", "", $page));
		$v = trim( cut_str($page, $match[1],'}') );
		if (!preg_match("|http:[^\"']+(.\+(\w+)\+.)[^\"']+|", $v, $link_match)) 
			html_error('Error get download link');
			
		if (!preg_match("/".$link_match[2]."=.([\w]+)/", $page, $match)) 
			html_error('Error 3');
			
		$Href = str_replace($link_match[1], $match[1], $link_match[0]);
		$Url = parse_url($Href);
		$FileName = !$FileName ? basename($Url["path"]) : $FileName;

		$this->RedirectDownload( $Href, $FileName );
	}
	
	private function str_conv($str_or)
	{
		if (!preg_match("/unescape\([^\)]([^\)]+)[^\)]\);\w+=([0-9]+);[^\{^]+charCodeAt\(.\)([0-9\^]+)?/", $str_or, $match))
			return $str_or;
			
		$str_de = urldecode($match[1]);
		$match[3] = $match[3] ? $match[3] : "";
		for ($i = 0; $i < $match[2]; $i++)
		{
			$c = ord(substr($str_de, $i, 1));
			eval ("\$c = \$c".$match[3].";");
			$str_re .= chr($c);
		}
		$str_re = str_replace($match[0], $str_re, $str_or);
		if (preg_match("/unescape\([^\)]([^\)]+)[^\)]\).+charCodeAt\(.\)([0-9\^]+)/", $str_re, $dummy))
			$str_re = $this->str_conv($str_re);
		return $str_re;
	}
}

/**********************mediafire.com****************************\
mediafire.com Download Plugin
WRITTEN by Raj Malhotra on 1 Jan 2011
\**********************mediafire.com****************************/
?>