<?php
function rl_pack() {
	global $list, $options;
?>
<form method="post" action="<?php echo $PHP_SELF; ?>"><input type="hidden" name="act" value="pack_go" />
<?php
	echo count ( $_GET ["files"] ) . " file" . (count ( $_GET ["files"] ) > 1 ? "s" : "") . ":<br />";
	for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
		$file = $list [$_GET ["files"] [$i]];
?>
<input type="hidden" name="files[]" value="<?php echo $_GET ["files"] [$i]; ?>" />
<b><?php echo basename ( $file ["name"] ); ?></b><?php echo $i == count ( $_GET ["files"] ) - 1 ? "." : ",&nbsp;"; }?><br />
	<br />
	<table align="center">
		<tr>
			<td><?php echo lang(195); ?>:&nbsp;<input type="text" name="arc_name" size="30" value="" />&nbsp;<b>.</b>&nbsp;
				<select name="arc_ext">
				<option value="tar" selected="selected">tar</option>
<?php
			if (!$options['disable_archive_compression']) {
?>
				<option value="tar.gz">tar.gz</option>
				<option value="tar.bz">tar.bz</option>
<?php
			}
?>
				</select>
			</td>
			<td><input type="submit" value="Pack" /></td>
		</tr>
<?php
  if ($options['download_dir_is_changeable']) {
?>
		<tr>
			<td><?php echo lang(40); ?>:&nbsp;<input type="text" name="saveTo" size="30"
			value="<?php echo addslashes ( $options['download_dir'] ); ?>" /></td>
		</tr>
<?php
  }
?>
	</table>
</form>
<?php
}

function pack_go() {
	global $list, $options;
	$arc_name = basename($_POST["arc_name"].'.'.$_POST["arc_ext"]);	
	$saveTo = ($options['download_dir_is_changeable'] ? stripslashes ( $_POST ["saveTo"] [$i] ) : realpath ( $options['download_dir'] )) . '/';
	$v_list = array();
	if (!$_POST["arc_name"] || !$_POST["arc_ext"]) {
		echo lang(196)."<br /><br />";
	}
	elseif (file_exists ( $saveTo . $arc_name )) {
		printf(lang(179),$arc_name);
		echo "<br /><br />";
	}
	else {
		for($i = 0; $i < count ( $_POST ["files"] ); $i ++) {
			$file = $list [$_POST ["files"] [$i]];
			if (file_exists ( $file ["name"] )) {
			$v_list [] = $file ["name"];
		} else {
			printf(lang(145),$file['name']);
			echo "<br /><br />";
		}
	}
	if (count($v_list) < 1) {
		echo lang(137)."<br /><br />";
	}
	else {
		$arc_name = $saveTo.$arc_name;
		require_once (CLASS_DIR . "tar.php");
		$tar = new Archive_Tar ( $arc_name );
		if ($tar->error != '') { echo $tar->error."<br /><br />"; }
		else {
			$remove_path = realpath($options['download_dir']).'/';
			$tar->createModify($v_list, '', $remove_path);
			if (! file_exists ( $arc_name )) {
			echo lang(197)."<br /><br />";
		}
		else {
			if (count ( $v_list = $tar->listContent () ) > 0) {
				for($i = 0; $i < sizeof ( $v_list ); $i ++) {
				printf(lang(198),$v_list[$i]['filename']);
				echo " <br />";
			}
			printf(lang(199),$arc_name);
			echo "<br />";
			$stmp = strtolower ( $arc_name );
			$arc_method = "Tar";
			if (!$options['disable_archive_compression']) {
				if (strrchr ( $stmp, "tar.gz" ) + 5 == strlen ( $stmp )) { $arc_method = "Tar.gz"; }
				elseif (strrchr ( $stmp, "tar.bz2" ) + 6 == strlen ( $stmp )) { $arc_method = "Tar.bz2"; }
			}
			unset ( $stmp );
			$time = explode ( " ", microtime () );
			$time = str_replace ( "0.", $time [1], $time [0] );
			$list [$time] = array ("name" => $arc_name, "size" => bytesToKbOrMbOrGb ( filesize ( $arc_name ) ), "date" => $time, "link" => "", "comment" => "archive " . $arc_method );
		} else {
			echo lang(200)."<br /><br />";
		}
		if (! updateListInFile ( $list )) {
			echo lang(9).'<br /><br />';
		}
        }
      }
    }
  }
}
?>