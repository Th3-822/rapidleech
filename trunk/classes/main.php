<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
}

// Load template functions
require_once(TEMPLATE_DIR . 'functions.php');

// Render the main screen
include(TEMPLATE_DIR.'header.php');
include(TEMPLATE_DIR.'main.php');
include(TEMPLATE_DIR.'footer.php');
?>