<?php
function delete() {
	global $disable_deleting,$list;
	if (count ( $_GET ["files"] ) < 1) {
			echo "Please select at least one file<br><br>";
		} elseif ($disable_deleting) {
			echo "File deletion is disabled";
		} else {
				?>
<form method="post"><input type="hidden" name="act" value="delete_go">
                              File<?php
			echo count ( $_GET ["files"] ) > 1 ? "s" : "";
				?>:
                              <?php
			for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
				$file = $list [$_GET ["files"] [$i]];
					?>
                                  <input type="hidden" name="files[]" value="<?php echo $_GET ["files"] [$i]; ?>"> <b><?php echo basename ( $file ["name"] ); ?></b><?php echo $i == count ( $_GET ["files"] ) - 1 ? "." : ",&nbsp"; ?>
<?php
				}
				?><br>Delete<?php echo count ( $_GET ["files"] ) > 1 ? " These Files" : " This File"; ?>?<br>
<table>
	<tr>
		<td><input type="submit" name="yes" style="width: 33px; height: 23px"
			value="Yes"></td>
		<td>&nbsp;&nbsp;&nbsp;</td>
		<td><input type="submit" name="no" style="width: 33px; height: 23px"
			value="No"></td>
	</tr>
</table>
</form>
<?php
	}
}

function delete_go() {
	global $list;
	if ($_GET ["yes"]) {
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [$_GET ["files"] [$i]];
			if (file_exists ( $file ["name"] )) {
				if (@unlink ( $file ["name"] )) {
					echo "File <b>" . $file ["name"] . "</b> Deleted<br><br>";
					unset ( $list [$_GET ["files"] [$i]] );
				} else {
					echo "Error deleting the file <b>" . $file ["name"] . "</b>!<br><br>";
				}
			} else {
				echo "File <b>" . $file ["name"] . "</b> Not Found!<br><br>";
			}
		}
		if (! updateListInFile ( $list )) {
			echo "Error in updating the list!<br><br>";
		}
	} else {
				?>
<script language="JavaScript">
	location.href="<?php echo $PHP_SELF . "?act=files"; ?>";
</script>
<?php
	}
}
?>