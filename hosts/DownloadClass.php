<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
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
		echo('<div id="mesg" width="100%" align="center">'.lang(300).'</div>');
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
	
	/**
	 * Use this function instead of insert_location so that we can improve this feature in the future
	 * 
	 * @param string $link The download link of the file
	 * @param string $FileName The name of the file
	 * @param string $cookie The cookie value
	 * @param array $post The post value will be serialized here
	 * @param string $referer The page that refered to this link
	 * @param string $auth In format username:password
	 * @param array $params This parameter allows you to add extra _GET values to be passed on
	 */
	public function RedirectDownload($link, $FileName, $cookie = 0, $post = 0, $referer = 0, $force_name = 0, $auth = "", $params = array() ) {
		global $pauth;
		if (!$referer) {
			global $Referer;
			$referer = $Referer;
		}
		$Url = parse_url($link);
		//if (substr($auth,0,6) != "&auth=") $auth = "&auth=" . $auth;
		if (!is_array($params)) {
			// Some problems with the plugin, quit it
			html_error('Plugin problem! Please report, error: "The parameter passed must be an array"');
		}
		$addon = "";
		if (count((array) $params) > 0) {
			foreach ($params as $name => $value) {
				if (is_array($value)) {
					$value = serialize($value);
				}
				$addon .= '&'.$name.'='.urlencode($value).'&';
			}
			$addon = substr($addon,0,-1);
		}
		$loc = "{$_SERVER['PHP_SELF']}?filename=" . urlencode ( $FileName ) . 
			"&host=" . $Url ["host"] . "&port=" . $Url ["port"] . "&path=" . 
			urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . 
			"&referer=" . urlencode ( $referer ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . 
			"&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . 
			"&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . 
			"&link=" . urlencode ( $link ) . ($_GET ["add_comment"] == "on" ? "&comment=" . 
			urlencode ( $_GET ["comment"] ) : "") . ($auth == 1 ? '&auth=1' : '&auth=' . urlencode($auth)) . ($pauth ? "&pauth=$pauth" : "") .
			($_GET ["uploadlater"] ? "&uploadlater=".$_GET["uploadlater"]."&uploadtohost=".$_GET['uploadtohost'] : "") .
			"&cookie=" . ($cookie ? encrypt(urlencode($cookie)) : 0) .
			"&post=" . urlencode ( serialize ( $post ) ) .
			($_POST ["uploadlater"] ? "&uploadlater=".$_POST["uploadlater"]."&uploadtohost=".urlencode($_POST['uploadtohost']) : "").
			($_POST ['autoclose'] ? "&autoclose=1" : "").
			(isset($_GET["audl"]) ? "&audl=doum" : "") . $addon;
			
		if ( $force_name )
		{
			$loc = $loc . "&force_name=" . urlencode ( $force_name );
		}
			
		insert_location ( $loc );
	}
	
	public function CountDown($countDown) {
		insert_timer ( $countDown, "Waiting link timelock" );
	}
	
	/**
	 * Use this function to create Captcha display form
	 * 
	 * @param string $captchaImg                    The link of the captcha image or downloaded captcha image on server
	 * @param array $inputs                             Key Value pairs for html form input elements ( these elements will be hidden form elements )
	 * @param string $captchaSize                   The size of captcha text box
	 */
	public function EnterCaptcha( $captchaImg, $inputs, $captchaSize = '5' ) 
	{
		$defaultParam_array = array();
		$defaultParam_array["comment"] = $_GET ["comment"];
		$defaultParam_array["email"] = $_GET ["email"];
		$defaultParam_array["partSize"] = $_GET ["partSize"];
		$defaultParam_array["method"] = $_GET ["method"];
		$defaultParam_array["proxy"] = $_GET ["proxy"];
		$defaultParam_array["proxyuser"] = $_GET ["proxyuser"];
		$defaultParam_array["proxypass"] = $_GET ["proxypass"];
		$defaultParam_array["path"] = $_GET ["path"];			
		
		$this->EnterCaptchaDefault( $captchaImg, $inputs, $captchaSize, $defaultParam_array );
	}
	
	/**
	 * Use this function to create Captcha display form
	 * 
	 * @param string $captchaImg                    The link of the captcha image or downloaded captcha image on server
	 * @param array $inputs                             Key Value pairs for html form input elements ( these elements will be hidden form elements )
	 * @param string $captchaSize                   The size of captcha text box
	 * @param array $defaultParam_array         Default Key Value pairs like proxy, method, email etc
	 */
	public function EnterCaptchaDefault( $captchaImg, $inputs, $captchaSize = '5', $defaultParam_array = array() ) {
		echo "\n";
		echo('<form name="dl" action="'.$_SERVER['PHP_SELF'].'" method="post">');
		echo "\n";
		
		foreach ( $inputs as $name => $input ) 
		{
			echo('<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.$input.'" />');
			echo "\n";
		}
		
		if ( !empty( $defaultParam_array ) )
		{
			foreach ( $defaultParam_array as $name => $input ) 
			{
				echo('<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.$input.'" />');
				echo "\n";
			}	
		}
		
		echo('<h4>'.lang(301).' <img src="'.$captchaImg.'" /> '.lang(302).': <input type="text" name="captcha" size="' . $captchaSize . '" />&nbsp;&nbsp;');
		echo "\n";
		echo( '<input type="submit" onclick="return check();" value="Enter Captcha" /></h4>');
		echo "\n";
		echo('<script type="text/javascript">');
		echo "\n";
		echo('function check() {');
		echo "\n";
		echo('var captcha=document.dl.captcha.value;');
		echo "\n";
		echo('if (captcha == "") { window.alert("You didn\'t enter the image verification code"); return false; }');
		echo "\n";
		echo('else { return true; }');
		echo "\n";
		echo('}');
		echo "\n";
		echo('</script>');
		echo "\n";
		echo('</form>');
		echo "\n";
		echo('</body>');
		echo "\n";
		echo('</html>');
	}

	public function changeMesg($mesg) {
		echo('<script>document.getElementById(\'mesg\').innerHTML=\''.stripslashes($mesg).'\';</script>');
	}
}

/**********************************************************	
Added support of force_name in RedirectDownload function by Raj Malhotra on 02 May 2010
Fixed  EnterCaptcha function ( Re-Write )  by Raj Malhotra on 16 May 2010
Added auto-encryption system (szal) 14 June 2010
**********************************************************/
?>