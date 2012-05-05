<?php
// Default Account Info =================
$Filesin_user = ''; //  Set you username
$Filesin_pass = ''; //  Set your password
//=======================================

			$not_done = true;
			$continue_up = false;
			if ($Filesin_user & $Filesin_pass) {
				$_REQUEST['fi_user'] = $Filesin_user;
				$_REQUEST['fi_pass'] = $Filesin_pass;
				$_REQUEST['action'] = "FORM";
				echo "<b><center>Automatic Login Filesin.com</center></b>\n";
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
            <tr><td nowrap>&nbsp;User*<td>&nbsp;<input type='text' name='fi_user' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='fi_pass' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td colspan=2 align=center><input type='submit' value='Upload' /></tr>
            <tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["filesin.com_premium"]; ?></b></small></tr>
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
            <div id='info' width='100%' align='center'>Login to Filesin.com</div>
        <?php
        if (!empty($_REQUEST['fi_user']) && !empty($_REQUEST['fi_pass'])){
		$post["username"] = trim($_REQUEST['fi_user']);
        $post["password"] = trim($_REQUEST['fi_pass']);
		$Url = parse_url("http://filesin.com/login.php");
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "www.filesin.com/login.php", 0, $post, 0, $_GET["proxy"], $pauth);
		is_page($page);
		is_present($page, "Please enter Username and Password", "Please enter Username and Password");
		is_present($page, "Wrong Username or Password", "Wrong Username or Password");
		$cookie = GetCookies($page);
		}else{
		html_error ('Error, user and/or password is empty, please go back and try again!');
	}
        ?> 
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
		$url = parse_url('http://filesin.com/');
		$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://www.filesin.com/", $cookie, 0, 0, $_GET["proxy"], $pauth);
		if(!preg_match('#name="UPLOAD_IDENTIFIER"[\r|\n|\s]+value="([^"]+)"#', $page, $id)){
				html_error('Cannot get id.', 0);
		}
		$url = parse_url('http://filesin.com/');
        $post["UPLOAD_IDENTIFIER"] = $id[1];
        $post["terms"] = 1;
		$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://www.filesin.com/", $cookie, $post, $lfile, $lname, "file1");
?>

<script>document.getElementById('progressblock').style.display='none';</script>
<?php
is_page($upfiles);
				if(!preg_match("#ocation: (.*)#", $upfiles, $link)){
					html_error('Error in upload.', 0);
				}
				$dl = explode('=', $link[1]);
				if(!empty($dl)){
					$download_link = 'http://www.filesin.com/'.$dl[1].'/download.html';
				}else{
					html_error ("Didn't find download link!");
				}
					
			}
				
/**
written by SD-88 07.04.2012
**/   
?>