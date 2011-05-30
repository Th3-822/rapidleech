<?php
if (!defined('RAPIDLEECH')) { require_once("index.html"); exit; }

### Here is a list of checkable links ###
// Only update here if needed so we can synchronize it with templates
$sites = array(
	array('name' => 'FileSonic.com', 'link' => '^http://(www\.)?filesonic\.[^/]+/file/[a-z]?[0-9]+', 'regex' => 'File size:'),
	array('name' => 'Rapidshare.com', 'link' => 'rapidshare\.com/files/', 'regex' => '(FILE DOWNLOAD|This file is larger than 200 Megabyte)'),
	array('name' => 'Megashares.com', 'link' => 'megashares\.com/\?d01=', 'regex' => 'Click here to download'),
	array('name' => 'Megaupload.com', 'link' => 'megaupload\.com/([a-z]{2}/)?\?d=', 'regex' => '(Filename:)|(All download slots assigned to your country)'),
	array('name' => 'Filefactory.com', 'link' => 'filefactory\.com/file/', 'regex' => '(download link)|(Please try again later)'),
	array('name' => 'Rapidshare.de', 'link' => 'rapidshare\.de/files/', 'regex' => 'You want to download'),
	array('name' => 'Mediafire.com', 'link' => 'mediafire\.com/(download\.php)?\?', 'regex' => 'You requested'),
	array('name' => 'Netload.in', 'link' => 'netload\.in/datei[0-9a-z]{32}/', 'regex' => 'download_load'),
	array('name' => 'Depositfiles.com', 'link' => 'depositfiles\.com/([a-z]{2}/)?files/', 'regex' => 'File Name', 'pattern' => '@(com\/files\/)|(com\/[a-z]{2}\/files\/)@i', 'replace' => '"com/en/files/'),
	array('name' => 'Sendspace.com', 'link' => 'sendspace\.com/file/', 'regex' => 'The download link is located below.'),
	array('name' => 'iFile.it', 'link' => 'ifile\.it/', 'Request Ticket'),
	array('name' => 'USAUpload.net', 'link' => 'usaupload\.net/d/', 'regex' => 'This is the download page for file'),
	array('name' => 'Badango.com', 'link' => 'badongo\.com/([a-z]{2}/)?(file)|(vid)/', 'regex' => 'fileBoxMenu'),
	array('name' => 'Uploading.com', 'link' => 'uploading\.com/files/', 'regex' => 'Download file'),
	array('name' => 'Savefile.com', 'link' => 'savefile\.com/files/', 'regex' => 'link to this file'),
	array('name' => 'Cocoshare.cc', 'link' => 'cocoshare\.cc/[0-9]+/', 'regex' => 'Filesize:'),
	array('name' => 'Axifile.com', 'link' => 'axifile\.com/?', 'regex' => 'You have request', 'pattern' => '@com\?@i', 'com/?'),
	array('name' => 'Turboupload.com', 'link' => '(d\.turboupload\.com/)|(turboupload.com/download/)', 'regex' => '(Please wait while we prepare your file.)|(You have requested the file)'),
	array('name' => 'Files.to', 'link' => 'files\.to/get/', 'regex' => 'You requested the following file'),
	array('name' => 'Gigasize.com', 'link' => 'gigasize\.com/get\.php\?d=', 'regex' => 'Downloaded'),
	array('name' => 'Ziddu.com', 'link' => 'ziddu\.com/', 'regex' => 'Download Link'),
	array('name' => 'ZShare.net', 'link' => 'zshare\.net/(download|audio|video)/', 'regex' => 'Last Download'),
	array('name' => 'Uploaded.to', 'link' => 'uploaded\.to/(\?id=|file/)', 'regex' => 'Filename:'),
	array('name' => 'Filefront.com', 'link' => 'filefront\.com/', 'regex' => 'http://static4.filefront.com/ffv6/graphics/b_download_still.gif'),
	array('name' => 'UploadPalace.com', 'link' => 'uploadpalace\.com/[a-zA-Z]{2}/file/[0-9]+/', 'regex' => 'Filename:'),
	array('name' => 'SpeedyShare.com', 'link' => 'speedyshare\.com/[0-9]+\.html', '/data/'),
	array('name' => 'Momupload.com', 'link' => 'momupload\.com/files/', 'regex' => 'You want to download the file'),
	array('name' => 'Rnbload.com', 'link' => 'rnbload\.com/file/', 'regex' => 'Filename:'),
	array('name' => 'iFolder.ru', 'link' => 'ifolder\.ru/[0-9]+', 'regex' => 'ints_code'),
	array('name' => 'ADrive.com', 'link' => 'adrive\.com/public/', 'regex' => 'view'),
	array('name' => 'Easy-share.com', 'link' => 'easy-share\.com', 'regex' => 'file url:'),
	array('name' => 'BitRoad.net', 'link' => 'bitroad\.net/download/[0-9a-z]+/', 'regex' => 'File:'),
	array('name' => 'Megarotic.com', 'link' => 'megarotic\.com/([a-z]{2}/)?\?d=', 'regex' => '(Filename:)|(All download slots assigned to your country'),
	array('name' => 'Egoshare.com', 'link' => 'egoshare\.com', 'regex' => 'You have requested'),
	array('name' => 'Flyupload.com', 'link' => 'flyupload\.flyupload.com/get\?fid', 'regex' => 'Download Now'),
	array('name' => 'Megashare.com', 'link' => 'megashare\.com/[0-9]+', 'regex' => 'Free'),
);
?>