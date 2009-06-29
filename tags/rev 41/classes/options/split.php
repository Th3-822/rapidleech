<?php
function rl_split() {
	global $PHP_SELF, $list, $download_dir_is_changeable, $disable_deleting;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select at least one file.<br><br>";
	} else {
?>
                          <form method="post"
			action="<?php echo $PHP_SELF; ?>"><input type="hidden" name="act"
			value="split_go">
		<table align="center">
			<tr>
				<td>
				<table>
<?php
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [$_GET ["files"] [$i]];
?>
                                        <tr>
						<td align="center"><input type="hidden" name="files[]"
							value="<?php echo $_GET ["files"] [$i]; ?>"> <b><?php echo basename ( $file ["name"] ); ?></b>
						</td>
					</tr>
					<tr>
						<td>Parts Size:&nbsp;<input type="text" name="partSize[]"
							size="2"
							value="<?php echo ($_COOKIE ["partSize"] ? $_COOKIE ["partSize"] : 10); ?>">&nbsp;MB
						</td>
					</tr>
<?php
					if ($download_dir_is_changeable) {
?>
                                        <tr>
						<td>Save To:&nbsp;<input type="text" name="saveTo[]" size="40"
							value="<?php echo addslashes ( dirname ( $file ["name"] ) ); ?>"></td>
					</tr>
<?php
					}
?>
                                        <tr>
						<td><input type="checkbox" name="del_ok"
							<?php echo $disable_deleting ? 'disabled' : 'checked'; ?>>&nbsp;Delete
						source file after successful split</td>
					</tr>
					<tr>
						<td>CRC32 generation mode:<br>
<?php
			if (function_exists ( 'hash_file' )) {
				?><input
							type="radio" name="crc_mode[<?php echo $i; ?>]"
							value="hash_file" checked>&nbsp;Use hash_file (Recommended)<br>
<?php
			}
?>
                                            <input type="radio"
							name="crc_mode[<?php echo $i; ?>]" value="file_read">&nbsp;Read
						file to memory<br>
						<input type="radio" name="crc_mode[<?php echo $i; ?>]"
							value="fake"
<?php
			if (! function_exists ( 'hash_file' )) {
				echo 'checked';
			}
?>>&nbsp;Fake
						crc</td>
					</tr>
					<tr>
						<td></td>
					</tr>
<?php
		}
?>
                                  </table>
				</td>
				<td><input type="submit" value="Split"></td>
			</tr>
			<tr>
				<td></td>
			</tr>
		</table>
		</form>
<?php
	}
}

