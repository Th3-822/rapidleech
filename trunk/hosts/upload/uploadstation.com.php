<div id="info" align="center">Uploading..</div>
<?php
$ip = gethostbyname('upload.uploadstation.com');
$getMySess = file_get_contents("http://{$ip}/upload/-1/1/?callback");
preg_match("#sessionId:'(.+)'}#",$getMySess,$callbacks);
$session_id = $callbacks[1];

$action_url = "http://{$ip}/upload/-1/1/{$session_id}/";

$url = parse_url($action_url);
$post['filename'] = $lname;
$post['name'] = 'file';

$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://www.uploadstation.com/upload.php', $cookie, 0, $lfile, $lname, "file");

is_page($upfiles);

$download_link = "http://www.uploadstation.com/file/".trim(cut_str($upfiles, '"shortenCode":"', '"'));
$delete_link = $download_link."/delete/".trim(cut_str($upfiles, '"deleteCode":"', '"'));

?>
<script>document.getElementById('info').style.display='none';</script>
