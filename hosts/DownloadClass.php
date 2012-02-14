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
     * @param string $link  The link of the page to retrieve
     * @param string $cookie  The cookie value if you need
     * @param array $post name=>value of the post data
     * @param string $referer The referer of the page, it might be the value you are missing if you can't get plugin to work
     * @param string $auth Page authentication, unneeded in most circumstances
     */

    public function GetPage($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0, $XMLRequest=0) {
        global $options;
        if (!$referer) {
            global $Referer;
            $referer = $Referer;
        }
        if ($options['use_curl']) {
            if (extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec')) $cURL = true;
            else $cURL = false;
        } else {
            $cURL = false;
        }            
        $Url = parse_url(trim($link));
        if ($Url ["scheme"] == 'https') {
            $chttps = false;
            if ($cURL) {
                $cV = curl_version();
                if (in_array('https', $cV['protocols'], true)) $chttps = true;
            }
            if (!extension_loaded('openssl') && !$chttps) html_error("This server doesn't support https connections.");
            elseif (!$chttps) $cURL = false;
        }

        if ($cURL) {
            if ($XMLRequest) $referer .= "\r\nX-Requested-With: XMLHttpRequest";
            $page = cURL($link, $cookie, $post, $referer, $auth);
        } else {
            global $pauth;
            $page = geturl($Url ["host"], !empty($Url ["port"]) ? $Url ["port"] : 80, $Url ["path"] . (!empty($Url ["query"]) ? "?" . $Url ["query"] : ""), $referer, $cookie, $post, 0, !empty($_GET ["proxy"]) ? $_GET ["proxy"] : '', $pauth, $auth, $Url ["scheme"], 0, $XMLRequest);
            is_page($page);
        }
        return $page;
    }

    /*
     * Use this function instead of insert_location so that we can improve this feature in the future
     * @param string $link   The download link of the file
     * @param string $FileName   The name of the file
     * @param string $cookie   The cookie value
     * @param array $post   The post value will be serialized here
     * @param string $referer   The page that refered to this link
     * @param string $auth   In format username:password
     * @param array $params   This parameter allows you to add extra _GET values to be passed on
     */

    public function RedirectDownload($link, $FileName, $cookie = 0, $post = 0, $referer = 0, $force_name = 0, $auth = "", $params = array()) {
        global $pauth;
        if (!$referer) {
            global $Referer;
            $referer = $Referer;
        }
        $Url = parse_url($link);
        //if (substr($auth,0,6) != "&auth=") $auth = "&auth=" . $auth;
        if (is_array($cookie)) {
            $cookie = CookiesToStr($cookie);
        }
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
                $addon .= '&' . $name . '=' . urlencode($value) . '&';
            }
            $addon = substr($addon, 0, -1);
        }
        $loc = "{$_SERVER['PHP_SELF']}?filename=" . urlencode($FileName) .
                "&host=" . $Url ["host"] . "&port=" . (isset($Url ["port"]) ? $Url ["port"] : '') . "&path=" .
                urlencode($Url ["path"] . (!empty($Url ["query"]) ? "?" . $Url ["query"] : "")) .
                "&referer=" . urlencode($referer) . "&email=" . (!empty($_GET ["domail"]) ? $_GET ["email"] : "") .
                "&partSize=" . (!empty($_GET ["split"]) ? $_GET ["partSize"] : "") . "&method=" . (!empty($_GET ["method"]) ? $_GET ["method"] : '') .
                (!empty($_GET ["proxy"]) ? "&useproxy=on&proxy=" . $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] .
                "&link=" . urlencode($link) . (isset($_GET ["add_comment"]) && $_GET ["add_comment"] == "on" && !empty($_GET ["comment"]) ? "&comment=" .
                urlencode($_GET ["comment"]) : "") . ($auth ? '&auth=' . ($auth == 1 ? 1 : urlencode($auth)) : "") . ($pauth ? "&pauth=$pauth" : "") .
                (!empty($_GET ["uploadlater"]) && !empty($_GET['uploadtohost']) ? "&uploadlater=" . $_GET["uploadlater"] . "&uploadtohost=" . $_GET['uploadtohost'] : "") .
                "&cookie=" . ($cookie ? urlencode(encrypt($cookie)) : "") .
                "&post=" . ($post ? urlencode(serialize($post)) : "") .
                (isset($_POST ['autoclose']) ? "&autoclose=1" : "") .
                (isset($_GET["audl"]) ? "&audl=doum" : "") . $addon;

        if ($force_name) {
            $loc = $loc . "&force_name=" . urlencode($force_name);
        }

        insert_location($loc);
    }

    /*
     * Use this function to move your multiples links array to auto downloader
     * @param array $link_array   Normal array containing all download links
     */

    public function moveToAutoDownloader($link_array) {
        global $nn, $options;
        if (count($link_array) == 0) {
            html_error('Error getting links from folder.');
        }

        if (!is_file("audl.php") || $options['auto_download_disable']) {
            html_error('audl.php not found or you have disable auto download feature!');
        }

        $links = "";
        foreach ($link_array as $key => $value) {
            $links .= $value . $nn;
        }

        echo "<form action='audl.php?GO=GO' method='post' >\n";
        echo "<input type='hidden' name='links' value='" . $links . "'>\n";
        $key_array = array("useproxy", "proxy", "proxyuser", "proxypass", "premium_acc", "premium_user", "premium_pass", "cookieuse", "cookie");
        foreach ($key_array as $v)
            if (isset($_GET [$v])) echo "<input type='hidden' name='" . $v . "' value='" . $_GET [$v] . "' >\n";
        echo "<script language='JavaScript'>void(document.forms[0].submit());</script>\n";
        echo "</form>\n";
        flush();
        exit();
    }

    public function CountDown($countDown) {
        insert_timer($countDown, "Waiting link timelock");
    }

    /*
     * Use this function to create Captcha display form
     * @param string $captchaImg   The link of the captcha image or downloaded captcha image on server
     * @param array $inputs   Key Value pairs for html form input elements ( these elements will be hidden form elements )
     * @param string $captchaSize   The size of captcha text box
     */

    public function EnterCaptcha($captchaImg, $inputs, $captchaSize = '5') {
        echo "\n";
        echo('<form name="dl" action="' . $_SERVER['PHP_SELF'] . '" method="post">');
        echo "\n";

        foreach ($inputs as $name => $input) {
            echo('<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $input . '" />');
            echo "\n";
        }

        echo('<h4>' . lang(301) . ' <img src="' . $captchaImg . '" /> ' . lang(302) . ': <input type="text" name="captcha" size="' . $captchaSize . '" />&nbsp;&nbsp;');
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
        if (is_array($cookie)) {
            $cookie = CookiesToStr($cookie);
        }

        $DParam = array();
        if ($link) $DParam['link'] = urlencode($link);
        if ($cookie) $DParam['cookie'] = urlencode($cookie);
        if ($referer) $DParam['referer'] = urlencode($referer);
        if (isset($_GET ["useproxy"]) && $_GET ["useproxy"] == 'on' && !empty($_GET ["proxy"])) {
            global $pauth;
            $DParam["useproxy"] = 'on';
            $DParam["proxy"] = $_GET ["proxy"];
            if ($pauth) $DParam["pauth"] = $pauth;
        }
        if (isset($_GET["autoclose"])) $DParam["autoclose"] = 1;
        if (isset($_GET["audl"])) $DParam["audl"] = "doum";
        $params = array("add_comment", "domail", "comment", "email", "split", "partSize", "method", "uploadlater", "uploadtohost");
        foreach ($params as $key) if (!empty($_GET [$key])) $DParam[$key] = $_GET [$key];
        return $DParam;
    }

    /* Use this function for filehost longer timelock
     * Param int $secs   The number of seconds to count down
     * Param array $post   Array variable (name=>value)to include as POST so you dont need to start over the process
     * Param $string $text   Default text you want to display when counting down
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
        if ($stop)
            exit("</body></html>");
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