<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?
	$ref='http://oron.com/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$upfrm = cut_str($page,'form-data" action="','"');
	$uid = $i=0; while($i<12){ $i++;}
	$uid += floor(rand() * 10);
	$post['upload_type']="file";
	$post['sess_id']="";
	$post['ut']="file";
	$post['link_rcpt']="";
	$post['link_pass']='';
	$post['tos']='1';
	$post['submit']=' Upload! ';
	$uurl=$upfrm.$uid.'&js_on=1&utype=anon&upload_type=file';
	$url=parse_url($upfrm.$uid.'&js_on=1&utype=anon&upload_type=file');
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	$locat=cut_str($upfiles,"name='fn'>","<");
	unset($post);
	$gpost['fn'] = "$locat" ;
	$gpost['st'] = "OK" ;
	$gpost['op'] = "upload_result" ;
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, 0, $gpost, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$ddl=cut_str($page,'" class="btitle">','</a></td>');
	$del=cut_str($page,$lname.'.html?killcode=','"');
	//echo $page;
	$download_link=$ddl;
	$delete_link= $ddl.'?killcode='.$del;
	
	
// Made by Baking 10/07/2009 14:04
?>