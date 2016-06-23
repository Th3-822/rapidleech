<?php
function rl_split() {
	global $PHP_SELF, $list, $options;
?>
<form method="post" action="<?php echo $PHP_SELF; ?>">
<input type="hidden" name="act" value="split_go" />
		<table align="center">
			<tr>
				<td>
				<table>
<?php
	for ($i = 0; $i < count($_GET['files']); $i++) {
		$file = $list[$_GET['files'][$i]];
?>
<tr>
<td align="center">
<input type="hidden" name="files[]" value="<?php echo $_GET['files'][$i]; ?>" /> <b><?php echo basename($file['name']); ?></b>
						</td>
					</tr>
					<tr>
						<td><?php echo lang(143); ?>:&nbsp;<input type="number" step="any" min="1" name="partSize[]" size="6" value="<?php echo ((!empty($_COOKIE['partSize']) && is_numeric($_COOKIE['partSize'])) ? max(1, $_COOKIE['partSize']) : 10); ?>" />&nbsp;MB
						</td>
					</tr>
<?php
		if ($options['download_dir_is_changeable']) {
?>
					<tr>
						<td><?php echo lang(40); ?>:&nbsp;<input type="text" name="saveTo[]" size="40" value="<?php echo htmlspecialchars(DOWNLOAD_DIR, ENT_QUOTES); ?>" /></td>
					</tr>
<?php
		}
?>
					<tr>
						<td><input type="checkbox" name="del_ok" <?php echo $options['disable_deleting'] ? 'disabled="disabled"' : 'checked="checked"'; ?> />&nbsp;<?php echo lang(203); ?></td>
					</tr>
					<tr>
						<td align="left">CRC32 generation mode:<br />
<?php
		if (function_exists('hash_file')) echo '<input type="radio" name="crc_mode['.$i.']" value="hash_file" checked="checked" />&nbsp;'.lang(172).'<br />';
?>
						<input type="radio" name="crc_mode[<?php echo $i; ?>]" value="fake" <?php if (!function_exists('hash_file')) echo ' checked="checked"'; ?> />&nbsp;<?php echo lang(174); ?></td>
					</tr>
					<tr>
						<td></td>
					</tr>
<?php
	}
?>
				</table>
				</td>
				<td><input type="submit" value="<?php echo lang(290); ?>" /></td>
			</tr>
			<tr>
				<td></td>
			</tr>
		</table>
		</form>
<?php
}

function split_go() {
	global $list, $options;
	for ($i = 0; $i < count($_POST['files']); $i++) {
		$split_ok = true;
		$file = $list[$_POST['files'][$i]];
		$partSize = round($_POST['partSize'][$i] * 1048576);
		$saveTo = ($options['download_dir_is_changeable'] ? stripslashes($_POST['saveTo'][$i]) : realpath(DOWNLOAD_DIR) . '/');
		if (substr($saveTo, -1) != '/') $saveTo .= '/';
		$dest_name = basename($file['name']);
		$fileSize = filesize($file['name']);
		$totalParts = ceil($fileSize / $partSize);
		if ($partSize < 1048576 || $totalParts < 2) {
			echo 'Invalid part size, or higher than filesize.<br /><br />';
			continue;
		}
		$crc = (($_POST['crc_mode'][$i] == 'hash_file' && function_exists('hash_file')) ? strtoupper(hash_file('crc32b', $file['name'])) : '111111');
		echo "Started to split file <b>$dest_name</b> parts of " . bytesToKbOrMbOrGb($partSize) . ", Using Method - Total Commander...<br />";
		echo "Total Parts: <b>$totalParts</b><br /><br />";
		for($j = 1; $j <= $totalParts; $j++) {
			if (file_exists("$saveTo$dest_name." . sprintf('%03d', $j))) {
				echo "It is not possible to split the file. A piece already exists <b>$dest_name." . sprintf('%03d', $j) . '</b> !<br /><br />';
				continue 2;
			}
		}
		if (file_exists("$saveTo$dest_name.crc")) echo "It is not possible to split the file. CRC file already exists <b>$dest_name.crc</b> !<br /><br />";
		elseif (!is_file($file['name'])) echo "It is not possible to split the file. Source file not found <b>{$file['name']}</b> !<br /><br />";
		elseif (!is_dir($saveTo)) echo "It is not possible to split the file. Directory doesn't exist<b>$saveTo</b> !<br /><br />";
		elseif (!@write_file("$saveTo$dest_name.crc", "filename=$dest_name\r\nsize=$fileSize\r\ncrc32=$crc\r\n")) echo "It is not possible to split the file. CRC Error<b>$dest_name.crc" . "</b> !<br /><br />";
		else {
			$time = filemtime("$saveTo$dest_name.crc");
			while (isset($list[$time])) $time++;
			$list[$time] = array('name' => realpath("$saveTo$dest_name.crc"), 'size' => bytesToKbOrMbOrGb(filesize("$saveTo$dest_name.crc")), 'date' => $time);
			$split_buffer_size = 2097152;
			$split_source = @fopen($file['name'], 'rb');
			if (!$split_source) {
				echo "It is not possible to open source file <b>{$file['name']}</b> !<br /><br />";
				continue;
			}
			for($j = 1; $j <= $totalParts; $j++) {
				$part = sprintf('%03d', $j);
				$part_name = "$dest_name.$part";
				$dest_file = $saveTo . $part_name;
				$split_dest = @fopen($dest_file, 'wb');
				if (!$split_dest) {
					echo "Error openning file <b>$part_name</b> !<br /><br />";
					$split_ok = false;
					break;
				}
				$split_write_times = floor($partSize / $split_buffer_size);
				for($k = 0; $k < $split_write_times; $k++) {
					$split_buffer = fread($split_source, $split_buffer_size);
					$split_written = fwrite($split_dest, $split_buffer);
					if ($split_written === false || $split_written != strlen($split_buffer)) {
						echo "Error writing the file <b>$part_name</b> !<br /><br />";
						$split_ok = false;
						break;
					}
				}
				$split_rest = $partSize - ($split_write_times * $split_buffer_size);
				if ($split_ok && $split_rest > 0) {
					$split_buffer = fread($split_source, $split_rest);
					$split_written = fwrite($split_dest, $split_buffer);
					if ($split_written === false || $split_written != strlen($split_buffer)) {
						echo "Error writing the file <b>$part_name</b> !<br /><br />";
						$split_ok = false;
					}
				}
				fclose($split_dest);
				if ($split_ok) {
					$time = filemtime($dest_file);
					while (isset($list[$time])) $time++;
					$list[$time] = array('name' => realpath($dest_file), 'size' => bytesToKbOrMbOrGb(filesize($dest_file)), 'date' => $time);
				}
			}
			fclose($split_source);
			if ($split_ok && !empty($_POST['del_ok']) && !$options['disable_deleting']) {
				if (@unlink($file['name'])) {
					unset($list[$_POST['files'][$i]]);
					echo 'Source file deleted.<br /><br />';
				} else echo 'Source file is<b>not deleted!</b><br /><br />';
			}
			if (!updateListInFile($list)) echo "Couldn't update file list. Problem writing to file!<br /><br />";
		}
	}
}
?>