<?php
function unzip() {
	global $list, $PHP_SELF;
?>
<form method="post" action="<?php echo $PHP_SELF; ?>">
<input type="hidden" name="act" value="unzip_go">
	<table align="center">
		<tr>
			<td>
				<table>
<?php
	for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
		$file = $list [$_GET ["files"] [$i]];
		require_once (CLASS_DIR . "unzip.php");
		if (file_exists($file['name'])) {
			$zip = new dUnzip2 ( $file['name'] );
			$flist = $zip->getList();
?>
					<tr><td align="center">
						<input type="hidden" name="files[]" value="<?php echo $_GET['files'][$i]; ?>">
						<b><?php echo basename($file['name']); ?></b> (<?php echo count($flist).' '.lang(204); ?>)
					</td></tr>
					<tr><td>
						<div style="overflow-y:scroll; height:150px; padding-left:5px;">
<?php
			foreach ($flist as $property) {
				echo($property['file_name'].'<br>');
			}
?>
						</div>
					</td></tr>
<?php
		}
	}
?>
				</table>
			</td>
			<td><input type="submit" name="submit" value="<?php echo lang(205); ?>"></td>
		</tr>
		<tr><td></td></tr>
	</table>
</form>
<?php
}

function unzip_go() {
	global $list, $forbidden_filetypes, $download_dir, $check_these_before_unzipping;
	require_once (CLASS_DIR . "unzip.php");
	$any_file_unzippped = false;
	for($i = 0; $i < count ( $_POST ["files"] ); $i ++) {
		$file = $list [$_POST ["files"] [$i]];
		if (file_exists ( $file ["name"] )) {
			$zip = new dUnzip2 ( $file ["name"] );
			$allf = $zip->getList ();
			$file_inside_zip_exists = false;
			$forbidden_inside_zip = false;
			foreach ($allf as $k => $properties) {
				if (file_exists($download_dir.basename($properties['file_name']))) {
					$file_inside_zip_exists = true; break;
				}
			}
			if ($check_these_before_unzipping) {
				foreach ( $allf as $k => $property ) {
					$zfiletype = strrchr ( $property ['file_name'], "." );
					if (is_array ( $forbidden_filetypes ) && in_array ( strtolower ( $zfiletype ), $forbidden_filetypes )) {
						$forbidden_inside_zip = true; break;
					}
				}
			}
			if ($file_inside_zip_exists) {
				echo 'Some file(s) inside <b>'.htmlentities(basename($file["name"])).'</b> already exist on download directory';
				echo "<br><br>";
			}
			elseif ($forbidden_inside_zip) {
				printf(lang(181), $zfiletype);
				echo "<br><br>";
			}
			else {
				$zip->unzipAll ( $download_dir );
				if ($zip->getList () != false) {
					$any_file_unzippped = true;
					echo '<b>'.htmlentities(basename($file["name"])).'</b>&nbsp;unzipped successfully<br><br>';
					foreach ($allf as $k => $properties) {
						$efile = $download_dir.basename($properties['file_name']);
						if (is_file($efile)) {
							$time = filemtime($efile); while (isset($list[$time])) { $time++; }
							$list[$time] = array("name" => $efile, "size" => bytesToKbOrMbOrGb(filesize($efile)), "date" => $time);
						}
					}
					if (!updateListInFile($list)) { echo lang(146)."<br><br>"; }
				}
				else {
					echo "File <b>".$file["name"]."</b> not found!<br><br>";
				}
			}
		}
	}
}
?>