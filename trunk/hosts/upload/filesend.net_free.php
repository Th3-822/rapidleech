<?php
			$url_id = "http://dl3.filesend.net/ubr_link_upload.php?config_file=ubr_default_config.php&rnd_id=".rand(1000000000000, 9999999999999);
			$Url = parse_url($url_id);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match('/startUpload\("(.*?)"/i', $page, $id);
			
			$url_action = "http://dl3.filesend.net/cgi-bin/uploada.cgi?upload_id=".$id[1];
			$fpost['confirm'] = 'on';
			
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$login_url, 0, $fpost, $lfile, $lname, "upfile_0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			$locat=trim(cut_str($upfiles,'Location: ',"\n"));
			$Url = parse_url($locat);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			
			$locat2=cut_str($page,"opener.location='","'");
			$Url = parse_url($locat2);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			
			
			$download_link = cut_str($page,'Download Link: <a href="','"');
			$delete_link = cut_str($page,'Delete Link: <a href="','"');
?>