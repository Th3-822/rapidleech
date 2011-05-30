<?php
function rl_list() {
	global $list, $options;
	if ($list) {
?>
<table><tr><td>
<table class="md5table">
<?php
		foreach($list as $file) {
			if(file_exists($file["name"])) {
			echo '<tr><td>'.htmlentities(basename($file["name"])).'</td></tr>'.$nn;
			}
			else if ($options['2gb_fix'] && file_exists($file) && !is_dir($file) && !is_link($file)) {
				echo '<tr><td>'.htmlentities(basename($file["name"])).'</td></tr>'.$nn;
			}
		}
?>
</table>
</td><td>
<table class="md5table">
<?php
		foreach($list as $file) {
			if(file_exists($file["name"])) {
				echo '<tr><td>'.link_for_file($file["name"], TRUE).'</td></tr>'.$nn;
			}
			else if ($options['2gb_fix'] && file_exists($file) && !is_dir($file) && !is_link($file)) {
				echo '<tr><td>'.link_for_file($file["name"], TRUE).'</td></tr>'.$nn;
			}
		}
?>
</table>
</td></tr></table>
<?php
	}
}
?>