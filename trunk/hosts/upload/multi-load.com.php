<?php
$ref='http://www.multi-load.com';
$rand = mt_rand(1000000000000, 1999999999999);
$page = geturl("www.multi-load.com", 80, "/", "", 0, 0, 0, "");
$page2 = geturl("www.multi-load.com", 80, "/ubr_link_upload.php?rnd_id=$rand", "", 0, 0, 0, "");
//echo $page2;
$upid = cut_str($page2,'Upload("','"');
$upid = "upload_id=".$upid;
//$upload_form = cut_str($page, 'enctype="multipart/form-data" action="', '"');
//$uploaded = cut_str($page, 'enctype="multipart/form-data" action="http://ul1.uploadline.com', '"');
//echo $upload_form;
//if (!$url = parse_url($upload_form)) html_error('Error getting upload url');
$fpost["rapidshare"] = "on";
$fpost["megaupload"] = "on";
$fpost["easyshare"] = "on";
$fpost["depositfiles"] = "on";
$fpost["zshare"] = "on";
$fpost["free"] = "on";
$fpost["flyupload"] = "on";
$fpost["sendspace"] = "on";
$fpost["sharedzilla"] = "on";
$fpost["badongo"] = "on";
$fpost["netload"] = "on";
$fpost["loadto"] = "on";
$fpost["mediafire"] = "on";
$fpost["megashare"] = "on";
$fpost["uploadedto"] = "on";
$fpost["filefactory"] = "on";
$fpost['mail'] = $lname;
$fpost["upload_button"] = "Upload";

$url=parse_url($ref.'/cgi/'.'ubr_upload.pl?'.$upid);
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('info').style.display='none';</script>
<?php	

		$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $ref, 0, $fpost, $lfile, $lname, "upfile_0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
		//$uploaded = cut_str($upfiles, 'moved <a href="', '"');
		//$uploaded2 = cut_str($upfiles, 'moved <a href="http://www.multi-load.com/process.php?', '"');
		//preg_match('/Location:http://www.multi-load.com/process.php (.*)\r\n/i', $page, $infos);
		//$dlink = $infos[1];
		$page = geturl("multi-load.com", 80, "/process.php?$upid", "", 0, 0, 0, "");
		//echo $uploaded2;
		//echo $upfiles;
		$endlink = cut_str($page,'Your download link is: <a href="','">');
		$download_link = 'http://www.multi-load.com/'.$endlink;
// Made by Baking 14/05/2009
?>