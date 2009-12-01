<?php
function rl_list() {
	global $list;
	if ($list) {
?>
<table><tr><td>
<table class="md5table">
<?php
		foreach($list as $file) {
			if(file_exists($file["name"])) {
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
		}
?>
</table>
</tr></table>
<?php
	}
}
?>