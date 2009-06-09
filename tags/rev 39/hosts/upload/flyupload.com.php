<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$page = geturl("flyupload.com", 80, "/", "", 0, 0, 0, "");
			is_page($page);
			$cookie = GetCookies($page);
			preg_match('/UPLOAD_IDENTIFIER.*value="(.*?)"/i', $page, $id);
			preg_match('/form.*action="(.*?)"/', $page, $uplink);
			$action_url = $uplink[1];
			$url = parse_url($action_url);
			$post['prefix'] = 'flyupload';
			$post['UPLOAD_IDENTIFIER'] = $id[1];
			$post['subbutton'] = 'Share';
			$post['agreetos'] = 'on';
?>
<script>document.getElementById('info').style.display='none';</script>

<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php 
            $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://flyupload.com/', $cookie, $post, $lfile, $lname, "file");
			is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php

			preg_match('/Location: *(.*)/', $upfiles, $redir);
			$Href = $redir[1];
			$Url = parse_url($Href);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://flyupload.com/', $cookie, 0, 0, $_GET["proxy"],$pauth);
			preg_match('/URL:.*value="(.*?)"/i', $page, $dllink);
			$download_link = $dllink[1];
?>