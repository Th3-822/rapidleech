<?php
if (!defined('RAPIDLEECH')) { require_once("index.html"); exit; }
$premium_acc = array();

### Remove '//' from the beginning and enter your username and password for enable premium account ###
//$premium_acc["rapidshare_com"] = array('user' => 'your username', 'pass' => 'your password');
// For multiple rapidshare premium accounts only - if you are using multiple accounts below, comment out the line above
//$premium_acc["rapidshare_com"] = array(array('user' => 'your username1', 'pass' => 'your password1'),array('user' => 'your username2', 'pass' => 'your password2'),array('user' => 'your username3', 'pass' => 'your password3'));
//$premium_acc["netload_in"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["cramit_in"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["megashare_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["gigasize_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["share-online_biz"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["megashares_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["vBulletin_acc"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["uploaded_to"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filefactory_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filedude_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["sendspace_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["depositfiles_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["crocko_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["hotfile_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["ifile_it"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filesonic_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["uploading_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["ugotfile_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["freakshare_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["oron_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["movshare_net"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["veehd_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["hellshare_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["bitshare_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["mediafire_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["uploadstation_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["turbobit_net"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["4shared_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["wupload_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filefat_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filejungle_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["youtube_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filesmonster_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["letitbit_net"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filedino_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filepost_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["fileape_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filesflash_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["extabit_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["netuploaded_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["furk_net"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["bayfiles_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["jumbofiles_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["fileserving_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["file4sharing_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["bulletupload_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["speedyshare_com"] = array('user' => 'your username', 'pass' => 'your password');
//$premium_acc["filegaze_com"] = array('user' => 'your username', 'pass' => 'your password');

###Premium cookie configuration, dont use this if you have already set another premium account config
//$premium_acc["depositfiles_com"] = array('cookie' => 'your premium cookie');
//$premium_acc["hotfile_com"] = array('cookie' => 'your premium cookie');
//$premium_acc["rapidshare_com"] = array('cookie' => 'your premium cookie');
//$premium_acc["uploaded_to"] = array('cookie' => 'your premium cookie');
//$premium_acc["uploading_com"] = array('cookie' => 'your premium cookie');
//$premium_acc["netload_in"] = array('cookie' => 'your premium cookie');

###Premium key configuration, dont use this if you have already set another premium account config
//$premium_acc["letitbit_net"] = array('pass' => 'your password');
//$premium_acc["vip_file_com"] = array('pass' => 'your password');
//$premium_acc["shareflare_net"] = array('pass' => 'your password');
//$premium_acc["fileflyer_com"] = array('pass' => 'your password');


###Auto Download Premium Account###
//$premium_acc["au_dl"] = array('user' => 'your username', 'pass' => 'your password'); # Remove '//' from the beginning and enter your username and password for rapidshare.de premium account

#Secret key for cookie encryption
#Make up a random one to protect your premium cookies (max length: 56). Example: $secretkey = 'UijSY5wjP1Ii'; - DO NOT use this example $secretkey, or your premium accounts/cookies could be stolen!!
#IF THIS IS NOT SET BEFORE YOU USE PREMIUM SERVICES, YOU WILL BE WARNED BY THE RAPIDLEECH SCRIPT. OTHERWISE YOUR PREMIUM ACCOUNTS AND/OR COOKIES COULD BE COMPROMISED!
$secretkey = '';
?>