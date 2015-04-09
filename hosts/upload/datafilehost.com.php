<?php

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$login = $not_done = false;
$domain = 'www.datafilehost.com';
$referer = "http://$domain/";

// Retrive upload ID
echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='info' width='100%' align='center'>Retrieving upload ID</div>\n";

$page = geturl($domain, 80, '/', $referer, 0, 0, 0, $_GET['proxy'], $pauth);is_page($page);
$cookie = GetCookiesArr($page);

if (!preg_match('@action="((https?://(?:[\w\-]+\.)*datafilehost\.com)?(/)?[^\"\'<>\s]+)"@i', $page, $up)) html_error('Error: Upload url not found.');

$post = array();
$post['MAX_FILE_SIZE'] = cut_str($page, 'name="MAX_FILE_SIZE" value="', '"');

$up_url = (empty($up[2]) ? "http://$domain".(empty($up[3]) ? '/' : '').$up[1] : $up[1]);

// Uploading
echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";

$url = parse_url($up_url);
$upfiles = upfile($url['host'], defport($url), $url['path'].(!empty($url['query']) ? '?'.$url['query'] : ''), $referer, $cookie, $post, $lfile, $lname, 'upfile', '', $_GET['proxy'], $pauth, 0, $url['scheme']);

// Upload Finished
echo "<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>\n";

is_page($upfiles);

if (!preg_match('@\nLocation: ((https?://(?:[\w\-]+\.)*datafilehost\.com)?/[^\r\n]+)@i', $upfiles, $redir)) html_error('Upload redirect not found.');
$cookie = GetCookiesArr($upfiles, $cookie);

$redir = parse_url((empty($redir[2]) ? 'http://www.datafilehost.com'.$redir[1] : $redir[1]));
$page = geturl($redir['host'], defport($redir), $redir['path'].(!empty($redir['query']) ? '?'.$redir['query'] : ''), $up_url, $cookie, 0, 0, $_GET['proxy'], $pauth, 0, $url['scheme']);is_page($page);

if (!preg_match('@https?://(?:www\.)?datafilehost\.com/d/[^\s\'\"<>/]+@i', $page, $lnk)) html_error('Download link not found.', 0);
$download_link = $lnk[0];
if (preg_match('@https?://(?:www\.)?datafilehost\.com/delete-[^\s\'\"<>/]+@i', $page, $lnk)) $delete_link = $lnk[0];

//[23-3-2014]  Written by Th3-822.

?>