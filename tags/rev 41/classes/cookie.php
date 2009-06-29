<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}

if ($_COOKIE ["clearsettings"]) {
	setcookie ( "domail", "", time () - 3600 );
	setcookie ( "email", "", time () - 3600 );
	setcookie ( "saveto", "", time () - 3600 );
	setcookie ( "path", "", time () - 3600 );
	setcookie ( "useproxy", "", time () - 3600 );
	setcookie ( "proxy", "", time () - 3600 );
	setcookie ( "proxyuser", "", time () - 3600 );
	setcookie ( "proxypass", "", time () - 3600 );
	setcookie ( "split", "", time () - 3600 );
	setcookie ( "partSize", "", time () - 3600 );
	setcookie ( "savesettings", "", time () - 3600 );
	setcookie ( "clearsettings", "", time () - 3600 );
	setcookie ( "premium_acc", "", time () - 3600 );
	setcookie ( "premium_user", "", time () - 3600 );
	setcookie ( "premium_pass", "", time () - 3600 );
}

if ($_REQUEST ["savesettings"] == "on") {
	setcookie ( "savesettings", TRUE, time () + 800600 );
	if ($_REQUEST ["domail"] == "on") {
		setcookie ( "domail", TRUE, time () + 800600 );
		if (checkmail ( $_REQUEST ["email"] )) {
			setcookie ( "email", $_REQUEST ["email"], time () + 800600 );
		} else {
			setcookie ( "email", "", time () - 3600 );
		}
		
		if ($_REQUEST ["split"] == "on") {
			setcookie ( "split", TRUE, time () + 800600 );
			if (is_numeric ( $_REQUEST ["partSize"] )) {
				setcookie ( "partSize", $_REQUEST ["partSize"], time () + 800600 );
			} else {
				setcookie ( "partSize", "", time () - 3600 );
			}
			if (in_array ( $_REQUEST ["method"], array ("tc", "rfc" ) )) {
				setcookie ( "method", $_REQUEST ["method"], time () + 800600 );
			} else {
				setcookie ( "method", "", time () - 3600 );
			}
		} else {
			setcookie ( "split", "", time () - 3600 );
		}
	} else {
		setcookie ( "domail", "", time () - 3600 );
	}
	
	if ($_REQUEST ["saveto"] == "on") {
		setcookie ( "saveto", TRUE, time () + 800600 );
		if (isset ( $_REQUEST ["path"] )) {
			setcookie ( "path", $_REQUEST ["path"], time () + 800600 );
		} else {
			setcookie ( "path", "", time () - 3600 );
		}
	} else {
		setcookie ( "saveto", "", time () - 3600 );
	}
	
	if ($_REQUEST ["useproxy"] == "on") {
		setcookie ( "useproxy", TRUE, time () + 800600 );
		if (strlen ( strstr ( $_REQUEST ["proxy"], ":" ) ) > 0) {
			setcookie ( "proxy", $_REQUEST ["proxy"], time () + 800600 );
		} else {
			setcookie ( "proxy", "", time () - 3600 );
		}
		
		if ($_REQUEST ["proxyuser"]) {
			setcookie ( "proxyuser", $_REQUEST ["proxyuser"], time () + 800600 );
		} else {
			setcookie ( "proxyuser", "", time () - 3600 );
		}
		
		if ($_REQUEST ["proxypass"]) {
			setcookie ( "proxypass", $_REQUEST ["proxypass"], time () + 800600 );
		} else {
			setcookie ( "proxypass", "", time () - 3600 );
		}
	} else {
		setcookie ( "useproxy", "", time () - 3600 );
	}
	
	if ($_REQUEST ["premium_acc"] == "on") {
		setcookie ( "premium_acc", $_REQUEST ["premium_acc"], time () + 800600 );
		if (isset ( $_REQUEST ["premium_user"] ) && isset ( $_REQUEST ["premium_pass"] )) {
			setcookie ( "premium_user", $_REQUEST ["premium_user"], time () + 800600 );
			setcookie ( "premium_pass", $_REQUEST ["premium_pass"], time () + 800600 );
		} else {
			setcookie ( "premium_user", "", time () - 3600 );
			setcookie ( "premium_pass", "", time () - 3600 );
		}
	} else {
		setcookie ( "premium_acc", "", time () - 3600 );
	}
}
?>