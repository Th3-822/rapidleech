<?php

function upload() {
	global $upload_services;

	$d = opendir(HOST_DIR . 'upload/');
	while (($modules = readdir($d)) !== false) {
		if (substr($modules, 0, 1) != '.' && preg_match('/\.index\.php$/i', $modules) && is_file(HOST_DIR . "upload/$modules")) include_once(HOST_DIR . "upload/$modules");
	}

	if (empty($upload_services)) {
		echo '<span class="warning"><b>' . lang(48) . "</b></span>";
		return;
	}

	sort($upload_services);
	reset($upload_services);
	$uploadtype = '';
	foreach($upload_services as $upl) $uploadtype .= "['$upl'," . (!empty($max_file_size[$upl]) ? $max_file_size[$upl] : 0) . "],";
	$uploadtype = substr($uploadtype, 0, -1);
	echo "<script type='text/javascript'>/* <![CDATA[ */var upservice=[$uploadtype];function fill_option(id){var elem=document.getElementById(id);for(var i=0;i<upservice.length;i++){elem.options[elem.options.length]=new Option(upservice[i][0].replace('_',' ')+(upservice[i][1]?' ('+upservice[i][1]+' MB)':''));elem.options[elem.options.length-1].value=upservice[i][0]}}function openwinup(id){var options='width=700,height=250,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=no';win=window.open('',id,options);win.focus();return true}/* ]]> */</script>\n<table align='center' border='0'>\n";
	for ($i = 0; $i < count($_GET['files']); $i++) {
		$file = $GLOBALS['list'][($_GET['files'][$i])];
		$tid = md5(time() . '_file' . $_GET['files'][$i]);
?>
<tr>
	<td valign="top"><?php echo '<b>' . basename($file['name']) . '</b>, ' . $file['size']; ?></td>
	<td valign="top" align="left">
		<form action="upload.php" method="get" target="<?php echo $tid?>" onsubmit="return openwinup('<?php echo $tid?>');" style="padding-bottom: 6px;">
			<select name="uploaded" id="d_<?php echo $tid;?>"></select><script type="text/javascript">/* <![CDATA[ */fill_option('d_<?php echo $tid;?>');/* ]]> */</script>
			<input type="hidden" name="filename" value="<?php echo base64_encode(basename($file['name'])); ?>" />
			<input type="submit" value="Upload" />
			<br /><input type="checkbox" name="useuproxy" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('uproxyconfig<?php echo $i; ?>').style.display=displ;" />&nbsp;<?php echo lang(245); ?>
			<br /><table style="display:none;" width="150" border="0" id="uproxyconfig<?php echo $i; ?>">
			<tr><td><?php echo lang(246); ?>:&nbsp;</td><td><input type="text" name="uproxy" size="20" /></td></tr>
			<tr><td><?php echo lang(247); ?>:&nbsp;</td><td><input type="text" name="uproxyuser" size="20" /></td></tr>
			<tr><td><?php echo lang(248); ?>:&nbsp;</td><td><input type="text" name="uproxypass" size="20" /></td></tr>
			</table>
		</form>
	</td>
</tr>
<?php
	}
	echo("</table><br />\n");
}
