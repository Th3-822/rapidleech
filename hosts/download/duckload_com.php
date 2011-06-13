<?php

if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class duckload_com extends DownloadClass {

    public function Download($link) {
        global $premium_acc;       
        $link = str_replace("/dl/", "/download/", $link);
		$link = str_replace("http://duckload.com","http://www.duckload.com",$link);
		if (strpos($link,"/divx/")){
			$link = str_replace("/divx/","/play/",$link);
			$link = str_replace(".html","",$link);
			}
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["duckload_com"] ["user"] && $premium_acc ["duckload_com"] ["pass"])) {
            $this->DownloadPremium($link);
        } else if (strpos($link, "/play/")) {
            $this->DownloadFreeVid($link);
        } else {
            $this->DownloadFree($link);
        }
    }

    private function DownloadFreeVid($link) {
        $Cookies="dl_set_lang = en";
		$page = $this->GetPage($link,$Cookies);
        is_present($page, "File not found", "File not found");
        $Cookies .= "; ".GetCookies($page);
        if (!preg_match("#Free Stream \((\d+)\)#", $page, $count)) {
            html_error("Error 0x11: Plugin is out of date");
        }
        insert_timer($count[1]);
        $post = array();
        if (!preg_match('#name="(\w+-\w+-\w+)".+value="(\w+)"#', $page, $temp)) {
            html_error("Error 0x12: Plugin is out of date");
        }
        $post[$temp[1]] = $temp[2];
        if (!preg_match('#<button name="(.*)" t#', $page, $temp)) {
            html_error("Error 0x13: Plugin is out of date");
        }
        $post[$temp[1]] = "";
        $page = $this->GetPage($link, $Cookies, $post, $link);
        if (!preg_match('#http://dl\d+[^"]+#', $page, $dlink)) {
            html_error("Error 0x14: Plugin is out of date");
        }
        $Url = parse_url(trim($dlink[0]));
        $FileName = basename($Url['path']);
        $this->RedirectDownload(trim($dlink[0]), $FileName, $Cookies, 0, $link);
        exit;
    }

    private function DownloadFree($link) {
		$Cookies="dl_set_lang = en";
        $page = $this->GetPage($link,$Cookies);
        is_present($page, "File not found", "File not found");
		is_present($page, "Database Maintenance - try again later","Database Maintenance - try again later");
        $Cookies .="; ". GetCookies($page);
        if (!preg_match("#Free Download.+\((\d+)\)#", $page, $count)) {
            html_error("Error 0x01: Plugin is out of date");
        }
        insert_timer($count[1], "Timer 1:");
        $post = array();
        if (!preg_match('#name="(\w+-\w+-\w+)".+value="(\w+)"#', $page, $temp)) {
            html_error("Error 0x02: Plugin is out of date");
        }
        $post[$temp[1]] = $temp[2];
        if (!preg_match('#<button name="(.*)" t#', $page, $temp)) {
            html_error("Error 0x03: Plugin is out of date");
        }
        $post[$temp[1]] = "";
        $page = $this->GetPage($link, $Cookies, $post, $link);
        if (!preg_match("#(\d+)</span> seconds#", $page, $count)) {
            html_error("Error 0x04: Plugin is out of date");
        }
        insert_timer($count[1], "Timer 2:");
        unset($post);
        $post = array();
        if (!preg_match('#name="(\w+-\w+-\w+)".+value="(\w+)"#', $page, $temp)) {
            html_error("Error 0x05: Plugin is out of date");
        }
        $post[$temp[1]] = $temp[2];
        if (!preg_match('#<button name="(.*)" t#', $page, $temp)) {
            html_error("Error 0x06: Plugin is out of date");
        }
        $post[$temp[1]] = "";
        $page = $this->GetPage($link, $Cookies, $post, $link);
        if (!preg_match("#http:\/\/dl\d+.+#", $page, $dlink)) {
            html_error("Error 0x07: Plugin is out of date");
        }
        $Url = parse_url(trim($dlink[0]));
        $FileName = basename($Url['path']);
        $this->RedirectDownload(trim($dlink[0]), $FileName, $Cookies, 0, $link);
        exit;
    }

    private function DownloadPremium($link) {
        global $premium_acc;
        $username = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["duckload_com"] ["user"];
        $password = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["duckload_com"] ["pass"];
        $apiurl = "http://www.duckload.com/api/public/login&user=" . $username . "&pw=" . $password . "&fmt=json&source=TOPNAV";
        $page = $this->GetPage($apiurl);
        is_present($page, "You have entered an incorrect password", "Login Failed , Bad username/password combination");
        $Cookies = GetCookies($page);
        $page = $this->GetPage($link, $Cookies);
		is_present($page, "Critical Error, please try again later","Duckload 's System get error, please try again later");
        is_present($page, "Database Maintenance - try again later","Database Maintenance - try again later");
		is_present($page, "File not found", "File not found");
        if (!preg_match('#Location: (.+)#', $page, $dlink)) {
            if (!preg_match('#<form action="([^"]+)"#', $page, $dlink)) {
                if (!preg_match("#http://.+ddl=1#", $page, $temp)) {
                    html_error("Error 1x01: Plugin is out of date");
                }
                $page = $this->GetPage($temp[0], $Cookies, 0, $link);
                if (!preg_match('#Location: (.+)#', $page, $dlink)) {
                    html_error("Error 1x02: Plugin is out of date");
                }
            }
        }
        $Url = parse_url(trim($dlink[1]));
        $FileName = basename($Url['path']);
        $this->RedirectDownload(trim($dlink[1]), $FileName, $Cookies, 0, $link, $FileName);
        exit;
    }
}

/*
 * Created by vdhdevil 30-Dec-2010
 * Updated June-6: fixed free download
 */
?>
