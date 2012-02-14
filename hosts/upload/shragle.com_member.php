<?php
// Default Account Info =================
$shragle_user = ''; //  Set you username
$shragle_pass = ''; //  Set your password
//=======================================

			$not_done = true;
			$continue_up = false;
			if ($shragle_user & $shragle_pass) {
				$_REQUEST['sg_user'] = $shragle_user;
				$_REQUEST['sg_pass'] = $shragle_pass;
				$_REQUEST['action'] = "FORM";
				echo "<b><center>Automatic Login Shragle.com</center></b>\n";
			}
			if ($_REQUEST['action'] == "FORM"){
				$continue_up = true;
			}else {
?>
					<script>document.getElementById('info').style.display='none';</script>
                    <div id='info' width='100%' align='center' style="font-weight:bold; font-size:16px">Login</div> 
    <table border=0 style="width:270px;" cellspacing=0 align=center>
        <form method="post">
        <input type='hidden' name='action' value='FORM' />
            <tr><td nowrap>&nbsp;User*<td>&nbsp;<input type='text' name='sg_user' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='sg_pass' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td colspan=2 align=center><input type='submit' value='Upload' /></tr>
            <tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["shragle.com_member"]; ?></b></small></tr>
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
            <div id='info' width='100%' align='center'>Login to Shragle.com</div>
        <?php
        if (!empty($_REQUEST['sg_user']) && !empty($_REQUEST['sg_pass'])){
        $post["cookie"] = "1";
		$post["username"] = trim($_REQUEST['sg_user']);
        $post["password"] = trim($_REQUEST['sg_pass']);
		$post["submit"] = "Log in";
		$Url = parse_url("http://www.shragle.com/login");
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.shragle.com/login", 0, $post, 0, $_GET["proxy"], $pauth);
		is_page($page);
		is_present($page, "Your login informations are incorrect.", "Your login informations are incorrect.");
		$cookie = GetCookies($page);
		$kie = explode(';', $cookie);
		$cookie = $kie[0].';'.$kie[1].';'.$kie[2].';'.$kie[4];
		if(empty($kie[2]) && empty($kie[4])){
			html_error('Your login informations are incorrect.');
		}
		if(empty($cookie)){
			html_error('Trouble in Login, please try again later');
		}
		}else{
		html_error ('Error, user and/or password is empty, please go back and try again!');
	}
        ?> 
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$url = parse_url('http://www.shragle.com/');
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://www.shragle.com/login", $cookie, 0, 0, $_GET["proxy"], $pauth);
			if(!preg_match('#class="upload"[\r|\n|\s]+action="([^"]+)"#', $page, $up)){
				html_error('Cannot get url action upload.', 0);
			}
			if(!preg_match('#name="MAX_FILE_SIZE"[\r|\n|\s]+value="([^"]+)"#', $page, $max)){
				html_error('Cannot get max file size.', 0);
			}
			if(!preg_match('#name="UPLOAD_IDENTIFIER"[\r|\n|\s]+value="([^"]+)"#', $page, $id)){
				html_error('Cannot get id.', 0);
			}
			if(!preg_match('#name="userID"[\r|\n|\s]+value="([^"]+)"#', $page, $userid)){
				html_error('Cannot get user id.', 0);
			}
			if(!preg_match('#name="password"[\r|\n|\s]+value="([^"]+)"#', $page, $pas)){
				html_error('Cannot get password.', 0);
			}
			if(!preg_match('#name="lang"[\r|\n|\s]+value="([^"]+)"#', $page, $lang)){
				$lang = explode('=', $kie[1]);
				if(empty($lang[1])){
					$lang[1] = 'en_GB';
				}
			}
			$url = parse_url($up[1]);
			$post["MAX_FILE_SIZE"] = $max[1];
			$post["userID"] = $userid[1];
            $post["UPLOAD_IDENTIFIER"] = $id[1];
           	$post["password"] = $pas[1];
			$post['lang'] = trim($lang[1]);
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://www.shragle.com/", $cookie, $post, $lfile, $lname, "file_1");
?>

<script>document.getElementById('progressblock').style.display='none';</script>
<?php
is_page($upfiles);
				if(preg_match("#files\/([^/]+)\/#", $upfiles, $dl)){
					$download_link = 'http://www.shragle.com/files/'.$dl[1];
				}else{
					html_error ("Didn't find download link!");
				}
					
				if(preg_match('#delete\/'.$dl[1].'\/([^/]+)"#', $upfiles, $del)){
					$delete_link = 'http://www.shragle.com/delete/'.$dl[1].'/'.$del[1];
				}else{
					html_error ("Didn't find delete link!");
				}
			}
				
/**
written by simplesdescarga 09/02/2012
**/   
?>