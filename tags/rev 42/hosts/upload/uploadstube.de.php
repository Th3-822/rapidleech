<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			/*$page = geturl("netload.in", 80, "/", "", 0, 0, 0, "");
			is_page($page);
			preg_match('/form.*(index\.php\?id=.*?")/i', $page, $uplink);*/
			$action_url = 'http://www.uploadstube.de/upload.php';
			$url = parse_url($action_url);
			$post['typ'] = 'file';
?>
<script>document.getElementById('info').style.display='none';</script>

<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php 
            $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://www.uploadstube.de/', 0, $post, $lfile, $lname, "upfile[]");
			is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 
			preg_match('%http://www\.uploadstube\.de/download\.php\?file=\d+%i', $upfiles, $dllink);
			preg_match('%http://www\.uploadstube\.de/download\.php\?file=\d+&del=\d+%', $upfiles, $dlink);
			$download_link = $dllink[0];
			$delete_link = $dlink[0];	
?>