<?php
####### Free Account Info. ###########
$ugot_login = "";
$ugot_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($wat_login & $wat_pass){
	$_REQUEST['username'] = $ugot_login;
	$_REQUEST['password'] = $ugot_pass;
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
<tr><td nowrap>&nbsp;Login*<td>&nbsp;<input type=text name=username value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=password value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["wat.tv"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to uGotFile.com</div>
<?php
			$post['ugfLoginUserName'] = $_REQUEST['username'];
			$post['ugfLoginPassword'] = $_REQUEST['password'];
			$post['ugfLoginRememberMe'] = 0;
			$post['login'] = 'Login';

			$page = geturl("ugotfile.com", 80, "/user/login/", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'Set-Cookie', 'Error logging in - are your logins correct? First');

			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie1 = $temp[1];
			$cookie = implode(';',$cookie1);
			
			$page = geturl("ugotfile.com", 80, "/", 'http://ugotfile.com/', $cookie, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'headerLogout', 'Error logging in - are your logins correct? Second');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$upload_srv = cut_str($page, 'uploadServer = "', '"');
			$upload_sid = cut_str($page, 'upload_url: uploadServer+"/upload/web?PHPSESSID=', '"');

			$upload_form = $upload_srv."/upload/web?PHPSESSID=".$upload_sid;
			
			$url = parse_url($upload_form);
			$post['Filename'] = $lname;
			$post['destinationFolder'] = 'Web Uploads';
			$post['Upload'] = 'Submit Query';

?>
<script>document.getElementById('info').style.display='none';</script>
<?php	
			$upagent = "Shockwave Flash";
			$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, $cookies, $post, $lfile, $lname, "Filedata",0,$upagent);
			is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			$ddl = cut_str($upfiles,"/file",'"');
			$del = cut_str($upfiles,'?remove=','"');
			$ddl1 = 'http://ugotfile.com/file'.str_replace('\\', "", $ddl);
			$download_link = $ddl1;
			$delete_link = $ddl1.'?remove='.$del;
	}		
// Made by Baking 10/08/2009 18:30
// Fixed by Baking 15/12/2009 17:44
?>