<?php

if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

class share_online_biz extends DownloadClass {

    var $jscript_base64 = '(function($){var keyString="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";var uTF8Encode=function(string){string=string.replace(/\x0d\x0a/g,"\x0a");var output="";for(var n=0;n<string.length;n++){var c=string.charCodeAt(n);if(c<128){output+=String.fromCharCode(c)}else if((c>127)&&(c<2048)){output+=String.fromCharCode((c>>6)|192);output+=String.fromCharCode((c&63)|128)}else{output+=String.fromCharCode((c>>12)|224);output+=String.fromCharCode(((c>>6)&63)|128);output+=String.fromCharCode((c&63)|128)}}return output};var uTF8Decode=function(input){var string="";var i=0;var c=c1=c2=0;while(i<input.length){c=input.charCodeAt(i);if(c<128){string+=String.fromCharCode(c);i++}else if((c>191)&&(c<224)){c2=input.charCodeAt(i+1);string+=String.fromCharCode(((c&31)<<6)|(c2&63));i+=2}else{c2=input.charCodeAt(i+1);c3=input.charCodeAt(i+2);string+=String.fromCharCode(((c&15)<<12)|((c2&63)<<6)|(c3&63));i+=3}}return string};$.extend({base64Encode:function(input){var output="";var chr1,chr2,chr3,enc1,enc2,enc3,enc4;var i=0;input=uTF8Encode(input);while(i<input.length){chr1=input.charCodeAt(i++);chr2=input.charCodeAt(i++);chr3=input.charCodeAt(i++);enc1=chr1>>2;enc2=((chr1&3)<<4)|(chr2>>4);enc3=((chr2&15)<<2)|(chr3>>6);enc4=chr3&63;if(isNaN(chr2)){enc3=enc4=64}else if(isNaN(chr3)){enc4=64}output=output+keyString.charAt(enc1)+keyString.charAt(enc2)+keyString.charAt(enc3)+keyString.charAt(enc4)}return output},base64Decode:function(input){var output="";var chr1,chr2,chr3;var enc1,enc2,enc3,enc4;var i=0;input=input.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(i<input.length){enc1=keyString.indexOf(input.charAt(i++));enc2=keyString.indexOf(input.charAt(i++));enc3=keyString.indexOf(input.charAt(i++));enc4=keyString.indexOf(input.charAt(i++));chr1=(enc1<<2)|(enc2>>4);chr2=((enc2&15)<<4)|(enc3>>2);chr3=((enc3&3)<<6)|enc4;output=output+String.fromCharCode(chr1);if(enc3!=64){output=output+String.fromCharCode(chr2)}if(enc4!=64){output=output+String.fromCharCode(chr3)}}output=uTF8Decode(output);return output}})})(jQuery);';

    public function Download($link) {
        global $premium_acc;
        if (($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) || ($_REQUEST ["premium_acc"] == "on" && $premium_acc ["shareonline_biz"] ["user"] && $premium_acc ["shareonline_biz"] ["pass"])) {
            $this->DownloadPremium($link);
        } else if ($_POST["step"] == "1") {
            $this->DownloadLink($link);
        } else {
            $this->Retrieve($link);
        }
    }

    private function DownloadLink($link) {
        $Cookies = $_POST['cookie'];
        $Referer = $_POST['referer'];
        $FileName = $_POST['filename'];
        $this->RedirectDownload(trim($link), $FileName, $Cookies, 0, $Referer);
        exit;
    }

    private function Retrieve($link) {
        $page = $this->GetPage($link);
        if (preg_match("#Location: (.+)#", $page, $temp)) {
            $link = "http://www.share-online.biz" . $temp[1];
            $page = $this->GetPage($link);
        }
        $Cookies = GetCookies($page);
        $post = array();
        $post['dl_free'] = "1";
        $page = $this->GetPage(trim($link) . "/free/", $Cookies, $post, $link);
        is_present($page, "failure/full/", "No free slots for free users");
        if (!preg_match("#var wait=(\d+)#", $page, $count)) {
            html_error("Error 0x01: Plugin is out of date");
        }
        $dl = cut_str($page, 'dl="', '"');
        insert_timer($count[1]);
        $this->GetLink($Cookies, $dl,$link);
        exit;
    }

    private function DownloadPremium($link) {
        global $premium_acc;
        $post = array();
        $post["user"] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc ["shareonline_biz"] ["user"];
        $post["pass"] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc ["shareonline_biz"] ["pass"];
        $post["l_rememberme"] = "1";
        $page = sslcurl("https://www.share-online.biz/user/login", $post, 0, "http://www.share-online.biz/");
        $Cookies = GetCookies($page);
        $page = $this->GetPage($link, $Cookies);
        if (preg_match("#Location: (.+)#", $page, $temp)) {
            $link = trim("http://www.share-online.biz" . $temp[1]);
            $page = $this->GetPage($link, $Cookies);
        }
        $Cookies.=GetCookies($page);
        $dl = cut_str($page, 'var dl="', '"');
        $this->GetLink($Cookies, $dl,$link);
        exit;
    }

    private function GetLink($Cookies, $dl,$link) {
        $ss = <<<HTML
<html>
<head>
<title>Form</title>
</head>
<body bgcolor="#FFFFFF" text="#000000">
<form method="post" name="plink" action="index.php">
<input id="link" name="link" type="hidden">
<input type="hidden" name="cookie" value="$Cookies" >
<input type="hidden" name="referer" value="$link" >
<input type="hidden" name="step" value="1" >
<input type="hidden" name="filename" value="test">
</form>
HTML;
        $script = $ss . '<script language="Javascript">' . $this->jscript_base64 . 'var dl=".' . $dl . '";' . 'document.getElementById("link").value=$.base64Decode(dl);document.plink.submit();</script>';
        echo ($script);
    }
}

/*
 * by vdhdevil Feb-10-2011
 * updated Feb-13-2011: fixed work with autodownload
 */