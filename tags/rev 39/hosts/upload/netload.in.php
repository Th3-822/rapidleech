<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
     // addon debug 
     /*
define('RAPIDLEECH', 'yes');
require_once("http.php");
require_once("other.php");
$nn = "\r\n";
*/
// end addon
			$page = geturl("netload.in", 80, "/", "", 0, 0, 0, "");
			is_page($page);
			preg_match('/http:\/\/.+netload\.in\/upload\.php\?id=\w+/i', $page, $uplink);
			$action_url = $uplink[0];
			$url = parse_url($action_url);
			$post['remote_file'] = 'http://';
?>
<script>document.getElementById('info').style.display='none';</script>

<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php 
            $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://netload.in/', 0, $post, $lfile, $lname, "file");
			is_page($upfiles);
			preg_match("/http:\/\/.+?\}/i",$upfiles,$arr);
            $location = parse_url($arr[0]);
			$pars2 = $location['scheme']."://".$location['host'].$location['path']."?".$location['query'];
			$page = geturl("netload.in", 80, $pars2, "", 0, 0, 0, "");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 

			preg_match('/http:\/\/netload\.in\/\w+/i', $page, $dllink);
			preg_match('/http:\/\/netload\.in\/index\.php\?id=\d{1,4}/i', $page, $dlink1);
			preg_match_all('/\w{32}/i', $page, $dlink2);
			$download_link = $dllink[0].".html";
			$delete_link = $dlink1[0]."&file_id=".$dlink2[0][1];

		// 030\03\2009  update by kaox ;	
?>