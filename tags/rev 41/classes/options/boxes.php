<?php
function boxes() {
	global $list, $disable_deleting;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select at least one file.<br><br>";
	} else {
		?>
                            <form method="post"><input type="hidden"
	name="act" value="boxes_go">
<?php
		echo count ( $_GET ["files"] ) . " file" . (count ( $_GET ["files"] ) > 1 ? "s" : "") . ":<br>";
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [($_GET ["files"] [$i])];
			?>
                                <input type="hidden" name="files[]"
	value="<?php echo $_GET ["files"] [$i]; ?>"> <b><?php echo basename ( $file ["name"] ); ?></b><?php echo $i == count ( $_GET ["files"] ) - 1 ? "." : ",&nbsp"; ?>
<?php
		}
?><br>
<br>
<table align="center">
	<tr>
		<td>Emails:&nbsp;<textarea name="emails" cols="30" rows="8"><?php
		if ($_COOKIE ["email"])
			echo $_COOKIE ["email"];
		?></textarea>
		</td>
		<td><input type="submit" value="Send"></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="del_ok"
			<?php if (! $disable_deleting) echo "checked='checked'"; ?> <?php if ($disable_deleting) echo "disabled='disabled'"; ?>>&nbsp;Delete
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
					<?php echo $_COOKIE ["split"] ? " checked" : ""; ?>>&nbsp;Split by Parts</td>
				<td>&nbsp;</td>
				<td id=methodtd2
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
							value="<?php echo ($_COOKIE ["partSize"] ? $_COOKIE ["partSize"] : 10); ?>">&nbsp;MB
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

function boxes_go() {
	global $list, $disable_deleting, $emails, $fromaddr;
	require_once (CLASS_DIR . "mail.php");
	$_GET ["partSize"] = ((isset ( $_GET ["partSize"] ) & $_GET ["split"] == "on") ? $_GET ["partSize"] * 1024 * 1024 : FALSE);
	$v_mails = explode ( "\n", $emails );
	$v_min = count ( (count ( $_GET ["files"] ) < count ( $v_mails )) ? $_GET ["files"] : $v_mails );
	
	for($i = 0; $i < $v_min; $i ++) {
		$file = $list [$_GET ["files"] [$i]];
		
		$v_mail = trim ( $v_mails [$i] );
		if (! checkmail ( $v_mail )) {
			echo "<b>$v_mail</b> - Invalid E-mail Address.<br><br>";
		} elseif (file_exists ( $file ["name"] )) {
			if (xmail ( "$fromaddr", $v_mail, "File " . basename ( $file ["name"] ), "File: " . basename ( $file ["name"] ) . "\r\n" . "Link: " . $file ["link"] . ($file ["comment"] ? "\r\nComments: " . str_replace ( "\\r\\n", "\r\n", $file ["comment"] ) : ""), $file ["name"], $_GET ["partSize"], $_GET ["method"] )) {
				if ($_GET ["del_ok"] && ! $disable_deleting) {
					if (@unlink ( $file ["name"] )) {
						$v_ads = " and deleted !";
						unset ( $list [$_GET ["files"] [$i]] );
					} else {
						$v_ads = ", but <b>not deleted !</b>";
					}
					;
				} else
					$v_ads = " !";
				echo "<script language=\"JavaScript\">mail('File <b>" . basename ( $file ["name"] ) . "</b> it is sent for the address <b>" . $v_mail . "</b>" . $v_ads . "', '" . md5 ( basename ( $file ["name"] ) ) . "');</script>\r\n<br>";
			} else {
				echo "Error sending file!<br>";
			}
		} else {
			echo "File <b>" . $file ["name"] . "</b> Not Found!<br><br>";
		}
	}
	
	if (count ( $_GET ["files"] ) < count ( $v_mails )) {
		echo "<b>Внимание!</b> Вы написали лишние адреса.<br><br><b>";
		for($i = count ( $_GET ["files"] ); $i < count ( $v_mails ); $i ++) {
			$v_mail = trim ( $v_mails [$i] );
			echo "$v_mail.</b><br><br>";
		}
		;
		echo "</b><br>";
	} elseif (count ( $_GET ["files"] ) > count ( $v_mails )) {
		echo "<b>Внимание!</b> Вы не написали адреса для следуйщий файлов:<br><br><b>";
		for($i = count ( $v_mails ); $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [$_GET ["files"] [$i]];
			if (file_exists ( $file ["name"] )) {
				echo $file ["name"] . "<br><br>";
			} else {
				echo "</b>Файл <b>" . $file ["name"] . "</b> не найден!<b><br><br>";
			}
		}
		echo "</b><br>";
	}
	;
	
	if ($_GET ["del_ok"]) {
		if (! updateListInFile ( $list )) {
			echo "Couldn't Update!<br><br>";
		}
	}
}
?>