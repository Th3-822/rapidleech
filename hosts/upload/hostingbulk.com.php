<table style="width:600px;margin:auto;">
</td></tr>
<tr><td align="center">
<div id="info" style="width:100%;text-align:center;">Retrive upload ID</div>
<?php
	$cookie = 'lang=english';
	$page = geturl("hostingbulk.com", 80, "/", 'http://hostingbulk.com/', $cookie, 0, 0, $_GET["proxy"], $pauth);is_page($page);
	if (!preg_match('@action="(http://[^/|"]+/[^\?|"]+)\?@i',$page, $up)) html_error('Error: Cannot find upload server.', 0);

	$uid = '';$i = 0;
	while($i < 12) {
		$uid .= rand(0,9);
		$i++;
	}

	$post = array();
	$post['upload_type'] = "file";
	$post['sess_id'] = '';
	$post['srv_tmp_url'] = urlencode(cut_str($page, 'name="srv_tmp_url" value="', '"'));
	$post['link_rcpt'] = "";
	$post['link_pass'] = "";
	$post['tos'] = 1;
	$post['submit_btn'] = " Upload! ";

	$up_url = $up[1]."?upload_id=$uid&js_on=1&utype=".cut_str($page, "var utype='", "'")."&upload_type=file";
?>
<script type="text/javascript">document.getElementById('info').style.display='none';</script>
<?php

	$url=parse_url($up_url);
	$upfiles=upfile($url["host"], 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://hostingbulk.com/', $cookie, $post, $lfile, $lname, "file_0");

?>
<script type="text/javascript">document.getElementById('progressblock').style.display='none';</script>
<?php
	is_page($upfiles);

	$post = array();
	$post['op'] = "upload_result";
	$post['fn'] = cut_str($upfiles,"'fn'>","<");
	$post['st'] = "OK";

	$page = geturl("hostingbulk.com", 80, "/", $up_url, $cookie, $post, 0, $_GET["proxy"], $pauth);is_page($page);

	if (preg_match('@(https?://(?:www\.)?hostingbulk\.com/\w+)(\.html)?\?killcode=\w+@i', $page, $lnk)) {
		$download_link = $lnk[1];
		$delete_link = $lnk[0];
	} else {
		html_error("Download link not found.", 0);
	}

//[18-12-2011] Written by Th3-822
//[18-12-2011] Edited for non member upload. (Based in member plugin) -Th3-822

?>