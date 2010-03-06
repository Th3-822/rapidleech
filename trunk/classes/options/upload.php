<?php
function upload() {
	global $upload_services, $list;
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
		echo "<span class='warning'><b>".lang(48)."</b></span>";
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
<table align="center" border="0">
<?php
				for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
					$file = $list [($_GET ["files"] [$i])];
					$tid = md5 ( time () . "_file" . $_GET ["files"] [$i] );
?>
	<tr>
		<td><?php echo "<b>" . basename ( $file ["name"] ) . "</b>  , " . $file ["size"]; ?></td>
		<td valign="middle">
			<form action='upload.php' method='get' target='<?php echo $tid?>' onSubmit="return openwinup('<?php echo $tid?>');">
			<select name='uploaded' id='d_<?php echo $tid;?>'></select><script type='text/javascript'>fill_option('d_<?php echo $tid;?>');</script>
			<input type="hidden" name="filename" value='<?php echo base64_encode ( $file ["name"] ); ?>'>
			<input type='submit' value='Upload'>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<input type="checkbox" id="useuproxy" name="useuproxy" onClick="javascript:var displ=this.checked?'':'none';document.getElementById('uproxyconfig').style.display=displ;">&nbsp;<?php echo lang(245); ?>
		<td>
	</tr>
	<tr>
			<td colspan="2" align="center">
				<table style="display:none;" width="150" border="0" id="uproxyconfig">
				<tr><td><?php echo lang(246); ?>:&nbsp;</td><td><input type="text" name="uproxy" id="uproxy" size="20">
				<tr><td><?php echo lang(247); ?>:&nbsp;</td><td><input type="text" name="uproxyuser" id="uproxyuser" size="20"></td></tr>
				<tr><td><?php echo lang(248); ?>:&nbsp;</td><td><input type="text" name="uproxypass" id="uproxypass" size="20"></td></tr>
				</table>
			</td>
	</tr>
<?php } ?>
										</form></table><br />
<?php
	}
}
?>