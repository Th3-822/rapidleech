<?php
function merge() {
	global $disable_deleting, $list, $PHP_SELF;
	if (count ( $_GET ["files"] ) !== 1) {
		echo "Please select only the .crc or .001 file!<br><br>";
	} else {
		$file = $list [$_GET ["files"] [0]];
		if (substr ( $file ["name"], - 4 ) == '.001' && is_file ( substr ( $file ["name"], 0, - 4 ) . '.crc' )) {
			echo "Please select the .crc file!<br><br>";
		} elseif (substr ( $file ["name"], - 4 ) !== '.crc' && substr ( $file ["name"], - 4 ) !== '.001') {
			echo "Please select the .crc or .001 file!<br><br>";
		} else {
			$usingcrcfile = (substr ( $file ["name"], - 4 ) === '.001') ? false : true;
?>
                          <form method="post" action="<?php echo $PHP_SELF; ?>"><input type="hidden"
			name="files[0]" value="<?php echo $_GET ["files"] [0]; ?>">
		<table>
<?php
			if ($usingcrcfile) {
?>
                             <tr>
			<td><input type="checkbox" name="crc_check" value="1" checked
				onClick="javascript:var displ=this.checked?'inline':'none';document.getElementById('crc_check_mode').style.display=displ;">&nbsp;Perform
			a CRC check? (recommended)<br>
			<span id="crc_check_mode">CRC32 check mode:<br>
<?php
				if (function_exists ( 'hash_file' )) {
?><input type="radio" name="crc_mode" value="hash_file" checked>&nbsp;Use hash_file (Recommended)<br>
<?php } ?>
			<input type="radio" name="crc_mode" value="file_read">&nbsp;Read file to memory<br>
			<input type="radio" name="crc_mode" value="fake"
<?php
				if (! function_exists ( 'hash_file' )) {
					echo 'checked';
				}
?>>&nbsp;Fake crc </span></td>
		</tr>
		<tr>
			<td><input type="checkbox" name="del_ok" <?php echo $disable_deleting ? 'disabled' : 'checked'; ?>>&nbsp;Delete
			source file after successful merge</td>
		</tr>
<?php
					} else {
?>
                             <tr>
			<td align="center">Note: <b>The file size and crc32 won't be check</b></td>
		</tr>
<?php
					}
?>
                             <tr>
			<td align="center"><input type="hidden" name="act"
				value="merge_go"> <input type="submit" value="Merge file"></td>
		</tr>
	</table>
	</form>
<?php
		}
	}
}

