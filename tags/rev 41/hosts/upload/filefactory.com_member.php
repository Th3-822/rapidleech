<?php

####### Account Info. ###########
$filefac_login = ""; //Set your filefactory email id (login)
$filefac_pass = ""; //Set your filefactory password
##############################

$not_done=true;
$continue_up=false;
if ($filefac_login && $filefac_pass){
	$_REQUEST['my_login'] = $filefac_login;
	$_REQUEST['my_pass'] = $filefac_pass;
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
<tr><td nowrap>&nbsp;Email Id*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["filefactory.com_member"]; ?></b></small></tr>
</table>
</form>

<?php
	}

if ($continue_up)
	{
		$not_done=false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=login width=100% align=center>Login to Filefactory</div>
<?php 
			$post = array();
			$post['email'] = trim($_REQUEST['my_login']);
            $post['password'] = trim($_REQUEST['my_pass']);
            $post['redirect'] = '/';
			$page = geturl("www.filefactory.com", 80, "/", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 302 Found', 'Error logging in - are your logins correct?');
			$cookie = GetCookies($page);
			if (!preg_match('%(ff_membership=.+)%', $cookie, $lcook)) html_error('Error getting login-cookie');
			$page = geturl("www.filefactory.com", 80, "/?login=1", 0, $lcook[1], 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'You have been logged in as', 'Error logging in - are your logins correct?');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$upload_form = cut_str($page, '<form accept-charset="UTF-8" id="uploader" action="', '"');
			if (!$url = parse_url($upload_form)) html_error('Error getting upload url');
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$fpost = array();
			$fpost['Filename'] = $lname;
			$fpost['cookie'] = urldecode(str_replace('ff_membership=', '', $lcook[1]));
			$fpost['folderViewhash'] = '0';
			$fpost['Upload'] = 'Submit+Query';
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $upload_form, 0, $fpost, $lfile, $lname, "Filedata");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			if (!preg_match('%\r\n\r\n([a-z0-9]{7})$%', $upfiles, $curi)) html_error('Couldn\'t get the download link, but the file might have been uploaded to your account ok');
			$completeurl = 'http://www.filefactory.com/file/complete.php/' . $curi[1] . '/';
			$Url = parse_url($completeurl);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $lcook[1], 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'Upload Complete', 'Error getting download link - The upload probably failed');
			$download_link = trim(cut_str($page, '<div class="metadata">', '</div>'));
	}
	////szal14-Jun-09
?>