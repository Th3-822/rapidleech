<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

require_once (HOST_DIR . 'download/' . "filerio_com.php");
$link = str_replace('filekeen.com', 'filerio.com', $LINK);
$hostClass = new filerio_com();
$hostClass->Download($link);

// only for temporary so old link from filekeen.com will be redirected into filerio.com
?>
