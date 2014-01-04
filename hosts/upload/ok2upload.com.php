<table width=600 align=center> 
</td></tr> 
<tr><td align=center> 
<div id=login width=100% align=center></div> 
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$url=parse_url('http://www.ok2upload.com/');
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://www.ok2upload.com/", 0, 0, 0, $_GET["proxy"], $pauth);
			if(!preg_match('#enctype="multipart/form-data"[\r|\n|\s]+action="([^"]+)"#', $page, $act)){
				html_error('Cannot get form action.', 0);
			}
			if(!preg_match('#name="srv_tmp_url"[\r|\n|\s]+value="([^"]+)"#', $page, $srv)){
				html_error('Cannot get form srv url.', 0);
			}
			$url = parse_url($act[1]);
			$post["upload_type"]= 'file';
			$post['sess_id'] = '';
			$post['srv_tmp_url'] = $srv[1];
			$post['link_rcpt'] = '';
			$post['link_pass'] = '';
			$post['tos'] = '1';
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://www.ok2upload.com/", 0, $post, $lfile, $lname, "file_1");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			is_page($upfiles);
			preg_match("#[\r|\n|\s]+Location:[\r|\n|\s]+([^[:space:]]+)#", $upfiles, $dlink);
			$link = split ('[&=]', $dlink[1]);
				if(!empty($link[2]))
					$download_link = 'http://www.ok2upload.com/'.$link[2];
				else
					html_error ("Didn't find download link!");
			$url = parse_url($dlink[1]);
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://www.ok2upload.com/", 0, 0, 0, $_GET["proxy"], $pauth);
			preg_match('#\?killcode=([0-9a-zA-Z]+)#', $page, $dele);
				if(!empty($dele[1]))
			$delete_link = $download_link.'?killcode='.$dele[1];
				else
					html_error ("Didn't find delete link!");
/**
written by simplesdescarga 14/01/2012
**/   
?>