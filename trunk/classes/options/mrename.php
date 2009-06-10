<?php
function mrename() {
	global $list;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Please select atleast one file<br><br>";
	} else {
		?>
                            <form method="post"><input type="hidden"
			name="act" value="mrename_go">
                            File<?php echo count ( $_GET ["files"] ) > 1 ? "s" : ""; ?>:
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
				<td valign="center"><b>Add extension&nbsp;</b><font size=2
					color="yellow">without&nbsp; <b>.</b>&nbsp; (dot)</font><b><input
					type="input" name="extension" style="width: 60px; height: 23px"
					value=''>&nbsp;to <?php echo count ( $_GET ["files"] ) > 1 ? " files" : " file"; ?>.</b>&nbsp;<input
					name="yes" type="submit" style="height: 23px" value="Rename?">&nbsp;&nbsp;<input
					name="no" type="submit" style="height: 23px" value="Cancel"></td>
			</tr>
		</table>
		</form>
<?php
	}
}

function mrename_go() {
	global $list, $forbidden_filetypes, $PHP_SELF;
	if ($_GET ["yes"] && @trim ( $_REQUEST ['extension'] )) {
		$_REQUEST ['extension'] = @trim ( $_REQUEST ['extension'] );
		
		while ( $_REQUEST ['extension'] [0] == '.' )
			$_REQUEST ['extension'] = substr ( $_REQUEST ['extension'], 1 );
		
		if ($_REQUEST [extension]) {
			for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
				$file = $list [$_GET ["files"] [$i]];
				if (file_exists ( $file ["name"] )) {
					$filetype = '.' . strtolower ( $_REQUEST ['extension'] );
					if (is_array ( $forbidden_filetypes ) && in_array ( '.' . strtolower ( $_REQUEST ['extension'] ), $forbidden_filetypes )) {
						print "The filetype $filetype is forbidden to be renamed<br><br>";
					} else {
						if (@rename ( $file ["name"], fixfilename ( $file ["name"] . ".{$_REQUEST['extension']}" ) )) {
							echo "<font color=yellow>File</font> <b>" . basename ( $file ["name"] ) . "</b> <font color=yellow>rename to</font> <b>" . fixfilename ( basename ( $file ["name"] . ".{$_REQUEST['extension']}" ) ) . "</b><br>";
							$list [$_GET ["files"] [$i]] ["name"] .= '.' . $_REQUEST ['extension'];
							$list [$_GET ["files"] [$i]] ["name"] = fixfilename ( $list [$_GET ["files"] [$i]] ["name"] );
						} else {
							echo "<font color=red>Error rename the file</font><b>" . basename ( $file ["name"] ) . "</b>!<br>";
						}
					}
				} else {
					echo "<font color=red>File</font> <b>" . basename ( $file ["name"] ) . "</b> <font color=red>Not Found!</font><br>";
				}
			}
			if (! updateListInFile ( $list ))
				echo "Error in updating the list!<br>";
		}
	} else {
?>
                            <script>
                              location.href="<?php echo substr ( $PHP_SELF, 0, strlen ( $PHP_SELF ) - strlen ( strstr ( $PHP_SELF, "?" ) ) ) . "?act=files"; ?>";
                            </script>
<?php
	}
}
?>