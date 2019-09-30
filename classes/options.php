<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
}
$all_act_files_exist = false;
if (!empty($_GET['act']) && $_GET['act'] !== 'files') {
	if (!empty($options['disable_actions'])) echo lang(328).'<br /><br />';
	elseif ($_GET['act'] == 'list') $all_act_files_exist = true;
	elseif ((!is_array($_GET['files']) || count($_GET['files']) < 1) && (!is_array($_POST['files']) || count($_POST['files']) < 1)) echo lang(138).'<br /><br />';
	else {
		$all_act_files_exist = true;
		foreach($_GET['files'] as $v) {
			if (empty($list[$v])) {
				$file = '';
				$size_time = false;
			} else if (!is_file(($file = $list[$v]['name']))) {
				if ($options['2gb_fix'] && in_array($_GET['act'], array('delete', 'delete_go')) && file_exists($file) && !is_dir($file) && !is_link($file)) $size_time = file_data_size_time($file);
				else $size_time = false;
			} else $size_time = true;
			if ($size_time === false) {
				$all_act_files_exist = false;
				echo sprintf(lang(64),'<b>'.htmlentities($file).'</b>').'<br />';
				break;
			}
		}
	}
}
if ($all_act_files_exist) {
	switch($_GET['act']) {
		case 'upload' :
			if (!empty($options['disable_upload'])) break;
			require(CLASS_DIR . 'options/upload.php');
			upload();
			break;

		case 'delete' :
			if (!empty($options['disable_delete'])) break;
			require(CLASS_DIR . 'options/delete.php');
			delete();
			break;

		case 'delete_go' :
			if (!empty($options['disable_delete'])) break;
			require(CLASS_DIR . 'options/delete.php');
			delete_go();
			break;

		case 'mail' :
			if (!empty($options['disable_email'])) break;
			require(CLASS_DIR . 'options/mail.php');
			rl_mail();
			break;

		case 'mail_go' :
			if (!empty($options['disable_email'])) break;
			require(CLASS_DIR . 'options/mail.php');
			mail_go();
			break;

		case 'boxes' :
			if (!empty($options['disable_mass_email'])) break;
			require(CLASS_DIR . 'options/boxes.php');
			boxes();
			break;

		case 'boxes_go' :
			if (!empty($options['disable_mass_email'])) break;
			require(CLASS_DIR . 'options/boxes.php');
			boxes_go();
			break;

		case 'md5' :
			if (!empty($options['disable_hashing'])) break;
			require(CLASS_DIR . 'options/md5.php');
			rl_md5();
			break;

		case 'crc32' :
			if (!empty($options['disable_hashing'])) break;
			require(CLASS_DIR . 'options/crc32.php');
			rl_crc32();
			break;

		case 'sha1' :
			if (!empty($options['disable_hashing'])) break;
			require(CLASS_DIR . 'options/sha1.php');
			rl_sha1();
			break;

		case 'md5_change' :
			if (!empty($options['disable_md5_change'])) break;
			require(CLASS_DIR . 'options/md5change.php');
			md5_change();
			break;

		case 'md5_change_go' :
			if (!empty($options['disable_md5_change'])) break;
			require(CLASS_DIR . 'options/md5change.php');
			md5_change_go();
			break;

		case 'unzip' :
			if (!empty($options['disable_unzip'])) break;
			require(CLASS_DIR . 'options/unzip.php');
			unzip();
			break;

		case 'unzip_go' :
			if (!empty($options['disable_unzip'])) break;
			require(CLASS_DIR . 'options/unzip.php');
			unzip_go();
			break;

		case 'split' :
			if (!empty($options['disable_split'])) break;
			require(CLASS_DIR . 'options/split.php');
			rl_split();
			break;

		case 'split_go' :
			if (!empty($options['disable_split'])) break;
			require(CLASS_DIR . 'options/split.php');
			split_go();
			break;

		case 'merge' :
			if (!empty($options['disable_merge'])) break;
			require(CLASS_DIR . 'options/merge.php');
			merge();
			break;

		case 'merge_go' :
			if (!empty($options['disable_merge'])) break;
			require(CLASS_DIR . 'options/merge.php');
			merge_go();
			break;

		case 'rename' :
			if (!empty($options['disable_rename'])) break;
			require(CLASS_DIR . 'options/rename.php');
			rl_rename();
			break;

		case 'rename_go' :
			if (!empty($options['disable_rename'])) break;
			require(CLASS_DIR . 'options/rename.php');
			rename_go();
			break;

		//MassRename
		case 'mrename' :
			if (!empty($options['disable_mass_rename'])) break;
			require(CLASS_DIR . 'options/mrename.php');
			mrename();
			break;

		case 'mrename_go' :
			if (!empty($options['disable_mass_rename'])) break;
			require(CLASS_DIR . 'options/mrename.php');
			mrename_go();
			break;
		
		//end MassRename

		case 'ftp' :
			if (!empty($options['disable_ftp'])) break;
			require(CLASS_DIR . 'options/ftp.php');
			ftp();
			break;

		case 'ftp_go' :
			if (!empty($options['disable_ftp'])) break;
			require(CLASS_DIR . 'options/ftp.php');
			ftp_go();
			break;

		case 'unrar' :
			if (!empty($options['disable_unrar'])) break;
			require(CLASS_DIR . 'options/unrar.php');
			unrar();
			break;

		case 'unrar_go' :
			if (!empty($options['disable_unrar'])) break;
			require(CLASS_DIR . 'options/unrar.php');
			unrar_go();
			break;

		case 'rar' :
			if (!empty($options['disable_rar'])) break;
			require(CLASS_DIR . 'options/rar.php');
			rar();
			break;

		case 'rar_go' :
			if (!empty($options['disable_rar'])) break;
			require(CLASS_DIR . 'options/rar.php');
			rar_go();
			break;

		case 'zip' :
			if (!empty($options['disable_zip'])) break;
			require(CLASS_DIR . 'options/zip.php');
			zip();
			break;

		case 'zip_go' :
			if (!empty($options['disable_zip'])) break;
			require(CLASS_DIR . 'options/zip.php');
			zip_go();
			break;

		case 'pack' :
			if (!empty($options['disable_tar'])) break;
			require(CLASS_DIR . 'options/pack.php');
			rl_pack();
			break;

		case 'pack_go' :
			if (!empty($options['disable_tar'])) break;
			require(CLASS_DIR . 'options/pack.php');
			pack_go();
			break;

		case 'list':
			if (!empty($options['disable_list'])) break;
			require(CLASS_DIR . 'options/list.php');
			rl_list();
			break;
	}
}
?>