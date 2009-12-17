<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class rapidshare_com extends DownloadClass
{
	public function Download($link)
	{
		global $premium_acc;
		if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["rs_com"]))
		{
			$this->DownloadPremium($link);
		}
		else
		{
			$this->DownloadFree($link);
		}
	}
	private function DownloadFree($link)
	{
		global $nn, $PHP_SELF, $pauth;
		$page = $this->GetPage($link);
		
		is_present ( $page, "Due to a violation of our terms of use, the file has been removed from the server." );
		is_present ( $page, "This limit is reached", "This file is neither allocated to a Premium Account, or a Collector's Account, and can therefore only be downloaded 10 times. This limit is reached." );
		is_present ( $page, "This file is suspected to contain illegal content and has been blocked." );
		is_present ( $page, "The file could not be found.", "The file could not be found. Please check the download link." );
		is_present ( $page, "The uploader has removed this file from the server." );
		is_present ( $page, "This file has been removed from the server, because the file has not been accessed in a long time." );
		is_present ( $page, "is momentarily not available", "This server is momentarily not available.  We are aware of this and are working to get this resolved." );
		is_present ( $page, "unavailable due to hardware-problems", "Server unavailable due to hardware-problems" );
		is_present ( $page, "is already downloading a file", "Your IP-address is already downloading a file, Please wait until the download is completed." );

		$post = array ();
		$post ["dl.start"] = "Free";

		$Href = trim ( cut_str ( $page, '<form action="', '"' ) );
		$Url = parse_url ( $Href );

		global $Referer;
		$Referer = $link;
		$page = $this->GetPage($Href,0,$post);

		is_present ( $page, "is not allowed to use the free-service anymore today", "No more free downloads from this IP today" );
		is_present ( $page, "This limit is reached", "This file is neither allocated to a Premium Account, or a Collector's Account, and can therefore only be downloaded 10 times. This limit is reached." );
		is_present ( $page, "This file exceeds your download-limit", "Download limit exceeded" );
		is_present ( $page, "is already downloading a file", "Your IP-address is already downloading a file, Please wait until the download is completed." );
		
		if (stristr ( $page, "This file can only be downloaded by becoming a" ))
		{
			html_error ( "This file can only be downloaded by becoming a Premium member", 0 );
		}
		
		if (stristr ( $page, "lot of users are downloading files" ))
		{
			$minutes = trim ( cut_str ( $page, "Please try again in ", " minutes" ) );
			html_error ( "Currently a lot of users are downloading files. Please try again in <font color=black><span id='waitTime'>$minutes</span></font> minutes or become a Premium member", 0 );
		}
		
		if (stristr ( $page, "try again in" ))
		{
			$minutes = trim ( cut_str ( $page, "Or try again in about ", " minutes." ) );
			if ($minutes)
			{
				echo('<script type="text/javascript">');
				echo('wait_time = '.(($minutes + 1) * 60000).';');
				echo('function waitLoop() {');
				echo('if (wait_time == 0) {');
				echo('location.reload();');
				echo('}');
				echo('wait_time = wait_time - 60000;');
				echo('document.getElementById("waitTime").innerHTML = wait_time / 60000;');
				echo('setTimeout("waitLoop()",60000);');
				echo('}');
				echo('</script>');
				html_error ( "Download limit exceeded. You have to wait <font color=black><span id='waitTime'>$minutes</span></font> minute(s) until the next download.<script>waitLoop();</script>", 0 );
			}
			else
			{
				html_error ( "Download limit exceeded.", 0 );
			}
		}
		
		if (stristr ( $page, "Too many users downloading right now" ) || stristr ( $page, "Too many connections" ))
		{
			html_error ( "Too many users downloading right now", 0 );
		}
		$countDown = trim ( cut_str ( $page, "var c=", ";" ) );
	
		$form_content = "";
		preg_match ( '%<form name="dlf?".*</form>%s', $page, $form_content );
		$middle_str = str_replace ( "\\", "", preg_replace ( '/(\' *\+.*?(\r\n)*.*?\'|display:none;)/s', '', $form_content [0] ) );
		$code = '<center>' . trim ( $middle_str );
		$FileAddr = trim ( cut_str ( $code, '<form name="dlf" action="', '"' ) );
		$Href = parse_url ( $FileAddr );
		$FileName = basename ( $Href ["path"] );

		if (! $FileAddr)
		{
			html_error ( "Error getting download link", 0 );
		}

		$code = str_replace ( $FileAddr, $PHP_SELF, $code );
		$code = preg_replace ( '/<input type=image.*?".*?>/', '<input type=submit value=Download  onclick="return check()">', $code );
		$code = preg_replace ( '%<div><img.*Advanced download settings</div>%s', '', $code );

		$matches = "";
		preg_match_all ( "/http:\/\/rs(.*).rapidshare.com\/(.*)" . $FileName . "/iU", $code, $matches );

		if (! $matches)
		{
			html_error ( "Error getting available server's list", 0 );
		}

		for($i = 0; $i < count ( $matches [0] ); $i ++)
		{
			$Url = parse_url ( $matches [0] [$i] );
			$code = str_replace ( "document.dlf.action='" . $matches [0] [$i], "document.dlf.host.value='" . $Url ["host"], $code );
		}
		$code = str_replace('checked','',$code);
		$temp = explode('<br />',$code);
		global $RSHost;
		foreach ($temp as $k=>$temp2)
		{
			if (stristr($temp2,$RSHost))
			{
				$temp[$k] = str_replace('<input  type="radio"','<input checked="checked" type="radio"',$temp2);
			}
		}
		$temp = implode('<br />',$temp);
		$code = $temp;

		$code = str_replace ( "</form>", $nn, $code );

		$code .= "<input type=\"hidden\" name=\"filename\" value=\"" . urlencode ( $FileName ) . "\">$nn<input type=\"hidden\" name=\"link\" value=\"" . urlencode ( $link ) . "\">$nn<input type=\"hidden\" name=\"referer\" value=\"" . urlencode ( $Referer ) . "\">$nn<input type=\"hidden\" name=\"saveto\" value=\"" . $_GET ["path"] . "\">$nn<input type=\"hidden\" name=\"host\" value=\"" . $Href ["host"] . "\">$nn<input type=\"hidden\" name=\"path\" value=\"" . urlencode ( $Href ["path"] ) . "\">$nn";

		$code .= ($_GET ["add_comment"] == "on" ? "<input type=\"hidden\" name=\"comment\" value=\"" . urlencode ( $_GET ["comment"] ) . "\">$nn" : "") . "<input type=\"hidden\" name=\"email\" value=\"" . ($_GET ["domail"] ? $_GET ["email"] : "") . "\">$nn<input type=\"hidden\" name=\"partSize\" value=\"" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "\">$nn";
		$code .= "<input type=\"hidden\" name=\"method\" value=\"" . $_GET ["method"] . "\">$nn<input type=\"hidden\" name=\"proxy\" value=\"" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "\">$nn" . ($pauth ? "<input type=\"hidden\" name=\"pauth\" value=\"" . $pauth . "\">$nn" : "");
		$code .= "</form></center>";

		$js_code = "<script language=\"JavaScript\">" . $nn . "function check() {" . $nn . "var imagecode=document.dlf.accesscode.value;" . $nn . "var path=document.dlf.path.value;" . $nn;
		$js_code .= 'if (imagecode == "") { window.alert("You didn\'t enter the image verification code"); return false; }' . $nn . 'else {' . $nn . 'document.dlf.path.value=path+escape("?accesscode="+imagecode);' . $nn . 'return true; }' . $nn . '}' . $nn . '</script>' . $nn;
		$js_code .= "<script type=\"text/javascript\">setTimeout(\"document.dlf.submit()\", 5000);</script>";

		if (! $countDown)
		{
			print $code . $nn . $nn . $js_code . "$nn</body>$nn</html>";
		}
		else
		{
			insert_new_timer ( $countDown, rawurlencode ( $code ), "Download-Ticket reserved.", $js_code );
		}
	}
	private function DownloadPremium($link)
	{
		global $premium_acc;
		$page = $this->GetPage($link);
		
		is_present ( $page, "The file could not be found.", "The file could not be found. Please check the download link." );
		is_present ( $page, "This limit is reached", "This file is neither allocated to a Premium Account, or a Collector's Account, and can therefore only be downloaded 10 times. This limit is reached." );
		is_present ( $page, "Due to a violation of our terms of use, the file has been removed from the server." );
		is_present ( $page, "This file is suspected to contain illegal content and has been blocked." );
		is_present ( $page, "The uploader has removed this file from the server." );
		is_present ( $page, "This file has been removed from the server, because the file has not been accessed in a long time." );
		is_present ( $page, "is momentarily not available", "This server is momentarily not available.  We are aware of this and are working to get this resolved." );
		is_present ( $page, "unavailable due to hardware-problems", "Server unavailable due to hardware-problems" );

		$FileName = basename ( trim ( cut_str ( $page, '<form action="', '"' ) ) );
		$Url = parse_url($link);
		! $FileName ? $FileName = basename ( $Url ["path"] ) : "";
		if (isset ( $premium_acc ["rs_com"] ['user'] ) || $_REQUEST["premium_user"] && $_REQUEST['premium_pass'])
		{
			$auth = $_REQUEST ["premium_user"] ? base64_encode ( $_REQUEST ["premium_user"] . ":" . $_REQUEST ["premium_pass"] ) : base64_encode ( $premium_acc ["rs_com"] ["user"] . ":" . $premium_acc ["rs_com"] ["pass"] );
			$page = $this->GetPage($link,0,0,0,$auth);
			is_present ( $page, "password is incorrect" );
			is_present ( $page, "Account not found" );

			if (stristr ( $page, "Location:" ))
			{
				$Href = trim ( cut_str ( $page, "Location:", "\n" ) );
				$Url = parse_url ( $Href );

				$this->RedirectDownload($Href,$FileName, 0, 0, 0, $auth);
			}
			else
			{
				html_error ( "Cannot use premium account", 0 );
			}
		}
		else
		{
			$totalpremium = count ( $premium_acc ["rs_com"] );
			$success = 0;
			for($i = 0; $i < $totalpremium; $i++)
			{
				$acc = $premium_acc ["rs_com"] [$i] ['user'];
				$pass = $premium_acc ["rs_com"] [$i] ['pass'];
				$auth = base64_encode ( $acc . ":" . $pass );
				$page = $this->GetPage($link,0,0,0,$auth);
				if (stristr($page,"Account found, but password is incorrect")) continue;
				if (stristr($page,"Account not found")) continue;
				if (stristr($page,"You have exceeded the download limit.")) continue;

				if (stristr ( $page, "Location:" ))
				{
					$Href = trim ( cut_str ( $page, "Location:", "\n" ) );
					$Url = parse_url ( $Href );

					$success = 1;
					$this->RedirectDownload($Href,$FileName, 0, 0, 0, $auth);
					break;
				}
			}
			if (! $success)
			{
				html_error ( "No usable premium account", 0 );
			}
		}
	}
}
// updated by rajmalhotra  on 17 Dec 09 :  added some error messages
?>