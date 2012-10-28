<?php
if (!defined('RAPIDLEECH')) { require_once('ndex.html'); exit; }

$sites = array();
### Here is a list of checkable links ###
// Only update here if needed so we can synchronize it with templates

$sites['Rapidshare.com'] = array(); // RS is checked in ajax.php (But we need this for show the name in linkchecker)
$sites['Share-Online.biz'] = array(); // This too ^^
$sites['Mediafire.com'] = array();
$sites['Filefactory.com'] = array('link' => '^http://(www\.)?filefactory\.com/file/\w+/?', 'regex' => '(download link)|(temporarily limited)', 'szregex' => "([\d|\.]+)\s+(\w{1,5}) file uploaded");
$sites['Hotfile.com'] = array('link' => '^http://(www\.)?hotfile\.com/dl/(\d+/\w+)/', 'regex' => '(Downloading:)|("arrow_down")', 'szregex' => '\|</span> <strong>([\d|\.]+)\s+(\w{1,5})');
$sites['ADrive.com'] = array('link' => '^http://(www\.)?adrive\.com/public/\w+\.html', 'regex' => '\/public\/view\/');
$sites['Depositfiles.com'] = array('link' => '^http://(www\.)?depositfiles\.com/([a-z]{2}/)?files/', 'regex' => 'File name:', 'szregex' => 'File size: <b>([\d|\.]+)(?:\s|(?:&nbsp;))+(\w{1,5})', 'pattern' => '@(com/files/)|(com/[a-z]{2}/files/)@i', 'replace' => 'com/en/files/', 'options' => array('cookie' => 'lang_current=en'));
$sites['Crocko.com'] = array('link' => '^http://((w\d+\.)|(www\.))?((easy-share)|(crocko))\.com/\d+(\.html)?', 'regex' => 'Download:', 'szregex' => 'class="tip1"><span class="inner">([\d|\.]+)\s+(\w{1,5})<', 'pattern' => '@((w\d+\.)|(www\.))?easy-share\.com@i', 'replace' => '${3}crocko.com');
$sites['Uploading.com'] = array('link' => '^http://(www\.)?uploading\.com/files/\w+/?', 'regex' => 'class="file_size">([\d|\.]+)\s+(\w{1,5})</', 'szregex' => true, 'options' => array('cookie' => 'lang=1'));
$sites['ZShare.net'] = array('link' => '^http://(www\.)?zshare\.net/(download|audio|video)/\w+/?', 'regex' => '(File Size: )|(/delete\.html\?)', 'szregex' => '(?:(?:Video)|(?:File)) Size: <font color="#\w{6}">([\d|\.]+)\s*(\w{1,5})<', 'options' => array('fixsize' => true));
$sites['Netload.in'] = array('link' => '^http://(www\.)?netload\.in/datei\w+((\.htm)|(/))?', 'regex' => '(download_load)|(dl_first_file_download)', 'szregex' => 'style="color: #\w{6};">, ([\d|\.]+)\s+(\w{1,5})<', 'pattern' => '@\.in/(datei\w+)/.*@i', 'replace' => '.in/${1}.htm');
$sites['Uploaded.to'] = array('link' => '^http://(www\.)?((uploaded\.to)|(ul\.to))/\w+', 'regex' => 'Download file:', 'szregex' => '\(([\d|\.|\,]+)\s+(\w{1,5})\) - uploaded\.to</title>');
$sites['UploadStation.com'] = array('link' => '^http://(www\.)?uploadstation\.com/file/\w+', 'regex' => 'Upload date:', 'szregex' => 'File size: <b>([\d|\.]+)\s+(\w{1,5})<');
$sites['Letitbit.net'] = array('link' => '^http://(www\.)?letitbit\.net/download/[^/]+/.+\.html', 'regex' => 'file_info_sssize=(\d+);', 'szregex' => true, 'options' => array('bytes' => true));
$sites['Turbobit.net'] = array('link' => '^http://(www\.)?turbobit\.net/\w+((\.html)|(/.+\.html))', 'regex' => 'Download file:', 'szregex' => '</span>\t+\(([\d|\.|\,]+)\s+(\w{1,5})\)', 'options' => array('cookie' => 'user_lang=en'));
$sites['2shared.com'] = array('link' => '^http://(www\.)?2shared\.com/[a-z]+/\w+/.+\.html?', 'regex' => '(File size:)|(Please enter password)|(Loading image)', 'szregex' => '(?:(?:File size:</span>\r?\n\s+)|(?:Loading image\s+\())([\d|\.|\,]+)\s+(\w{1,5})');
$sites['4shared.com'] = array('link' => '^http://(www\.)?4shared\.com/[^/]+/\w+/.+\.html?', 'regex' => '(<h1 class=\"fileName[\s|\"])|(Please enter a password)', 'szregex' => '</a>[\r\n\s\t]*([\d|\.|\,]+)\s+(\w{1,5})\s*\|', 'pattern' => '@4shared\.com/get/@i', 'replace' => '4shared.com/file/', 'options' => array('cookie' => '4langcookie=en', 'fixsize' => true, 'fixsizeP' => ','));
$sites['Badongo.com'] = array('link' => '^http://(www\.)?badongo\.com/([a-z]{2}/)?((file)|(vid))/\d+', 'regex' => "'((file)|(vid))_fileinfo'", 'szregex' => 'class="ffileinfo">[^\|]+\|[^:|<]+:\s+([\d|\.|\,]+)\s+(\w{1,5})<', 'options' => array('cookie' => 'badongoL=en'));
$sites['Oron.com'] = array('link' => "^http://(www\.)?oron\.com/\w+", 'regex' => "Filename:", 'szregex' => 'File size: ([\d|\.|\,]+)\s+(\w{1,5})<', 'options' => array('cookie' => 'lang=english'));
$sites['Shareflare.net'] = array('link' => "^http://(www\.)?shareflare\.net/download/[^/]+/.+\.html?", 'regex' => 'file_info_sssize=\d+;', 'szregex' => 'file_info_sssize=(\d+);', 'options' => array('bytes' => true));
$sites['Bitshare.com'] = array('link' => "^http://(www\.)?bitshare\.com/((files/)|(\?f=))\w+(/.+(\.html)?)?", 'regex' => 'class="download"', 'szregex' => ' - ([\d|\.|\,]+)\s+(\w{1,2})\w{3}</h1>');
$sites['Enterupload.com'] = array('link' => "^http://(www\.)?enterupload\.com/\w+(/.+(\.html)?)?", 'regex' => "File size:", 'szregex' => 'File size:\s+([\d|\.|\,]+)\s+(\w{1,5})', 'pattern' => '@/$@');

?>