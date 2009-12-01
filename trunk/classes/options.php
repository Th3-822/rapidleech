<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
}
$all_act_files_exist = false;
if ((isset($_GET["act"]) || isset($_POST["act"])) && @$_GET["act"] !== 'files') {
  if ($disable_action) { echo lang(328)."<br><br>"; }
  elseif ($_GET['act'] == 'list') { $all_act_files_exist = true; }
  elseif ((!is_array($_GET['files']) || count($_GET['files']) < 1) && (!is_array($_POST['files']) || count($_POST['files']) < 1)) {
    echo lang(138)."<br><br>";
  }
  else {
    $all_act_files_exist = true;
    foreach($_GET["files"] as $v) {
      if (!is_file($list[$v]["name"])) {
        $all_act_files_exist = false;
        echo sprintf(lang(64),htmlentities($list[$v]["name"]));
        break;
      }
    }
  }
}
if ($all_act_files_exist) {
	switch ($_GET ["act"]) {
		case "upload" :
			require(CLASS_DIR . "options/upload.php");
			upload();
			break;
		
		case "delete" :
			require(CLASS_DIR . "options/delete.php");
			delete();
			break;
		
		case "delete_go" :
			require(CLASS_DIR . "options/delete.php");
			delete_go();
			break;
		
		case "mail" :
			require(CLASS_DIR . "options/mail.php");
			rl_mail();
			break;
		
		case "mail_go" :
			require(CLASS_DIR . "options/mail.php");
			mail_go();
			break;
		
		case "boxes" :
			require(CLASS_DIR . "options/boxes.php");
			boxes();
			break;
		
		case "boxes_go" :
			require(CLASS_DIR . "options/boxes.php");
			boxes_go();
			break;
		
		case "md5" :
			require(CLASS_DIR . "options/md5.php");
			rl_md5();
			break;
		
		case "unzip" :
			require(CLASS_DIR . "options/unzip.php");
			unzip();
			break;
		
		case "unzip_go" :
			require(CLASS_DIR . "options/unzip.php");
			unzip_go();
			break;
		
		case "split" :
			require(CLASS_DIR . "options/split.php");
			rl_split();
			break;
		
		case "split_go" :
			require(CLASS_DIR . "options/split.php");
			split_go();
			break;
		
		case "merge" :
			require(CLASS_DIR . "options/merge.php");
			merge();
			break;
		
		case "merge_go" :
			require(CLASS_DIR . "options/merge.php");
			merge_go();
			break;
		
		case "rename" :
			require(CLASS_DIR . "options/rename.php");
			rl_rename();
			break;
		
		case "rename_go" :
			require(CLASS_DIR . "options/rename.php");
			rename_go();
			break;
		
		//MassRename
		case "mrename" :
			require(CLASS_DIR . "options/mrename.php");
			mrename();
			break;
		
		case "mrename_go" :
			require(CLASS_DIR . "options/mrename.php");
			mrename_go();
			break;
		
		//end MassRename
		

		case "ftp" :
			require(CLASS_DIR . "options/ftp.php");
			ftp();
			break;
		
		case "ftp_go" :
			require(CLASS_DIR . "options/ftp.php");
			ftp_go();
			break;
		
		case "zip" :
			require(CLASS_DIR . "options/zip.php");
			zip();
			break;
		
		case "zip_go" :
			require(CLASS_DIR . "options/zip.php");
			zip_go();
			break;
		
		case "pack" :
			require(CLASS_DIR . "options/pack.php");
			rl_pack();
			break;
		
		case "pack_go" :
			require(CLASS_DIR . "options/pack.php");
			pack_go();
			break;
		
		case "list":
			require(CLASS_DIR . "options/list.php");
			rl_list();
			break;
	}
}
?>