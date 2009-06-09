<?php
function unzip() {
	global $list;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select at least one file.<br><br>";
	} else {
?>
                          <form method="post"><input type="hidden"
			name="act" value="unzip_go">
		<table align="center">
			<tr>
				<td>
				<table>
<?php
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [$_GET ["files"] [$i]];
?>
                                    <input type="hidden"
						name="files[]" value="<?php echo $_GET ["files"] [$i]; ?>" />
					<tr>
						<td align="center"><b><?php echo basename ( $file ["name"] ); ?></b></td>
					</tr>
					<tr>
						<td></td>
					</tr>
<?php
		}
?>
                                  </table>
				</td>
				<td><input type="submit" value="Unzip"></td>
			</tr>
			<tr>
				<td></td>
			</tr>
		</table>
		</form>
<?php
	}
}

function unzip_go() {
	global $list, $forbidden_filetypes, $download_dir, $check_these_before_unzipping;
	$unzip_file = FALSE;
	require_once (CLASS_DIR . "unzip.php");
	for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
		$file = $list [$_GET ["files"] [$i]];
		if (file_exists ( $file ["name"] )) {
			//$zip_dir = basename($file["name"], ".zip");
			/*if(!@mkdir($download_dir.$zip_dir, 0777))
						{
							html_error('Error : Unable to create director', 0);
						}*/
			$zip = new dUnzip2 ( $file ["name"] );
			//$zip->debug = true;
			
	
			if ($check_these_before_unzipping) {
				$allf = $zip->getList ();
				foreach ( $allf as $file => $property ) {
					$zfiletype = strrchr ( $property ['file_name'], "." );
					if (is_array ( $forbidden_filetypes ) && in_array ( strtolower ( $zfiletype ), $forbidden_filetypes )) {
						exit ( "The filetype $zfiletype is forbidden to be unzipped<script>alert('The filetype $zfiletype is forbidden to be unzipped');window.location.replace('http://{$_SERVER['SERVER_NAME']}')</script>" );
					}
				
				}
			}
			$zip->unzipAll ( $download_dir );
			if ($zip->getList () != false) {
				echo '<b>' . $file ["name"] . '</b>&nbsp;unzipped successfully<br>';
			}
			$unzip_file = TRUE;
		} else {
			echo "File <b>" . $file ["name"] . "</b> not found!<br><br>";
		}
	}
	if ($unzip_file) {
		if (! updateListInFile ( $list )) {
			echo "Couldn't Update<br><br>";
		}
	}
}
?>