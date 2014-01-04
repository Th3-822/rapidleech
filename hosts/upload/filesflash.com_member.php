<?php
// Default Account Info =================
$filesflash_email = ""; //  Set you mail user
$filesflash_pass = ""; //  Set your password
//=======================================

			$not_done = true;
			$continue_up = false;
			if ($filesflash_email & $filesflash_pass) {
				$_REQUEST['ff_email'] = $filesflash_email;
				$_REQUEST['ff_pass'] = $filesflash_pass;
				$_REQUEST['action'] = "FORM";
				echo "<b><center>Automatic Login to Filesflash.com</center></b>\n";
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
            <tr><td nowrap>&nbsp;User*<td>&nbsp;<input type='text' name='ff_email' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='ff_pass' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td colspan=2 align=center><input type='submit' value='Upload' /></tr>
            <tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["filesflash.com_member"]; ?></b></small></tr>
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
            <div id='info' width='100%' align='center'>Login to Filesflash.com</div>
<?php
        if (!empty($_REQUEST['ff_email']) && !empty($_REQUEST['ff_pass'])){
        $post["email"] = trim($_REQUEST['ff_email']);
        $post["password"] = trim($_REQUEST['ff_pass']);
		$post["submit"] = 'Login';
		$page2 = geturl("filesflash.com", 80, "/", 0, 0, 0);
		$coind = GetCookies($page2);
		$Url = parse_url("http://filesflash.com/login.php");
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://filesflash.com/", $coind, $post, 0, $_GET["proxy"], $pauth);
		is_page($page);
		is_present($page, "Invalid email address or password.", "Invalid email address or password.");
		$cookie = GetCookies($page);
		if(!preg_match('#userid=([^=;]+);#', $cookie, $user)){
			html_error ('Error in login!');
		}
		if($user[1] == 'deleted'){
			html_error ('Invalid email address or password.');
		}
		$cookie .= '; '.$coind;
		}else{
		html_error ('Error, user and/or password is empty, please go back and try again!');
		}
?>
        		<script>document.getElementById('info').style.display='none';</script>
				<div id='info' width='100%' align='center'>Retrive upload ID</div> 
<?php 

		$page = geturl("filesflash.com", 80, "/index.php", 0, $cookie);
		is_page($page);
		if(!preg_match('#iframe[\r|\n|\s]+src="([^"]+)"#', $page, $up)){
			html_error ('Cannot get url reference upload.', 0);
		}
		$url = parse_url($up[1]);
		$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://filesflash.com/", $cookie, 0, 0, $_GET["proxy"], $pauth);
		if(!preg_match('#action="([^"]+)"#', $page, $act)){
			html_error ('Cannot get url upload.', 0);
		}
		if(!preg_match('#name="refcode"[\r|\n|\s]+value="([^\"]+)"#', $page, $usu)){
			html_error ('Cannot get reference user.', 0);
		}
		$code = id();
		//$ref = explode('userid=', $cookie);
		$post['refcode'] = $usu[1];
		$post['tag'] = '';
		$post['tags'] = '';
		$post['linkcode'] = '';
		$url =  parse_url($act[1].$code);
		$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://filesflash.com/', $cookie, $post, $lfile, $lname, 'uploadfile');	
?>
				<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			is_page($upfiles);
			preg_match('#"_top">([^><]+)<#', $upfiles, $dll);
			 if (!empty($dll[1]))
			$download_link =  $dll[1];
			else
              html_error ('Didn\'t find downloadlink!');
			preg_match('#del/([^/"]+)"#', $upfiles, $del);
			if (!empty($del[1]))
			$delete_link =  'http://filesflash.com/del/'.$del[1];
			else
              html_error ('Didn\'t find deletelink!');
			}
?>
<?php
	function id(){
				$ext = "0123456789";
				$hex = "0123456789abcdef";
				$let = str_split($hex,1);
				$contbase = strlen($hex);
				$comple = '';
						for($i=0; $i < 46; $i++){
						$rand .= $ext{mt_rand() % strlen($ext)};
						}
				$base = $rand;
				for ($i = 0; $i < 32; $i++) {
					$comple = $let[bcmod($base,$contbase)].$comple;
					$base = bcdiv($base,$contbase,0);
				}
			return $comple;
	//function by simplesdescarga 02/02/2012
	}
	//upload plugin by simplesdescarga 02/02/2012 at 19:19
?>