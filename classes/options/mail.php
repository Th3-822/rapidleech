<?php
function rl_mail() {
	global $options, $list, $PHP_SELF;
?>
<form method="post" action="<?php echo $PHP_SELF; ?>"><input type="hidden" name="act" value="mail_go" />
<?php echo lang(104); ?>:
<?php
	for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
		$file = $list [($_GET ["files"] [$i])];
?>
                <input type="hidden" name="files[]" value="<?php echo $_GET ["files"] [$i]; ?>" /> <b><?php echo basename ( $file ["name"] ); ?></b><?php echo $i == count ( $_GET ["files"] ) - 1 ? "." : ",&nbsp"; ?>
<?php
	}
?><br />
<br />
<table align="center">
	<tr>
		<td><?php echo lang(164); ?>:&nbsp;<input type="text" name="email"
			value="<?php echo ($_COOKIE ["email"] ? $_COOKIE ["email"] : ""); ?>" />
		</td>
		<td><input type="submit" value="Send" /></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="del_ok"<?php echo $options['disable_deleting'] ? ' disabled="disabled"' : ' checked="checked"';?> />&nbsp;<?php echo lang(165); ?></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr><td>
		<table>
			<tr>
				<td><input type="checkbox" name="split"
					onclick="javascript:var displ=this.checked?'':'none';document.getElementById('methodtd2').style.display=displ;"
					<?php echo $_COOKIE ["split"] ? ' checked="checked"' : ''; ?> />&nbsp;<?php echo lang(142); ?></td>
				<td>&nbsp;</td>
				<td id="methodtd2"<?php echo $_COOKIE ["split"] ? '' : ' style="display: none;"'; ?>>
				<table>
					<tr>
						<td><?php echo lang(124); ?>:&nbsp;<select name="method">
							<option value="tc"
								<?php echo $_COOKIE ["method"] == "tc" ? " selected" : ""; ?>>Total	Commander</option>
							<option value="rfc"
								<?php echo $_COOKIE ["method"] == "rfc" ? " selected" : ""; ?>>RFC 2046</option>
						</select></td>
					</tr>
					<tr>
						<td><?php echo lang(143); ?>:&nbsp;<input type="text" name="partSize" size="2"
							value="<?php echo $_COOKIE ["partSize"] ? $_COOKIE ["partSize"] : 10; ?>" />&nbsp;MB
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
  </td></tr>
</table>
</form>
<?php
}

function mail_go() {
	global $list, $options, $fromaddr;
	require_once (CLASS_DIR . "mail.php");
	if (! checkmail ( $_POST ["email"] )) {
		echo lang(166)."<br /><br />";
	} else {
		$_POST ["partSize"] = ((isset ( $_POST ["partSize"] ) & $_POST ["split"] == "on") ? $_POST ["partSize"] * 1024 * 1024 : FALSE);
		for($i = 0; $i < count ( $_POST ["files"] ); $i ++) {
			$file = $list [$_POST ["files"] [$i]];
			if (file_exists ( $file ["name"] )) {
				if (xmail ( "$fromaddr", $_POST ['email'], "File " . basename ( $file ["name"] ), "File: " . basename ( $file ["name"] ) . "\r\n" . "Link: " . $file ["link"] . ($file ["comment"] ? "\r\nComments: " . str_replace ( "\\r\\n", "\r\n", $file ["comment"] ) : ""), $file ["name"], $_POST ["partSize"], $_POST ["method"] )) {
					if ($_POST["del_ok"] && !$options['disable_deleting']) {
						if (@unlink ( $file ["name"] )) {
							$v_ads = " and deleted.";
							unset ( $list [$_POST ["files"] [$i]] );
						} else {
							$v_ads = ", but <b>not deleted!</b>";
						}
						;
					} else
						$v_ads = " !";
					echo '<script type="text/javascript">'."<!--\r\nmail('File <b>" . basename ( $file ["name"] ) . "</b> it is sent for the address <b>" . $_POST ["email"] . "</b>" . $v_ads . "', '" . md5 ( basename ( $file ["name"] ) ) . "');//-->\r\n</script>\r\n<br />";
				} else {
					echo lang(12)."<br />";
				}
			} else {
				printf(lang(145),$file['name']);
				echo "<br /><br />";
			}
		}
	}
}
?>