function split_go() {
	global $list, $download_dir, $download_dir_is_changeable, $disable_deleting;
	for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
		$split_ok = true;
		$file = $list [$_GET ["files"] [$i]];
		$partSize = round ( ($_GET ["partSize"] [$i]) * 1024 * 1024 );
		$saveTo = ($download_dir_is_changeable ? stripslashes ( $_GET ["saveTo"] [$i] ) : realpath ( $download_dir )) . '/';
		$dest_name = basename ( $file ["name"] );
		$fileSize = filesize ( $file ["name"] );
		$totalParts = ceil ( $fileSize / $partSize );
		$crc = ($_GET ['crc_mode'] [$i] == 'file_read') ? dechex ( crc32 ( read_file ( $file ["name"] ) ) ) : (($_GET ['crc_mode'] [$i] == 'hash_file' && function_exists ( 'hash_file' )) ? hash_file ( 'crc32b', $file ["name"] ) : '111111');
		$crc = str_repeat ( "0", 8 - strlen ( $crc ) ) . strtoupper ( $crc );
		echo "Started to split file <b>" . basename ( $file ["name"] ) . "</b> parts of " . bytesToKbOrMbOrGb ( $partSize ) . ", Using Method - Total Commander...<br>";
		echo "Total Parts: <b>" . $totalParts . "</b><br><br>";
		for($j = 1; $j <= $totalParts; $j ++) {
			if (file_exists ( $saveTo . $dest_name . '.' . sprintf ( "%03d", $j ) )) {
				echo "It is not possible to split the file. A piece already exists<b>" . $dest_name . '.' . sprintf ( "%03d", $j ) . "</b> !<br><br>";
				continue 2;
			}
		}
		if (file_exists ( $saveTo . $dest_name . '.crc' )) {
			echo "It is not possible to split the file. CRC file already exists<b>" . $dest_name . '.crc' . "</b> !<br><br>";
		} elseif (! is_file ( $file ["name"] )) {
			echo "It is not possible to split the file. Source file not found<b>" . $file ["name"] . "</b> !<br><br>";
		} elseif (! is_dir ( $saveTo )) {
			echo "It is not possible to split the file. Directory doesn't exist<b>" . $saveTo . "</b> !<br><br>";
		} elseif (! @write_file ( $saveTo . $dest_name . ".crc", "filename=" . $dest_name . "\r\n" . "size=" . $fileSize . "\r\n" . "crc32=" . $crc . "\r\n" )) {
			echo "It is not possible to split the file. CRC Error<b>" . $dest_name . ".crc" . "</b> !<br><br>";
		} else {
			$time = filectime ( $saveTo . $dest_name . '.crc' );
			while ( isset ( $list [$time] ) ) {
				$time ++;
			}
			$list [$time] = array ("name" => $saveTo . $dest_name . '.crc', "size" => bytesToKbOrMbOrGb ( filesize ( $saveTo . $dest_name . '.crc' ) ), "date" => $time );
			$split_buffer_size = 2 * 1024 * 1024;
			$split_source = @fopen ( $file ["name"], "rb" );
			if (! $split_source) {
				echo "It is not possible to open source file <b>" . $file ["name"] . "</b> !<br><br>";
				continue;
			}
			for($j = 1; $j <= $totalParts; $j ++) {
				$split_dest = @fopen ( $saveTo . $dest_name . '.' . sprintf ( "%03d", $j ), "wb" );
				if (! $split_dest) {
					echo "Error openning file <b>" . $dest_name . '.' . sprintf ( "%03d", $j ) . "</b> !<br><br>";
					$split_ok = false;
					break;
				}
				$split_write_times = floor ( $partSize / $split_buffer_size );
				for($k = 0; $k < $split_write_times; $k ++) {
					$split_buffer = fread ( $split_source, $split_buffer_size );
					if (fwrite ( $split_dest, $split_buffer ) === false) {
						echo "Error writing the file <b>" . $dest_name . '.' . sprintf ( "%03d", $j ) . "</b> !<br><br>";
						$split_ok = false;
						break;
					}
				}
				$split_rest = $partSize - ($split_write_times * $split_buffer_size);
				if ($split_ok && $split_rest > 0) {
					$split_buffer = fread ( $split_source, $split_rest );
					if (fwrite ( $split_dest, $split_buffer ) === false) {
						echo "Error writing the file <b>" . $dest_name . '.' . sprintf ( "%03d", $j ) . "</b> !<br><br>";
						$split_ok = false;
					}
				}
				fclose ( $split_dest );
				if ($split_ok) {
					$time = filectime ( $saveTo . $dest_name . '.' . sprintf ( "%03d", $j ) );
					while ( isset ( $list [$time] ) ) {
						$time ++;
					}
					$list [$time] = array ("name" => $saveTo . $dest_name . '.' . sprintf ( "%03d", $j ), "size" => bytesToKbOrMbOrGb ( filesize ( $saveTo . $dest_name . '.' . sprintf ( "%03d", $j ) ) ), "date" => $time );
				}
			}
			fclose ( $split_source );
			if ($split_ok) {
				if ($_GET ["del_ok"] && ! $disable_deleting) {
					if (@unlink ( $file ["name"] )) {
						unset ( $list [$_GET ["files"] [$i]] );
						echo "Source file deleted.<br><br>";
					} else {
						echo "Source file is<b>not deleted!</b><br><br>";
					}
				}
			}
			if (! updateListInFile ( $list )) {
				echo "Couldn't update file list. Problem writing to file!<br><br>";
			}
		}
	}
}
?>