<?php
function ftp() {
	global $list, $disable_deleting;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select at least one file.<br><br>";
	} else {
		?>
                            <form method="post"><input type="hidden"
			name="act" value="ftp_go">
                            File<?php echo count ( $_GET ["files"] ) > 1 ? "s" : ""; ?>:
<?php
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
				<td>
				<table>
					<tr>
						<td>Host:</td>
						<td><input type="text" name="host" id="host" <?php echo $_COOKIE ["host"] ? " value=\"" . $_COOKIE ["host"] . "\"" : ""; ?>
							size="23"></td>
					</tr>
					<tr>
						<td>Port:</td>
						<td><input type="text" name="port" id="port" <?php echo $_COOKIE ["port"] ? " value=\"" . $_COOKIE ["port"] . "\"" : " value=\"21\""; ?>
							size="4"></td>
					</tr>
					<tr>
						<td>Username:</td>
						<td><input type="text" name="login" id="login" <?php echo $_COOKIE ["login"] ? " value=\"" . $_COOKIE ["login"] . "\"" : ""; ?>
							size="23"></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><input type="password" name="password" id="password" <?php echo $_COOKIE ["password"] ? " value=\"" . $_COOKIE ["password"] . "\"" : ""; ?>
							size="23"></td>
					</tr>
					<tr>
						<td>Directory:</td>
						<td><input type="text" name="dir" id="dir" <?php echo $_COOKIE ["dir"] ? " value=\"" . $_COOKIE ["dir"] . "\"" : " value=\"/\""; ?>
							size="23"></td>
					</tr>
					<tr>
						<td><input type="checkbox" name="del_ok" <?php if ($disable_deleting) echo "disabled"; ?>>&nbsp;Delete source file after successful upload</td>
					</tr>
				</table>
				</td>
				<td>&nbsp;</td>
				<td>
				<table>
					<tr align="center">
						<td><input type="submit" value="Upload"></td>
					</tr>
					<tr align="center">
						<td>Options</td>
					</tr>
					<tr align="center">
						<td><script language="JavaScript">
                                        document.write(
                                          '<a href="javascript:setFtpParams();" id="hrefSetFtpParams" style="color: ' + (getCookie('ftpParams') == 1 ? '#808080' : '#0000FF') + ';">Copy Files</a> | ' +
                                          '<a href="javascript:delFtpParams();" id="hrefDelFtpParams" style="color: ' + (getCookie('ftpParams') == 1 ? '#0000FF' : '#808080') + '";">Move Files</a>'
                                        );
                                        </script></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</form>
<?php
	}
}

function ftp_go() {
	global $list, $disable_deleting;
	require_once (CLASS_DIR . "ftp.php");
	$ftp = new ftp ( );
	if (! $ftp->SetServer ( $_POST ["host"], ( int ) $_POST ["port"] )) {
		$ftp->quit ();
		echo "Couldn't connect to the server" . $_POST ["host"] . ":" . $_POST ["port"] . ".<br>" . "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
	} else {
		if (! $ftp->connect ()) {
			$ftp->quit ();
			echo "<br>Couldn't connect to the server " . $_POST ["host"] . ":" . $_POST ["port"] . ".<br>" . "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
		} else {
			echo "Connected to: <b>ftp://" . $_POST ["host"] . "</b> at port <b>" . $_POST ["port"] . "</b>";
			if (! $ftp->login ( $_POST ["login"], $_POST ["password"] )) {
				$ftp->quit ();
				echo "<br>Wrong username and/or password <b>" . $_POST ["login"] . ":" . $_POST ["password"] . "</b>.<br>" . "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
			} else {
				//$ftp->Passive(FALSE);
				if (! $ftp->chdir ( $_POST ["dir"] )) {
					$ftp->quit ();
					echo "<br>Cannot locate the folder<b>" . $_POST ["dir"] . "</b>.<br>" . "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				} else {
					?>
<br>
				<div id="status"></div>
				<br>
				<table cellspacing="0" cellpadding="0">
					<tr>
						<td></td>
						<td>
						<div
							style='border: #BBBBBB 1px solid; width: 300px; height: 10px;'>
						<div id="progress"
							style='background-color: #000099; margin: 1px; width: 0%; height: 8px;'>
						</div>
						</div>
						</td>
						<td></td>
					
					
					<tr>
					
					
					<tr>
						<td align="left" id="received">0 KB</td>
						<td align="center" id="percent">0%</td>
						<td align="right" id="speed">0 KB/s</td>
					</tr>
				</table>
				<br>
<?php
					$FtpUpload = TRUE;
					for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
						$file = $list [$_GET ["files"] [$i]];
						echo "<script>changeStatus('" . basename ( $file ["name"] ) . "', '" . $file ["size"] . "');</script>";
						$FtpBytesTotal = filesize ( $file ["name"] );
						$FtpChunkSize = round ( $FtpBytesTotal / 333 );
						$FtpTimeStart = getmicrotime ();
						if ($ftp->put ( $file ["name"], basename ( $file ["name"] ) )) {
							$time = round ( getmicrotime () - $FtpTimeStart );
							$speed = round ( $FtpBytesTotal / 1024 / $time, 2 );
							echo "<script>pr(100, '" . bytesToKbOrMbOrGb ( $FtpBytesTotal ) . "', " . $speed . ")</script>\r\n";
							flush ();
							
							if ($_GET ["del_ok"] && ! $disable_deleting) {
								if (@unlink ( $file ["name"] )) {
									$v_ads = " and deleted ";
									unset ( $list [$_GET ["files"] [$i]] );
								} else {
									$v_ads = ", but <b>not deleted </b>";
								}
								;
							} else
								$v_ads = "";
							
							echo "File <a href=\"ftp://" . $_POST ["login"] . ":" . $_POST ["password"] . "@" . $_POST ["host"] . ":" . $_POST ["port"] . $_POST ["dir"] . "/" . basename ( $file ["name"] ) . "\"><b>" . basename ( $file ["name"] ) . "</b></a> successfully uploaded$v_ads!" . "<br>Time: <b>" . sec2time ( $time ) . "</b><br>Average speed: <b>" . $speed . " KB/s</b><br><br>";
						} else {
							echo "Couldn't upload the file <b>" . basename ( $file ["name"] ) . "</b>!<br>";
						}
					}
					$ftp->quit ();
				}
			}
		}
	
	}
}
?>