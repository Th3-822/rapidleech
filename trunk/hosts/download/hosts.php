<?php
$d = dir("hosts/download/");
while (false !== ($entry = $d->read())) {
   if (stristr($entry,'.php') && !stristr($entry,'.JD')) {
		$hostname = substr($entry,0,-4);
		$hostname = str_replace('_','.',$hostname);
		if ($hostname == 'easy.share.com') $hostname = 'easy-share.com';
		if ($hostname == 'share.online.biz') $hostname = 'share-online.biz';
		if ($hostname == 'cash.file.net') $hostname = 'cash-file.net';
		if ($hostname == 'i.filez.com') $hostname = 'i-filez.com';
		if ($hostname == 'fast.debrid.com') $hostname = 'fast-debrid.com';
		if ($hostname == 'real.debrid.com') $hostname = 'real-debrid.com';
		if ($hostname == 'generic.minifilehost' || $hostname == 'youtube.com(1)' || $hostname == 'vBulletin.plug' || $hostname == 'hosts') continue;
		$host[$hostname] = $entry;
   }
}
$d->close();

// Filesonic extra domains...
if (is_array($host) && array_key_exists('filesonic.com', $host)) {
	$host['sharingmatrix.com'] = $host['filesonic.com'];
	$filesonic_domains = array('net', 'jp', 'tw', 'it', 'in', 'kr', 'vn', 'hk', 'co.il',
		'sg', 'pk', 'fr', 'at', 'be', 'bg', 'ch', 'cl', 'co.id', 'co.th', 'com.au', 'com.eg',
		'com.hk', 'com.tr', 'com.vn', 'cz', 'es', 'fi', 'gr', 'hr', 'hu', 'mx', 'my', 'pe',
		'pt', 'ro', 'rs', 'se', 'sk', 'ua', 'asia', 'cc', 'co.nz', 'me', 'nl', 'tv');
	foreach ($filesonic_domains as $tld) {
		$host["filesonic.$tld"] = $host['filesonic.com'];
	}
}

?>
