<?php
function rl_pack() {
	global $list;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select at least one file.<br><br>";
	} else {
?>
                            <form method="post"><input type="hidden"
			name="act" value="pack_go">
<?php
		echo count ( $_GET ["files"] ) . " file" . (count ( $_GET ["files"] ) > 1 ? "s" : "") . ":<br>";
		
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [$_GET ["files"] [$i]];
			?>
                                <input type="hidden" name="files[]"
			value="<?php echo $_GET ["files"] [$i]; ?>"> <b><?php echo basename ( $file ["name"] ); ?></b><?php echo $i == count ( $_GET ["files"] ) - 1 ? "." : ",&nbsp;"; }?><br>
		<br>
		<table align="center">
			<tr>
				<td>Archive Name:&nbsp;<input type="text" name="arc_name"
					size="30"></td>
				<td><input type="submit" value="Pack"></td>
			</tr>
			<tr>
				<td>Save To:&nbsp;<input type="text" name="path" size="30"
					value="<?php echo ($_COOKIE ["path"] ? $_COOKIE ["path"] : (strstr ( ROOT_DIR, "\\" ) ? addslashes ( dirname ( __FILE__ ) ) : dirname ( __FILE__ ))); ?>">
				</td>
			</tr>
		</table>
		<table align="center">
			<tr>
				<td>For use compress gz or bz2 write extension as Tar.gz or
				Tar.bz2;<br>
				Else this archive will be uncompress Tar<br>
				</td>
			</tr>
		</table>
		</form>
<?php
	}
}

function pack_go() {
	global $list;
	$smthExists = true;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select at least one file.<br><br>";
		break;
	}
	
	$arc_name = $_GET ["arc_name"];
	if (! $arc_name) {
		echo "Please enter an archive name!<br><br>";
		break;
	}
	;
	
	if (file_exists ( $arc_name )) {
		echo "File <b>" . $arc_name . "</b> already exists!<br><br>";
		break;
	}
	
	for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
		$file = $list [$_GET ["files"] [$i]];
		if (file_exists ( $file ["name"] )) {
			$v_list [] = $file ["name"];
		} else {
			echo "File <b>" . $file ["name"] . "</b> not found!<br><br>";
		}
	}
	if (! $v_list) {
		echo "An error occured!<br><br>";
		break;
	}
	$arc_name = $path . '/' . $arc_name;
	//$arc_name = dirname($arc_name).PATH_SPLITTER.$arc_name;
	

	require_once (CLASS_DIR . "tar.php");
	$tar = new Archive_Tar ( $arc_name );
	$tar->create ( $v_list, $arc_method );
	if (! file_exists ( $arc_name )) {
		echo "Error! Archive not created.<br><br>";
		break;
	}
	
	if (count ( $v_list = $tar->listContent () ) > 0) {
		echo "File";
		echo count ( $v_list ) > 1 ? "s" : "";
		echo "<br>";
		for($i = 0; $i < sizeof ( $v_list ); $i ++) {
			echo "File " . $v_list [$i] ["filename"] . " was packed <br>";
		}
		echo "Packed in archive <b>$arc_name</b><br>";
		
		$stmp = strtolower ( $arc_name );
		if (strrchr ( $stmp, "tar.gz" ) + 5 == strlen ( $stmp )) {
			$arc_method = "Tar.gz";
		} elseif (strrchr ( $stmp, "tar.bz2" ) + 6 == strlen ( $stmp )) {
			$arc_method = "Tar.bz2";
		} else {
			$arc_method = "Tar";
		}
		;
		unset ( $stmp );
		
		$time = explode ( " ", microtime () );
		$time = str_replace ( "0.", $time [1], $time [0] );
		$list [$time] = array ("name" => $arc_name, "size" => bytesToKbOrMbOrGb ( filesize ( $arc_name ) ), "date" => $time, "link" => "", "comment" => "archive " . $arc_method );
	} else {
		echo "Error! Archive is Empty.<br><br>";
	}
	if (! updateListInFile ( $list )) {
		echo "Couldn't Update!<br><br>";
	}
}
?>