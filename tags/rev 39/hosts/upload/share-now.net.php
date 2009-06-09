<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://share-now.net/';
			$page = geturl("share-now.net", 80, "/", 0, 0, 0, 0, "");
			is_page($page);
			$form=explode('<form',$page);
			$action_url=trim(cut_str($form[2],'action="','"'));
			if ($action_url && !strpos($action_url,'.net/')) $action_url=$action_url.'/';
			if (!$action_url) $action_url='http://s2.share-now.net/';
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["APC_UPLOAD_PROGRESS2"]=cut_str($page,'"APC_UPLOAD_PROGRESS2" value="','"');
			$post["upload_files"]='1';
			$post["userid"]='';
			$post["submit"]=urlencode('Upload!');
			$post["textfield"]='';

			$url=parse_url($action_url);

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "file1");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$tmp=cut_str($upfiles,'"download"','</a>');
			$download_link=cut_str($tmp,'<a href="','"');
			$tmp=cut_str($upfiles,'"delete"','</a>');
			$delete_link=cut_str($tmp,'<a href="','"');
?>
<script>document.getElementById('final').style.display='none';</script>
<?php

// sert 14.08.2008
?>