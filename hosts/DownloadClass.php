<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
}

class DownloadClass {
	/*
	 * Prints the initial form for displaying messages
	 * @return void
	 */

	public function __construct() {
		echo('<table width="600" align="center">');
		echo('<tr>');
		echo('<td align="center">');
		echo('<div id="mesg" width="100%" align="center">' . lang(300) . '</div>');
	}

	/*
	 * You can use this function to retrieve pages without parsing the link
	 * @param string $link -> The link of the page to retrieve
	 * @param string $cookie -> The cookie value if you need
	 * @param array $post -> Array name=>value of the post data
	 * @param string $referer -> The referer of the page, it might be the value you are missing if you can't get plugin to work
	 * @param string $auth -> Page authentication, unneeded in most circumstances
	 */

	public function GetPage($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0, $XMLRequest = 0) {
		global $options;
		if (!$referer) {
			global $Referer;
			$referer = $Referer;
		}
		$cURL = ($options['use_curl'] && extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec')) ? true : false;
		$Url = parse_url(trim($link));
		if ($Url ['scheme'] == 'https') {
			$chttps = false;
			if ($cURL) {
				$cV = curl_version();
				if (in_array('https', $cV['protocols'], true)) $chttps = true;
			}
			if (!extension_loaded('openssl') && !$chttps) html_error('This server doesn\'t support https connections.');
			elseif (!$chttps) $cURL = false;
		}

		if ($cURL) {
			if ($XMLRequest) $referer .= "\r\nX-Requested-With: XMLHttpRequest";
			$page = cURL($link, $cookie, $post, $referer, $auth);
		} else {
			global $pauth;
			$page = geturl($Url ['host'], !empty($Url ['port']) ? $Url ['port'] : 80, $Url ['path'] . (!empty($Url ['query']) ? '?' . $Url ['query'] : ''), $referer, $cookie, $post, 0, !empty($_GET ['proxy']) ? $_GET ['proxy'] : '', $pauth, $auth, $Url ['scheme'], 0, $XMLRequest);
			is_page($page);
		}
		return $page;
	}

	/*
	 * Use this function instead of insert_location so that we can improve this feature in the future
	 * @param string $link -> The download link of the file
	 * @param string $FileName -> The name of the file
	 * @param string $cookie -> The cookie value
	 * @param array $post -> The post value will be serialized here
	 * @param string $referer -> The page that refered to this link
	 * @param string $auth -> In format username:password
	 * @param array $params -> This parameter allows you to add extra _GET values to be passed on
	 */

	public function RedirectDownload($link, $FileName, $cookie = 0, $post = 0, $referer = 0, $force_name = 0, $auth = 0, $addon = array()) {
		global $pauth;
		if (!$referer) {
			global $Referer;
			$referer = $Referer;
		}
		$url = parse_url($link);
		$params = $this->DefaultParamArr($link, (!empty($cookie) ? (is_array($cookie) ? encrypt(CookiesToStr($cookie)) : encrypt($cookie)) : 0), $referer);
		$params['filename'] = urlencode($FileName);
		if (!empty($force_name)) $params['force_name'] = urlencode($force_name);
		$params['host'] = urlencode($url['host']);
		if (!empty($url['port'])) $params['port'] = urlencode($url['port']);
		$params['path'] = urlencode($url['path'] . (!empty($url['query']) ? '?' . $url['query'] : ''));
		if (!empty($post)) $params['post'] = encrypt(serialize($post));
		if (!empty($auth)) $params['auth'] = ($auth == 1 ? 1 : urlencode($auth));
		if (!empty($addon)) {
			if (!is_array($addon)) html_error('Plugin problem! Please report, error: "The parameter passed must be an array"'); // Some problems with the plugin, quit it
			foreach ($addon as $name => $value) $params[$name] = (is_array($value) ? urlencode(serialize($value)) : urlencode($value));
		}
		insert_location($params);
	}

	/*
	 * Use this function to move your multiples links array to auto downloader
	 * @param array $link_array -> Normal array containing all download links
	 */

	public function moveToAutoDownloader($link_array) {
		global $PHP_SELF, $options;
		if (empty($link_array) || !is_array($link_array) || count($link_array) == 0) html_error('Error getting links from folder.');

		if (!is_file('audl.php') || !empty($options['auto_download_disable'])) html_error('audl.php not found or you have disable auto download feature!');

		$pos = strrpos($PHP_SELF, '/');
		$audlpath = ($pos !== false) ? substr($PHP_SELF, 0, $pos + 1).'audl.php?GO=GO' : 'audl.php?GO=GO';
		$inputs = GetDefaultParams();
		$inputs['links'] = implode("\r\n", $link_array);

		$key_array = array('premium_acc', 'premium_user', 'premium_pass', 'cookieuse', 'cookie');
		foreach ($key_array as $v) if (!empty($_GET[$v])) $inputs[$v] = urlencode($_GET[$v]);
		insert_location($inputs, $audlpath);
		exit();
	}

	public function CountDown($countDown) {
		insert_timer($countDown, "Waiting link timelock");
	}

	/*
	 * Use this function to create Captcha display form
	 * @param string $captchaImg -> The link of the captcha image or downloaded captcha image on server
	 * @param array $inputs -> Key Value pairs for html form input elements ( these elements will be hidden form elements )
	 * @param string $captchaSize -> The size of captcha text box
	 */

	public function EnterCaptcha($captchaImg, $inputs, $captchaSize = '5') {
		echo "\n<form name='dl' action='{$_SERVER['SCRIPT_NAME']}' method='POST'>\n";
		foreach ($inputs as $name => $input) echo "\t<input type='hidden' name='$name' id='$name' value='$input' />\n";
		echo "\t<h4>" . lang(301) . " <img alt='CAPTCHA Image' src='$captchaImg' /> " . lang(302) . ": <input type='text' name='captcha' size='$captchaSize' />&nbsp;&nbsp;\n\t\t<input type='submit' onclick='return check();' value='Enter Captcha' />\n\t</h4>\n";
		echo "<script type='text/javascript'>\n\tfunction check() {\n\t\tvar captcha=document.dl.captcha.value;\n\t\tif (captcha == '') {\n\t\t\twindow.alert('You didn\'t enter the image verification code');\n\t\t\treturn false;\n\t\t} else return true;\n\t}\n</script>";
		echo "</form>\n</body\n</html>";
	}

	/*
	 * This function will return a array with the Default Key Value pairs including proxy, method, email, etc.
	 * @param string $link -> Adds the link value to the array url encoded if you need it.
	 * @param string $cookie -> Adds the cookie value to the array url encoded if you need it.
	 * @param string $referer -> Adds the referer value to the array url encoded if you need it. If isn't set, it will load $Referer value. (Set as 0 or false for don't add it in the array.)
	 */

	public function DefaultParamArr($link = 0, $cookie = 0, $referer = 1) {
		if ($referer == 1) {
			global $Referer;
			$referer = $Referer;
		}
		if (is_array($cookie)) $cookie = CookiesToStr($cookie);

		$DParam = GetDefaultParams();
		if ($link) $DParam['link'] = urlencode($link);
		if ($cookie) $DParam['cookie'] = urlencode($cookie);
		if ($referer) $DParam['referer'] = urlencode($referer);
		return $DParam;
	}

	/* Use this function for filehost longer timelock
	 * Param int $secs -> The number of seconds to count down
	 * Param array $post -> Variable array to include as POST so you dont need to start over the process
	 * Param $string $text -> Default text you want to display when counting down
	 */

	public function JSCountdown($secs, $post = 0, $text='Waiting link timelock', $stop = 1) {
		global $PHP_SELF;
		echo "<p><center><span id='dl' class='htmlerror'><b>ERROR: Please enable JavaScript. (Countdown)</b></span><br /><span id='dl2'>Please wait</span></center></p>\n";
		echo "<form action='$PHP_SELF' name='cdwait' method='POST'>\n";
		if ($post) {
			foreach ($post as $name => $input) {
				echo "<input type='hidden' name='$name' id='$name' value='$input' />\n";
			}
		}
		?> <script type="text/javascript">
		var c = <?php echo $secs; ?>;var text = "<?php echo $text; ?>";var c2 = 0;var dl = document.getElementById("dl");var a2 = document.getElementById("dl2");fc();fc2();
		function fc() {
			if (c > 0) {
				if (c > 120) {
					dl.innerHTML = text+". Please wait <b>"+ Math.round(c/60) +"</b> minutes...";
				} else {
					dl.innerHTML = text+". Please wait <b>"+c+"</b> seconds...";
				}
				c = c - 1;
				setTimeout("fc()", 1000);
			} else {
				dl.style.display="none";
				void(<?php if ($post) echo 'document.forms.cdwait.submit()';else echo 'location.reload()'; ?>);
			}
		}
		function fc2(){if(c>120){if(c2<=20){a2.innerHTML=a2.innerHTML+".";c2=c2+1}else{c2=10;a2.innerHTML=""}setTimeout("fc2()",100)}else{dl2.style.display="none"}}<?php
		echo "</script></form><br />";
		if ($stop) exit("</body></html>");
	}

	public function changeMesg($mesg) {
		echo('<script>document.getElementById(\'mesg\').innerHTML=\'' . stripslashes($mesg) . '\';</script>');
	}

}

/**********************************************************
  Added support of force_name in RedirectDownload function by Raj Malhotra on 02 May 2010
  Fixed  EnterCaptcha function ( Re-Write )  by Raj Malhotra on 16 May 2010
  Added auto-encryption system (szal) 14 June 2010
  Added GetPage support function for https connection by Th3-822 21 April 2011
  Added GetPage support function for xml request by vdhdevil 9 July 2011
  Tweaked DefaultParamArr code by Th3-822 22 July 2011
  Moved JSCountdown function for future use by Th3-822
  Add CheckBack function to test correctly download link by vdhdevil
  Remove declaration of checkback function, it automatically signed in the plugin itself
  Add new limitation options by Ruud v.Tony
 **********************************************************/
?>