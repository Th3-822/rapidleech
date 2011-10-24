<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class shareflare_net extends DownloadClass {

    public function Download($link) {
        global $premium_acc;
        
        if (($_REQUEST['premium_acc'] == 'on' && $_REQUEST['premium_pass']) || ($_REQUEST['premium_acc'] == 'on' && $premium_acc['shareflare_net']['pass'])) {
            html_error('Not supported now!');
        } else {
            $this->Free($link);
        }
    }

    private function Free($link) {

        $page = $this->GetPage($link);
        is_present($page, 'File not found');
        $cookie = GetCookies($page);
        if (!preg_match('%<form action="(.*)" method="post" id="dvifree">%', $page, $free)) html_error('Error: Free link can\'t be found!');
        $flink = "http://shareflare.net".$free[1];
        $post['uid5'] = cut_str($page, 'name="uid5" value="', '"');
        $post['uid'] = cut_str($page, 'name="uid" value="', '"');
        $post['name'] = cut_str($page, 'name="name" value="', '"');
        $post['pin'] = cut_str($page, 'name="pin" value="', '"');
        $post['realuid'] = cut_str($page, 'name="realuid" value="', '"');
        $post['realname'] = cut_str($page, 'name="realname" value="', '"');
        $post['host'] = cut_str($page, 'name="host" value="', '"');
        $post['ssserver'] = cut_str($page, 'name="ssserver" value="', '"');
        $post['sssize'] = cut_str($page, 'name="sssize" value="', '"');
        $post['dir'] = cut_str($page, 'name="dir" value="', '"');
        $post['optiondir'] = cut_str($page, 'name="optiondir" value="', '"');
        $post['lsarrserverra'] = cut_str($page, 'name="lsarrserverra" value="', '"');
        $post['page_url'] = cut_str($page, 'name="page_url" value="', '"');
        $post['return_error'] = '1';
        $post['md5crypt'] = cut_str($page, 'name="md5crypt" value="', '"');
        $post['tarif'] = 'default';
        $post['realuid_free'] = cut_str($page, 'name="realuid_free" value="', '"');
        $post['submit_ifree'] = 'Download file';
        $page = $this->GetPage($flink, $cookie, $post, $link);
        if (!preg_match('%<form action="(.*)" method="post" id="dvifree">%', $page, $redir)) html_error ('Error: Redirect link 1 can\'t be found!');
        $rlink = trim($redir[1]);
        $t = explode(";", GetCookies($page));
        $cookie .=";" . $t[0] . ";" . $t[2];
        unset($post);
        $post['frameset'] = 'Download file.';
        $post['md5crypt'] = cut_str($page, 'name="md5crypt" value="', '"');
        $post['uid5'] = cut_str($page, 'name="uid5" value="', '"');
        $post['uid'] = cut_str($page, 'name="uid" value="', '"');
        $post['name'] = cut_str($page, 'name="name" value="', '"');
        $post['pin'] = cut_str($page, 'name="pin" value="', '"');
        $post['realuid'] = cut_str($page, 'name="realuid" value="', '"');
        $post['realname'] = cut_str($page, 'name="realname" value="', '"');
        $post['host'] = cut_str($page, 'name="host" value="', '"');
        $post['ssserver'] = cut_str($page, 'name="ssserver" value="', '"');
        $post['sssize'] = cut_str($page, 'name="sssize" value="', '"');
        $post['optiondir'] = cut_str($page, 'name="optiondir" value="', '"');
        $post['tarif'] = 'default';
        $page = $this->GetPage($rlink, $cookie, $post, $flink);
        if (!preg_match('%<frame src="(.*)" name="topFrame"%', $page, $redir)) html_error ('Error: Redirect link 2 can\'t be found!');
        $tlink = "http://shareflare.net".$redir[1];

        return $this->BeforeDownload($tlink, $cookie, $this->GetPage($tlink, $cookie, 0, $rlink));
    }

    private function BeforeDownload($link, $cookie, $page) {

        if (!preg_match('@(\d+)<\/span> seconds@', $page, $wait)) html_error ('Error: Timer not found!');
        $this->CountDown($wait[1]);
        if (!preg_match('@window\.location\.href="(.*)"@', $page, $redir)) html_error('Error: Redirect link 3 can\'t be found!');
        $rlink = trim($redir[1]);

        return $this->ContinueDownload($rlink, $cookie, $this->GetPage($rlink, $cookie, 0, $link));
    }

    private function ContinueDownload($link, $cookie, $page) {

        if (stristr($page, 'Wait your turn')) {
            return $this->BeforeDownload($link, $cookie, $this->GetPage($link, $cookie, 0, $link));
        }
        
        if (!preg_match('@http:\/\/.+download(\d+)?\/sha(\d+)?\/[^\'"]+@i', $page, $dl)) html_error('Error: Download link not found, plugin need to be updated!');
        $dlink = trim($dl[0]);
        $filename = parse_url($dlink);
        $FileName = basename($filename['path']);
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
        exit();
    }
}

//shareflare.net free download plugin by Ruud v.Tony 14-10-2011
?>
