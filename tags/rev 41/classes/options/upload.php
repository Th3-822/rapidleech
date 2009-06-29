<?php
function upload() {
	global $upload_services, $list;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select at least one file.<br><br>";
	} else {
		$d = opendir ( HOST_DIR . "upload/" );
		while ( false !== ($modules = readdir ( $d )) ) {
			if ($modules != "." && $modules != "..") {
				if (is_file ( HOST_DIR . "upload/" . $modules )) {
					if (strpos ( $modules, ".index.php" ))
						include_once (HOST_DIR . "upload/" . $modules);
				}
			}
		}
		
		if (empty ( $upload_services )) {
			echo "<span style='color:#FF6600'><b>No Supported Upload Services!</b></span>";
		} else {
			sort ( $upload_services );
			reset ( $upload_services );
			$cc = 0;
			foreach ( $upload_services as $upl ) {
				$uploadtype .= "\tupservice[" . ($cc ++) . "]=new Array('" . $upl . "','" . (str_replace ( "_", " ", $upl ) . " (" . ($max_file_size [$upl] == false ? "Unlim" : $max_file_size [$upl] . "Mb") . ")") . "');\n";
			}
					?>
<script type="text/javascript">
	var upservice = new Array();

	function fill_option(id)
		{
			var elem=document.getElementById(id);
			
			for (var i=0; i<upservice.length;i++)
				{
					elem.options[elem.options.length]=new Option(upservice[i][1]);
					elem.options[elem.options.length-1].value=upservice[i][0];
				}
		}

<?php echo $uploadtype; ?>

	function openwinup(id)
		{
			var options = "width=700,height=250,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=no";
			win=window.open('', id, options);
			win.focus();
			return true;
		}
</script>
<table align="center">
<?php
					for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
						$file = $list [($_GET ["files"] [$i])];
						$tid = md5 ( time () . "_file" . $_GET ["files"] [$i] );
?>                                      
	<tr>
		<form action='upload.php' method='get' target='<?php echo $tid?>' onSubmit="return openwinup('<?php echo $tid?>');">
		
		
		<td><b><?php echo basename ( $file ["name"] ) . "</b>  , " . $file ["size"]; ?></td>
		<td><select name='uploaded' id='d_<?php echo $tid;?>'></select><script type='text/javascript'>fill_option('d_<?php echo $tid;?>');</script></td>
		<td><input type='submit' value='Upload'></td>
	</tr>
	<tr>
		<td colspan="3" align="center"><input type=hidden name=filename
			value='<?php echo base64_encode ( $file ["name"] ); ?>'></td>
		</form>
	</tr>
			<?php } ?>
										</table>
<?php
		}
	}
}
?>