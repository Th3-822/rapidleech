<?php
####### Account Info. ###########
$ok2upload_login = ""; //Set you username
$ok2upload_pass = ""; //Set your password
##############################

						$not_done=true;
						$continue_up=false;
						if ($ok2upload_login && $ok2upload_pass){
							$_REQUEST['ok2upload_login'] = $ok2upload_login;
							$_REQUEST['ok2upload_pass'] = $ok2upload_pass;
							$_REQUEST['action'] = "FORM";
							echo "<b><center>Using Default Login and Pass.</center></b>\n";
						}
						if ($_REQUEST['action'] == "FORM")
							$continue_up=true;
						else{
?>
<table border='0' style="width:270px;" cellspacing='0' align='center'>
<form method='post'>
<input type='hidden' name='action' value='FORM' />
<tr><td nowrap>&nbsp;User*<td>&nbsp;<input type='text' name='ok2upload_login' value='' style='width:160px;' />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='ok2upload_pass' value='' style='width:160px;' />&nbsp;</tr>
<tr><td colspan='2' align='center'><input type='submit' value='Upload' /></tr>
</table>
</form>

<?php
	}

if ($continue_up)
	{
		$not_done=false;
?>
<table width='600' align='center'>
</td></tr>
<tr><td align='center'>
<div id='login' width='100%' align='center'>Login to Ok2upload</div>
<?php 
	if (!empty($_REQUEST['ok2upload_login']) && !empty($_REQUEST['ok2upload_pass'])) {
		$post['login'] = $_REQUEST['ok2upload_login'];
		$post['password'] = $_REQUEST['ok2upload_pass'];
		$post['op'] = 'login';
		$post['redirect'] = '';
		$post['sess_id'] = '';
		$page = geturl("www.ok2upload.com", 80, "/", "http://www.ok2upload.com/", 0, $post, 0, $_GET["proxy"], $pauth);
		is_page($page);
		$cookie = GetCookies($page);
		$ck = split('; ', $cookie);
		if(empty($ck[1]) || empty($ck[2])){html_error('Incorrect Login or Password');}
	} else {
			html_error("Is empty user and/or password");
	}
?>
					<script type='text/javascript'>document.getElementById('login').style.display='none';</script>
                    <div id='info' width='100%' align='center'>Retrive upload ID</div>
<?php
			$page = geturl("www.ok2upload.com", 80, "/", 0, $cookie, 0);
			if(!preg_match('#enctype="multipart/form-data"[\r|\n|\s]+action="([^"]+)"#', $page, $act)){
				html_error('Cannot get form action.', 0);
			}
			if(!preg_match('#name="sess_id"[\r|\n|\s]+value="([^"]+)"#', $page, $sid)){
				html_error('Cannot get form session id.', 0);
			}
			if(!preg_match('#name="srv_tmp_url"[\r|\n|\s]+value="([^"]+)"#', $page, $srv)){
				html_error('Cannot get form srv url.', 0);
			}
			$url = parse_url($act[1]);
			$post["upload_type"]= 'file';
			$post['sess_id'] = $sid[1];
			$post['srv_tmp_url'] = $srv[1];
			$post['link_rcpt'] = '';
			$post['link_pass'] = '';
			$post['tos'] = '1';
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://www.ok2upload.com/", $cookie, $post, $lfile, $lname, "file_1");
?>
					<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			is_page($upfiles);
			preg_match("#[\r|\n|\s]+Location:[\r|\n|\s]+([^[:space:]]+)#", $upfiles, $dlink);
			$link = split ('[&=]', $dlink[1]);
				if(!empty($link[2]))
					$download_link = 'http://www.ok2upload.com/'.$link[2];
				else
					html_error ("Didn't find download link!");
			$url = parse_url($dlink[1]);
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://www.ok2upload.com/", $cookie, 0, 0, $_GET["proxy"], $pauth);
			preg_match('#\?killcode=([0-9a-zA-Z]+)#', $page, $dele);
				if(!empty($dele[1]))
			$delete_link = $download_link.'?killcode='.$dele[1];
				else
					html_error ("Didn't find delete link!");
	}
/**
written by simplesdescarga 14/01/2012
**/   
?>