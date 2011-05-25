<?php
$ref='http://uptotal.com';
$rand = mt_rand(1000000000000, 1999999999999);
$page = geturl("uptotal.com", 80, "/", "", 0, 0, 0, "");

$upload_form = cut_str($page, 'enctype="multipart/form-data" action="', '"');
$uploaded = cut_str($page, 'enctype="multipart/form-data" action="http://uptotal.com/enviar.php', '"');
echo $upload_form;
if (!$url = parse_url($upload_form)) html_error('Error getting upload url');
$fpost["loadto"] = "on";
$fpost["megaupload"] = "on";
$fpost["easyshare"] = "on";
$fpost["depositfiles"] = "on";
$fpost["zshare"] = "on";
$fpost["hotfile"] = "on";
$fpost["fileserve"] = "on";
$fpost["sendspace"] = "on";
$fpost["filesonic"] = "on";
$fpost["uploaded"] = "on";
$fpost["uploading"] = "on";
$fpost["mediafire"] = "on";
$fpost["freakshare"] = "on";
$fpost["2shared"] = "on";
$fpost["turbobit"] = "on";
$fpost["bitshare"] = "on";
$fpost['x7to'] = "on";
$fpost["upload_button"] = "Upload";

$url=parse_url($ref.'/enviar.php');
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('info').style.display='none';</script>
<?php	

		$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $ref, 0, $fpost, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
		//$uploaded = cut_str($upfiles, 'moved <a href="', '"');
		//$uploaded2 = cut_str($upfiles, 'moved <a href="http://www.multi-load.com/process.php?', '"');
		//preg_match('/Location:http://www.multi-load.com/process.php (.*)\r\n/i', $page, $infos);
		//$dlink = $infos[1];
		$page = $upfiles;
		//echo $uploaded2;
		//echo $upfiles;

		$endlink = cut_str($page,'Seu Link de Download: <a href="','">');
		$download_link = $ref."/".$endlink;
// Made by Baking 14/05/2009
?>