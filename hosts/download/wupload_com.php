<?php

/**
 * Wupload Download Plugin
 *
 * This class support premium user only.
 *
 * @author marco
 */
class wupload_com extends DownloadClass {
	
	/**
	 * Will be called automaticly when a download is called on wupload.
	 *
	 * @param string $link
	 * @throws Exception If something goes wrong
	 */
	public function Download($link) {
		global $premium_acc;

		if ($_REQUEST["premium_acc"] == "on" && !empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass'])) {
			$user = $_REQUEST['premium_user'];
			$pass = $_REQUEST['premium_pass'];
		} else if ($_REQUEST["premium_acc"] == "on" && !empty($premium_acc["wupload_com"]["user"]) && !empty($premium_acc["wupload_com"]["pass"])) {
			$user = $premium_acc["wupload_com"]['user'];
			$pass = $premium_acc["wupload_com"]['pass'];
		} else throw new Exception('WU: This download plugin only support Premium user.');

		// Display error in case of trouble
		try {
			//Get password
			$lpass = '';
			$arr = explode("|", $link, 2);
			if (count($arr)>=2) {
				$link = $arr[0];
				$lpass = $arr[1];
			}

			// Define the link ID
			if (!preg_match('|/file/(([a-z][0-9]+/)?[0-9]+)(/.*)?$|', $link, $matches)) throw new Exception("Invalid Wupload Link");
			$id = str_replace('/', '-', $matches[1]);

			// Call the API to get a download link
			$post = array('u' => urlencode($user), 'p' => urlencode($pass), 'ids' => $id, "passwords[$id]" => urlencode($lpass));
			$page = $this->GetPage("http://api.wupload.com/link?method=getDownloadLink", 0, $post);
			if (stristr($page, 'We have detected some suspicious behaviour') || stristr($page, 'blocked.wupload.com/')) throw new Exception("WU has blocked your IP.");

			$body = substr($page, strpos($page,"\r\n\r\n") + 4);
			$body = substr($body, strpos($body, "{"));
			$body = substr($body, 0, strrpos($body, "}") + 1);

			$apiResponse = json_decode($body, true);

			if (!$apiResponse || !isset($apiResponse['FSApi_Link']) || !isset($apiResponse['FSApi_Link']['getDownloadLink']) || !isset($apiResponse['FSApi_Link']['getDownloadLink']['status'])) {
				throw new Exception("WU: Unable to get download link, unknown API response. [DEBUG]: " . htmlentities($page));
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

			if ($download['status'] == 'NOT_AVAILABLE') {
				throw new Exception("This file was deleted");
			}
			if ($download['status'] == 'WRONG_PASSWORD') {
				if (empty($lpass)) throw new Exception("WU: Password protected link. Please input link with password: Link|Password.");
				else throw new Exception("WU: Link password is incorrect. Please input link with password: Link|Password.");
			}
			if ($download['status'] != 'AVAILABLE') {
				throw new Exception("Unable to download this file: " . $download['status']);
			}
			$this->RedirectDownload($download['url'], $download['filename']);
		} catch(Exception $e) {
			html_error ($e->getMessage());
		}
	}
}
/* Edited by Th3-822:
  Changed geturl() for GetPage() and more edits for make it look like fsc plugin.
 */
?>