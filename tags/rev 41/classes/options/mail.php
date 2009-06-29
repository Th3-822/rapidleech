<?php
function rl_mail() {
	global $disable_deleting, $list;
	if (count ( $_GET ["files"] ) < 1) {
			echo "Select at least one file.<br><br>";
		} else {
				?>
<form method="post"><input type="hidden" name="act" value="mail_go">
                              File<?php echo count ( $_GET ["files"] ) > 1 ? "s" : ""; ?>:
<?php
			for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
				$file = $list [($_GET ["files"] [$i])];
?>
                <input type="hidden" name="files[]" value="<?php echo $_GET ["files"] [$i]; ?>"> <b><?php echo basename ( $file ["name"] ); ?></b><?php echo $i == count ( $_GET ["files"] ) - 1 ? "." : ",&nbsp"; ?>
<?php
				}
?><br>
<br>
<table align="center">
	<tr>
		<td>Email:&nbsp;<input type="text" name="email"
			value="<?php echo ($_COOKIE ["email"] ? $_COOKIE ["email"] : ""); ?>">
		</td>
		<td><input type="submit" value="Send"></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="del_ok"
			<?php
				if (! $disable_deleting)
					echo "checked";
				?>
			<?php
				if ($disable_deleting)
					echo "disabled";
				?>>&nbsp;Delete
		successful submits</td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<table>
			<tr>
				<td><input id="splitchkbox" type="checkbox" name="split"
					onClick="javascript:var displ=this.checked?'':'none';document.getElementById('methodtd2').style.display=displ;"
					<?php echo $_COOKIE ["split"] ? " checked" : ""; ?>>&nbsp;Split by
				Parts</td>
				<td>&nbsp;</td>
				<td id="methodtd2"
					<?php echo $_COOKIE ["split"] ? "" : " style=\"display: none;\""; ?>>
				<table>
					<tr>
						<td>Method:&nbsp;<select name="method">
							<option value="tc"
								<?php echo $_COOKIE ["method"] == "tc" ? " selected" : ""; ?>>Total
							Commander</option>
							<option value="rfc"
								<?php echo $_COOKIE ["method"] == "rfc" ? " selected" : ""; ?>>RFC
							2046</option>
						</select></td>
					</tr>
					<tr>
						<td>Parts Size:&nbsp;<input type="text" name="partSize" size="2"
							value="<?php echo $_COOKIE ["partSize"] ? $_COOKIE ["partSize"] : 10; ?>">&nbsp;MB
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</form>
<?php
			}
}

function mail_go() {
	global $list, $disable_deleting;
	require_once (CLASS_DIR . "mail.php");
	if (! checkmail ( $_GET ["email"] )) {
		echo "Invalid E-mail Address.<br><br>";
	} else {
		$_GET ["partSize"] = ((isset ( $_GET ["partSize"] ) & $_GET ["split"] == "on") ? $_GET ["partSize"] * 1024 * 1024 : FALSE);
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [$_GET ["files"] [$i]];
			if (file_exists ( $file ["name"] )) {
				if (xmail ( "$fromaddr", $_GET ['email'], "File " . basename ( $file ["name"] ), "File: " . basename ( $file ["name"] ) . "\r\n" . "Link: " . $file ["link"] . ($file ["comment"] ? "\r\nComments: " . str_replace ( "\\r\\n", "\r\n", $file ["comment"] ) : ""), $file ["name"], $_GET ["partSize"], $_GET ["method"] )) {
					if ($_GET ["del_ok"] && ! $disable_deleting) {
						if (@unlink ( $file ["name"] )) {
							$v_ads = " and deleted.";
							unset ( $list [$_GET ["files"] [$i]] );
						} else {
							$v_ads = ", but <b>not deleted!</b>";
						}
						;
					} else
						$v_ads = " !";
					echo "<script language=\"JavaScript\">mail('File <b>" . basename ( $file ["name"] ) . "</b> it is sent for the address <b>" . $_GET ["email"] . "</b>" . $v_ads . "', '" . md5 ( basename ( $file ["name"] ) ) . "');</script>\r\n<br>";
				} else {
					echo "Error sending file!<br>";
				}
			} else {
				echo "File <b>" . $file ["name"] . "</b> not found!<br><br>";
			}
		}
	}
}
?>