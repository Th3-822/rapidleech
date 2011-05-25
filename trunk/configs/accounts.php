<?php
if (!defined('RAPIDLEECH')) { require_once("index.html"); exit; }
$premium_acc = array();

### Remove '//' from the beginning and enter your username and password for enable premium account ###
//$premium_acc["rapidshare_com"] = array('user' => 'your username', 'pass' => 'your password');
// For multiple rapidshare premium accounts only - if you are using multiple accounts below, comment out the line above
//$premium_acc["rapidshare_com"] = array(array('user' => 'your username1', 'pass' => 'your password1'),array('user' => 'your username2', 'pass' => 'your password2'),array('user' => 'your username3', 'pass' => 'your password3'));
//$premium_acc["rapidshare_com"] = array('cookie' => 'Your premium cookie');
//$premium_acc["megaupload_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["netload_in"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["megashare_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["gigasize"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["shareonline_biz"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["megashares"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["vBulletin_acc"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["uploaded_to"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filefactory_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filedude_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["sendspace"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["depositfiles"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["easyshare_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["torrific_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["hotfile_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["hotfile_com"] = array('cookie' => 'your premium cookie');
//$premium_acc["ifile_it"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filesonic_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["uploading"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["storage"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["x7"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["megaporn_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["fileserve_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["ugotfile_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["freakshare_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["oron_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["movshare_net"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["enterupload_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["kickload_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["veehd_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["hellshare_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["bitshare_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["mediafire_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["uploadstation_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["duckload_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["turbobit_net"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["letitbit_net"] = array('pass' => 'your password');
//$premium_acc["vip_file_com"] = array('pass' => 'your password');
//$premium_acc["shareflare_net"] = array('pass' => 'your password');

### Megaupload cookie ###
//$mu_cookie_user_value = '';  // like: b658b369856766f621ca292fac113a5c, that contains username&pass of premium account

### Megaporn cookie ###
//$megaporn_cookie_user_value = '';  // like: b658b369856766f621ca292fac113a5c, that contains username&pass of premium account and can be shared to others, because it's an encrypted value.

### Imageshack Torrent Account ###
//$imageshack_acc = array('user' => 'your username', 'pass' => 'your password');

###Auto Download Premium Account###
//$premium_acc["au_dl"] = array('user' => 'your username', 'pass' => 'your password'); # Remove '//' from the beginning and enter your username and password for rapidshare.de premium account

#Secret key for cookie encryption
#Make up a random one to protect your premium cookies (max length: 56). Example: $secretkey = 'UijSY5wjP1Ii'; - DO NOT use this example $secretkey, or your premium accounts/cookies could be stolen!!
#IF THIS IS NOT SET BEFORE YOU USE PREMIUM SERVICES, YOU WILL BE WARNED BY THE RAPIDLEECH SCRIPT. OTHERWISE YOUR PREMIUM ACCOUNTS AND/OR COOKIES COULD BE COMPROMISED!
$secretkey = '';
?>