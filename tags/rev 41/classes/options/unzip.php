<?php
function unzip() {
	global $list, $PHP_SELF;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select at least one file.<br><br>";
	} else {
		echo('<form method="post" action="'.$PHP_SELF.'">');
		echo('<input type="hidden" name="act" value="unzip_go" />');
		echo('<table align="center">');
		echo('<tr>');
		echo('<td>');
		echo('<table>');
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [$_GET ["files"] [$i]];
			require_once (CLASS_DIR . "unzip.php");
			if (file_exists($file['name'])) {
				$zip = new dUnzip2 ( $file['name'] );
				$flist = $zip->getList();
				echo('<input type="hidden" name="files[]" value="'.$_GET['files'][$i].'" />');
				echo('<tr>');
				echo('<td align="center"><b>'.basename($file['name']).'</b> ('.count($flist).' files and folders)</tr>');
				echo('</tr>');
				echo('<tr><td>');
				echo('<div style="overflow-y:scroll; height:150px; padding-left:5px;">');
				foreach ($flist as $property) {
					echo($property['file_name'].'<br />');
				}
				echo('</div>');
				echo('</td></tr>');
			}
		}
		echo('</table>');
		echo('</td>');
		echo('<td><input type="submit" name="submit" value="Unzip"></td>');
		echo('</tr>');
		echo('<tr><td></td></tr>');
		echo('</table>');
		echo('</form>');
	}
}

function unzip_go() {
	global $list, $forbidden_filetypes, $download_dir, $check_these_before_unzipping;
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