<?php
####### Account Info. ###########
$turboupload_login = "";
$turboupload_pass = "";
##############################

				$not_done=true;
				$continue_up=false;
				if ($turboupload_login & $turboupload_pass){
					$_REQUEST['turboupload_login'] = $turboupload_login;
					$_REQUEST['turboupload_pass'] = $turboupload_pass;
					$_REQUEST['action'] = "FORM";
					echo "<b><center>Using Default Login and Pass.</center></b>\n";
				}
				if ($_REQUEST['action'] == "FORM")
					$continue_up=true;
				else{
?>
<form method="post">
<table border="1" style="width:270px;" cellspacing="0" align="center">
<input type="hidden" name="action" value="FORM">
<tr><td nowrap>&nbsp;Login<td>&nbsp;<input type="text" name="turboupload_login" value="" style="width:195px;">&nbsp;</tr>
<tr><td nowrap>&nbsp;Password<td>&nbsp;<input type="password" name="turboupload_pass" value="" style="width:195px;">&nbsp;</tr>
<tr><td colspan="2" align="center"><input type="submit" value="Upload"></tr>
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
<div id='login' width='100%' align='center'>Login to Turboupload</div>
<?php
		if (!empty($_REQUEST['turboupload_login']) && !empty($_REQUEST['turboupload_pass'])) {
		$post['login'] = $_REQUEST['turboupload_login'];
		$post['password'] = $_REQUEST['turboupload_pass'];
		$post['op'] = 'login';
		$post['redirect'] = '';
		$post['sess_id'] = '';
		$page = geturl("turboupload.com", 80, "/", "http://turboupload.com/", 0, $post, 0, $_GET["proxy"], $pauth);
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
			$page = geturl("turboupload.com", 80, "/", 0, $cookie, 0);
			if(!preg_match('#enctype="multipart/form-data"[\r|\n|\s]+action="([^"]+)"#', $page, $act)){
				html_error('Cannot get form action.', 0);
			}
			if(!preg_match('#name="sess_id"[\r|\n|\s]+value="([^"]+)"#', $page, $sid)){
				html_error('Cannot get form session id.', 0);
			}
			if(!preg_match('#name="srv_tmp_url"[\r|\n|\s]+value="([^"]+)"#', $page, $srv)){
				html_error('Cannot get form srv url.', 0);
			}
			$post['sess_id'] = $sid[1];
			$post['srv_tmp_url'] = $srv[1];
			$post['upload_type'] = 'file';
			$post['link_rcpt'] = '';
			$post['link_pass'] = '';
			$post['tos'] = '1';
			$url = parse_url($act[1]);
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), 0, $cookie, $post, $lfile, $lname, 'file_1');
?>
					<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			is_page($upfiles);
			preg_match("#[\r|\n|\s]+Location:[\r|\n|\s]+([^[:space:]]+)#", $upfiles, $dlink);
			$link = split ('[&=]', $dlink[1]);
				if(!empty($link[2]))
					$download_link = 'http://turboupload.com/'.$link[2];
				else
					html_error ("Didn't find download link!");
			$url = parse_url($dlink[1]);
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://turboupload.com/", $cookie, 0, 0, $_GET["proxy"], $pauth);
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