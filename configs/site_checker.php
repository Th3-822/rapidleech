<?php
if (!defined('RAPIDLEECH')) { require_once("index.html"); exit; }

### Here is a list of checkable links ###
// Only update here if needed so we can synchronize it with templates
$sites = array(
//	array('name' => 'Rapidshare.com', 'link' => '^https?://(www\.)?rapidshare\.com/(files/)|(#!download)', 'regex' => '^[^(ERROR: )]|(^ERROR: ((You need RapidPro)|(This file is too big)|(Please stop)|(Too many users)|(All free)))', 'pattern' => '/#!download\|[^\|]+\|(\d+)\|([^\|]+)(.*)/i', 'replace' => 'files/${1}/${2}'), // RS may lock temporally your IP if too many links (~1000 or more) are checked in a short time (>3 minutes). Need HTTPS support.
	array('name' => 'Megaupload.com', 'link' => '^http://(www\.)?megaupload\.com/([a-z]{2}/)?\?d=\w{8}', 'regex' => '(File name:)|(All download slots assigned to your country)|(trying to download is larger)', 'pattern' => '/\/([a-z]{2}\/)\?d=/i', 'replace' => '/?d='),
	array('name' => 'Filefactory.com', 'link' => '^http://(www\.)?filefactory\.com/file/\w+/?', 'regex' => '(download link)|(temporarily limited)'),
	array('name' => 'Megaporn.com', 'link' => '^http://(www\.)?megarotic\.com/([a-z]{2}/)?\?d=\w{8}', 'regex' => '(File name:)|(All download slots assigned to your country', 'pattern' => '/\/([a-z]{2}\/)\?d=/i', 'replace' => '/?d='),
	array('name' => 'FileServe.com', 'link' => '^http://(www\.)?fileserve\.com/file/\w+/?', 'regex' => '(/images/down_arrow\.gif)|(slower_download_btn)'),
	array('name' => 'Hotfile.com', 'link' => '^http://(www\.)?hotfile\.com/dl/(\d+/\w+)/', 'regex' => '(Downloading:)|("arrow_down")'),
	array('name' => 'ADrive.com', 'link' => '^http://(www\.)?adrive\.com/public/\w+\.html', 'regex' => '\/public\/view\/'),
	array('name' => 'FileSonic.com', 'link' => '^http://(www\.)?filesonic\.[^/]+/file/[a-z]?[0-9]+', 'regex' => 'File size:'), // Copied from r356 by beausoleilm
	array('name' => 'Depositfiles.com', 'link' => '^http://(www\.)?depositfiles\.com/([a-z]{2}/)?files/', 'regex' => 'File name:', 'pattern' => '@(com/files/)|(com/[a-z]{2}/files/)@i', 'replace' => 'com/en/files/', 'options' => array('cookie' => 'lang_current=en')),
	array('name' => 'Easy-share.com', 'link' => '^http://((w\d+\.)|(www\.))?easy-share\.com/\d+(\.html)?', 'regex' => 'You are requesting:'),
	array('name' => 'Uploading.com', 'link' => '^http://(www\.)?uploading\.com/files/\w+/?', 'regex' => '(File download)|(File size: )', 'options' => array('cookie' => 'lang=1')),
	array('name' => 'ZShare.net', 'link' => '^http://(www\.)?zshare\.net/(download|audio|video)/\w+/?', 'regex' => '(File Size: )|(/delete\.html\?)'),
	array('name' => 'Netload.in', 'link' => '^http://(www\.)?netload\.in/datei\w+((\.htm)|(/))?', 'regex' => '(download_load)|(dl_first_file_download)', 'pattern' => '@\.in/(datei\w+)/.*@i', 'replace' => '.in/${1}.htm'),
	array('name' => 'Ul.to', 'link' => '^http://(www\.)?ul\.to/\w+', 'regex' => 'Download file:'),
	array('name' => 'Uploaded.to', 'link' => '^http://(www\.)?uploaded\.to/\w+', 'regex' => 'Download file:'),
	array('name' => 'UploadStation.com', 'link' => '^http://(www\.)?uploadstation\.com/file/\w+', 'regex' => 'Upload date:'),
	array('name' => 'Letitbit.net', 'link' => '^http://(www\.)?letitbit\.net/download/[^/]+/.+\.html', 'regex' => 'file_info_sssize=\d+;'),
	array('name' => 'Turbobit.net', 'link' => '^http://(www\.)?turbobit\.net/\w+((\.html)|(/.+\.html))', 'regex' => 'Download file:', 'options' => array('cookie' => 'user_lang=en')),
	array('name' => '2shared.com', 'link' => '^http://(www\.)?2shared\.com/file/\w+/.+\.html', 'regex' => '(File size:)|(Please enter password)'),
	array('name' => 'Badongo.com', 'link' => '^http://(www\.)?badongo\.com/file/\d+', 'regex' => "'file_fileinfo'", 'options' => array('cookie' => 'badongoL=en')),
	array('name' => 'Wupload.com', 'link' => '^http://(www\.)?wupload\.[^/]+/file/[a-z]?[0-9]+', 'regex' => 'Filename:'), // Copied from r364. Added by beausoleilm
	array('name' => 'Mediafire.com', 'link' => '^http://(www\.)mediafire.com/(((download\.php)?\?)|(file/))(\w+)', 'regex' => '(Location: ((http://download\d+\.)|(/\?\w+)))|(class="download_file_title")', 'options' => array('follow'=>0)),
	array('name' => 'Oron.com', 'link' => "^http://(www\.)?oron\.com/\w+", 'regex' => "Filename:", 'options' => array('cookie' => 'lang=english')),
	array('name' => 'Shareflare.net', 'link' => "^http://(www\.)?shareflare\.net/download/[^/]+/.+\.html", 'regex' => '"file-desc"'),
	array('name' => 'Bitshare.com', 'link' => "^http://(www\.)?bitshare\.com/files/\w+/.+(\.html)?", 'regex' => 'class="download"'),
	array('name' => 'Enterupload.com', 'link' => "^http://(www\.)?enterupload\.com/\w+(/.+(\.html)?)?", 'regex' => "File size:", 'pattern' => '@/$@'),
	array('name' => 'filejungle.com', 'link' => "^http://(www\.)?filejungle\.com/f/", 'regex' => '<div id="file_name">')
);
?>