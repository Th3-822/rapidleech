<?php

echo "<center>transfer.sh plugin by <b>NimaH79</b></center><br>\n";
$ch = curl_init('https://transfer.sh/'.basename($lfile));
curl_setopt($ch, CURLOPT_PUT, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$fp = fopen($lfile, 'r');
curl_setopt($ch, CURLOPT_INFILE, $fp);
curl_setopt($ch, CURLOPT_INFILESIZE, filesize($lfile));
$download_link = curl_exec($ch);
curl_close($ch);

// [11-08-2017] - Written by NiamH79