<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class uploadstation_com extends DownloadClass
{
	public function Download($link)
	{
		global $premium_acc;
		$isRequest = ($_REQUEST["premium_user"] && $_REQUEST["premium_pass"]);
		$user = $isRequest?$_REQUEST["premium_user"]:$premium_acc["uploadstation_com"]["user"];
		$pass = $isRequest?$_REQUEST["premium_pass"]:$premium_acc["uploadstation_com"]["pass"];
		
		if ($_REQUEST["premium_acc"] == "on" && $user && $pass) {
			$this->DownloadPremium($link,$user,$pass);
		}else{
			html_error("Free Download not supported yet");
		}
	}

	public function DownloadPremium($link, $user, $pass)
	{
		$rootURL = 'http://www.uploadstation.com/';
		if(!preg_match('#http://(?:www\.)?uploadstation.com/file/(.+)#i',$link)){
			html_error("Invalid URL");
		}else{
			$login = array(
				'loginFormSubmit'				=>	'Login'	,
				'loginUserName'					=>	$user	,
				'loginUserPassword'				=>	$pass	,
				'autoLogin'						=> 	'on'
			);
			$page=$this->getPage("{$rootURL}login.php", 0, $login);
			
			$cookie = GetCookies($page);
			is_notpresent($cookie, "Cookie=", "Login error. Cookie not found.");
		
			$page = $this->GetPage("{$rootURL}dashboard.php", $cookie, 0, $rootURL);
			is_present($page, "acctype_free", "Error:Not Premium [0]");
			is_notpresent($page, "Expiry date: ", "Error:Not Premium [1]");
			
			$page = $this->GetPage($link, $cookie,array('download'=>'premium'));
			
			if (stristr($page, "HTTP/1.1 200 OK")) {
				$page = $this->GetPage($this->link, $cookie, array('download'=>'premium'));
			}
			if (preg_match('/Location: (http:\/\/d\d+.uploadstation.com[^\r\n]+)/i', $page, $tmpDownload)) {
				$download_link = $tmpDownload[1];
			} else {
				is_present($page, "You are not able to download", "Error:Not Premium [2]");
			}

			$downloadElements = parse_url($download_link);
			$filename = urldecode(basename($downloadElements["path"]));
			
			$this->RedirectDownload($download_link, $filename, $cookie);
		}
	}
}

// For uploadstation download plugin which support both type of download, please refer to this thread http://www.rapidleech.com/index.php/topic/11352-req-uploadstation-plugin/page__view__findpost__p__50936
?>