function merge_go() {
	global $list, $check_these_before_unzipping, $forbidden_filetypes, $disable_deleting;
	if (count ( $_GET ["files"] ) !== 1) {
		echo "Please select only the .crc or .001 file!<br><br>";
	} else {
		$file = $list [$_GET ["files"] [0]];
		if (substr ( $file ["name"], - 4 ) == '.001' && is_file ( substr ( $file ["name"], 0, - 4 ) . '.crc' )) {
			echo "Please select the .crc file!<br><br>";
		} elseif (substr ( $file ["name"], - 4 ) !== '.crc' && substr ( $file ["name"], - 4 ) !== '.001') {
			echo "Please select the .crc or .001 file!<br><br>";
		} else {
			$usingcrcfile = (substr ( $file ["name"], - 4 ) === '.001') ? false : true;
			if (! $usingcrcfile) {
				$data = array ('filename' => basename ( substr ( $file ["name"], 0, - 4 ) ), 'size' => - 1, 'crc32' => '00111111' );
			} else {
				$fs = @fopen ( $file ["name"], "rb" );
			}
			if ($usingcrcfile && ! $fs) {
				echo "Can't read the .crc file!<br><br>";
			} else {
				if ($usingcrcfile) {
					$data = array ();
					while ( ! feof ( $fs ) ) {
						$data_ = explode ( '=', trim ( fgets ( $fs ) ), 2 );
						$data [$data_ [0]] = $data_ [1];
					}
					fclose ( $fs );
				}
				$path = realpath ( DOWNLOAD_DIR ) . '/';
				$filename = basename ( $data ['filename'] );
				$partfiles = array ();
				$partsSize = 0;
				for($j = 1; $j < 10000; $j ++) {
					if (! is_file ( $path . $filename . '.' . sprintf ( "%03d", $j ) )) {
						if ($j == 1) {
							$partsSize = - 1;
						}
						break;
					}
					$partfiles [] = $path . $filename . '.' . sprintf ( "%03d", $j );
					$partsSize += filesize ( $path . $filename . '.' . sprintf ( "%03d", $j ) );
				}
				if (file_exists ( $path . $filename )) {
					echo "Error, Output file already exists <b>" . $path . $filename . "</b><br><br>";
				} elseif ($usingcrcfile && $partsSize != $data ['size']) {
					echo "Error, missing or incomplete parts<br><br>";
				} elseif ($check_these_before_unzipping && is_array ( $forbidden_filetypes ) && in_array ( strtolower ( strrchr ( $filename, "." ) ), $forbidden_filetypes )) {
					echo "Error, The filetype " . strrchr ( $filename, "." ) . " is forbidden<br><br>";
				} else {
					$merge_buffer_size = 2 * 1024 * 1024;
					$merge_dest = @fopen ( $path . $filename, "wb" );
					if (! $merge_dest) {
						echo "It is not possible to open destination file <b>" . $path . $filename . "</b><br><br>";
					} else {
						$merge_ok = true;
						foreach ( $partfiles as $part ) {
							$merge_source = @fopen ( $part, "rb" );
							while ( ! feof ( $merge_source ) ) {
								$merge_buffer = fread ( $merge_source, $merge_buffer_size );
								if ($merge_buffer === false) {
									echo "Error reading the file <b>" . $part . "</b> !<br><br>";
									$merge_ok = false;
									break;
								}
								if (fwrite ( $merge_dest, $merge_buffer ) === false) {
									echo "Error writing the file <b>" . $path . $filename . "</b> !<br><br>";
									$merge_ok = false;
									break;
								}
							}
							fclose ( $merge_source );
							if (! $merge_ok) {
								break;
							}
						}
						fclose ( $merge_dest );
						if ($merge_ok) {
							$fc = ($_GET ['crc_mode'] == 'file_read') ? dechex ( crc32 ( read_file ( $path . $filename ) ) ) : (($_GET ['crc_mode'] == 'hash_file' && function_exists ( 'hash_file' )) ? hash_file ( 'crc32b', $path . $filename ) : '111111');
							$fc = str_repeat ( "0", 8 - strlen ( $fc ) ) . strtoupper ( $fc );
							if ($fc != strtoupper ( $data ["crc32"] )) {
								echo "CRC32 checksum doesn't match!<br><br>";
							} else {
								echo "File <b>" . $filename . "</b> successfully merged!<br><br>";
								if ($usingcrcfile && $fc != '00111111' && $_GET ["del_ok"] && ! $disable_deleting) {
									if ($usingcrcfile) {
										$partfiles [] = $file ["name"];
									}
									foreach ( $partfiles as $part ) {
										if (@unlink ( $part )) {
											foreach ( $list as $list_key => $list_file ) {
												if ($list_file ["name"] === $part) {
													unset ( $list [$list_key] );
												}
											}
											echo "<b>" . basename ( $part ) . "</b> deleted.<br>";
										} else {
											echo "<b>" . basename ( $part ) . "</b> not deleted.<br>";
										}
									}
									echo "<br>";
								}
								$time = filectime ( $path . $filename );
								while ( isset ( $list [$time] ) ) {
									$time ++;
								}
								$list [$time] = array ("name" => $path . $filename, "size" => bytesToKbOrMbOrGb ( $partsSize ), "date" => $time );
								if (! updateListInFile ( $list )) {
									echo "Couldn't update file list. Problem writing to file!<br><br>";
								}
							}
						}
					}
				}
			}
		}
	}
}
?>