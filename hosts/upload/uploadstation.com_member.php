<div id="info" align="center">Uploading..</div>
<?php
$login['loginUserName']         = $premium_acc["uploadstation"]['user'];//OR INPUT USERNAME HERE eg. ="username";
$login['loginUserPassword']     = $premium_acc["uploadstation"]['pass'];//OR INPUT PASSWORD HERE eg. ="password";
$login['autoLogin']             = 'on';
$login['loginFormSubmit']       = 'Login';
$ip = gethostbyname('upload.uploadstation.com');
$Url=parse_url('http://www.uploadstation.com/login.php');
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, 0, $login, 0, $_GET["proxy"], $pauth);

if (!strpos($page,'Welcome <strong>')){html_error ("Login Error.");}
$cookies = GetCookies($page);

$upload_url=parse_url('http://www.uploadstation.com/upload.php');
$page_upload = geturl($upload_url["host"], $upload_url["port"] ? $upload_url["port"] : 80, $upload_url["path"] . ($upload_url["query"] ? "?" . $upload_url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);
$user_id = cut_str($page_upload, 'action="http://upload.uploadstation.com/upload/','/1/"');


$getMySess = file_get_contents("http://{$ip}/upload/{$user_id}/1/?callback");
preg_match("#sessionId:'(.+)'}#",$getMySess,$callbacks);
$session_id = $callbacks[1];

$action_url = "http://{$ip}/upload/{$user_id}/1/{$session_id}/";

$url = parse_url($action_url);
$post['filename'] = $lname;
$post['name'] = 'file';

$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://www.uploadstation.com/upload.php', $cookie, 0, $lfile, $lname, "file");

is_page($upfiles);

$download_link = "http://www.uploadstation.com/file/".trim(cut_str($upfiles, '"shortenCode":"', '"'));
$delete_link = $download_link."/delete/".trim(cut_str($upfiles, '"deleteCode":"', '"'));

?>
<script>document.getElementById('info').style.display='none';</script>
