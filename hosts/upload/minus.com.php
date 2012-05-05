<?php
####### Account Info. ###########
$minus_user = "";
$minus_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($minus_user && $minus_pass){
	$_REQUEST['mn_user'] = $minus_user;
	$_REQUEST['mn_pass'] = $minus_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Automatic Login Minus.com</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
					<script>document.getElementById('info').style.display='none';</script>
                    <div id='info' width='100%' align='center' style="font-weight:bold; font-size:16px">LOGIN</div> 
    <table border=0 style="width:270px;" cellspacing=0 align=center>
        <form method="post">
        <input type='hidden' name='action' value='FORM' />
            <tr><td nowrap>&nbsp;User*<td>&nbsp;<input type='text' name='mn_user' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='mn_pass' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td colspan=2 align=center><input type='submit' value='Upload' /></tr>
            <tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["minus.com"]; ?></b></small></tr>
        </form>
    </table>
<?
}

if ($continue_up)
	{
		$not_done=false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('info').style.display='none';</script>
            <div id='info' width='100%' align='center'>Login to Minus.com</div>
<?php
				if (!empty($_REQUEST['mn_user']) && !empty($_REQUEST['mn_pass'])){
				$Url=parse_url('http://minus.com/api/login/login');
				$post["password1"]=$_REQUEST['mn_pass'];
				$post["username"]=$_REQUEST['mn_user'];
				$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://minus.com/api/login/login", 0, $post, 0, $_GET["proxy"], $pauth);
				is_page($page);	
				is_present($page, "The password does not match.", "The password does not match.");
				is_present($page, "A username is required.", "A username is required.");
				$cookies=GetCookies($page);
				}else{
		html_error ('Error, user and/or password is empty, please go back and try again!');
	}
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$time = str_replace('.', '', microtime(true)*1000);
			$url = 'http://minus.com/api/CreateGallery_Web/?d='.((strlen($time) > 13) ? substr($time, 0, -1) : $time);
			$Url=parse_url($url);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://minus.com/", $cookies, 0, 0, $_GET["proxy"], $pauth);
			$cookie = GetCookies($page);
			if(!preg_match('#"editor_id": "([^"]+)"#', $page, $id)){
				html_error('Cannot get url action upload.', 0);
			}
			$dl = $id[1];
			$url = parse_url('http://minus.com/api/UploadItem_Web/?editor_id='.$id[1].'&key=-&filename='.$lname);
			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://minus.com/", $cookie, 0, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			is_page($upfiles);
			preg_match('#HTTP\/1\.1 (\w*)#', $upfiles, $status);
			preg_match('#"filesize": "([^"]+)"#', $upfiles, $size);
			if($status[1] == 403 || $status[1] == 404){
				html_error('Error in upload [Update plugin]');
			}
			if($size[1] == '0 bytes' || $size[1] == '0'){
				html_error('Error in upload');
			}
			$download_link = 'http://minus.com/m'.$dl;
}
//working by SD-88 07.04.2012
?>