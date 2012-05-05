<?php
if (!defined('RAPIDLEECH')) {
	require('index.html');
	exit();
}

class filegaze_com extends DownloadClass {
	public $page, $link, $cookie;
	public function Download($link) {
		global $premium_acc;
		
		$this->cookie = 'lang=english';
		if (!$_REQUEST['step']) {
			$this->page = $this->GetPage($link, $this->cookie);
			is_present($this->page, "The file you were looking for could not be found, sorry for any inconvenience.");
		}
		$this->link = $link;
		if ($_REQUEST['premium_acc'] == 'on' && (($_REQUEST['premium_user'] && $_REQUEST['premium_pass'])||($premium_acc['filegaze_com']['user'] && $premium_acc['filegaze_com']['pass']))) {
			return $this->Premium();
		} else {
			return $this->Free();
		}
	}
	
	private function Free() {
		$form = cut_str($this->page, "<Form method=\"POST\" action=''>", '</Form>');
		if (!preg_match_all('/<input type="hidden" name="([^\r\n"]+)" value="([^\r\n"]+)?">/', $form, $one) || !preg_match_all('/<input type="submit" name="(\w+_free)" value="([^"]+)">/', $form, $two)) html_error('Can\'t find post form [FREE] data 1, try to set curl to true in $options in your config.php!');
		$match = array_merge(array_combine($one[1], $one[2]), array_combine($two[1], $two[2]));
		$post = array();
		foreach ($match as $k => $v) {
			$post[$k] = $v;
		}
		$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		$form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
		is_present($form, '<input type="password" name="password" class="myForm">', 'This file is password protected!');
		is_present($form, cut_str($form, '<p class="err">', '<br />'));
		if (preg_match('/(\d+)<\/span> seconds/', $form, $wait)) $this->CountDown ($wait[1]);
		if (!preg_match_all('/<input type="hidden" name="([^\r\n"]+)" value="([^\r\n"]+)?">/', $form, $match)) html_error('Can\'t find post form [FREE] data 2, try to set curl to true in $options in your config.php!');
		$match = array_combine($match[1], $match[2]);
		// get the captcha, is not captcha anyway, we can automate that, look in filerio or another captcha skip form in existing plugin, check the regex is it match or not with the form captcha
        if (!preg_match_all("#<span style='[^\d]+(\d+)[^\d]+\d+\w+;'>\W+(\d+);</span>#", $form, $temp)) html_error('Automatic Captcha Form doesn\'t match!');
		for ($i=0;$i<count($temp[1])-1;$i++){
			for ($j=$i+1;$j<count($temp[1]);$j++){
				if ($temp[1][$i]>$temp[1][$j]){
					$n=1;
					do {
						$tmp=$temp[$n][$i];
						$temp[$n][$i]=$temp[$n][$j];
						$temp[$n][$j]=$tmp;
						$n++;
					} while ($n<=2);
				}
			}
		}
		$captcha="";
		foreach($temp[2] as $value) {
			$captcha.=chr($value);
		}
		// merge the first array $match and captcha so we can post that together
		$postform = array_merge($match, array('code' => $captcha));
		// ok, we need to post the second form
		$post = array();
		foreach ($postform as $k => $v) {
			$post[$k] = $v;
		}
		$page = $this->GetPage($this->link, $this->cookie, $post, $this->link);
		if (!preg_match('/ocation: (https?:\/\/[^\r\n]+)/', $page, $dl)) html_error('Download link [FREE] not found!');
		$dlink = trim($dl[1]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $this->cookie);
		exit;
	}
	
	private function Premium() {
		$cookie = $this->login();
		$page = $this->GetPage($this->link, $cookie);
		if (!preg_match('/https?:\/\/s\d+\.filegaze\.com(:\d+)?\/[^\r\n\'"]+/', $page, $dl)) {
			$form = cut_str($page, '<Form name="F1" method="POST"', '</Form>');
			is_present($form, '<input type="password" name="password" class="myForm">', 'This file is password protected!');
			if (!preg_match_all('/<input type="hidden" name="([^\r\n"]+)" value="([^\r\n"]+)?">/', $form, $match)) html_error('Can\'t find post form [PREMIUM] data, try to set curl to true in $options in your config.php!');
			$match = array_combine($match[1], $match[2]);
			$post = array();
			foreach ($match as $k => $v) {
				$post[$k] = $v;
			}
			$page = $this->GetPage($this->link, $cookie, $post);
			if (!preg_match('/https?:\/\/s\d+\.filegaze\.com(:\d+)?\/[^\r\n\'"]+/', $page, $dl)) html_error('Download link [PREMIUM] not found!');
		}
		$dlink = trim($dl[0]);
		$filename = basename(parse_url($dlink, PHP_URL_PATH));
		$this->RedirectDownload($dlink, $filename, $cookie);
	}
	
	private function login() {
		global $premium_acc;
		
        $user = ($_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["filegaze_com"] ["user"]);
        $pass = ($_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["filegaze_com"] ["pass"]);
        if (empty($user) || empty($pass)) html_error("Login failed, $user [user] or $pass [password] is empty!");
		
        $url = 'http://filegaze.com/';
        $post['op'] = 'login';
        $post['redirect'] = urlencode($url);
        $post['login'] = $user;
        $post['password'] = $pass;
		$post['x'] = rand(11,99);
		$post['y'] = rand(11,99);
        $page = $this->GetPage($url, $this->cookie, $post, $url."login.html");
		is_present($page, 'Incorrect Login or Password');
		$cookie = GetCookies($page).'; '.$this->cookie;
        //check account
        $page = $this->GetPage($url."?op=my_account", $cookie, 0, $url);
        is_notpresent($page, '<TD>Username:</TD>', 'Invalid account!');
        is_notpresent($page, '<TD>Premium account expire:</TD>', 'Account not premium???');
        
        return $cookie;
	}
}

/*
 * by Ruud v.Tony 06-04-2012
 */
?>
