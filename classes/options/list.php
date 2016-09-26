<?php

function rl_list() {
	if (empty($GLOBALS['list'])) return;

	if (empty($_GET['files']) || !($list = array_intersect_key($GLOBALS['list'], array_flip($_GET['files'])))) {
		$list = &$GLOBALS['list'];
	}

	// List
	echo "<table><tr><td>\n<table class='md5table'>";
	foreach ($list as $file) {
		if (file_exists($file['name'])) {
			echo '<tr><td>' . htmlspecialchars(basename($file['name'])) . "</td></tr>\n";
		}
	}
	echo "</table>\n</td><td>\n<table class='md5table'>\n";
	foreach ($list as $file) {
		if (file_exists($file['name'])) {
			echo '<tr><td>' . link_for_file($file['name'], true) . "</td></tr>\n";
		}
	}
	echo "</table>\n</td></tr></table>";

}
