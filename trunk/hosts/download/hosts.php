<?php
$host = array();
$d = dir(HOST_DIR . 'download/');
$HostnamesToFix = array('cash.file.net' => 'cash-file.net', 'd.h.st' => 'd-h.st', 'ex.load.com' => 'ex-load.com', 'share.now.net' => 'share-now.net','share.online.biz' => 'share-online.biz');
$HostnamesToIgnore = array('generic.minifilehost', 'youtube.com(1)', 'vBulletin.plug', 'hosts');
while (false !== ($entry = $d->read())) {
	if (strtolower(strrchr($entry, '.')) == '.php' && stripos($entry, '.JD') === false) {
		$hostname = strtolower(substr($entry, 0, -4));
		if (empty($hostname)) continue;
		$hostname = str_replace('_', '.', $hostname);
		if (array_key_exists($hostname, $HostnamesToFix)) $hostname = $HostnamesToFix[$hostname];
		if (in_array($hostname, $HostnamesToIgnore, true)) continue;
		$host[$hostname] = $entry;
		switch ($hostname) {
		//define the domain so we dont need to make new plugin with only 3-6 lines code
			case '1fichier.com':
				foreach(array('alterupload.com', 'cjoint.net', 'desfichiers.com', 'dfichiers.com', 'megadl.fr', 'mesfichiers.org', 'piecejointe.net', 'pjointe.com', 'tenvoi.com', 'dl4free.com') as $d1fichier) $host["$d1fichier"] = $host['1fichier.com'];
				break;
			case 'bayfiles.net':
				$host['bayfiles.com'] = $host['bayfiles.net'];
				break;
			case 'cloudzer.net':
				$host['clz.to'] = $host['cloudzer.net'];
				break;
			case 'cramit.in':
				foreach(array('cramitin.eu', 'cramitin.net', 'cramitin.us') as $cramit) $host["$cramit"] = $host['cramit.in'];
				break;
			case 'crocko.com':
				$host['easy-share.com'] = $host['crocko.com'];
				break;
			case 'depositfiles.com':
				foreach(array('depositfiles.in', 'depositfiles.mobi', 'depositfiles.net', 'depositfiles.net.cn', 'depositfiles.org', 'depositfiles.co.uk', 'depositfiles.info', 'dfiles.eu', 'dfiles.ru', 'dfiles.co', 'dfiles.co.uk') as $dfdomains) $host["$dfdomains"] = $host['depositfiles.com'];
				break;
			case 'd-h.st':
				$host['dev-host.org'] = $host['d-h.st'];
				break;
			case 'filecloud.io':
				$host['ifile.it'] = $host['filecloud.io'];
				break;
			case 'filepost.com':
				$host['fp.io'] = $host['filepost.com'];
				break;
			case 'filerio.com':
				$host['filekeen.com'] = $host['filerio.com'];
				break;
			case 'firedrive.com':
				$host['putlocker.com'] = $host['firedrive.com'];
				break;
			case 'freakshare.com':
				$host['freakshare.net'] = $host['freakshare.com'];
				break;
			case 'keep2share.cc':
				foreach(array('keep2share.com', 'keep2s.cc', 'k2s.cc') as $k2sdomains) $host["$k2sdomains"] = $host['keep2share.cc'];
				break;
			case 'multiupload.nl':
				$host['multiupload.com'] = $host['multiupload.nl'];
				break;
			case 'rapidgator.net':
				$host['rg.to'] = $host['rapidgator.net'];
				break;
			case 'speedyshare.com':
				$host['speedy.sh'] = $host['speedyshare.com'];
				break;
			case 'turbobit.net':
				$host['turbobit.ru'] = $host['turbobit.net'];
				$host['unextfiles.com'] = $host['turbobit.net'];
				break;
			case 'uploaded.net':
				foreach (array('ul.to', 'uploaded.to') as $uploaded) $host["$uploaded"] = $host['uploaded.net'];
				break;
			case 'uploadhero.co':
				$host['uploadhero.com'] = $host['uploadhero.co'];
				break;
			case 'uppit.com':
				$host['up.ht'] = $host['uppit.com'];
				break;
			case 'upstore.net':
				$host['upsto.re'] = $host['upstore.net'];
				break;
			case 'youtube.com':
				$host['youtu.be'] = $host['youtube.com'];
				break;
		}
	}
}
unset($HostnamesToFix, $HostnamesToIgnore);
$d->close();
?>