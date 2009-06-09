<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$page = file_get_contents('http://load.to/');
			is_page($page);
			$cookie = GetCookies($page);
			preg_match('/<form.*name="form_upload".*action="(.*?)"/i', $page, $uplink);
			$action_url = $uplink[1];
			$url = parse_url($action_url);
			$post['imbedded_progress_bar'] = 1;
			$post['upload_range'] = 1;
?>
<script>document.getElementById('info').style.display='none';</script>

<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php 
            $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://load.to/', $cookie, $post, $lfile, $lname, "upfile_0");
			is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 
			preg_match('/Location: *(.*)/', $upfiles, $redir);
			$Href = trim($redir[1]);
			$Url = parse_url($Href);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			preg_match_all('%align="left"><a href="(http://www.load.to/.*?)"%i', $page, $dllink);
			$download_link = $dllink[1][0];
			$delete_link = $dllink[1][1];	
?>