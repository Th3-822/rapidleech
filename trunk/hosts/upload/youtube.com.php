<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

##Youtube Login Details## Add your youtube logins here
$youtube_login = '';				// Your UTube email you use to login
$youtube_password = '';				//Your UTube password


///// DO NOT TOUCH /////
if (empty($youtube_login) || empty($youtube_password)) html_error('No UTube Login Details specified. You can add them in the youtube.com.php',0);

$Url=parse_url("http://www.youtube.com/login?next=/")  ;
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://www.youtube.com/", $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

//$cook=biscottiDiKaox($page);
$cook=GetCookies($page);

if(preg_match('/ocation: *(.+)/', $page, $redir))
{
 $geturl=rtrim($redir["1"]);
}
$contents=sslcurl("get",$geturl,0,$cook,0);
$cookie_GALX = GetCookies($contents);

$post_url = "https://www.google.com/accounts/ServiceLoginAuth?service=youtube";
$post = array();
$post['ltmpl'] = 'sso';
$post['continue'] = 'http://www.youtube.com/signup?hl=en_US&warned=&nomobiletemp=1&next=/index';
$post['service'] = 'youtube';
$post['uilel'] = '3';
$post['ltmpl'] = 'sso';
$post['hl'] = 'en_US' ;
$post['ltmpl'] = 'sso';
$post['GALX'] = $cookie_GALX;
$post['Email'] = $youtube_login;
$post['Passwd'] = $youtube_password;
$post['PersistentCookie'] = 'yes';
$post['rmShown'] = '1';
$post['signIn'] = 'Sign in';
$post['asts'] = '';
$cookie=$cook."; GALX=".$cookie_GALX."; GoogleAccountsLocale_session=en";
$contents=sslcurl("post",$post_url,$post,$cookie,$geturl);

function sslcurl ($method,$link,$post,$cookie,$refer){

        if ($method=="post"){$mm=1;
            $postdata = formpostdata($post);
            }elseif($method=="get"){$mm=0;}

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $link);
        curl_setopt( $ch, CURLOPT_HEADER, 1 );
        curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U;Windows NT 5.1; de;rv:1.8.0.1)\r\nGecko/20060111\r\nFirefox/1.5.0.1' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POST, $mm);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_REFERER,$refer);
        curl_setopt($ch, CURLOPT_COOKIE,$cookie) ;
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
     // curl_setopt ( $ch , CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        $contents .= curl_exec( $ch );
     //   $info = curl_getinfo($ch);
     //  $stat = $info['http_code'];
        curl_close( $ch );
		return $contents;
}
 if(preg_match('/Location: *(.+)/', $contents, $redir))
 {
    $redirect=rtrim($redir["1"]);
    $Url = parse_url($redirect);
 }
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

$cookie_LOGIN_INFO = cut_str($page, 'Set-Cookie: LOGIN_INFO=', ';');
$utube_login_cookie = $cookie.'; LOGIN_INFO='.$cookie_LOGIN_INFO;

$url = 'http://'.$Url['host'].'/my_videos_upload';
$Url = parse_url($url);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""),  $url, $utube_login_cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
is_notpresent($page ,$youtube_login, 'Error retrieving upload form.');
print_r($page); exit;
$action_url = cut_str($page, '<form class="file-form" enctype="multipart/form-data" method="post" action="', '">');
if (empty($action_url)) html_error("Error retrive action url!");
$Url = parse_url($action_url);
$dkv_val = cut_str($page, 'Set-Cookie: dkv=', ';');
$dkv_cookie = 'dkv='.$dkv_val;
$upload_cookie = $utube_login_cookie.'; '.$dkv_cookie;
$return_address = cut_str($page, '<input type="hidden" name="return_address" value="', '">');
$uploader_type = cut_str($page, '<input type="hidden" name="uploader_type" value="', '">');
$upload_key = cut_str($page, '<input type="hidden" name="upload_key" value="', '">');
$session_token = cut_str($page, "\t\tgXSRF_token = '", "';");
$post = array();
$post['uploader_type'] = $uploader_type;
$post['return_address'] = $return_address;
$post['upload_key'] = $upload_key;
$post['action_postvideo'] = '1';
$post['session_token'] = $session_token;
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php
$upfiles = upfile($Url["host"],$Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $url, $upload_cookie, $post, $lfile, $lname, "field_uploadfile");
is_page($upfiles);
is_notpresent($upfiles, 'HTTP/1.0 303 See Other', 'Error - Upload Failed');
if (!preg_match("%ocation: .+&video_id=(.+)\r\n%", $upfiles, $video_id)) html_error('Couldn\'t find the video ID - perhaps the upload failed?');
$download_link = 'http://www.youtube.com/watch?v='.$video_id[1];

function biscottiDiKaox($content)
 {
 preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
 foreach ($matches[1] as $coll) {
 $bis0=split(";",$coll);
 $bis1=$bis0[0]."; ";
 $bis2=split("=",$bis1);
 $cek=" ".$bis2[0]."=";
 if(strpos($bis1,"=deleted") || strpos($bis1,$cek.";")) {
 }else{
if  (substr_count($bis,$cek)>0)
{$patrn=" ".$bis2[0]."=[^ ]+";
$bis=preg_replace("/$patrn/"," ".$bis1,$bis);
} else {$bis.=$bis1;}}}
$bis=str_replace("  "," ",$bis);
return rtrim($bis);}
// written by kaox 26/05/09
?>
<script>document.getElementById('progressblock').style.display='none';</script>