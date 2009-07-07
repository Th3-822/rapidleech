<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
}
// Render the main screen
include(TEMPLATE_DIR.'header.php');
include(TEMPLATE_DIR.'main.php');
include(TEMPLATE_DIR.'footer.php');
?>