<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}

if (($_GET ["premium_acc"] == "on" && $_GET ["premium_user"] && $_GET ["premium_pass"]) || ($_GET ["premium_acc"] == "on" && $premium_acc ["depositfiles"] ["user"] && $premium_acc ["depositfiles"] ["pass"])) {
	function BiscottiDiKaox($content) {
		preg_match_all ( "/Set-Cookie: (.*)\n/", $content, $matches );
		foreach ( $matches [1] as $coll ) {
			$bis0 = split ( ";", $coll );
			$bis1 = $bis0 [0] . "; ";
			$bis2 = split ( "=", $bis1 );
			if (substr_count ( $bis, $bis2 [0] ) > 0) {
				$patrn = $bis2 [0] . "[^ ]+";
				$bis = preg_replace ( "/$patrn/", $bis1, $bis );
			} else {
				$bis .= $bis1;
			}
		}
		$bis = str_replace ( "  ", " ", $bis );
		return rtrim ( $bis );
	}
	
	// login 
	$login = "http://depositfiles.com/en/login.php";
	$urlg = parse_url ( $login );
	$post ["login"] = $_GET ["premium_user"] ? $_GET ["premium_user"] : $premium_acc ["depositfiles"] ["user"];;
	$post ["password"] = $_GET ["premium_pass"] ? $_GET ["premium_pass"] : $premium_acc ["depositfiles"] ["pass"];
	$post ["go"] = "1";
	$page = geturl ( $urlg ["host"], $urlg ["port"] ? $urlg ["port"] : 80, $urlg ["path"] . ($urlg ["query"] ? "?" . $urlg ["query"] : ""), "http://depositfiles.com/en/", 0, $post, 0, $_GET ["proxy"], $pauth );
	$cook = BiscottiDiKaox ( $page );
	
	// end login 
	

	is_notpresent ( $cook, "autologin", "Login failed<br>Wrong login/password?" );
	
	$Url ["path"] = preg_replace ( "/\/.*files/", "/en/files", $Url ["path"] );
	$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), 0, $cook, 0, 0, $_GET ["proxy"], $pauth );
	is_present ( $page, 'has been removed', "The file has been removed" );
	preg_match ( "/http:\/\/.+auth-[^'\"]+/i", $page, $dw );
	$Url = parse_url ( $dw [0] );
	$FileName = basename ( $Url ["path"] );
	
	insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] . "&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&cookie=" . urlencode ( $cookie ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . $_POST ["link2"] . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . ($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : "") );
} else {
	if (preg_match ( '%^/ru/files/%', $Url ["path"] ) != 0) {
		$Url ["path"] = preg_replace ( '%^/ru/files/%', '/en/files/', $Url ["path"] );
	} elseif (preg_match ( '%^/de/files/%', $Url ["path"] ) != 0) {
		$Url ["path"] = preg_replace ( '%^/de/files/%', '/en/files/', $Url ["path"] );
	} elseif (preg_match ( '%^/es/files/%', $Url ["path"] ) != 0) {
		$Url ["path"] = preg_replace ( '%^/es/files/%', '/en/files/', $Url ["path"] );
	} elseif (preg_match ( '%^/files/%', $Url ["path"] ) != 0) {
		$Url ["path"] = preg_replace ( '%^/files/%', '/en/files/', $Url ["path"] );
	}
	$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), 0, 0, 0, 0, $_GET ["proxy"], $pauth );
	//file_put_contents("depositfiles_1.txt", $page);
	is_page ( $page );
	is_present ( $page, "Such file does not exist or it has been removed for infringement of copyrights." );
	is_present ( $page, "Your IP is already downloading a file from our system." );
	
	if (stristr ( $page, 'You used up your limit for file downloading!' )) {
		preg_match ( '/([0-9]+) minute\(s\)/', $page, $minutes );
		html_error ( "Download limit exceeded. Try again in " . trim ( $minutes [1] ) . " minute(s)", 0 );
	}
	
	if (preg_match ( '/<input type="submit" class="button" value="FREE downloading"/', $page )) {
		$post = Array ();
		$post ["gateway_result"] = 1;
		$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), $LINK, 0, $post, 0, $_GET ["proxy"], $pauth );
		//file_put_contents("depositfiles_2.txt", $page);
		is_page ( $page );
	}
	
	preg_match ( '/<span id="download_waiter_remain">(.*)<\/span>/', $page, $countDown );
	$countDown = ( int ) $countDown [1];
	
	insert_timer ( $countDown, "The file is being prepared.", "", true );
	
	if (preg_match ( '/<form action="(.*)" method="get" onSubmit="download_started()/U', $page, $dlink )) {
		$Url = parse_url ( trim ( $dlink [1] ) );
		$FileName = basename ( $Url ["path"] );
	} else {
		html_error ( "Error getting download link", 0 );
	}
	
	insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] . "&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $LINK ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . ($pauth ? "&pauth=$pauth" : "") . (isset ( $_GET ["audl"] ) ? "&audl=doum" : "") );
}
?>