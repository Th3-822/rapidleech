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
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit;
}

class filesonic_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;

        $user = $pass = $lpass = '';
        if (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) {
            $user = $_REQUEST['premium_user'];
            $pass = $_REQUEST['premium_pass'];
        } else if (!empty($premium_acc["filesonic_com"]["user"]) && !empty($premium_acc["filesonic_com"]["pass"])) {
            $user = $premium_acc["filesonic_com"]['user'];
            $pass = $premium_acc["filesonic_com"]['pass'];
        } else
            html_error('FSC: This download plugin only support Premium user.');

        try {
            $regex = '@file/((?:(?:[a-z]\d+/)?\d+)|(?:\w+))(/.*)?$@';
            $matches = array();
            preg_match($regex, $link, $matches);

            //Get password
            $arr = explode("|", $link, 2);
            if (count($arr) >= 2) {
                $link = $arr[0];
                $lpass = $arr[1];
            }

            if (!isset($matches[1])) {
                throw new Exception("Invalid FileSonic Link");
            }
            $id = str_replace('/', '-', $matches[1]);

            $post = array(
                'u' => $user,
                'p' => $pass,
                'ids' => $id,
                "passwords[$id]" => urlencode($lpass)
            );

            $page = $this->GetPage("http://api.filesonic.com/link?method=getDownloadLink", 0, $post);
            if (stristr($page, 'We have detected some suspicious behaviour') || stristr($page, 'blocked.filesonic.com/')) throw new Exception("FSC has blocked your IP.");

            $body = substr($page, strpos($page, "\r\n\r\n") + 4);
            $body = substr($body, strpos($body, "{"));
            $body = substr($body, 0, strrpos($body, "}") + 1);

            $apiResponse = json_decode($body, true);
            if (!$apiResponse || !isset($apiResponse['FSApi_Link']) || !isset($apiResponse['FSApi_Link']['getDownloadLink']) || !isset($apiResponse['FSApi_Link']['getDownloadLink']['status'])) {
                throw new Exception("Unable to get download link, unknow API response. Debugging: " . $page);
            }

            if ($apiResponse['FSApi_Link']['getDownloadLink']['status'] == 'failed') {
                $msg = '';
                foreach ($apiResponse['FSApi_Link']['getDownloadLink']['errors'] AS $type => $errors) {
                    switch ($type) {
                        case 'FSApi_Auth_Exception':
                            $msg .= $errors . ' (user: ' . $user . ')' . "\n";
                            break;
                        default:
                            $msg .= $errors . "\n";
                    }
                }
                throw new Exception("Failed to get download link with message: " . $msg);
            }

            $download = $apiResponse['FSApi_Link']['getDownloadLink']['response']['links'];
            $rid = array_keys($download);
            $download = $download[$rid[0]];

            if ($download['status'] == 'PASSWORD_REQUIRED') {
                if (empty($lpass)) throw new Exception("FSC: Password protected link. Please input link with password: Link|Password.");
                array_pop($post);
                $post["passwords[{$rid[0]}]"] = $lpass;

                $page = $this->GetPage("http://api.filesonic.com/link?method=getDownloadLink", 0, $post);
                if (stristr($page, 'We have detected some suspicious behaviour') || stristr($page, 'blocked.filesonic.com/')) throw new Exception("FSC has blocked your IP.");

                $body = substr($page, strpos($page, "\r\n\r\n") + 4);
                $body = substr($body, strpos($body, "{"));
                $body = substr($body, 0, strrpos($body, "}") + 1);
                $apiResponse = json_decode($body, true);
                $download = $apiResponse['FSApi_Link']['getDownloadLink']['response']['links'][$rid[0]];
            }

            if ($download['status'] == 'NOT_AVAILABLE') {
                throw new Exception("This file was deleted");
            }
            if ($download['status'] == 'WRONG_PASSWORD') {
                if (empty($lpass)) throw new Exception("FSC: Password protected link. Please input link with password: Link|Password.");
                else throw new Exception("FSC: Link password is incorrect. Please input link with password: Link|Password.");
            }
            if ($download['status'] != 'AVAILABLE') {
                throw new Exception("Unable to download this file: " . $download['status']);
            }
            $this->RedirectDownload($download['url'], $download['filename']);
        } catch (Exception $e) {
            html_error($e->getMessage());
        }
    }

}

/* Edited by Th3-822:
  Changed geturl() & insert_location() for GetPage() & RedirectDownload(). (Using RedirectDownload should be a easy fix for the audl problem.)
  Little edit in login's if() & Added a error msj for login isn't found. ('Use premium account' or audl login won't work if it's a FSC login defined in accounts.)
  Updated by vdhdevil for new links format 24-12-2011
  Fixed for new links support and link with pass support && Added a error msg when your ip gets banned @ 24-12-2011
 */
?>