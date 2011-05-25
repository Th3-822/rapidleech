<?php

$uptotal_login = "";
$uptotal_pass = "";

$not_done=true;
$continue_up=false;
if ($uptotal_login & $uptotal_pass){
	$_REQUEST['my_login'] = $uptotal_login;
	$_REQUEST['my_pass'] = $uptotal_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;Login*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["uptotal.com"]; ?></b></small></tr>
</table>
</form>

<?php
	}

if ($continue_up)
	{
		$not_done=false;

$Url=parse_url('http://uptotal.com/login.php?do=logar');

$post["submit_login"]="1";
$post["user_login"]=$_REQUEST['my_login'];
$post["user_pass"]=$_REQUEST['my_pass'];
$post["wp-submit"]="1";


$page = geturl("uptotal.com", 80, "/login.php?do=logar", 0, 0, $post, 0, $_GET["proxy"], $pauth);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://uptotal.com/", 0, $post, 0, $_GET["proxy"],$pauth);

$cook = GetCookies($page,true);

			


if (!empty($cook))
{
$ref='http://uptotal.com';
$rand = mt_rand(1000000000000, 1999999999999);
$page = geturl("uptotal.com", 80, "/", "", 0, 0, 0, "");

$upload_form = cut_str($page, 'enctype="multipart/form-data" action="', '"');
$uploaded = cut_str($page, 'enctype="multipart/form-data" action="http://uptotal.com/enviar.php', '"');
echo $upload_form;
if (!$url = parse_url($upload_form)) html_error('Error getting upload url');
			$fpost["turbobit"] = "on";
			$fpost["megaupload"] = "on";
			$fpost["depositfiles"] = "on";
			$fpost["2shared"] = "on";
			$fpost["filesonic"] = "on";
			$fpost["hotfile"] = "on";
			$fpost["loadto"] = "on";
			$fpost["duckload"] = "on";
						
			$fpost["badongo"] = "on";
			$fpost["easyshare"] = "on";
			$fpost["zshare"] = "on";
			$fpost["putlocker"] = "on";
			$fpost["sendspace"] = "on";
			$fpost["filebeam"] = "on";
			$fpost["uploadedto"] = "on";
			$fpost["bitshare"] = "on";
			$fpost["uploadstation"] = "on";				
			$fpost["hostingbulk"] = "on";
			$fpost["fileserve"] = "on";
			$fpost["x7to"] = "on";
			$fpost["uploadbox"] = "on";
			$fpost["freakshare"] = "on";
			$fpost["mediafire"] = "on";
			$fpost["extabit"] = "on";
			$fpost["rapidleech"] = "on";
$fpost["upload_button"] = "Upload";

$url=parse_url($ref.'/enviar.php');
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('info').style.display='none';</script>
<?php	

		$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $ref, $cook, $fpost, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
		//$uploaded = cut_str($upfiles, 'moved <a href="', '"');
		//$uploaded2 = cut_str($upfiles, 'moved <a href="http://www.com/process.php?', '"');
		//preg_match('/Location:http://www.com/process.php (.*)\r\n/i', $page, $infos);
		//$dlink = $infos[1];
		$page = $upfiles;
		//echo $uploaded2;
		//echo $upfiles;

		$endlink = cut_str($page,'Seu Link de Download: <a href="','">');
		$download_link = $ref."/".$endlink;
// Made by Rudny 11/05/2011
} else { echo "erro no sistema"; }}
?>