<?php
// Default Account Info =================
$crocko_user = ""; //  Set you username
$crocko_pass = ""; //  Set your password
//=======================================

			$not_done = true;
			$continue_up = false;
			if ($crocko_user & $crocko_pass) {
				$_REQUEST['cr_user'] = $crocko_user;
				$_REQUEST['cr_pass'] = $crocko_pass;
				$_REQUEST['action'] = "FORM";
				echo "<b><center>Automatic Login</center></b>\n";
			}
			if ($_REQUEST['action'] == "FORM"){
				$continue_up = true;
			}else {
?>
					<script>document.getElementById('info').style.display='none';</script>
                    <div id='info' width='100%' align='center' style="font-weight:bold; font-size:16px">LOGIN</div> 
    <table border=0 style="width:270px;" cellspacing=0 align=center>
        <form method="post">
        <input type='hidden' name='action' value='FORM' />
            <tr><td nowrap>&nbsp;User*<td>&nbsp;<input type='text' name='cr_user' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='cr_pass' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td colspan=2 align=center><input type='submit' value='Upload' /></tr>
            <tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["crocko.com_member"]; ?></b></small></tr>
        </form>
    </table>
<?php
			}
			if ($continue_up) {
				$not_done = false;
?>
    <table width=600 align=center>
    </td></tr>
    <tr><td align=center>
    		<script>document.getElementById('info').style.display='none';</script>
            <div id='info' width='100%' align='center'>Login to crocko.com</div>
        <?php
        if (!empty($_REQUEST['cr_user']) && !empty($_REQUEST['cr_pass'])){
        $Url = parse_url("https://www.crocko.com/accounts/login/");
        $post["remember"] = "1";
        $post["login"] = $_REQUEST['cr_user'];
        $post["password"] = $_REQUEST['cr_pass'];
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "https://www.crocko.com/accounts/login/", 0, $post, 0, $_GET["proxy"], $pauth);
	is_page($page);
		preg_match('#/pages([a-z/]+)#', $page, $blocked);
		if($blocked[0] == '/pages/blocked'){
		html_error ('Sorry, but you have over 5 invalid login attempts per minute. Please wait at least 10 minutes and try again. Or use proxy.');}
	$cookies = GetCookies($page);
		}else{
		html_error ('Error, user and/or password is empty, please go back and try again!');
	}
	preg_match('#PHPSESSID=([0-9a-zA-Z%]+);#', $cookies, $sid);
	if(empty($sid[1])){
		html_error ('Invalid login or password!');
	}else{
        ?>
        		<script>document.getElementById('info').style.display='none';</script>
				<div id='info' width='100%' align='center'>Retrive upload ID</div> 
        <?php 
	$uplink = 'http://upload.crocko.com/accounts/upload_backend/perform/ajax';
	$url = parse_url($uplink);
	$post = array();
	$post['folder']='0';
	$post['PHPSESSID'] = $sid[1];
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'https://crocko.com/accounts/upload/#/ajax/', $cookies, $post, $lfile, $lname, '');
?>
				<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			is_page($upfiles);
			preg_match('#value="([^"]+)#', $upfiles, $dll);
			 if (!empty($dll[1]))
			$download_link =  $dll[1];
			else
              html_error ('Didn\'t find downloadlink!');
			preg_match('#href="javascript:;">([/a-zA-Z0-9.:]+)#', $upfiles, $del);
			if (!empty($del[1]))
			$delete_link =  $del[1];
			else
              html_error ('Didn\'t find deletelink!');
	}
	}
	/*
	upload plugin produced by simplesdescarga day 03/01/2011 at 18:47.
	Easy-share.com === Crocko.com
	*/
?>