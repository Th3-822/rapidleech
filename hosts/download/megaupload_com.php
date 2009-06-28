<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}

if (preg_match ( "/f=(\w+)/", $Url ["query"], $matches )) {
	$page = geturl ( "www.megaupload.com", 80, "/xml/folderfiles.php?folderid=" . $matches [1], 0, 0, 0, 0, $_GET ["proxy"], $pauth );
	if (! preg_match_all ( "/url=\"(http[^\"]+)\"/", $page, $matches )) html_error ( 'link not found' );
	
	if (! is_file ( "audl.php" )) html_error ( 'audl.php not found' );
	echo "<form action=\"audl.php?GO=GO\" method=post>\n";
	echo "<input type=hidden name=links value='" . implode ( "\r\n", $matches [1] ) . "'>\n";
	foreach ( array (
		"useproxy", "proxy", "proxyuser", "proxypass" 
	) as $v )
		echo "<input type=hidden name=$v value=" . $_GET [$v] . ">\n";
	echo "<script language=\"JavaScript\">void(document.forms[0].submit());</script>\n</form>\n";
	flush ();
	exit ();
}

if ($_GET ["step"] != "1") {
	list ( $LINK, $filepassword ) = explode ( "|", $LINK, 2 );
	$LINK = preg_replace ( "/\.com\/[a-z]{2}\//", ".com/", $LINK );
	$Url = parse_url ( $LINK );
	$filepassword = trim($filepassword);
}

