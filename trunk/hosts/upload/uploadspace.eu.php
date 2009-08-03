<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?
	$ref='http://uploadspace.eu/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$upsrv = cut_str($page,'"multipart/form-data" action="','cgi-bin/upload.cgi?upload_id=');
	$uid = $i=0; while($i<12){ $i++;}
	$uid += floor(rand() * 10);
	$post['upload_type']="file";
	$post['sess_id']="";
	$post['tos']='1';
	$post['submit_btn']=' Upload! ';
	
	$url=parse_url($upsrv.'cgi-bin/upload.cgi?upload_id='.$uid.'&js_on=1&utype=anon&upload_type=file');
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$upsrv, 0, $post, $lfile, $lname, "file_0");

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
	
	is_notpresent($page, 'Link for forums:', 'Uploadspace.eu server is overloaded, Try again later.'); 
	
	$ddl=cut_str($page,'Download Link:</b></td><td colspan=2><a href="','"');
	$del=cut_str($page,$ddl.'?killcode=','"');
		
	$download_link=$ddl;
	$delete_link= $ddl.'?killcode='.$del;
	
	
// Made by Baking 15/07/2009 17:00
?>