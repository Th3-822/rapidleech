<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$page = geturl("shareator.com", 80, "/", "", 0, 0, 0, "");
			is_page($page);
			$action_url = 'http://shareator.com/upload/1/?X-Progress-ID='.rand(100000000000, 999999999999);
			$url = parse_url($action_url);
			$post['upload_type'] = 'file';
			$post['file_0_public'] = '1';
			$post['submit_btn'] = ' Upload! ';
?>
<script>document.getElementById('info').style.display='none';</script>

<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php 
            $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://shareator.com/', 0, $post, $lfile, $lname, "file_0");
			is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 
			preg_match('/name=\'fn\' *value=\'(.*?)\'/i', $upfiles, $fn);
			$fpost['op'] = 'upload_result';
			$fpost['st'] = 'OK';
			$fpost['fn'] = $fn[1];
			$Url = parse_url('http://shareator.com/');
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $action_url, 0, $fpost, 0, $_GET["proxy"],$pauth);
			preg_match('/direct Link:.*value="(.*?)"/i', $page, $dllink);
			preg_match('/Delete Link:.*value="(.*?)"/i', $page, $dlink);
			$download_link = $dllink[1];
			$delete_link = $dlink[1];	
?>