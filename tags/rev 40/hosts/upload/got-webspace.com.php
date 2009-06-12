<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://www.got-webspace.com/';
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["file_0_descr"]='';
			$post["link_rcpt"]='';
			$post["link_pass"]='';
			$post["tos"]='1';
			$rnd=mt_rand(100000,999999);
			$rnd1=mt_rand(100000,999999);

			$url=parse_url($ref.'cgi-bin/upload.cgi?upload_id='.$rnd.$rnd1.'&js_on=1');

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "file_0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
//			echo "<!--$upfiles-->";exit;
			$form=cut_str($upfiles,'<form','</form>');
			$id=cut_str($form,"name='filename'>","</");
			$did=cut_str($form,"name='del_id'>","</");
			$fn=cut_str($form,"name='filename_original'>","</");
			if (!$id || !$did || !$fn) html_error ('File not upload'.$upfiles);
			$download_link=$ref.$id.'/'.$fn.'.html';
			$delete_link=$ref.'del-'.$id.'-'.$did.'/'.$fn;
?>
<script>document.getElementById('final').style.display='none';</script>
<?php

// sert 14.08.2008
?>