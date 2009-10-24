<?php
if (!defined('RAPIDLEECH'))
{
	require_once("index.html");
	exit;
}

$premium_acc = array();
### Remove '//' from the beginning and enter your username and password for enable premium account ###
//$premium_acc["rs_com"] = array('user' => 'your username', 'pass' => 'your password');
// For multiple rapidshare premium accounts only - if you are using multiple accounts below, comment out the line above
//$premium_acc["rs_com"] = array(array('user' => 'your username1', 'pass' => 'your password1'),array('user' => 'your username2', 'pass' => 'your password2'),array('user' => 'your username3', 'pass' => 'your password3'));
//$premium_acc["rs_de"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["megaupload"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["netload"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["megashare"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["gigasize"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["share_online"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["megashares"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["vBulletin_acc"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["uploaded_to"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filefactory"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["sendspace"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["uploaded_to"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["depositfiles"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["easyshare"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["btaccel"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["hotfile"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["ifile_it"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["sharingmatrix"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["letitbit"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["turbobit"] = array('pass' => 'your password');

$no_cache = true; # true - Prohibition by Browser; otherwise allowed (You should leave this intact unless you know what you are doing)
//$images_via_php = false; # true - RapidShare images are downloaded through the script, but it requires ssl support; turn it off if you can't see the image.
$redir = true; # true - Redirect passive method (You should leave this intact unless you know what you are doing)
$show_all = true; # true - To show all files in the catalog, false to hide it

$login = false; # false - Authorization mode is off, true - on
$users = array('test' => 'test'); # false - Authorization mode is off, enter the username and password in the given way

$forbidden_filetypes = array('.htaccess', '.htpasswd', '.php', '.php3', '.php4', '.php5', '.phtml', '.asp', '.aspx', '.cgi'); # Enter the forbidden filetypes in the given way, if you want to allow all then after the = put two '
$rename_these_filetypes_to = '.xxx'; # If you want to prevent them from downloading then set this to false without the '.
$check_these_before_unzipping = true;

$download_dir = "files/"; // This is where your downloaded files are saved
$download_dir_is_changeable = false; // Set it to true to allow users to change the download dir

### /\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\ ###

### CPU,  Memory & Time Info ###
$server_info = true;
### Ajax Auto Refresh Server Info ###
$ajax_refresh = true;

### Megaupload cookie ###
//$mu_cookie_user_value = '';  // like: b658b369856766f621ca292fac113a5c, that contains username&pass of premium account and can be shared to others, because it's an encrypted value.

### Imageshack Torrent Account ###
//$imageshack_acc = array('user' => 'your username', 'pass' => 'your password');

###Auto Download Premium Account###
//$premium_acc["au_dl"] = array('user' => 'your username', 'pass' => 'your password'); # Remove '//' from the beginning and enter your username and password for rapidshare.de premium account

### Bandwidth Saving ###
$bw_save = true;

### Disable Delete & Rename Action ###
$options['disable_deleting'] = false; //Set it to True to disallow users to DELETE OR RENAME files(useful for public servers)

### Auto-Delete ###
$delete_delay = 0; // Time in SECONDS before downloaded files are deleted // 0 = disabled

### Auto-Rename ###
$rename_prefix = ''; //i.e : prefix_filename.ext
$rename_suffix = ''; //i.e : filename_suffix.ext

### Template ###
$options['template_used'] = "plugmod";
$options['default_language'] = "en";

### File Actions ###
$options["disable_actions"] = false; // Disable all actions // false = use individual settings below
$options["disable_delete"] = false; // Disable Delete action // false = allow
$options["disable_email"] = false; // Disable Email action // false = allow
$options["disable_ftp"] = false; // Disable FTP action // false = allow
$options["disable_mass_email"] = true; // Disable Mass Email action // false = allow
$options["disable_mass_rename"] = false; // Disable Mass Rename action // false = allow
$options["disable_md5"] = false; // Disable MD5 action // false = allow
$options["disable_merge"] = false; // Disable Merge action // false = allow
$options["disable_rename"] = false; // Disable Rename action // false = allow
$options["disable_split"] = false; // Disable Split action // false = allow
$options["disable_tar"] = false; // Disable TAR action // false = allow
$options["disable_untar"] = false; // Disable Untar action // false = allow
$options["disable_unzip"] = false; // Disable Unzip action // false = allow
$options["disable_upload"] = false; // Disable Upload action // false = allow
$options["disable_zip"] = false; // Disable ZIP action // false = allow
$options['disable_list'] = false; // Disable list links // false = allow

### Here is a list of checkable links ###
// Only update here if needed so we can synchronize it with templates
$sites = array(
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