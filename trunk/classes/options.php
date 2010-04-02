<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
}
$all_act_files_exist = false;
if ((isset($_GET["act"]) || isset($_POST["act"])) && @$_GET["act"] !== 'files') {
  if ($options["disable_actions"]) { echo lang(328)."<br /><br />"; }
  elseif ($_GET['act'] == 'list') { $all_act_files_exist = true; }
  elseif ((!is_array($_GET['files']) || count($_GET['files']) < 1) && (!is_array($_POST['files']) || count($_POST['files']) < 1)) {
    echo lang(138)."<br /><br />";
  }
  else {
    $all_act_files_exist = true;
    foreach($_GET["files"] as $v) {
      if (!is_file($list[$v]["name"])) {
        $all_act_files_exist = false;
        echo sprintf(lang(64),'<b>'.htmlentities($list[$v]["name"]).'</b>').'<br />';
        break;
      }
    }
  }
}
if ($all_act_files_exist) {
	switch ($_GET ["act"]) {
		case "upload" :
			if ($options['disable_upload']) { break; }
			require(CLASS_DIR . "options/upload.php");
			upload();
			break;
		
		case "delete" :
			if ($options['disable_delete'] || $options['disable_deleting']) { break; }
			require(CLASS_DIR . "options/delete.php");
			delete();
			break;
		
		case "delete_go" :
			if ($options['disable_delete'] || $options['disable_deleting']) { break; }
			require(CLASS_DIR . "options/delete.php");
			delete_go();
			break;
		
		case "mail" :
			if ($options['disable_email']) { break; }
			require(CLASS_DIR . "options/mail.php");
			rl_mail();
			break;
		
		case "mail_go" :
			if ($options['disable_email']) { break; }
			require(CLASS_DIR . "options/mail.php");
			mail_go();
			break;
		
		case "boxes" :
			if ($options['disable_mass_email']) { break; }
			require(CLASS_DIR . "options/boxes.php");
			boxes();
			break;
		
		case "boxes_go" :
			if ($options['disable_mass_email']) { break; }
			require(CLASS_DIR . "options/boxes.php");
			boxes_go();
			break;
		
		case "md5" :
			if ($options['disable_md5']) { break; }
			require(CLASS_DIR . "options/md5.php");
			rl_md5();
			break;
		
		case "unzip" :
			if ($options['disable_unzip']) { break; }
			require(CLASS_DIR . "options/unzip.php");
			unzip();
			break;
		
		case "unzip_go" :
			if ($options['disable_unzip']) { break; }
			require(CLASS_DIR . "options/unzip.php");
			unzip_go();
			break;
		
		case "split" :
			if ($options['disable_split']) { break; }
			require(CLASS_DIR . "options/split.php");
			rl_split();
			break;
		
		case "split_go" :
			if ($options['disable_split']) { break; }
			require(CLASS_DIR . "options/split.php");
			split_go();
			break;
		
		case "merge" :
			if ($options['disable_merge']) { break; }
			require(CLASS_DIR . "options/merge.php");
			merge();
			break;
		
		case "merge_go" :
			if ($options['disable_merge']) { break; }
			require(CLASS_DIR . "options/merge.php");
			merge_go();
			break;
		
		case "rename" :
			if ($options['disable_rename'] || $options['disable_deleting']) { break; }
			require(CLASS_DIR . "options/rename.php");
			rl_rename();
			break;
		
		case "rename_go" :
			if ($options['disable_rename'] || $options['disable_deleting']) { break; }
			require(CLASS_DIR . "options/rename.php");
			rename_go();
			break;
		
		//MassRename
		case "mrename" :
			if ($options['disable_mass_rename'] || $options['disable_deleting']) { break; }
			require(CLASS_DIR . "options/mrename.php");
			mrename();
			break;
		
		case "mrename_go" :
			if ($options['disable_mass_rename'] || $options['disable_deleting']) { break; }
			require(CLASS_DIR . "options/mrename.php");
			mrename_go();
			break;
		
		//end MassRename
		

		case "ftp" :
			if ($options['disable_ftp']) { break; }
			require(CLASS_DIR . "options/ftp.php");
			ftp();
			break;
		
		case "ftp_go" :
			if ($options['disable_ftp']) { break; }
			require(CLASS_DIR . "options/ftp.php");
			ftp_go();
			break;
		
		case "unrar" :
			if ($options['disable_unrar']) { break; }
			require(CLASS_DIR . "options/unrar.php");
			unrar();
			break;
		
		case "unrar_go" :
			if ($options['disable_unrar']) { break; }
			require(CLASS_DIR . "options/unrar.php");
			unrar_go();
			break;
		
		case "rar" :
			if ($options['disable_rar']) { break; }
			require(CLASS_DIR . "options/rar.php");
			rar();
			break;
		
		case "rar_go" :
			if ($options['disable_rar']) { break; }
			require(CLASS_DIR . "options/rar.php");
			rar_go();
			break;
		
		case "zip" :
			if ($options['disable_zip']) { break; }
			require(CLASS_DIR . "options/zip.php");
			zip();
			break;
		
		case "zip_go" :
			if ($options['disable_zip']) { break; }
			require(CLASS_DIR . "options/zip.php");
			zip_go();
			break;
		
		case "pack" :
			if ($options['disable_tar']) { break; }
			require(CLASS_DIR . "options/pack.php");
			rl_pack();
			break;
		
		case "pack_go" :
			if ($options['disable_tar']) { break; }
			require(CLASS_DIR . "options/pack.php");
			pack_go();
			break;
		
		case "list":
			if ($options['disable_list']) { break; }
			require(CLASS_DIR . "options/list.php");
			rl_list();
			break;
	}
}
?>