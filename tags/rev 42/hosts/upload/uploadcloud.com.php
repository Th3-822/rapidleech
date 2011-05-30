<script>document.getElementById('login').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
	$ref='http://uploadcloud.com/index.php';
	$Url=parse_url($ref);
	
	$page = geturl($Url["host"], 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://uploadcloud.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$upfrm = cut_str($page,'name="uploadform" action="','"');
	$sessionid = cut_str($page,'name="sessionid" value="','"');
	$aceskey = cut_str($page,'name="AccessKey" value="','"');
	$maxsize = cut_str($page,'name="maxfilesize" value="','"');
	$phpscript = cut_str($page,'name="phpuploadscript" value="','"');
	$returnurl = cut_str($page,'name="returnurl" value="','"');
	
	$post['sessionid']=$sessionid;
	$post['UploadSession']= $sessionid;
	$post['AccessKey']= $aceskey;
	$post['maxfilesize']= $maxsize;
	$post['phpuploadscript']= $phpscript;
	$post['returnurl']= $returnurl;
	$post['uploadmode']= "1";
	$post['file_descr[0]']="";
	$post['file_password[0]']="";
	$post['flash_descr']= "";
	$post['flash_password']= "";
	$post['uploadurl[0]']= "";
	$post['url_descr[0]']= "";
	$post['url_password[0]']= "";
		
	$url=parse_url($upfrm);
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"], 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://upfordown.com/', $cookies, $post, $lfile, $lname, "uploadfile_0");
	is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
	$locat=trim(cut_str($upfiles,'Location:',"\n"));
	$Url=parse_url($locat);
	$page = geturl($Url["host"],  80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://uploadcloud.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$url3 = cut_str($page,'<meta HTTP-EQUIV=Refresh CONTENT="1; URL=','">');
	
	$Url=parse_url($url3);
	$page = geturl($Url["host"],  80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://uploadcloud.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
			
	$gpost['UploadSession']= $sessionid;
	$gpost['AccessKey']= $aceskey;
	$gpost['uploadmode']= "1";
	$gpost['submitnums']= "0";
	$gpost['fromemail']= "";
	$gpost['toemail']= "";
	$gpost['terms']= "on";
	
	$page = geturl("uploadcloud.com", 80, "/emaillinks.php", 'http://uploadcloud.com/', 0, $gpost, 0, "");
	is_page($page);
	is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?');
		
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
	
	$ddl=cut_str($page,'<a href="http://uploadcloud.com/file/','">');
	$del=cut_str($page,'<a href="http://uploadcloud.com/delete.php?id=','">');
	
	$download_link= 'http://uploadcloud.com/file/'.$ddl;
	$delete_link= 'http://uploadcloud.com/delete.php?id='.$del;
	
	
// Made by Baking 13/07/2009 18:43

?>