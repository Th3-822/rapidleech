<?php
$d = dir("hosts/download/");
while (false !== ($entry = $d->read())) {
   if (stristr($entry,'.php') && !stristr($entry,'.JD')) {
		$hostname = substr($entry,0,-4);
		$hostname = str_replace('_','.',$hostname);
		if ($hostname == 'easy.share.com') $hostname = 'easy-share.com';
		if ($hostname == 'generic.minifilehost') continue;
		if ($hostname == 'youtube.com(1)') continue;
		if ($hostname == 'vBulletin.plug') continue;
		if ($hostname == 'hosts') continue;
		$host[$hostname] = $entry;
   }
}
$d->close();
?>
