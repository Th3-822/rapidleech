<?php
if (!defined('RAPIDLEECH')) { require_once("index.html"); exit; }

### Here is a list of checkable links ###
// Only update here if needed so we can synchronize it with templates
$sites = array(
	array('name' => 'Rapidshare.com'), // RS is checked in ajax.php (But we need this for show the name in linkchecker)
	array('name' => 'Megaupload.com', 'link' => '^http://(www\.)?megaupload\.com/([a-z]{2}/)?\?d=\w{8}', 'regex' => '(File name:)|(All download slots assigned to your country)|(trying to download is larger)', 'szregex' => 'File size:</strong> ([\d|\.]+)\s+(\w{1,5})', 'pattern' => '/\/([a-z]{2}\/)\?d=/i', 'replace' => '/?d='),
	array('name' => 'Filefactory.com', 'link' => '^http://(www\.)?filefactory\.com/file/\w+/?', 'regex' => '(download link)|(temporarily limited)', 'szregex' => "([\d|\.]+)\s+(\w{1,5}) file uploaded"),
	array('name' => 'Megaporn.com', 'link' => '^http://(www\.)?megarotic\.com/([a-z]{2}/)?\?d=\w{8}', 'regex' => '(File name:)|(All download slots assigned to your country', 'szregex' => 'File size:</strong> ([\d|\.]+)\s+(\w{1,5})', 'pattern' => '/\/([a-z]{2}\/)\?d=/i', 'replace' => '/?d='),
	array('name' => 'FileServe.com', 'link' => '^http://(www\.)?fileserve\.com/file/\w+/?', 'regex' => '(/images/down_arrow\.gif)|(slower_download_btn)', 'szregex' => '([\d|\.]+)\s+(\w{1,5})</strong> \| Uploaded on'),
	array('name' => 'Hotfile.com', 'link' => '^http://(www\.)?hotfile\.com/dl/(\d+/\w+)/', 'regex' => '(Downloading:)|("arrow_down")', 'szregex' => '\|</span> <strong>([\d|\.]+)\s+(\w{1,5})'),
	array('name' => 'ADrive.com', 'link' => '^http://(www\.)?adrive\.com/public/\w+\.html', 'regex' => '\/public\/view\/'),
	array('name' => 'FileSonic.com', 'link' => '^http://(www\.)?filesonic\.[^/]+/file/[a-z]?[0-9]+', 'regex' => 'File size:', 'szregex' => 'class="size">([\d|\.]+)\s+(\w{1,5})<'), // Copied from r356 by beausoleilm - [T-8] Added regex for filesize
	array('name' => 'Depositfiles.com', 'link' => '^http://(www\.)?depositfiles\.com/([a-z]{2}/)?files/', 'regex' => 'File name:', 'szregex' => 'File size: <b>([\d|\.]+)(?:\s|(?:&nbsp;))+(\w{1,5})', 'pattern' => '@(com/files/)|(com/[a-z]{2}/files/)@i', 'replace' => 'com/en/files/', 'options' => array('cookie' => 'lang_current=en')),
	array('name' => 'Crocko.com', 'link' => '^http://((w\d+\.)|(www\.))?((easy-share)|(crocko))\.com/\d+(\.html)?', 'regex' => 'Download:', 'szregex' => 'class="tip1"><span class="inner">([\d|\.]+)\s+(\w{1,5})<', 'pattern' => '@((w\d+\.)|(www\.))?easy-share\.com@i', 'replace' => '${3}crocko.com'),
	array('name' => 'Uploading.com', 'link' => '^http://(www\.)?uploading\.com/files/\w+/?', 'regex' => '(File download)|(File size: )', 'szregex' => 'File size: ([\d|\.]+)\s+(\w{1,5})', 'options' => array('cookie' => 'lang=1')),
	array('name' => 'ZShare.net', 'link' => '^http://(www\.)?zshare\.net/(download|audio|video)/\w+/?', 'regex' => '(File Size: )|(/delete\.html\?)', 'szregex' => '(?:(?:Video)|(?:File)) Size: <font color="#\w{6}">([\d|\.]+)\s*(\w{1,5})<'),
	array('name' => 'Netload.in', 'link' => '^http://(www\.)?netload\.in/datei\w+((\.htm)|(/))?', 'regex' => '(download_load)|(dl_first_file_download)', 'szregex' => 'style="color: #\w{6};">, ([\d|\.]+)\s+(\w{1,5})<', 'pattern' => '@\.in/(datei\w+)/.*@i', 'replace' => '.in/${1}.htm'),
	array('name' => 'Uploaded.to', 'link' => '^http://(www\.)?((uploaded\.to)|(ul\.to))/\w+', 'regex' => 'Download file:', 'szregex' => '\(([\d|\.|\,]+)\s+(\w{1,5})\) - uploaded\.to</title>'),
	array('name' => 'UploadStation.com', 'link' => '^http://(www\.)?uploadstation\.com/file/\w+', 'regex' => 'Upload date:', 'szregex' => 'File size: <b>([\d|\.]+)\s+(\w{1,5})<'),
	array('name' => 'Letitbit.net', 'link' => '^http://(www\.)?letitbit\.net/download/[^/]+/.+\.html', 'regex' => 'file_info_sssize=\d+;', 'szregex' => 'file_info_sssize=(\d+);', 'options' => array('bytes' => true)),
	array('name' => 'Turbobit.net', 'link' => '^http://(www\.)?turbobit\.net/\w+((\.html)|(/.+\.html))', 'regex' => 'Download file:', 'szregex' => '</span>\t+\(([\d|\.|\,]+)\s+(\w{1,5})\)', 'options' => array('cookie' => 'user_lang=en')),
	array('name' => '2shared.com', 'link' => '^http://(www\.)?2shared\.com/file/\w+/.+\.html?', 'regex' => '(File size:)|(Please enter password)', 'szregex' => 'File size:</span>\r?\n\s+([\d|\.|\,]+)\s+(\w{1,5})'),
	array('name' => 'Badongo.com', 'link' => '^http://(www\.)?badongo\.com/([a-z]{2}/)?((file)|(vid))/\d+', 'regex' => "'((file)|(vid))_fileinfo'", 'szregex' => 'class="ffileinfo">[^\|]+\|[^:|<]+:\s+([\d|\.|\,]+)\s+(\w{1,5})<', 'options' => array('cookie' => 'badongoL=en')),
	array('name' => 'Wupload.com', 'link' => '^http://(www\.)?wupload\.[^/]+/file/[a-z]?[0-9]+', 'regex' => 'Filename:', 'szregex' => 'class="size">([\d|\.]+)\s+(\w{1,5})<'), // Copied from r364. Added by beausoleilm - [T-8] Added regex for filesize
	array('name' => 'Mediafire.com', 'link' => '^http://(www\.)mediafire.com/(((download\.php)?\?)|(file/))(\w+)', 'regex' => '(class="download_file_title")', 'szregex' => 'id="sharedtabsfileinfo1-fs" value="([\d|\.]+)\s+(\w{1,5})"'),
	array('name' => 'Oron.com', 'link' => "^http://(www\.)?oron\.com/\w+", 'regex' => "Filename:", 'szregex' => 'File size: ([\d|\.|\,]+)\s+(\w{1,5})<', 'options' => array('cookie' => 'lang=english')),
	array('name' => 'Shareflare.net', 'link' => "^http://(www\.)?shareflare\.net/download/[^/]+/.+\.html?", 'regex' => 'file_info_sssize=\d+;', 'szregex' => 'file_info_sssize=(\d+);', 'options' => array('bytes' => true)),
	array('name' => 'Bitshare.com', 'link' => "^http://(www\.)?bitshare\.com/((files/)|(\?f=))\w+(/.+(\.html)?)?", 'regex' => 'class="download"', 'szregex' => ' - ([\d|\.|\,]+)\s+(\w{1,2})\w{3}</h1>'),
	array('name' => 'Enterupload.com', 'link' => "^http://(www\.)?enterupload\.com/\w+(/.+(\.html)?)?", 'regex' => "File size:", 'szregex' => 'File size:\s+([\d|\.|\,]+)\s+(\w{1,5})', 'pattern' => '@/$@')
	//array('name' => 'Archive.org', 'link' => "^http://(www\.)?archive\.org/details/.+", 'regex' => "All Files:")
);
?>