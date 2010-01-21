<?php
/**
 * Renders the file actions, this function does NOT output directly to the browser
 * 
 * @return string The string will contain the <select></select> tag
 *
 */
function renderActions() {
	global $options;
	$return = "";
	$return .= '<select name="act" onChange="javascript:void(document.flist.submit());">';
	if ($options['disable_actions']) {
		$return .= '<option selected="selected">'.lang(328).'</option>';
		$return .= '</select>';
		return $return;
	}
	$return .= '<option selected="selected">'.lang(285).'</option>';
	if (!$options['disable_upload']) $return .= '<option value="upload">'.lang(286).'</option>';
	if (!$options['disable_ftp']) $return .= '<option value="ftp">'.lang(287).'</option>';
	if (!$options['disable_email']) $return .= '<option value="mail">'.lang(288).'</option>';
	if (!$options['disable_mass_email']) $return .= '<option value="boxes">'.lang(289).'</option>';
	if (!$options['disable_split']) $return .= '<option value="split">'.lang(290).'</option>';
	if (!$options['disable_merge']) $return .= '<option value="merge">'.lang(291).'</option>';
	if (!$options['disable_md5']) $return .= '<option value="md5">'.lang(292).'</option>';
	if ((file_exists ( CLASS_DIR . "pear.php" ) || file_exists ( CLASS_DIR . "tar.php" )) && !$options['disable_tar'])
		$return .= '<option value="pack">'.lang(293).'</option>';
	if (file_exists ( CLASS_DIR . "pclzip.php" ) && !$options['disable_zip'])
		$return .= '<option value="zip">'.lang(294).'</option>';
	if (file_exists ( CLASS_DIR . "unzip.php" ) && !$options['disable_unzip'])
		$return .= '<option value="unzip">'.lang(295).'</option>';
	if (substr(PHP_OS, 0, 3) != "WIN" && @file_exists(CLASS_DIR."rar.php")) {
		$return .= '<option value="rar">'.lang(338).'</option>';
		if (@file_exists(ROOT_DIR.'/rar/rar') || @file_exists(ROOT_DIR.'/rar/unrar')) { $return .= '<option value="unrar">'.lang(339).'</option>'; }
	}
	if (!$options['disable_deleting']) {
		if (!$options['disable_rename']) $return .= '<option value="rename">'.lang(296).'</option>';
		if (!$options['disable_mass_rename']) $return .= '<option value="mrename">'.lang(297).'</option>';
		if (!$options['disable_delete']) $return .= '<option value="delete">'.lang(298).'</option>';
	}
	if (!$options['disable_list']) $return .= '<option value="list">'.lang(299).'</option>';
	$return .= '</select>';
	return $return;
}
?>
