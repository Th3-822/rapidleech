<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}

class d4shared_com extends DownloadClass {
	public function Download($link) {
		global $Referer, $pauth;
		$page = $this->GetPage($link);
		$cook = "";
		preg_match_all ( '/Set-Cookie: ([^;]+)/', $page, $cook );
		$cookie = implode ( '; ', $cook [1] );
		$newredir = ""; $count = "";
		if (preg_match ( '/Location: (.*)/', $page, $newredir )) {
			$Url = parse_url ( $newredir [1] );
			$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"], $Referer, $cookie, 0, 0, $_GET ["proxy"], $pauth );
			is_page ( $page );
			//}elseif(preg_match('/href="(.*html)".*Download Now/', $page, $newredir))
		} elseif (preg_match ( '/href="([^"]+)".*Download Now/', $page, $newredir )) {
			$redir = 'http://' . $Url ["host"] . $newredir [1];
			//$Url = parse_url($redir);
			$Url = parse_url ( $newredir [1] );
			$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"], $Referer, $cookie, 0, 0, $_GET ["proxy"], $pauth );
			is_page ( $page );
		}
		if (preg_match ( '/var c = ([0-9]+);/', $page, $count )) {
			$countDown = $count [1];
			$this->CountDown($countDown);
		}
		if (preg_match ( '%window\.location = "(http://.+?)";%', $page, $redir )) {
			$link = $redir [1];
		} elseif (preg_match ( '/(http.*?)\'.*Click here to download/', $page, $redir )) {
			$link = $redir [1];
		} else {
			html_error ( "Download-link not found.", 0 );
		}
		$Url = parse_url ( $link );
		$FileName =basename ( $Url ["path"] );
		$this->RedirectDownload($link, $FileName);	
	}
}
?>