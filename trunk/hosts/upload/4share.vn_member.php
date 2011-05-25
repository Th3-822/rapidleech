<?php
####### Account Info. ###########
$fourshare_vn_login = "xxxxxx"; //Set your user id (login)
$fourshare_vn_pass = "xxxxxx"; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($fourshare_vn_login && $fourshare_vn_pass){
	$_REQUEST['my_login'] = $fourshare_vn_login;
	$_REQUEST['my_pass']  = $fourshare_vn_pass;
	$_REQUEST['action']   = "FORM";
	echo "<b><center>Use Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;Username*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["4share.vn"];exit; ?></b></small></tr>
</table>
</form>

<?php
	}
$continue_up=true;
if ($continue_up)
	{
		$not_done=false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=login width=100% align=center>Login to up.4share.vn</div>
<?php 
			$page = geturl("up.4share.vn", 80, "/", 0, 0, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
			$cookie = GetCookies($page);
                        $post = array();
			$post['inputUserName'] = trim($_REQUEST['my_login']);
			$post['inputPassword'] = trim($_REQUEST['my_pass']);
                        $post['submit'] = 'Login';			
			if (!preg_match('%(SHARINGSESSID=.+)%', $cookie, $lcook))html_error('Error getting login-cookie');
                        $page = geturl("up.4share.vn", 80, "/", 0, $cookie, $post, 0, $_GET["proxy"], $pauth);
	?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive test</div>
<script>document.getElementById('info').style.display='none';</script>
<?php 
                        $url_up = "http://up.4share.vn/scripts/uploadify1.php";
                        $fpost = array(
			'Filename' => $lname,
			'name'     => 'public_upload',
			'folder'   => '/files',
                        'Upload'   => 'Submit Query');
			$url=parse_url($url_up);
		        $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://up.4share.vn/", $cookie, $fpost, $lfile, $lname, "Filedata");                        
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
                        preg_match("%<a href='(.*)' target='_blank'>%",$upfiles, $match);
                        if (!$match[1]) html_error('Error getting return url');
                        $download_link=$match[1];                		
	}

// written by VinhNhaTrang 08/11/2010
?>