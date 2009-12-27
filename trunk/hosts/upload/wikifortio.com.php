<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

	$ref='http://www.wikifortio.com/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	
	$upost['MAX_FILE_SIZE'] = cut_str($page,'"hidden" name="MAX_FILE_SIZE" value="' ,'"');
	$upost['_sourcePage'] = cut_str($page,'"hidden" name="_sourcePage" value="' ,'"');

	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	$cookies = implode(';',$cookie);
	
		for ($i=0; $i < strlen($lname); $i++) {
				$Bconv = "\\u00".(bin2hex($lname[$i]));
				$Aconv .= $Bconv;
		}	
		
	$post['getUplAddrAndFileId'] = '1';
	$post['fileName'] = $Aconv;
	$post['stamp'] = time()."000";
	
	$page = geturl("www.wikifortio.com", 80, "/upload/dispatch.php", $ref, $cookies, $post, 0, $_GET["proxy"], $pauth);
	is_page($page);	
	is_notpresent($page, 'details.php?saveDetails=1', 'Error - unable to retrive the first upload link.');
	
	$upfrom = "http://".cut_str($page,'http://' ,';');
		$refup = "http://".cut_str($upform,'http://' ,'/')."/";
	$fid = cut_str($page,'http://www.wikifortio.com/' ,'/');	
		
	$upost['fid'] = $fid;
	$upost['fileName'] = $Aconv;
	$upost['__fp'] = '';
	
	$dpost['saveDetails'] = "1";
	$dpost['storagePeriod'] = "90";
	$dpost['fid'] = $fid;
	$dpost['node'] = cut_str($refup,'http://' ,'/');
	$page = geturl("www.wikifortio.com", 80, "/upload/details.php", $refup, $cookies, $dpost, 0, $_GET["proxy"], $pauth);
	is_page($page);	
	is_notpresent($page, 'details.php?detailsDialog', 'Error - unable to retrive the second upload link.');
	
	$admin = cut_str($page,"document.location.href='" ,"'");	

	$url=parse_url($upfrom);
?>
<script>document.getElementById('info').style.display='none';</script>
<?
	$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), $refup, 0, $upost, $lfile, $lname, "uploadFormFile");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	$Url=parse_url($admin);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	is_notpresent($page, $fid, 'Error - unable to retrive the download link.');
		
	$download_link=	cut_str($page,'downloadlink"><strong><a href="' ,'"');	
	$adm_link= $admin;
	
// Made by Baking 24/12/2009 21:54
?>