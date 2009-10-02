<?php
function merge() {
	global $disable_deleting, $list, $PHP_SELF;
	if (count ( $_GET ["files"] ) !== 1) {
		echo lang(167)."<br /><br />";
	} else {
		$file = $list [$_GET ["files"] [0]];
		if (substr ( $file ["name"], - 4 ) == '.001' && is_file ( substr ( $file ["name"], 0, - 4 ) . '.crc' )) {
			echo lang(168)."<br /><br />";
		} elseif (substr ( $file ["name"], - 4 ) !== '.crc' && substr ( $file ["name"], - 4 ) !== '.001') {
			echo lang(169)."<br /><br />";
		} else {
			echo lang(306)."<b>".basename(substr($file["name"], 0, -4))."</b><br><br>";
			$usingcrcfile = (substr ( $file ["name"], - 4 ) === '.001') ? false : true;
?>
<form method="post" action="<?php echo $PHP_SELF; ?>"><input type="hidden" name="files[0]" value="<?php echo $_GET ["files"] [0]; ?>">
<table>
<?php
			if ($usingcrcfile) {
?>
<tr>
<td><input type="checkbox" name="crc_check" value="1" checked onClick="javascript:var displ=this.checked?'inline':'none';document.getElementById('crc_check_mode').style.display=displ;">&nbsp;<?php echo lang(170); ?><br />
			<span id="crc_check_mode"><?php echo lang(171); ?>:<br />
<?php
				if (function_exists ( 'hash_file' )) {
?><input type="radio" name="crc_mode" value="hash_file" checked>&nbsp;<?php echo lang(172); ?><br>
<?php } ?>
<input type="radio" name="crc_mode" value="file_read">&nbsp;<?php echo lang(173); ?><br />
<input type="radio" name="crc_mode" value="fake"<?php if (! function_exists ( 'hash_file' )) { echo 'checked="checked"'; }?>>&nbsp;<?php echo lang(174); ?></span></td>
</tr>
<tr>
<td><input type="checkbox" name="del_ok" <?php echo $disable_deleting ? 'disabled' : 'checked'; ?>>&nbsp;<?php echo lang(175); ?></td>
</tr>
<?php
					} else {
?>
<tr>
<td align="center"><?php echo lang(176); ?>: <b><?php echo lang(177); ?></b></td>
</tr>
<?php
					}
?>
<tr>
<td align="center"><input type="hidden" name="act" value="merge_go"> <input type="submit" value="Merge file"></td>
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
		echo lang(167)."<br /><br />";
	} else {
		$file = $list [$_GET ["files"] [0]];
		if (substr ( $file ["name"], - 4 ) == '.001' && is_file ( substr ( $file ["name"], 0, - 4 ) . '.crc' )) {
			echo lang(168)."<br /><br />";
		} elseif (substr ( $file ["name"], - 4 ) !== '.crc' && substr ( $file ["name"], - 4 ) !== '.001') {
			echo lang(169)."<br /><br />";
		} else {
			$usingcrcfile = (substr ( $file ["name"], - 4 ) === '.001') ? false : true;
			if (! $usingcrcfile) {
				$data = array ('filename' => basename ( substr ( $file ["name"], 0, - 4 ) ), 'size' => - 1, 'crc32' => '00111111' );
			} else {
				$fs = @fopen ( $file ["name"], "rb" );
			}
			if ($usingcrcfile && ! $fs) {
				echo lang(178)."<br /><br />";
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
					printf(lang(179),$path . $filename);
					echo "<br /><br />";
				} elseif ($usingcrcfile && $partsSize != $data ['size']) {
					echo lang(180)."<br><br>";
				} elseif ($check_these_before_unzipping && is_array ( $forbidden_filetypes ) && in_array ( strtolower ( strrchr ( $filename, "." ) ), $forbidden_filetypes )) {
					printf(lang(181),strrchr ( $filename, "." ));
					echo "<br /><br />";
				} else {
					$merge_buffer_size = 2 * 1024 * 1024;
					$merge_dest = @fopen ( $path . $filename, "wb" );
					if (! $merge_dest) {
						printf(lang(182),$path . $filename);
						echo "<br /><br />";
					} else {
						$merge_ok = true;
						foreach ( $partfiles as $part ) {
							$merge_source = @fopen ( $part, "rb" );
							while ( ! feof ( $merge_source ) ) {
								$merge_buffer = fread ( $merge_source, $merge_buffer_size );
								if ($merge_buffer === false) {
									printf(lang(65),$part);
									echo "<br><br>";
									$merge_ok = false;
									break;
								}
								if (fwrite ( $merge_dest, $merge_buffer ) === false) {
									printf(lang(183),$path . $filename);
									echo "<br /><br />";
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
								echo lang(184)."<br /><br />";
							} else {
								printf(lang(185),$filename);
								echo '!<br /><br />';
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
											echo "<b>" . basename ( $part ) . "</b> ".lang(186).".<br />";
										} else {
											echo "<b>" . basename ( $part ) . "</b> ".lang(187).".<br />";
										}
									}
									echo "<br />";
								}
								$time = filectime ( $path . $filename );
								while ( isset ( $list [$time] ) ) {
									$time ++;
								}
								$list [$time] = array ("name" => $path . $filename, "size" => bytesToKbOrMbOrGb ( $partsSize ), "date" => $time );
								if (! updateListInFile ( $list )) {
									echo lang(146)."<br /><br />";
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