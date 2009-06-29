<?php
function zip() {
	global $list;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select at least one file.<br><br>";
	} else {
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [($_GET ["files"] [$i])];
		}
		print "What do you want to do?<br><br>";
?>
<form name="ziplist" method="post">
		<table cellspacing="5">
			<tr>
				<td align="center"><select name="act" id="act" onChange="zip();">
					<option selected>Select an Action</option>
					<option value="zip_add">Add files to a ZIP archive</option>
				</select></td>
				<td></td>
				<td id="add" align="center" style="DISPLAY: none;">
				<table>
					<tr>
						<td>Archive Name:&nbsp;<input type="text" name="archive"
							size="25" value=".zip"><br>
						</td>
					</tr>
					<tr>
						<td><input type="checkbox" name="no_compression">&nbsp;Do not
						use compression<br>
						</td>
					</tr>
				</table>
				<table>
					<tr>
						<td><input type="submit" value="Add Files"></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
<?php
		echo "<br>Selected File" . (count ( $_GET ["files"] ) > 1 ? "s" : "") . ": ";
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [($_GET ["files"] [$i])];
			print "<input type=\"hidden\" name=\"files[]\" value=\"{$_GET[files][$i]}\">\r\n";
			echo "<b>" . basename ( $file ["name"] ) . "</b>";
			echo ($i == count ( $_GET ["files"] ) - 1) ? "." : ",&nbsp;";
		}
?>
</form>
<?php
	}
}

function zip_go() {
	global $list, $download_dir;
	$saveTo = realpath ( $download_dir ) . '/';
	$_GET ["archive"] = (strlen ( trim ( urldecode ( $_GET ["archive"] ) ) ) > 4 && substr ( trim ( urldecode ( $_GET ["archive"] ) ), - 4 ) == ".zip") ? trim ( urldecode ( $_GET ["archive"] ) ) : "archive.zip";
	$_GET ["archive"] = $saveTo.$_GET ["archive"];
	for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
		$files [] = $list [($_GET ["files"] [$i])];
	}
	foreach ( $files as $file ) {
		$CurrDir = ROOT_DIR;
		$inCurrDir = stristr ( dirname ( $file ["name"] ), $CurrDir ) ? TRUE : FALSE;
		if ($inCurrDir) {
			$add_files [] = substr ( $file ["name"], (strlen ( $CurrDir ) + 1) );
		}
	}
	require_once (CLASS_DIR . "pclzip.php");
	$archive = new PclZip ( $_GET ["archive"] );
	$no_compression = ($_GET ["no_compression"] == "on") ? PCLZIP_OPT_NO_COMPRESSION : 77777;
	$remove_path = PCLZIP_OPT_REMOVE_ALL_PATH;
	if (file_exists ( $_GET ["archive"] )) {
		$v_list = $archive->add ( $add_files, $no_compression, $remove_path );
	} else {
		$v_list = $archive->create ( $add_files, $no_compression, $remove_path );
	}
	if ($v_list == 0) {
		echo "Error: " . $archive->errorInfo ( true ) . "<br><br>";
		return;
	} else {
		echo "Archive <b>" . $_GET ["archive"] . "</b> successfully created!<br><br>";
	}
	if (!file_exists ( $_GET ['archive'] ) ) {
		// Add zip file into files list
		$time = filemtime($_GET['archive']);
		$list[$time] = array('name' => $_GET['archive'],
			"size" => bytesToKbOrMbOrGb ( filesize ( $_GET['archive'] ) ),
			"date" => $time);
		if (! updateListInFile ( $list )) {
			echo "Couldn't update file list. Problem writing to file!<br><br>";
		}
	}
}
?>