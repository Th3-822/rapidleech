<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://www.enterupload.com/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	$cookies = implode(';',$cookie);
	is_page($page);
	$upfrm = cut_str($page,'multipart/form-data" action="','cgi-bin/upload.cgi?');
	$uid = $i=0; while($i<12){ $i++;}
	$uid += floor(rand() * 10);
	$post['upload_type']= 'file';
	$post['sess_id']= '';
	$post['file_0_descr']=$_REQUEST['descript'];
	$post['file_0_public']='1';
	$post['link_rcpt']='';
	$post['link_pass']='';
	$post['tos']='1';
	$post['submit_btn']=' Upload! ';
	$uurl= $upfrm.'/cgi-bin/upload.cgi?upload_id='.$uid.'&js_on=1&utype=anon&upload_type=file';
	$url=parse_url($upfrm.'/cgi-bin/upload.cgi?upload_id='.$uid.'&js_on=1&utype=anon&upload_type=file');
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	$locat=cut_str($upfiles,'rea name=\'fn\'>' ,'</textarea>');
	unset($post);
	$gpost['fn'] = "$locat" ;
	$gpost['st'] = "OK" ;
	$gpost['op'] = "upload_result" ;
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $uurl, $cookies, $gpost, 0, $_GET["proxy"],$pauth);
	
	$ddlLinkData = trim ( cut_str($page,'input id="ic2','>') ); 
	$ddl = trim ( cut_str( $ddlLinkData,'value="','"') );
	
	$delLinkData = trim ( cut_str( $page,'input id="ic3','>') ); 
	$del = trim ( cut_str( $delLinkData,'value="','"') );
	$download_link=$ddl;
	$delete_link= $del;

/**************************************************\  
WRITTEN by churongcon 12 Jan 2010
Fixed by rajmalhotra 03 Feb 2010 -> Error is that Download and delete links are not visible
\**************************************************/
?>