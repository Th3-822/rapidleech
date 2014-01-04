<?php
function rl_crc32() {
	global $list;
?>
<table class="md5table" align="center" border="0" cellspacing="2" cellpadding="4">
	<tr>
		<th align="center"><?php echo lang(104); ?></th>
		<th align="center"><?php echo lang(56); ?></th>
		<th align="center">CRC32</th>
	</tr>
<?php
	foreach ($_GET['files'] as $v) {
		$file = empty($list[$v]) ? '' : $list[$v];
		if (!empty($file) && file_exists($file['name'])) {
		$name = basename($file['name']);
		$crc = strtoupper(hash_file('crc32b', $file['name']));
		if (preg_match("@[\(_\{]($crc)[\}_\)]@i", $name)) $tstyle = ' style="color: green" title="'.lang(391).'">';
		elseif (preg_match('@\[([a-fA-F0-9]{8})\]@', $name, $fcrc) || preg_match('@_([a-fA-F0-9]{8})_@', $name, $fcrc) || preg_match('@\(([a-fA-F0-9]{8})\)@', $name, $fcrc) || preg_match('@\{([a-fA-F0-9]{8})\}@', $name, $fcrc)) {
			$tstyle = ((!empty($fcrc[2]) || strtoupper($fcrc[1]) == $crc) ? ' style="color: green" title="'.lang(391).'">' : ' style="color: red" title="'.sprintf(lang(392),$fcrc[1]).'">');
		} else $tstyle = '>';
?>
	<tr>
		<td nowrap="nowrap">&nbsp;<b><?php echo htmlspecialchars($name); ?></b>&nbsp;</td>
		<td align="center">&nbsp;<?php echo $file['size']; ?>&nbsp;</td>
		<td nowrap="nowrap">&nbsp;<b<?php echo "$tstyle$crc" ?></b>&nbsp;</td>
	</tr>
<?php
		}
	}
	echo "</table>\n<br />";
}
?>