<?php
function rl_rename() {
	global $disable_deleting, $list;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select at least one file.<br><br>";
	} elseif ($disable_deleting) {
		echo "you don't have permission to rename files";
	} else {
?>
<form method="post"><input type="hidden" name="act" value="rename_go">
		<table align="center">
			<tr>
				<td>
				<table>
<?php
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [$_GET ["files"] [$i]];
?>
<input type="hidden" name="files[]" value="<?php echo $_GET ["files"] [$i]; ?>" />
<tr>
	<td align="center"><b><?php echo basename ( $file ["name"] ); ?></b></td>
</tr>
<tr>
	<td><?php echo lang(201); ?>:&nbsp;<input type="text" name="newName[]" size="25"
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
				printf(lang(82),$filetype);
				echo "<br /><br />";
			} else {
				if (@rename ( $file ["name"], $newName )) {
					printf(lang(194),$file['name'],basename($newName));
					echo "<br><br>";
					$list [$_GET ["files"] [$i]] ["name"] = $newName;
				} else {
					printf(lang(202),$file['name']);
					echo "<br><br>";
				}
			}
		} else {
			printf(lang(145),$file['name']);
			echo "<br /><br />";
		}
	}
	if ($smthExists) {
		if (! updateListInFile ( $list )) {
			echo lang(9)."<br /><br />";
		}
	}
}
?>