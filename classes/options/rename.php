<?php
function rl_rename() {
	global $disable_deleting, $list;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select at least one file.<br><br>";
	} elseif ($disable_deleting) {
		echo "you don't have permission to rename files";
	} else {
?>
                          <form method="post"><input type="hidden"
			name="act" value="rename_go">
		<table align="center">
			<tr>
				<td>
				<table>
<?php
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [$_GET ["files"] [$i]];
			?>
                                    <input type="hidden"
						name="files[]" value="<?php echo $_GET ["files"] [$i]; ?>" />
					<tr>
						<td align="center"><b><?php echo basename ( $file ["name"] ); ?></b></td>
					</tr>
					<tr>
						<td>New name:&nbsp;<input type="text" name="newName[]" size="25"
							value="<?php echo basename ( $file ["name"] ); ?>"></td>
					</tr>
					<tr>
						<td></td>
					</tr>
<?php } ?>
                                  </table>
				</td>
				<td><input type="submit" value="Rename"></td>
			</tr>
			<tr>
				<td></td>
			</tr>
		</table>
		</form>
<?php
	}
}

function rename_go() {
	global $list, $forbidden_filetypes;
	$smthExists = FALSE;
	for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
		$file = $list [$_GET ["files"] [$i]];
		
		if (file_exists ( $file ["name"] )) {
			$smthExists = TRUE;
			$newName = dirname ( $file ["name"] ) . PATH_SPLITTER . $_GET ["newName"] [$i];
			$filetype = strrchr ( $newName, "." );
			
			if (is_array ( $forbidden_filetypes ) && in_array ( strtolower ( $filetype ), $forbidden_filetypes )) {
				print "The filetype $filetype is forbidden to be renamed<br><br>";
			} else {
				if (@rename ( $file ["name"], $newName )) {
					echo "File <b>" . $file ["name"] . "</b> renamed to <b>" . basename ( $newName ) . "</b><br><br>";
					$list [$_GET ["files"] [$i]] ["name"] = $newName;
				} else {
					echo "Couldn't rename the file <b>" . $file ["name"] . "</b>!<br><br>";
				}
			}
		} else {
			echo "File <b>" . $file ["name"] . "</b> not found!<br><br>";
		}
	}
	if ($smthExists) {
		if (! updateListInFile ( $list )) {
			echo "Couldn't Update<br><br>";
		}
	}
}
?>