<?php
function mrename() {
	global $list;
	if ($disable_deleting) {
		echo lang(147);
	}
	else {
?>
<form method="post"><input type="hidden" name="act" value="mrename_go">
<?php echo lang(104); ?>:
<?php
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [$_GET ["files"] [$i]];
?>
<input type="hidden" name="files[]" value="<?php echo $_GET ["files"] [$i]; ?>"> <b><?php echo basename ( $file ["name"] ); ?></b><?php echo $i == count ( $_GET ["files"] ) - 1 ? "." : ",&nbsp"; ?>
<?php
		}
?>
<table>
<tr>
<td valign="center"><b><?php echo lang(188); ?>&nbsp;</b><font size=2 color="yellow">&nbsp; <b><?php echo lang(189); ?>.</b>&nbsp; (dot)</font><b><input type="input" name="extension" style="width: 60px; height: 23px" value=''>&nbsp;<?php echo lang(190); ?> <?php echo lang(104); ?>.</b>&nbsp;
<input name="yes" type="submit" style="height: 23px" value="<?php echo lang(191); ?>">&nbsp;&nbsp;
<input name="no" type="submit" style="height: 23px" value="<?php echo lang(192); ?>"></td>
</tr>
</table>
</form>
<?php
	}
}

function mrename_go() {
	global $list, $forbidden_filetypes;
	if ($_POST ["yes"] && @trim ( $_POST ['extension'] )) {
		$_POST ['extension'] = @trim ( $_POST ['extension'] );
		
		while ( $_POST ['extension'] [0] == '.' )
			$_POST ['extension'] = substr ( $_POST ['extension'], 1 );
		
		if ($_POST [extension]) {
			for($i = 0; $i < count ( $_POST ["files"] ); $i ++) {
				$file = $list [$_POST ["files"] [$i]];
				if (file_exists ( $file ["name"] )) {
					$filetype = '.' . strtolower ( $_POST ['extension'] );
					if (is_array ( $forbidden_filetypes ) && in_array ( '.' . strtolower ( $_POST ['extension'] ), $forbidden_filetypes )) {
						printf(lang(82),$filetype);
						echo('<br><br>');
					} else {
						if (@rename ( $file ["name"], fixfilename ( $file ["name"] . ".{$_POST['extension']}" ) )) {
							printf(lang(194).'<br>',basename($file['name']),fixfilename ( basename ( $file ["name"] . ".{$_POST['extension']}" ) ));
							$list [$_POST ["files"] [$i]] ["name"] .= '.' . $_POST ['extension'];
							$list [$_POST ["files"] [$i]] ["name"] = fixfilename ( $list [$_POST ["files"] [$i]] ["name"] );
						} else {
							printf(lang(193),basename($file['name']));
							echo '<br>';
						}
					}
				} else {
					printf(lang(145),basename($file['name']));
					echo('<br>');
				}
			}
			if (! updateListInFile ( $list ))
				echo lang(146)."<br>";
		}
	} else {
?>
<script type="text/javascript" language="javascript">location.href="<?php echo substr ( $PHP_SELF, 0, strlen ( $PHP_SELF ) - strlen ( strstr ( $PHP_SELF, "?" ) ) ) . "?act=files"; ?>";</script>
<?php
	}
}
?>