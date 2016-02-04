<?php

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$login = $not_done = false;
$domain = 'demo.ovh.eu';
$referer = "http://$domain/";

// Retrive upload ID
echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrieving upload ID</div>\n";

$page = geturl($domain, 80, '/en/', $referer, 0, 0, 0, $_GET['proxy'], $pauth);is_page($page);
$cookie = GetCookiesArr($page);

if (!preg_match('@action="((https?://(?:[\w\-]+\.)*demo\.ovh\.eu)?(/[^\"\'<>\s]*)?)"@i', $page, $up)) html_error('Error: Cannot find upload server.');

$post = array();
$post['step'] = trim(cut_str($page, 'name="step" value="', '"'));
$post['comment_author'] = ''; // Max: 50 Chars
$post['comment'] = 'Uploaded with Rapidleech.'; // Max: 200 Chars

$up_url = (empty($up[2]) ? "http://$domain".(empty($up[3]) ? '/' : '').$up[1] : $up[1]);

// Uploading
echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

$url = parse_url($up_url);
$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, $post, $lfile, $lname, 'upload_filename', '', $_GET['proxy'], $pauth);

// Upload Finished
echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

is_page($upfiles);

if (!preg_match('@\nLocation: https?://demo\.ovh\.eu/\w+/([^\r\n]+)@i', $upfiles, $redir)) html_error('Download link not found.');
$page = geturl($domain, 80, '/en/'.$redir[1], $up_url, $cookie, 0, 0, $_GET['proxy'], $pauth);is_page($page);
if (($timeleft = cut_str($page, "Your files will be available for: ", "<")) != false) echo "\n<table width='100%' border='0'>\n<tr><td width='100' nowrap='nowrap' align='right'>Your file will be available for: <td width='80%'><input value='".htmlspecialchars($timeleft, ENT_QUOTES)."' class='upstyles-dllink' readonly='readonly' /></tr>\n</table>\n";

$download_link = 'http://demo.ovh.eu/en/'.$redir[1];

//[07-2-2015] Written by Th3-822

?>
