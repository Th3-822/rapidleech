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
		
		// Display error in case of trouble
		try {
			$url = parse_url($link);
			
			// Define credentials based on the url
			$user = isset($url['user']) && trim($url['user']) != '' ? $url['user'] : null;
			$pass = isset($url['pass']) && trim($url['pass']) != '' ? $url['pass'] : null;
			
			if ($user === null || $pass === null) {
				if ($_REQUEST ["premium_acc"] == "on" &&  isset($_REQUEST['premium_user']) && trim($_REQUEST['premium_user']) != '' &&
					isset($_REQUEST['premium_pass']) && trim($_REQUEST['premium_pass']) != '') {
					$user = $_REQUEST['premium_user'];
					$pass = $_REQUEST['premium_pass'];
				} else if ($_REQUEST ["premium_acc"] == "on" &&  isset($premium_acc["wupload"]["user"]) && trim($premium_acc["wupload"]["user"]) != '' &&
					isset($premium_acc["wupload"]["pass"]) && trim($premium_acc["wupload"]["pass"]) != '') {
					$user = $premium_acc["wupload"]["user"];
					$pass = $premium_acc["wupload"]["pass"];
				}
			}
			
			// Define the link ID
	        $regex = '|/file/(([a-z][0-9]+/)?[0-9]+)(/.*)?$|';
	        $matches = array();
			preg_match($regex, $link, $matches);
			if (!isset($matches[1])) throw new Exception("Invalid Wupload Link");
			$linkId = str_replace('/', '-', $matches[1]);
			
			// Call the API to get a download link
			$page = geturl("api.wupload.com", 80, '/link?method=getDownloadLink', 0, 0, array('u' => $user, 'p' => $pass, 'ids' => $linkId), 0, $_GET["proxy"]);
			
			// Check the API status code
	        $regex = '|^HTTP\/[^\ ]+\ ([0-9]+)|';
	        $matches = array();
			preg_match($regex, $page, $matches);
			
			// If the response is not a 200, throw an exception
			if (!isset($matches[1]) || (int)$matches[1] != 200) {
				throw new Exception("Unable to contact the Wupload API. [DEBUG] $page");
			}
			
			// Get the response as an object
			$body = substr($page, strpos($page,"\r\n\r\n") + 4);
			$body = substr($body, strpos($body, "{"));
			$body = substr($body, 0, strrpos($body, "}") + 1);
			$response = json_decode($body);
			
			// Throw an exception if the response is not a json object
			if (!$response) {
				throw new Exception("Unable to read response from Wupload API. [DEBUG] $body");
			}
			
			if ($response->FSApi_Link->getDownloadLink->status != 'success') {
				foreach($response->FSApi_Link->getDownloadLink->errors AS $key => $value) {
					if ($key == 'FSApi_Auth_Exception') {
						throw new Exception('This plugin require <a href="http://www.wupload.com/premium" style="background-color: white; color: red;">PREMIUM ACCOUNT</a>.');
					}
					
					throw new Exception($value);
				}
			}
			
			// Should be only one (1) link
			foreach($response->FSApi_Link->getDownloadLink->response->links AS $link) {
				$status = $link->status;
				
				// If the link is not available, throw an exception
				if ($status != 'AVAILABLE') {
					throw new Exception("The status of that file is: " . $status);
				}
				
				$filename = $link->filename;
				$downloadUrl = $link->url;
			}
			
			// If we was able to define the download link
			if (isset($downloadUrl) && isset($filename)) {
				// Start the download
				$this->RedirectDownload($downloadUrl, $filename);
			}
		
		} catch(Exception $e) {
        	html_error ($e->getMessage());
		}
	}
}
?>