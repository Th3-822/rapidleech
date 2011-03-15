<?php


####### Account Info. ###########
$filesonic_login = "";                   //Set your filesonic.com user
$filesonic_pass = "";                    //Set your filesonic.com password
##############################

/**
 * FileSonic Logged-In User Upload
 *
 * This class is using the FileSonic API to process uploads.
 *
 * @author MathieuB (Filesonic Developer)
 * @version 1.0
 *
 */

$not_done=true;
$continue_up=false;


if ($filesonic_login && $filesonic_pass){
    $_REQUEST['my_login'] = $filesonic_login;
    $_REQUEST['my_pass'] = $filesonic_pass;
    $_REQUEST['action'] = "FORM";
    echo "<b><center>Use Default login/pass.</center></b>\n";
} else if (isset($premium_acc) && isset($premium_acc["filesonic_com"]['user']) && $premium_acc["filesonic_com"]['user'] != '' && $premium_acc["filesonic_com"]['pass'] != '') {
    $_REQUEST['my_login'] = $premium_acc["filesonic_com"]['user'];
    $_REQUEST['my_pass'] = $premium_acc["filesonic_com"]['pass'];
    $_REQUEST['action'] = "FORM";
    echo "<b><center>Use Default login/pass.</center></b>\n";
}

class FileSonic {
	var $_username = null;
	var $_password = null;
	var $_uploadUrl = null;
	var $_uploadMaxFilesize = null;

	function __construct($username = null, $password = null) {
        if (!is_null($username)) $this->_username = $username;
        if (!is_null($password)) $this->_password = $password;
	}

	function getUploadUrl() {
		try {
	        $page = geturl("api.filesonic.com", 80, '/upload?method=getUploadUrl&format=json&u=' . $this->_username . '&p=' . $this->_password, 0, 0, 0, 0, $_GET["proxy"], $pauth);
	        $response = explode("\n", $page);
	        $body = '';
	        foreach($response AS $content) {
	        	$content = trim($content);
	        	if (isset($isBody) && $isBody <=1 ) { $isBody++; }
	        	if ($content == '') {
	        		$isBody = 0;
	        	}
	        	if ($content == '0') {
	        		break;
	        	}
	        	if ($isBody == 2) $body = $content;
	        }

		    $apiResponse = json_decode($body, true);
		    if (!$apiResponse || !isset($apiResponse['FSApi_Upload']) || !isset($apiResponse['FSApi_Upload']['getUploadUrl']) || !isset($apiResponse['FSApi_Upload']['getUploadUrl']['status'])) {
		        throw new Exception("Unable to get upload server, unknow API response. Debugging: " . $page);
		    }

		    if ($apiResponse['FSApi_Upload']['getUploadUrl']['status'] == 'failed') {
		    	$msg = '';
		    	foreach($apiResponse['FSApi_Upload']['getUploadUrl']['errors'] AS $type => $errors) {
		    		switch($type) {
		    			case 'FSApi_Auth_Exception':
                            $msg .= "Please verify your Username and Password\n";
		    				break;
		    			default:
                        $msg .= $errors . "\n";
		    		}
		    	}
	            throw new Exception("Failed to get upload server with message: " . $msg);
		    }

		    $this->_uploadUrl = $apiResponse['FSApi_Upload']['getUploadUrl']['response']['url'];
		    $this->_uploadMaxFilesize = $apiResponse['FSApi_Upload']['getUploadUrl']['response']['url'];

            return $this->_uploadUrl;
		} catch (Exception $e) {
			echo '<pre>' . $e->getMessage() . '</pre>';
			exit;
		}
	}

	function uploadFile($lfile, $lname) {
        try {
	        $uploadUrl = $this->getUploadUrl();
	        $url = parse_url($uploadUrl);

	        $page = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://www.filesonic.com/', false, array(), $lfile, $lname, "files[]", null, null, null, "RapidLeech");

            $response = explode("\n", $page);
            $body = '';
            $isBody = false;
            foreach($response AS $content) {
                $content = trim($content);
                if ($content == '') {
                    $isBody = true;
                }
                if ($isBody == true) $body .= $content;
            }

            $apiResponse = json_decode($body, true);

            if (!$apiResponse || !isset($apiResponse['FSApi_Upload']) || !isset($apiResponse['FSApi_Upload']['postFile']) || !isset($apiResponse['FSApi_Upload']['postFile']['status'])) {
                throw new Exception("Unable to get upload server, unknow API response. Debugging: " . $page);
            }

            if ($apiResponse['FSApi_Upload']['postFile']['status'] == 'failed') {
                $msg = '';
                foreach($apiResponse['FSApi_Upload']['postFile']['errors'] AS $type => $errors) {
                    switch($type) {
                        case 'FSApi_Upload_Exception':
                            $msg .= "Upload Link expired\n";
                            break;
                        default:
                        $msg .= $errors . "\n";
                    }
                }
                throw new Exception("Failed to get upload server with message: " . $msg);
            }

            return $apiResponse['FSApi_Upload']['postFile']['response']['files'];
        } catch (Exception $e) {
            echo '<pre>' . $e->getMessage() . '</pre>';
            exit;
        }
	}
}

if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;Email*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo __FILE__; ?></b></small></tr>
</table>
</form>

<?php
    exit;
	}

if ($continue_up)
	{
		$not_done=false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Upload Process</div>
<script>document.getElementById('info').style.display='none';</script>
<?php
    $filesonic = new FileSonic($_REQUEST['my_login'], $_REQUEST['my_pass']);
    $files = $filesonic->uploadFile($lfile, $lname);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
	    foreach($files AS $file) {
	    	$download_link = $file['url'];
	    }

	}
?>