<table width=600 align=center>
	</td>
	</tr>
	<tr>
		<td align=center>
		<div id=info width=100% align=center>Retrive upload ID</div>
<?php


$page = geturl ( "gigaup.fr", 80, "/", "", 0, 0, 0, 0 );
is_page ( $page );
preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
$cookie = $temp[1];
$cook = implode(';',$cookie);
//preg_match ( '/<form action="(.*)"/U', $page, $uplink );
$action_url = 'http://gigaup.fr/up/uploader.php';
$post['UPLOAD_IDENTIFIER'] = floor(rand() * 65535)+ time();
$url = parse_url ( $action_url );
//var_dump($page);exit;


?>
<script>document.getElementById('info').style.display='none';</script>

		<table width=600 align=center>
			</td>
			</tr>
			<tr>
				<td align=center>
<?php
 
$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://gigaup.fr/", $cook, $post, $lfile, $lname, "file[]");

//var_dump($upfiles);exit;

$lk="http://gigaup.fr/up/index.php";
$url = parse_url($lk);
$page = geturl ( $url['host'], 80, $url['path'].'?'.$url['query'], "http://gigaup.fr/", $cook, 0, 0 ,0,0);
is_page ( $page );

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php

$jj= fopen(getcwd()."/logga.txt","a");
 fwrite($jj,"=================================");
 fwrite($jj,$page);
 fclose($jj);
 
preg_match ('/http:\/\/.+g=\w+/', $page,$dllink);

$download_link = $dllink [0];
?>