<?php
function rl_sha1() {
	global $list;
?>
<table class="md5table" align="center" border="0" cellspacing="2" cellpadding="4">
	<tr>
		<th align="center"><?php echo lang(104); ?></th>
		<th align="center"><?php echo lang(56); ?></th>
		<th align="center">SHA1</th>
	</tr>
<?php
	foreach ($_GET['files'] as $v) {
		$file = $list[$v];
		if (file_exists($file['name'])) {
?>
	<tr>
		<td nowrap="nowrap">&nbsp;<b><?php echo htmlentities(basename($file['name'])); ?></b></td>
		<td align="center">&nbsp;<?php echo $file['size']; ?>&nbsp;</td>
		<td nowrap="nowrap"><b>&nbsp;<?php echo sha1_file($file['name'])?>&nbsp;</b></td>
	</tr>
<?php
		}
	}
	echo "</table>\n<br />";
}
?>