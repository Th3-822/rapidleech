<?php
function rl_list() {
	global $list, $PHP_SELF;
	if ($list) {
		foreach($list as $file) {
			if(file_exists($file["name"])) {
				$inCurrDir = strstr(dirname($file["name"]), ROOT_DIR) ? TRUE : FALSE;
				if($inCurrDir) {
					$Path = parse_url($PHP_SELF);
					$Path = 'http://'.urldecode($_SERVER['HTTP_HOST']).substr($Path["path"], 0, strlen($Path["path"]) - strlen(strrchr($Path["path"], "/")));
					echo($Path.str_replace("\\",'/',substr(dirname($file["name"]), strlen(ROOT_DIR)))."/".basename($file["name"]).'<br />');
				}
			}
		}
	}
}
?>