if (($_GET ["premium_acc"] == "on" && $_GET ["premium_user"] && $_GET ["premium_pass"]) || ($_GET ["premium_acc"] == "on" && $premium_acc ["megaupload"] ["user"] && $premium_acc ["megaupload"] ["pass"] || $_GET ["mu_acc"] == "on" && $_GET ["mu_cookie"]) || $_GET ["mu_acc"] == "on" && $mu_cookie_user_value) {
	if ($_GET['step'] == 1) {
		$post ["filepassword"] = $_GET ['filepassword'];
		$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), $Referer, $premium_cookie, $post, 0, $_GET ["proxy"], $pauth );
	} else {
		$post = array ();
		$post ['login'] = 1;
		$post ['redir'] = 1;
		
		$post ["username"] = $_GET ["premium_user"] ? $_GET ["premium_user"] : $premium_acc ["megaupload"] ["user"];
		$post ["password"] = $_GET ["premium_pass"] ? $_GET ["premium_pass"] : $premium_acc ["megaupload"] ["pass"];
		$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, "/?c=login", 0, 0, $post, 0, $_GET ["proxy"], $pauth );
		is_page ( $page );
		
		$premium_cookie = trim ( cut_str ( $page, "Set-Cookie:", ";" ) );
		
		if ($mu_cookie_user_value) {
			$premium_cookie = 'user=' . $mu_cookie_user_value;
		} elseif ($_GET ["mu_acc"] == "on" && $_GET ["mu_cookie"]) {
			$premium_cookie = 'user=' . $_GET ["mu_cookie"];
		} elseif (! stristr ( $premium_cookie, "user" )) {
			html_error ( "Cannot use premium account", 0 );
		}
		
		$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), 0, $premium_cookie, $filepassword ? array (
			"filepassword" => $filepassword 
		) : 0, 0, $_GET ["proxy"], $pauth );
		is_page ( $page );
		
		$Href = $LINK;
		$Referer = $LINK;
		if (stristr ( $page, 'password protected' )) {
			html_error("You should insert link with format: http://www.megaupload.com/?d=xxxxxxxx|password");
		}
	}
	
	if (stristr ( $page, "Location:" )) {
		$Href = trim ( cut_str ( $page, "Location: ", "\n" ) );
		$Url = parse_url ( $Href );
		$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
		
		insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] . "&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&cookie=" . urlencode ( $premium_cookie ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . ($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : "") );
	} elseif ($page = cut_str ( $page, 'downloadlink">', '</div>' )) {
		$Href = cut_str ( $page, 'href="', '"' );
		$Referer = $LINK;
		$Url = parse_url ( $Href );
		$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
		
		insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] . "&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&cookie=" . urlencode ( $premium_cookie ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . ($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : "") );
	} else {
		html_error ( "Download link not found", 0 );
	}
} else {
	$cookie = array (
		"user=XTOKCFTTQUGFM50AQ9C6AAY1SDEH-34O; megauploadtoolbar_id=D910E987B19B436EBF452B3C0D503909; megauploadtoolbar_visible=yes; toolbar=1; MUTBI=E%3D3%2CP%3D3; v=1" 
	);
	
	if ($_GET ["step"] == "1" || $filepassword) {
		if ($_GET ["step"] == "1") {
			$post ["captchacode"] = $_GET ["imagecode"];
			$post ["captcha"] = $_GET ["imagestring"];
			$post ["megavar"] = $_GET ["megavar"];
		} else
			$post ["filepassword"] = $filepassword;
		
		$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), $Referer, $cookie, $post, 0, $_GET ["proxy"], $pauth );
		is_page ( $page );
		
		is_present ( $page, "The file you are trying to access is temporarily unavailable" );
		if (! stristr ( $page, "id=\"captchaform" )) {
			$countDown = trim ( cut_str ( $page, "count=", ";" ) );
			$countDown = (! is_numeric ( $countDown ) ? 26 : $countDown);
			
			$Href = cut_str ( $page, 'downloadlink"><a href="', '"' );
			$Url = parse_url ( $Href );
			if (! is_array ( $Url )) {
				html_error ( "Download link not found", 0 );
			}
			
			insert_timer ( $countDown, "The file is being prepared.", "", true );
			
			$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
			
			insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] . "&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . ($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : "") );
			exit ();
		}
	} else {
		$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), 0, $cookie, 0, 0, $_GET ["proxy"], $pauth );
		is_page ( $page );
		
		if (stristr ( $page, "Location:" )) {
			$Referer = $LINK;
			$Href = trim ( cut_str ( $page, "ocation:", "\n" ) );
			$Url = parse_url ( $Href );
			
			$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), $Referer, $cookie, 0, 0, $_GET ["proxy"], $pauth );
			is_page ( $page );
			
			is_present ( $page, "All download slots assigned to your country", "All download slots assigned to your country are currently in use" );
			
			if (! stristr ( $page, "gencap.php?" )) {
				print "An error occured, see details below:<br>" . $nn . str_replace ( "<HEAD>", "<HEAD>$nn<base href=\"http://www.megaupload.com\">", $page );
				exit ();
			}
			$LINK = $Href;
		}
		
		is_present ( $page, 'The file you are trying to access is temporarily unavailable' );
		is_present ( $page, 'the link you have clicked is not available', 'Invalid link' );
		is_present ( $page, 'This file has expired due to inactivity' );
		
		$Href = $LINK;
		$Referer = $LINK;
		if (stristr ( $page, 'password protected' )) {
			print "<form name=\"dl\" action=\"$PHP_SELF\" method=\"post\">\n";
			print "<input type=\"hidden\" name=\"link\" value=\"" . urlencode ( $Href ) . "\">\n<input type=\"hidden\" name=\"referer\" value=\"" . urlencode ( $Referer ) . "\">\n<input type=\"hidden\" name=\"fileid\" value=\"$fid\">\n<input type=\"hidden\" name=\"imagecode\" value=\"$imagecode\">\n<input type=\"hidden\" name=\"megavar\" value=\"$megavar\">\n<input type=\"hidden\" name=\"step\" value=\"1\">\n";
			print "<input type=\"hidden\" name=\"comment\" id=\"comment\" value=\"" . $_GET ["comment"] . "\">\n<input type=\"hidden\" name=\"email\" id=\"email\" value=\"" . $_GET ["email"] . "\">\n<input type=\"hidden\" name=\"partSize\" id=\"partSize\" value=\"" . $_GET ["partSize"] . "\">\n<input type=\"hidden\" name=\"method\" id=\"method\" value=\"" . $_GET ["method"] . "\">\n";
			print "<input type=\"hidden\" name=\"proxy\" id=\"proxy\" value=\"" . $_GET ["proxy"] . "\">\n<input type=\"hidden\" name=\"proxyuser\" id=\"proxyuser\" value=\"" . $_GET ["proxyuser"] . "\">\n<input type=\"hidden\" name=\"proxypass\" id=\"proxypass\" value=\"" . $_GET ["proxypass"] . "\">\n<input type=\"hidden\" name=\"path\" id=\"path\" value=\"" . $_GET ["path"] . "\">\n";
			print "<h4>Enter password here: <input type=\"text\" name=\"filepassword\" size=\"13\">&nbsp;&nbsp;<input type=\"submit\" onclick=\"return check()\" value=\"Download File\"></h4>\n";
			print "<script language=\"JavaScript\">" . $nn . "function check() {" . $nn . "var imagecode=document.dl.imagestring.value;" . $nn . 'if (imagecode == "") { window.alert("You didn\'t enter the image verification code"); return false; }' . $nn . 'else { return true; }' . $nn . '}' . $nn . '</script>' . $nn;
			print "</form>\n</body>\n</html>";
			exit ();
		}
		
		if (stristr ( $page, "?c=happyhour" )) {
			preg_match ( '/<a href="(.*)" style="font-size:15px;"/', $page, $tmp );
			if (! $tmp [1]) {
				html_error ( "Download link not found in happy hour" );
			}
			$Href = $tmp [1];
			$Url = parse_url ( $Href );
			if (! is_array ( $Url )) {
				html_error ( "Download link not found", 0 );
			}
			$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
			insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] . "&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . ($pauth ? "&pauth=$pauth" : "") . (isset ( $_GET ["audl"] ) ? "&audl=doum" : "") );
			exit ();
		}
		
		if (! stristr ( $page, "id=\"captchaform" )) html_error ( "Image code not found", 0 );
	}
	
	$Href = $LINK;
	$Referer = $LINK;
	$page = cut_str ( $page, 'id="captchaform">', '</FORM>' );
	$imagecode = cut_str ( $page, 'captchacode" value="', '"' );
	$megavar = cut_str ( $page, '<input type="hidden" name="megavar" value="', '">' );
	
	$access_image_url = cut_str ( $page, 'img src="', '"' );
	
	print "<form name=\"dl\" action=\"$PHP_SELF\" method=\"post\">\n";
	print "<input type=\"hidden\" name=\"link\" value=\"" . urlencode ( $Href ) . "\">\n<input type=\"hidden\" name=\"referer\" value=\"" . urlencode ( $Referer ) . "\">\n<input type=\"hidden\" name=\"fileid\" value=\"$fid\">\n<input type=\"hidden\" name=\"imagecode\" value=\"$imagecode\">\n<input type=\"hidden\" name=\"megavar\" value=\"$megavar\">\n<input type=\"hidden\" name=\"step\" value=\"1\">\n";
	print "<input type=\"hidden\" name=\"comment\" id=\"comment\" value=\"" . $_GET ["comment"] . "\">\n<input type=\"hidden\" name=\"email\" id=\"email\" value=\"" . $_GET ["email"] . "\">\n<input type=\"hidden\" name=\"partSize\" id=\"partSize\" value=\"" . $_GET ["partSize"] . "\">\n<input type=\"hidden\" name=\"method\" id=\"method\" value=\"" . $_GET ["method"] . "\">\n";
	print "<input type=\"hidden\" name=\"proxy\" id=\"proxy\" value=\"" . $_GET ["proxy"] . "\">\n<input type=\"hidden\" name=\"proxyuser\" id=\"proxyuser\" value=\"" . $_GET ["proxyuser"] . "\">\n<input type=\"hidden\" name=\"proxypass\" id=\"proxypass\" value=\"" . $_GET ["proxypass"] . "\">\n<input type=\"hidden\" name=\"path\" id=\"path\" value=\"" . $_GET ["path"] . "\">\n";
	print "<h4>Enter <img src=\"$access_image_url\"> here: <input type=\"text\" name=\"imagestring\" size=\"3\">&nbsp;&nbsp;<input type=\"submit\" onclick=\"return check()\" value=\"Download File\"></h4>\n";
	print "<script language=\"JavaScript\">" . $nn . "function check() {" . $nn . "var imagecode=document.dl.imagestring.value;" . $nn . 'if (imagecode == "") { window.alert("You didn\'t enter the image verification code"); return false; }' . $nn . 'else { return true; }' . $nn . '}' . $nn . '</script>' . $nn;
	print "</form>\n</body>\n</html>";
}

?>