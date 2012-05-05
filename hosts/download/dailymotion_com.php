<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class dailymotion_com extends DownloadClass {

    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, 'HTTP/1.1 410 Gone', 'This video has been removed by the user.');
        is_present($page, 'HTTP/1.1 403 Forbidden', 'This video is forbidden to download!');
        is_present($page, '(private)', 'This video is set to be PRIVATE!');
        $Cookies = GetCookies($page);
		if(preg_match('#ocation: (.*)#', $page, $loc)){
		$page = $this->GetPage('http://www.dailymotion.com'.$loc[1]);
		is_page($page);
		is_present($page, 'HTTP/1.1 404', 'Page Not Found! [0*01]');
		if(!preg_match('#trackPageview\("([^"]+)"#', $page, $family)){
			html_error('Error in process Download [0*01]');
		}
		$page = $this->GetPage('http://www.dailymotion.com'.$family[1].'&enable=false');
		is_page($page);
		is_present($page, 'HTTP/1.1 404', 'Page Not Found! [0*02]');
		if(!preg_match('#ocation: (.*)#', $page, $loc2)){
			html_error('Error in process Download [0*02]');
		}
		$page = $this->GetPage($loc2[1]);
		is_page($page);
		is_present($page, 'HTTP/1.1 404', 'Page Not Found! [0*03]');
		$Cookies = $Cookies.'; '.GetCookies($page);
		if(!preg_match('#ocation: (.*)#', $page, $loc3)){
			html_error('Error in process Download [0*03]');
		}
		$page = $this->GetPage($loc3[1], $Cookies);
		is_page($page);
	    is_present($page, 'HTTP/1.1 404', 'Page Not Found! [0*04]');
		}
        $FileName = trim(cut_str($page, '<title>', '</title>'));
        $FileName = str_replace(" ", "_", $FileName) . ".mp4";
		
        try {
            if (!preg_match('@"hqURL":"([^|\r|\n|"]+)@i', urldecode($page), $temp)) {
                preg_match('@"(?:sd)?URL":"([^|\r|\n|"]+)@i', urldecode($page), $temp);
            }
            if (!isset($temp[1])) {
                throw new Exception("Error : Video link not found!");
            }
            $temp = str_replace("\/", "/", $temp[1]) . "&redirect=0";
            $page = $this->GetPage($temp, $Cookies);
            if (!preg_match('#http://.+dailymotion.com/video/[^\r\n]+#', $page, $dlink)) {
                throw new Exception("Error : Direct link not found!");
            }
            $this->RedirectDownload($dlink[0], $FileName, $Cookies, 0, $temp);
            exit();
        } catch (Exception $e) {
            html_error($e->getMessage());
        }
    }
}

// dailymotion download plugin by vdhdevil
// small fix in regex & filename by Ruud v.Tony 07-11-2011
// small fix in regex based on SVN patch by Th3-822 13-12-2011
// fixed bug with videos for over 18 years by SD-88 08.04.2012
?>