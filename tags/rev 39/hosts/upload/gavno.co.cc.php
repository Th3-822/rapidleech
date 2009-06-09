<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://gavno.co.cc/';
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["from"]='Paste file url here';
			$post["operation"]='1';
			$post["agreecheck"]='on';

			$url=parse_url($ref.'upload.php');

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "upfile");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			
			$tmp=cut_str($upfiles,'http://gavno.co.cc/download.php?','"');
			if ($tmp) {
				$download_link='http://gavno.co.cc/download.php?'.$tmp;
				$tmpd=cut_str($upfiles,'&del=','"');
				if ($tmpd) $delete_link=$download_link.'&del='.$tmpd;
			}
?>
<script>document.getElementById('final').style.display='none';</script>
<?php

// sert 04.08.2008
?>