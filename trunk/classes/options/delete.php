<?php
function delete() {
	global $disable_deleting,$list;
	if ($disable_deleting) {
		echo lang(147);
	}
	else {
?>
<form method="post"><input type="hidden" name="act" value="delete_go">
<?php echo lang(104); ?>:
<?php
			for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
				$file = $list [$_GET ["files"] [$i]];
?>
	<input type="hidden" name="files[]" value="<?php echo $_GET ["files"] [$i]; ?>"> <b><?php echo basename ( $file ["name"] ); ?></b><?php echo $i == count ( $_GET ["files"] ) - 1 ? "." : ",&nbsp"; ?>
<?php
		}
?>
<br>
<?php echo lang(148); ?>?
<br>
<table>
	<tr>
		<td><input type="submit" name="yes" style="width: 33px; height: 23px"
			value="<?php echo lang(149); ?>">
		</td>
		<td>&nbsp;&nbsp;&nbsp;</td>
		<td><input type="submit" name="no" style="width: 33px; height: 23px"
			value="<?php echo lang(150); ?>">
		</td>
	</tr>
</table>
</form>
<?php
	}
}

function delete_go() {
	global $list;
	if (isset($_POST["yes"])) {
		for($i = 0; $i < count ( $_POST ["files"] ); $i ++) {
			$file = $list [$_POST ["files"] [$i]];
			if (file_exists ( $file ["name"] )) {
				if (@unlink ( $file ["name"] )) {
					printf(lang(151),$file['name']);
					echo "<br>";
					unset ( $list [$_POST ["files"] [$i]] );
				} else {
					printf(lang(152),$file['name']);
					echo "<br>";
				}
			} else {
				unset ( $list [$_POST ["files"] [$i]] );
				printf(lang(145),$file['name']);
				echo "<br>";
			}
		}
		if (! updateListInFile ( $list )) {
			echo lang(146)."<br><br>";
		}
	} else {
		echo('<script type="text/javascript">location.href="'.$PHP_SELF.'?act=files";</script>');
	}
}
?>