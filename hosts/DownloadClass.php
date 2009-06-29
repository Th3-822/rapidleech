<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}

class DownloadClass {
	/**
	 * Prints the initial form for displaying messages
	 * 
	 * @return void
	 */
	public function __construct() {
		echo('<table width="600" align="center">');
		echo('<tr>');
		echo('<td align="center">');
		echo('<div id="mesg" width="100%" align="center">Retrieving download page</div>');
	}
	
	/**
	 * You can use this function to retrieve pages without parsing the link
	 * 
	 * @param string $link The link of the page to retrieve
	 * @param string $cookie The cookie value if you need
	 * @param array $post name=>value of the post data
	 * @param string $referer The referer of the page, it might be the value you are missing if you can't get plugin to work
	 * @param string $auth Page authentication, unneeded in most circumstances
	 */
	public function GetPage($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0) {
		global $pauth;
		if (!$referer) {
			global $Referer;
			$referer = $Referer;
		}
		$Url = parse_url(trim($link));
		$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), $referer, $cookie, $post, 0, $_GET ["proxy"], $pauth, $auth );
		is_page ( $page );
		return $page;
	}
	
	public function RedirectDownload($link, $FileName, $cookie = 0, $post = 0, $referer = 0, $auth = "") {
		global $pauth;
		if (!$referer) {
			global $Referer;
			$referer = $Referer;
		}
		$Url = parse_url($link);
		if (substr($auth,0,6) != "&auth=") $auth = "&auth=" . $auth;
		$loc = "{$_SERVER['PHP_SELF']}?filename=" . urlencode ( $FileName ) . 
			"&host=" . $Url ["host"] . "&port=" . $Url ["port"] . "&path=" . 
			urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . 
			"&referer=" . urlencode ( $referer ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . 
			"&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . 
			"&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . 
			"&link=" . urlencode ( $link ) . ($_GET ["add_comment"] == "on" ? "&comment=" . 
			urlencode ( $_GET ["comment"] ) : "") . $auth . ($pauth ? "&pauth=$pauth" : "") . 
			($_GET ["uploadlater"] ? "&uploadlater=".$_GET["uploadlater"]."&uploadtohost=".$_GET['uploadtohost'] : "") .
			"&cookie=" . urlencode($cookie) .
			"&post=" . urlencode ( serialize ( $post ) ) .
			($_POST ["uploadlater"] ? "&uploadlater=".$_POST["uploadlater"]."&uploadtohost=".urlencode($_POST['uploadtohost']) : "").
			($_POST ['autoclose'] ? "&autoclose=1" : "").
			(isset($_GET["audl"]) ? "&audl=doum" : "");
		insert_location ( $loc );
	}
	
	public function CountDown($countDown) {
		insert_timer ( $countDown, "Waiting link timelock" );
	}
	
	public function EnterCaptcha($captchaImg, $inputs) {
		echo('<form name="dl" action="'.$_SERVER['PHP_SELF'].'" method="post">');
		foreach ($inputs as $name => $input) {
			echo('<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.$input.'" />');
		}
		echo('<h4>Enter <img src="'.$captchaImg.'" /> here: <input type="text" name="captcha" size="5" />&nbsp;&nbsp;<input type="submit" onclick="return check();" value="Transload File" /></h4>');
		echo('<script type="text/javascript">');
		echo('function check() {');
		echo('var captcha=document.dl.captcha.value;');
		echo('if (captcha == "") { window.alert("You didn\'t enter the image verification code"); return false; }');
		echo('else { return true; }');
		echo('}');
		echo('</script>');
		echo('</form>');
		echo('</body>');
		echo('</html>');
	}
	
	public function changeMesg($mesg) {
		echo('<script>document.getElementById(\'mesg\').innerHTML=\''.stripslashes($mesg).'\';</script>');
	}
}

?>