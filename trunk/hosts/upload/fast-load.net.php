<table width=600 align=center>
</td></tr>
<tr><td align=center>

<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://www.fast-load.net/';
			$page = geturl("www.fast-load.net", 80, "/", "", 0, 0, 0, "");
			is_page($page);
//            $cook = trim(cut_str($page,"Cookie:",";"));
			$ifurl=cut_str($page,'<iframe','</iframe>');
			$ifurl=cut_str($ifurl,'src="','"');
			if (!$ifurl) html_error ('Error get iframe url');
			$Url=parse_url($ifurl);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);

			$servid=cut_str($page,'name="serverid" value="','"');
			$post['user']='';
			$post['password']='';
			$post['ref']='';
			$post['serverid']=$servid?$servid:'21';

?>
	<script>document.getElementById('info').style.display='none';</script>
<?php

			$upfiles=upfile($Url["host"],defport($Url), "/upload.php","", 0, $post, $lfile, $lname, "gfile");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			is_page($upfiles);
//            is_notpresent($upfiles,"ok","Error Upload File!".$upfiles);
            $tmp = cut_str($upfiles,"Download",true);
            $download_link = cut_str($tmp,'value="','"');
            $tmp = cut_str($tmp,"Delete",true);
            $delete_link = cut_str($tmp,'value="','"');

// sert 19.07.2008
?>