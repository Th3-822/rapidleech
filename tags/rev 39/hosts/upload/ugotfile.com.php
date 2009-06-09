<table width=600 align=center>
</td></tr>
<tr><td align=center>

<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://ugotfile.com/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
        preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
        $cookie = $temp[1];
        $cookies = implode(';',$cookie);
	$upurl=cut_str($page,'upload_url: "','"');
	if (!$upurl || !strpos($upurl,'ugotfile.com')) html_error ('Error get upload Url');
	$post['Filename']=$lname;
	$post['destinationFolder']='Web Uploads';
	$post['Upload']='Submit Query';
	$url=parse_url($upurl);
	$agent='Shockwave Flash';
?>
<script>document.getElementById('info').style.display='none';</script>
<?php

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $post, $lfile, $lname, "Filedata");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
	is_page($upfiles);
	unset($post);
	$tmp=cut_str($upfiles,"'text' value='","'");
	$tmp=str_replace('\\','',$tmp);
	if (!$tmp) html_error ('Error retrive download url');
	$download_link=$tmp;

// sert 14.05.2009
?>