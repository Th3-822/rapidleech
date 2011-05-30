<script>document.getElementById('login').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://fryshare.com/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	$cookies = implode(';',$cookie);
	is_page($page);
	$uid = $i=0; while($i<12){ $i++;}
	$uid += floor(rand() * 10);
	$post['upload_type']= 'file';
	$post['sess_id']= '';
	$post['file_0_descr']= "";
	$post['link_rcpt']='';
	$post['link_pass']='';
	$post['tos']='1';
	$url=parse_url('http://fryshare.com'.'/cgi-bin/upload.cgi?upload_id='.$uid.'&js_on=1');
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://fryshare.com/', $cookies, $post, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?

	unset($post);
	$gpost['filename'] = cut_str($upfiles,'filename\'>' ,'</textarea>') ;
	$gpost['del_id'] = cut_str($upfiles,'del_id\'>' ,'</textarea>') ;
	$gpost['filename_original'] = cut_str($upfiles,'filename_original\'>' ,'</textarea>') ;
	$gpost['status'] = "OK" ;
	$gpost['size'] = cut_str($upfiles,'size\'>' ,'</textarea>') ;
	$gpost['description'] = "";
	$gpost['file_0_mime'] = cut_str($upfiles,'file_0_mime\'>' ,'</textarea>') ;
	$gpost['number_of_files'] = "1" ;
	$gpost['ip'] = cut_str($upfiles,'ip\'>' ,'</textarea>') ;
	$gpost['host'] = "" ;
	$gpost['duration'] = cut_str($upfiles,'duration\'>' ,'</textarea>') ;
	$gpost['act'] = "upload_result" ;
	
	
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $uurl, $cookies, $gpost, 0, $_GET["proxy"],$pauth);
	$ddl=cut_str($page,'Download Link:</b></td><td><a href="','"');
	$del=cut_str($page,'Delete Link:</b></td><td><input type="text" onFocus="copy(this);" value="','"');
	$download_link=$ddl;
	$delete_link= $del;
	
// Made by Baking 16/10/2009 08:06
?>