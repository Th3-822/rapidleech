<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}

require_once (HOST_DIR . 'download/' . "crocko_com.php");
$link = str_replace('easy-share.com', 'crocko.com', $LINK);
$hostClass = new crocko_com();
$hostClass->Download($link);

// only for temporary so old link from easy-share.com will be redirected into crocko.com
?>