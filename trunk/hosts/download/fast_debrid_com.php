<?php

if (!defined('RAPIDLEECH')) {
    require_once('index.html');
    exit();
}

class fast_debrid_com extends DownloadClass {

    public $usecurl;

    public function Download($link) {
        global $premium_acc;
        // Check for https support. (copied from Th3-822 rapidshare download plugin).
        $this->usecurl = $cantuse = false;
        if (!extension_loaded('openssl')) {
            if (extension_loaded('curl')) {
                $cV = curl_version();
                if (!in_array('https', $cV['protocols'], true)) $cantuse = true;
                $this->usecurl = true;
            } else {
                $cantuse = true;
            }
            if ($cantuse) html_error("Need OpenSSL enabled or cURL (with SSL) to use this plugin.");
        }

        try {

            $user = ($_REQUEST["premium_user"] ? $_REQUEST["premium_user"] : $premium_acc["fastdebrid_com"]["user"]);
            $pass = ($_REQUEST["premium_pass"] ? $_REQUEST["premium_pass"] : $premium_acc["fastdebrid_com"]["pass"]);
            if (empty($user) || empty($pass)) {
                throw new Exception('Username or password is empty, you need to insert your login detail!');
            }

            $posturl = 'https://www.fast-debrid.com/';
            $post['pseudo'] = $user;
            $post['password'] = $pass;
            $post['connexion.x'] = rand(1, 62);
            $post['connexion.y'] = rand(1, 13);
            $page = $this->GetPageS($posturl . 'index.php', 'lang=en', $post, $posturl);
            is_present($page, 'Pseudo ou mot de passe incorrect !', 'User or password is incorrect!');
            is_present($page, ' - Normal', 'Account free, what do you want with free account?');
            $cookie = GetCookies($page) . '; lang=en';

            if (preg_match('@http:\/\/fast-debrid\.com\/[?]([^\r\n]+)@i', $link, $ck) || preg_match('@https:\/\/fast-debrid\.com\/[?]([^\r\n]+)@i', $link, $ck)) {
                $check = $ck[1];
                if (stristr($check, "|")) {
                    $arr = explode('|', $check);
                    $urlhost = $arr[0];
                    $password = $arr[1];
                } else { //no password input
                    $urlhost = $check;
                }
            } else {
                throw new Exception('Format link unknown, please input like this http://fast-debrid.com/?http://www.megaupload.com/?d=VLV1UJ0C');
            }
            unset($post);
            $post['liens'] = $urlhost;
            $post['vision'] = 'download';
            $post['magiclink'] = 'on';
            $post['pass'] = $password;
            $page = $this->GetPageS($posturl . 'debi.php', $cookie, $post, $posturl . 'index.php');
            is_present($page, 'Vous n\'avez pas entr&eacute; de lien(s) valide(s)', 'This host is not supported!');
            if (!preg_match('@(http:\/\/.+fast-debrid\.com\/[\w.]+\/' . $user . '\/[^\'"]+)"@i', $page, $temp)) {
                throw new Exception('Error: Redirect link not found!');
            }
            $link = trim($temp[1]);
            $page = $this->GetPageS($link, $cookie, 0, $posturl . 'debi.php');
            is_present($page, 'Adresse IP Invalide', 'IP address not recognized!');
            is_present($page, 'Lien invalide', 'File have been removed or required password to download, try input link with format: http://fast-debrid.com/?http://www.megaupload.com/?d=VLV1UJ0C | password');
            if (!preg_match('@ocation: (\/[^|\r|\n]+)@', $page, $dl)) {
                throw new Exception('Error: Download link not found, plugin need to be updated!');
            }
            $server = cut_str($link, 'http://', '.fast-');
            $dlink = "http://$server.fast-debrid.com$dl[1]";
            $FileName = basename(parse_url($dlink, PHP_URL_PATH));
            $this->RedirectDownload($dlink, $FileName, $cookie);
            
        } catch (Exception $e) {
            html_error($e->getMessage());
        }
    }

    private function GetPageS($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0) {
        if (!$referer) {
            global $Referer;
            $referer = $Referer;
        }
        $url = parse_url(trim($link));
        if ($this->usecurl) $page = $this->cURL($link, $cookie, $post, $referer, base64_decode($auth));
        else $page = $this->GetPage($link, $cookie, $post, $referer, $auth);

        return $page;
    }

    private function cURL($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0) {
        $opt = array(CURLOPT_HEADER => 1, CURLOPT_COOKIE => $cookie, CURLOPT_REFERER => $referer,
            CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.6) Gecko/20050317 Firefox/1.0.2");
        if ($post != '0') {
            $POST = "";
            foreach ($post as $k => $v) {
                $POST .= "$k=$v&";
            }
            $opt[CURLOPT_POST] = 1;
            $opt[CURLOPT_POSTFIELDS] = substr($POST, 0, -1);
        }
        if ($auth) {
            $opt[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
            $opt[CURLOPT_USERPWD] = $auth;
        }
        $ch = curl_init($link);
        foreach ($opt as $O => $V) { // Using this instead of 'curl_setopt_array'
            curl_setopt($ch, $O, $V);
        }
        $page = curl_exec($ch);
        $errz = curl_errno($ch);
        $errz2 = curl_error($ch);
        curl_close($ch);

        if ($errz != 0) html_error("Fast-Debrid:[cURL:$errz] $errz2");
        return $page;
    }

}

//fast-debrid download plugin by Ruud v.Tony 26-10-2011
?>
