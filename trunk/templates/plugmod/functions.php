<?php
/**
 * Renders the file actions, this function does NOT output directly to the browser
 * 
 * @return string The string will contain the <select></select tag
 *
 */
function renderActions() {
	global $options;
	$return = "";
	$return .= '<select name="act" onChange="javascript:void(document.flist.submit());">';
	if ($options['disable_actions']) {
		$return .= '<option selected="selected">Actions Disabled</option>';
		$return .= '</select>';
		return $return;
	}
	$return .= '<option selected="selected">Action</option>';
	if (!$options['disable_upload']) $return .= '<option value="upload">Upload</option>';
	if (!$options['disable_ftp']) $return .= '<option value="ftp">FTP File</option>';
	if (!$options['disable_email']) $return .= '<option value="mail">E-Mail</option>';
	if (!$options['disable_mass_email']) $return .= '<option value="boxes">Mass E-mail</option>';
	if (!$options['disable_split']) $return .= '<option value="split">Split Files</option>';
	if (!$options['disable_merge']) $return .= '<option value="merge">Merge Files</option>';
	if (!$options['disable_md5']) $return .= '<option value="md5">MD5 Hash</option>';
	if ((file_exists ( CLASS_DIR . "pear.php" ) || file_exists ( CLASS_DIR . "tar.php" )) && !$options['disable_tar'])
		$return .= '<option value="pack">Pack Files</option>';
	if (file_exists ( CLASS_DIR . "pclzip.php" ) && !$options['disable_zip'])
		$return .= '<option value="zip">ZIP Files</option>';
	if (file_exists ( CLASS_DIR . "unzip.php" ) && !$options['disable_unzip'])
		$return .= '<option value="unzip">Unzip Files</option>';
	if (!$options['disable_deleting']) {
		if (!$options['disable_rename']) $return .= '<option value="rename">Rename</option>';
		if (!$options['disable_mass_rename']) $return .= '<option value="mrename">Mass Rename</option>';
		if (!$options['disable_delete']) $return .= '<option value="delete">Delete</option>';
	}
	if (!$options['disable_list']) $return .= '<option value="list">List Links</option>';
	$return .= '</select>';
	return $return;
}
?>