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
	exit ();
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

            $download = $apiResponse['FSApi_Link']['getDownloadLink']['response']['links'][0];
            if ($download['status'] == 'NOT_AVAILABLE') {
            	throw new Exception("This file was deleted");
            }
            if ($download['status'] != 'AVAILABLE') {
            	throw new Exception("Unable to download this file: " . $download['status']);
            }

			$this->RedirectDownload($download['url'], $download['filename']);
			exit();







        } catch (Exception $e) {
        	html_error ($e->getMessage());
        }
	}
}

class filesonic_net extends filesonic_com {}
class sharingmatrix_com extends filesonic_com {}
class filesonic_jp extends filesonic_com {}
class filesonic_tw extends filesonic_com {}
class filesonic_it extends filesonic_com {}
class filesonic_in extends filesonic_com {}
class filesonic_kr extends filesonic_com {}
class filesonic_vn extends filesonic_com {}
class filesonic_hk extends filesonic_com {}
class filesonic_co_il extends filesonic_com {}
class filesonic_sg extends filesonic_com {}
class filesonic_pk extends filesonic_com {}
class filesonic_fr extends filesonic_com {}
class filesonic_at extends filesonic_com {}
class filesonic_be extends filesonic_com {}
class filesonic_bg extends filesonic_com {}
class filesonic_ch extends filesonic_com {}
class filesonic_cl extends filesonic_com {}
class filesonic_co_id extends filesonic_com {}
class filesonic_co_th extends filesonic_com {}
class filesonic_com_au extends filesonic_com {}
class filesonic_com_eg extends filesonic_com {}
class filesonic_com_hk extends filesonic_com {}
class filesonic_com_tr extends filesonic_com {}
class filesonic_com_vn extends filesonic_com {}
class filesonic_cz extends filesonic_com {}
class filesonic_es extends filesonic_com {}
class filesonic_fi extends filesonic_com {}
class filesonic_gr extends filesonic_com {}
class filesonic_hr extends filesonic_com {}
class filesonic_hu extends filesonic_com {}
class filesonic_mx extends filesonic_com {}
class filesonic_my extends filesonic_com {}
class filesonic_pe extends filesonic_com {}
class filesonic_pt extends filesonic_com {}
class filesonic_ro extends filesonic_com {}
class filesonic_rs extends filesonic_com {}
class filesonic_se extends filesonic_com {}
class filesonic_sk extends filesonic_com {}
class filesonic_ua extends filesonic_com {}
class filesonic_asia extends filesonic_com {}
class filesonic_cc extends filesonic_com {}
class filesonic_co_nz extends filesonic_com {}
class filesonic_me extends filesonic_com {}
class filesonic_nl extends filesonic_com {}
class filesonic_tv extends filesonic_com {}

/* Edited by Th3-822:
Changed geturl() & insert_location() for GetPage() & RedirectDownload(). (Using RedirectDownload should be a easy fix for the audl problem.) 
Little edit in login's if() & Added a error msj for login isn't found. ('Use premium account' or audl login won't work if it's a FSC login defined in accounts.)
*/
 ?>