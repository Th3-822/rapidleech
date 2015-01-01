<?php

if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit();
}

function unsetcookies() {
	foreach (func_get_args() as $name) {
		if (!empty($name) && !empty($_COOKIE[$name])) {
			setcookie($name, '', time() - 3600);
			unset($_COOKIE[$name]);
		}
	}
}

if (!empty($_COOKIE['clearsettings'])) unsetcookies('domail', 'email', 'saveto', 'path', 'useproxy', 'proxy', 'proxyuser', 'proxypass', 'split', 'partSize', 'savesettings', 'clearsettings', 'premium_acc', 'premium_user', 'premium_pass');

if (!empty($_GET['savesettings']) && $_GET['savesettings'] == 'on') {
	$expiretime = time() + 800600;
	setcookie('savesettings', '1', $expiretime);

	if (!empty($_GET['domail']) && $_GET['domail'] == 'on' && !empty($_GET['email']) && checkmail($_GET['email'])) {
		setcookie('domail', '1', $expiretime);
		setcookie('email', $_GET['email'], $expiretime);
		if (!empty($_GET['split']) && $_GET['split'] == 'on') {
			setcookie('split', '1', $expiretime);
			if (!empty($_GET['partSize']) && is_numeric($_GET['partSize'])) setcookie('partSize', $_GET['partSize'], $expiretime);
			else unsetcookies('partSize');
			if (!empty($_GET['method']) && in_array($_GET['method'], array('tc', 'rfc'))) setcookie('method', $_GET['method'], $expiretime);
			else unsetcookies('method');
		} else unsetcookies('split', 'partSize', 'method');
	} else unsetcookies('domail', 'email', 'split', 'partSize', 'method');

	if ($options['download_dir_is_changeable'] && !empty($_GET['saveto']) && $_GET['saveto'] == 'on' && !empty($_GET['path'])) {
		setcookie('saveto', '1', $expiretime);
		setcookie('path', $_GET['path'], $expiretime);
	} else unsetcookies('saveto', 'path');

	if (!empty($_GET['useproxy']) && $_GET['useproxy'] == 'on' && !empty($_GET['proxy'])) {
		setcookie('useproxy', '1', $expiretime);
		if (strlen(strstr($_GET['proxy'], ':')) > 0) setcookie('proxy', $_GET['proxy'], $expiretime);
		else unsetcookies('proxy');
		if (!empty($_GET['proxyuser']) && !empty($_GET['proxypass'])) {
			setcookie('proxyuser', $_GET['proxyuser'], $expiretime);
			setcookie('proxypass', $_GET['proxypass'], $expiretime);
		} else unsetcookies('proxyuser', 'proxypass');
	} else unsetcookies('useproxy', 'proxy', 'proxyuser', 'proxypass');
	unset($expiretime);
}

?>