<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}
if (! $disable_action) {
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
		
		case "zip_add" :
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