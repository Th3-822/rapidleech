<?php

class btaccel_com extends DownloadClass
{
	private $page;
	private $cookie;

	private function loginto($link)
	{
		global $premium_acc;
		$this->page = $this->GetPage($link, 0, array('email' => urlencode($premium_acc['btaccel']['user']), 'password' => $premium_acc['btaccel']['pass']));
		$this->cookie = GetCookies($this->page);
		$this->page = $this->GetPage('http://www.btaccel.com/home/', $this->cookie);
		is_notpresent($this->page, 'logout', 'Error logging in - perhaps logins are incorrect');
	}

	public function Download($link)
	{
		global $premium_acc;
		if (($_POST ["premium_acc"] == "on" && $_POST ["premium_user"] && $_POST ["premium_pass"]) || ($_POST ["premium_acc"] == "on" && $premium_acc ["btaccel"]))
		{
			$this->loginto('http://www.btaccel.com/login/');
			$this->page = $this->GetPage($link, $this->cookie);
			$string = cut_str($this->page, '<form name="form" method="post" action="http://94.75.237.89:80/getfile/"', '</form>');
			preg_match('%id="info_hash" value="(.+)"%U', $string, $infohash);
			preg_match('%id="url_hash" value="(.+)"%U', $string, $urlhash);
			preg_match('%name="checksum" value="(.+)"%U', $string, $checksum);
			preg_match('%name="file_name" value="(.+)"%U', $string, $fname);
			preg_match_all('%&file_name=(.+)" onclick%', $string, $files);
			array_shift($files[1]);
			foreach ($files[1] as $file)
			{
				$flist .= $file . "\r\n";
			}
			$post = array();
			$post['info_hash'] = $infohash[1];
			$post['urlhash'] = $urlhash[1];
			$post['checksum'] = $checksum[1];
			$post['file_name'] = $fname[1];
			$post['files'] = $flist;
			$FileName = $fname[1];
			$this->RedirectDownload('http://94.75.237.89:80/getfile/', $FileName, 0, $post);
		}
		else html_error('There are no BTAccel logins set. Please set them in the config.php.');
	}
}
//szal 01jul09
?>