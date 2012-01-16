<?php
####### Account Info. ###########
$put_login = "";
$put_pass = "";
##############################

				$not_done=true;
				$continue_up=false;
				if ($put_login & $put_pass){
					$_REQUEST['put_login'] = $put_login;
					$_REQUEST['put_pass'] = $put_pass;
					$_REQUEST['action'] = "FORM";
					echo "<b><center>Automatic Login</center></b>\n";
				}
				if ($_REQUEST['action'] === "FORM")
					$continue_up=true;
				else{
?>
						<script>document.getElementById('info').style.display='none';</script>
            <div id='info' width='100%' align='center' style="font-weight:bold; font-size:16px">LOGIN</div>
        <form method="post">
        <input type='hidden' name='action' value='FORM' />
        <table border="0" style="width:270px;" cellspacing="0" align="center">
        <tr><td nowrap>&nbsp;Login<td>&nbsp;<input type="text" name="put_login" style="width:195px;">&nbsp;</tr>
        <tr><td nowrap>&nbsp;Password<td>&nbsp;<input type="password" name="put_pass" style="width:195px;">&nbsp;</tr>
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
		<script>document.getElementById('info').style.display='none';</script>
		<div id='info' width='100%' align='center'>Login to Putlocker</div>
<?php
			$Url=parse_url('http://www.putlocker.com/authenticate.php?login');
			if ($_REQUEST['action'] === "FORM" && $_REQUEST['put_login'] && $_REQUEST['put_pass']){
			$post["user"]=$_REQUEST['put_login'];
			$post["pass"]=$_REQUEST['put_pass'];
			$post["remember"]="1";
			$post["login_submit"]="Login";
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "www.putlocker.com", 0, $post, 0, $_GET["proxy"], $pauth);
			$cookies = GetCookies($page);
			}else{
				html_error('Is empty User or Password');
			}
			preg_match("#auth=([0-9a-zA-Z%]+)#",$cookies,$auth);
			if(empty($auth[1])){
				html_error('No such username or wrong password');
			}else{
?>
					<script>document.getElementById('info').style.display='none';</script>
					<div id=info width=100% align=center>Connecting Upload</div> 
<?php
	$page = geturl("www.putlocker.com", 80, "/upload_form.php", 0, $cookies);
	is_page($page);
	$cookies .= "; ".GetCookies($page);
	preg_match("#'auth_hash':'([0-9a-zA-Z%=]+)'#",$page,$GetID);
	preg_match("#'session':[\r|\n|\s]+'([0-9a-zA-Z%]+)'#",$page,$session);
	preg_match("#'script'[\r|\n|\s]+:+[\r|\n|\s]+'([0-9a-zA-Z./:]+)'#",$page,$urlup);
	$post['upload_folder'] = '';
	$post['auth_hash'] = $GetID[1];
	$post['session']=$session[1];
	$url = parse_url($urlup[1]);
	$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), 'http://www.putlocker.com/upload_form.php', $cookies, $post, $lfile, $lname, 'Filedata');
	?>
    			<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>
	<?php 
	preg_match("#upload_hash=([0-9a-zA-Z%]+)#",$cookies,$upID);
	$Url = parse_url('http://www.putlocker.com/upload_form.php?done='.$upID[1]);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.putlocker.com/upload_form.php",$cookies, 0, 0, $_GET["proxy"], $pauth);
	$Url = parse_url('http://www.putlocker.com/cp.php?uploaded='.$upID[1]);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 'http://www.putlocker.com/cp.php?uploaded='.$upID[1], $cookies, 0, 0, $_GET["proxy"], $pauth);
	if(preg_match('#href="/file/([^"]+)"#',$page,$link)){
	$download_link = 'http://www.putlocker.com/file/'.$link[1];
	}else{
		html_error('Nothing has been uploaded.');
	}
			}
}
/**
written by simplesdescarga 12/01/2012
**/   
?>