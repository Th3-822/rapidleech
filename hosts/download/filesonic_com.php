<?php

/**
 * FileSonic Premium Download
 *
 * This class is using the FileSonic API to process downloads.
 *
 * FileSonic only support Premium user to download with third party applications.
 *
 * @author MathieuB (Filesonic Developer)
 * @version 1.0
 *
 */

if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit;
}

class filesonic_com extends DownloadClass {

	public function Download($link) {
		global $premium_acc;

		$user = $pass = '';
		if ($_REQUEST['iuser'] != '' && $_REQUEST['ipass'] != '') {
			$user = $_REQUEST['iuser'];
			$pass = $_REQUEST['ipass'];
		} else if ($_REQUEST['premium_user'] != '' && $_REQUEST['premium_pass'] != '') {
			$user = $_REQUEST['premium_user'];
			$pass = $_REQUEST['premium_pass'];
		} else if ($premium_acc["filesonic_com"]['user'] && $premium_acc["filesonic_com"]['pass']) {
			$user = $premium_acc["filesonic_com"]['user'];
			$pass = $premium_acc["filesonic_com"]['pass'];
		} else {
			html_error('This download plugin only support Premium user.');
		}

        try {
        	$regex = '|/file/(([a-z][0-9]+/)?[0-9]+)(/.*)?$|';
        	$matches = array();
		    preg_match($regex, $link, $matches);

		    if (!isset($matches[1])) {
                throw new Exception("Invalid FileSonic Link");
		    }
		    $id = str_replace('/', '-', $matches[1]);

		    $post = array(
		      'u' => $user,
		      'p' => $pass,
		      'ids' => $id
		    );

			$page = $this->GetPage("http://api.filesonic.com/link?method=getDownloadLink", 0, $post);
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
            if (!$apiResponse || !isset($apiResponse['FSApi_Link']) || !isset($apiResponse['FSApi_Link']['getDownloadLink']) || !isset($apiResponse['FSApi_Link']['getDownloadLink']['status'])) {
                throw new Exception("Unable to get download link, unknow API response. Debugging: " . $page);
            }

            if ($apiResponse['FSApi_Link']['getDownloadLink']['status'] == 'failed') {
                $msg = '';
                foreach($apiResponse['FSApi_Link']['getDownloadLink']['errors'] AS $type => $errors) {
                    switch($type) {
                        case 'FSApi_Auth_Exception':
                            $msg .= $errors . ' (user: ' . $user . ')' . "\n";
                            break;
                        default:
                        $msg .= $errors . "\n";
                    }
                }
                throw new Exception("Failed to get download link with message: " . $msg);
            }

            $download = $apiResponse['FSApi_Link']['getDownloadLink']['response']['links'][$id];
            if ($download['status'] == 'NOT_AVAILABLE') {
            	throw new Exception("This file was deleted");
            }
            if ($download['status'] != 'AVAILABLE') {
            	throw new Exception("Unable to download this file: " . $download['status']);
            }
			$this->RedirectDownload($download['url'], $download['filename']);
        } catch (Exception $e) {
        	html_error ($e->getMessage());
        }
	}
}

/* Edited by Th3-822:
Changed geturl() & insert_location() for GetPage() & RedirectDownload(). (Using RedirectDownload should be a easy fix for the audl problem.) 
Little edit in login's if() & Added a error msj for login isn't found. ('Use premium account' or audl login won't work if it's a FSC login defined in accounts.)
Added edit posted by casolari @ 26-6-2011
*/
 ?>