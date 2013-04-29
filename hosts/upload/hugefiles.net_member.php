<?php
// Default Account Info =================
$hugefiles_user = ''; //  Set you username
$hugefiles_pass = ''; //  Set your password
//=======================================

			$not_done = true;
			$continue_up = false;
			if ($hugefiles_user & $hugefiles_pass) {
				$_REQUEST['hf_user'] = $hugefiles_user;
				$_REQUEST['hf_pass'] = $hugefiles_pass;
				$_REQUEST['action'] = "FORM";
				echo "<b><center>Automatic Login Hugefiles.net</center></b>\n";
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
            <tr><td nowrap>&nbsp;User*<td>&nbsp;<input type='text' name='hf_user' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='hf_pass' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td colspan=2 align=center><input type='submit' value='Upload' /></tr>
            <tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["hugefiles.net_member"]; ?></b></small></tr>
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
            <div id='info' width='100%' align='center'>Login to Hugefiles.net</div>
        <?php
        if (!empty($_REQUEST['hf_user']) && !empty($_REQUEST['hf_pass'])){
        $post["op"] = "login";
        $post["redirect"] = "http://hugefiles.net/";
		$post["login"] = trim($_REQUEST['hf_user']);
        $post["password"] = trim($_REQUEST['hf_pass']);
		$page = geturl("hugefiles.net", 80, "/", "http://hugefiles.net/login.html", 0, $post, 0, $_GET["proxy"], $pauth);
		is_page($page);
		is_present($page, "Incorrect Login or Password");
		$cookie = GetCookies($page);
		}else{
		html_error ('Error, user and/or password is empty, please go back and try again!');
	}
        ?> 
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$url = parse_url('http://hugefiles.net/');
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://hugefiles.net/", $cookie, 0, 0, $_GET["proxy"], $pauth);
			$form = cut_str($page, '<form name="file" enctype="multipart/form-data"', '</form>');
            if(!preg_match("#var[\r|\n|\s]+utype='([^']+)'#", $page, $utype)){
				html_error('Cannot get utype');
			}
		    if(!preg_match('#action="([^"]+)"#', $page, $up)){
				html_error('Cannot URL for Upload');
			}
			if(!preg_match_all('#<input[\r|\n|\s]+type="hidden"[\r|\n|\s]+name="([^"]+)"[\r|\n|\s]+value="([^"]+)"#', $form, $dt)){
				html_error('Cannot get data form upload');
			}
			$post = array_combine($dt[1], $dt[2]);
			$uid = uid();
			$url = parse_url($up[1].$uid.'&js_on=1&utype='.$utype[1].'&upload_type=file');
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://hugefiles.net/", $cookie, $post, $lfile, $lname, "file_1");
?>

<script>document.getElementById('progressblock').style.display='none';</script>
<?php
is_page($upfiles);
			preg_match_all("#>([^><]+)<#", $upfiles, $dl);
			if($dl[1][1] != 'OK')
				html_error('Erro in upload');
			$post['fn'] = $dl[1][0];
			$post['st'] = 'OK';
			$post['op'] = 'upload_result';
			$page = geturl("hugefiles.net", 80, "/", 'http://hugefiles.net/', $cookie, $post, 0, $_GET["proxy"], $pauth);
				if(!empty($dl[1][0]))
					$download_link = 'http://hugefiles.net/'.$dl[1][0].'/'.$lname.'.html';
				else
					html_error ("Didn't find download link!");
				if(preg_match("#killcode=([^=<]+)<#", $page, $del))
					$delete_link = $download_link.'?killcode='.$del[1];
				else
					html_error ("Didn't find delete link!");
	}
function uid(){
				$nu = "0123456789";
				for($i=0; $i < 12; $i++){
				$rand .= $nu{mt_rand() % strlen($nu)};
				}
				return $rand;
				//function by simplesdescraga 05/02/2012
	}
/**
written by SD-88 09.04.2013
**/   
?>