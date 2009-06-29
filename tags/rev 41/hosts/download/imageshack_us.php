<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}
if ($_GET ["imageshack_tor"] == "on" && $_GET ["tor_user"] && $_GET ["tor_pass"] || $_GET ["imageshack_tor"] == "on" && $imageshack_acc ['user'] && $imageshack_acc ['pass']) {
	$mainlink = $LINK;
	$auth_link = 'http://' . $Url ["host"] . '/auth.php';
	
	$Url = parse_url ( $auth_link );
	
	$post = array ();
	$post ["username"] = $_GET ["tor_user"] ? $_GET ["tor_user"] : $imageshack_acc ['user'];
	$post ["password"] = $_GET ["tor_pass"] ? $_GET ["tor_pass"] : $imageshack_acc ['pass'];
	
	$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), $Referer, 0, $post, 0, $_GET ["proxy"], $pauth );
	is_page ( $page );
	
	if (preg_match ( '/fail/i', $page )) {
		html_error ( 'Incorrect un/pass', 0 );
	}
	
	if (preg_match_all ( '/Set-Cookie: *(.+);/', $page, $cook )) {
		$cookie = implode ( ';', $cook [1] );
	} else {
		html_error ( 'Cookie not found.', 0 );
	}
	preg_match_all ( '/Set-Cookie: (.*);/U', $page, $temp );
	$cookie = $temp [1];
	$cookie = implode ( ';', $cookie );
	if (preg_match ( '/\?action=zip/', $mainlink )) {
		$Url = parse_url ( $mainlink );
		$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), $Referer, $cookie, 0, 0, $_GET ["proxy"], $pauth );
		
		preg_match ( '/Location:.*(\?id.*)/', $page, $final );
		$mainlink = 'http://' . $Url ["host"] . $Url ["path"] . $final [1];
		//die($mainlink);
	}
	
	$Url = parse_url ( $mainlink );
	$Referer = substr ( $mainlink, 0, - strlen ( basename ( $Url ['path'] ) ) );
	$Url = parse_url ( $Referer );
	$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), $Referer, $cookie, 0, 0, $_GET ["proxy"], $pauth );
	preg_match_all ( '/Set-Cookie: (.*);/U', $page, $temp );
	$cook = $temp [1];
	$cookie .= ';' . implode ( ';', $cook );
	
	$mainlink = $Referer . '/' . urlencode ( basename ( $mainlink ) );
	$Url = parse_url ( $mainlink );
	
	$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
	
	insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] . "&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&cookie=" . urlencode ( $cookie ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . ($pauth ? "&pauth=$pauth" : "") . (isset ( $_GET ["audl"] ) ? "&audl=doum" : "") );

} else {
	html_error ( 'Use imageshack account', 0 );
}
?>