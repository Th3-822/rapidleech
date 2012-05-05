<?php
if (!defined('RAPIDLEECH')) {
	require_once ("index.html");
	exit();
}

class wupload_com extends DownloadClass {
	public $site;
	private $cookie;
	public function Download($link) {
		$this->cookie = array('lang' => 'en', 'isJavascriptEnable' => 1);
		$link = parse_url($link);
		$link['host'] = $this->GetWUDomain();
		$this->site = 'http://'.$link['host'].'/';
		$link = rebuild_url($link);

		if (preg_match("@^https?://[^/]+/folder/.+@i", $link)) {
			$page = $this->GetPage($link, $this->cookie);
			is_present($page, 'Error 9001', 'The requested folder do not exist or was deleted by the owner.');
			is_present($page, 'Error 9002', 'The requested folder is not public.');
			is_present($page, 'No links to show', 'The requested folder is empty.');
			is_notpresent($page, 'Set-Cookie: lastUrlLinkId=', 'Error. The requested folder is empty?');
			if (!preg_match_all('@[\"|\'](https?://[^/]+/file/[^\"|\'|]+)[\"|\']@i', $page, $links)) html_error('Error getting links.');
			$links = $links[1];
			// Test if folder is yours
			$this->Login();
			$page = $this->GetPage($links[0], $this->cookie);
			is_present($page, 'Wupload does not allow files to be shared', 'This folder isn\'t yours, login with the owner account for download.');
			return $this->moveToAutoDownloader($links);
		}

		$page = $this->GetPage($link, $this->cookie);
		is_notpresent($page, 'Set-Cookie: lastUrlLinkId=', 'Error. File exists?');

		// Test if file is yours
		$this->Login();
		$page = $this->GetPage($link, $this->cookie);
		is_present($page, 'Wupload does not allow files to be shared', 'This file isn\'t yours, login with the owner account for download.');
		// Card: "FILE is yours" :D

		if (!preg_match('@https?://s\d+\.wupload\.[^/]+/download/[^\r|\n|\"|\']+@i',$page, $dl)) html_error('Download Link Not Found.');
		$this->RedirectDownload($dl[0], '[T-8]_Wupload', $this->cookie);
	}

	private function Get_Reply($page) { // TO-DO: Rename this funtion. :D
		if (!function_exists('json_decode')) html_error("Error: Please enable JSON in php.");
		$json = substr($page, strpos($page,"\r\n\r\n") + 4);
		$json = substr($json, strpos($json, "{"));
		$json = substr($json, 0, strrpos($json, "}") + 1);
		$rply = json_decode($json, true);
		if (!$rply || (is_array($rply) && count($rply) == 0)) html_error("Error getting json data.");
		return $rply;
	}

	private function Login() {
		global $premium_acc;
		$pA = (empty($_REQUEST["premium_user"]) || empty($_REQUEST["premium_pass"])) ? false : true;
		$user = ($pA ? $_REQUEST["premium_user"] : $premium_acc["wupload_com"]["user"]);
		$pass = ($pA ? $_REQUEST["premium_pass"] : $premium_acc["wupload_com"]["pass"]);
		if (empty($user) || empty($pass)) html_error("Login Failed: Username or Password are empty. Please check login data.");

		$post = array();
		$post['email'] = urlencode($user);
		$post['redirect'] = '%2F';
		$post['password'] = urlencode($pass);
		$post['rememberMe'] = 1;
		$page = $this->GetPage($this->site.'account/login', $this->cookie, $post, $this->site."\r\nX-Requested-With: XMLHttpRequest\r\nAccept: application/json, text/javascript, */*; q=0.01"); // Now it's like the ajax login :D
		$json = $this->Get_Reply($page);
		if ($json['status'] != 'success') html_error('Login Error '.htmlentities('['.key($json['messages']).']: '.$json['messages'][0]));
		is_present($json['redirect'], '/banned', 'Login Failed: Account is banned.');
		is_notpresent($page, 'Set-Cookie: email=', 'Login Error: Cookie "email" not found.');
		$this->cookie = GetCookiesArr($page, $this->cookie);
	}

	private function GetWUDomain() {
		$proxy = (isset($_GET['useproxy']) && !empty($_GET['proxy'])) ? true : false;
		// return "www.wupload.com"; // Uncomment this line for override the domain check (don't remove the www.)
		$opt = array(CURLOPT_NOBODY => 1, CURLOPT_HEADER => 0,
		CURLOPT_FOLLOWLOCATION => true,  CURLOPT_MAXREDIRS => 5,
		CURLOPT_FAILONERROR => 1, CURLOPT_AUTOREFERER => 1,
		CURLOPT_USERAGENT => "Opera/9.80 (Windows NT 6.1; U; en-US) Presto/2.10.229 Version/11.61");
		if ($proxy) {
			global $pauth;
			$opt[CURLOPT_HTTPPROXYTUNNEL] = true;
			$opt[CURLOPT_PROXY] = $_GET["proxy"];
			if ($pauth) $opt[CURLOPT_PROXYUSERPWD] = base64_decode($pauth);
		}
		$opt[CURLOPT_CONNECTTIMEOUT] = $opt[CURLOPT_TIMEOUT] = 30;

		$ch = curl_init("http://www.wupload.com/");
		foreach ($opt as $O => $V) { // Using this instead of 'curl_setopt_array'
			curl_setopt($ch, $O, $V);
		}
		$curl = curl_exec($ch);
		$redirect = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		curl_close($ch);

		if ($curl === false) html_error('Error checking wupload domain for this '.($proxy?'proxy\'s':'server\'s').' IP.');

		$domain = parse_url($redirect, PHP_URL_HOST);
		$domain = "www.".substr($domain, strripos($domain, "wupload."));
		return $domain;
	}
}

//  Plugin for download (your account's) files from wupload.
//[05-4-2012]  Written by Th3-822.
//[06-4-2012]  Fixed Login(). -Th3-822.

?>