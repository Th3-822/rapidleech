<?php
####### Account Info. ###########
$put_cookie = ""; //set value your cookie auth of putlocker.com. Example: auth=Mffggdvgvdgvgh8j67-0jhhMTI5ZjZhNmU5MTgwYTQx place only Mffggdvgvdgvgh8j67-0jhhMTI5ZjZhNmU5MTgwYTQx
##############################

				$not_done=true;
				$continue_up=false;
				if ($put_cookie){
					$_REQUEST['cookie'] = $put_cookie;
					$_REQUEST['action'] = "FORM";
					echo "<b><center>Automatic Login to Putlocker.com</center></b>\n";
				}
				if ($_REQUEST['action'] === "FORM")
					$continue_up=true;
				else{
?>
						<script>document.getElementById('info').style.display='none';</script>
            <div id='info' width='100%' align='center' style="font-weight:bold; font-size:16px">LOGIN</div>
        <form method="post">
        <input type='hidden' name='action' value='FORM' />
        <table border="0" style="width:300px;" cellspacing="0" align="center">
        <tr><td nowrap>&nbsp;Cookie Value<td>&nbsp;<input type="text" name="cookie" style="width:195px;">&nbsp;</tr>
        <tr><td colspan="2" align="center"><input type="submit" value="Upload"></tr>
        <tr><td colspan="2" align="center">Example: Mffggdvgvdgvgh8j67H0jhhMTI5ZjZhNmU5MTgwYTQx</tr>
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
		<div id='info' width='100%' align='center'>Login to Putlocker.com</div>
<?php
			if (!empty($_REQUEST['cookie']) && $_REQUEST['action'] == "FORM"){
            $Url=parse_url('http://www.putlocker.com/profile.php');
			$cookie = 'auth='.$_REQUEST['cookie'];
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.putlocker.com", $cookie, 0, 0, $_GET["proxy"], $pauth);
			if(preg_match('#ocation: (.*)#', $page)){
				html_error('Invalid cookie');
			}
			$cookies = $cookie.'; '.GetCookies($page);
			}else{
				html_error('Is empty User and/or Password');
			}
?>
			
            <script>document.getElementById('info').style.display='none';</script>
					<div id=info width=100% align=center>Connecting Upload</div> 
<?php
	$page = geturl("www.putlocker.com", 80, "/upload_form.php", 0, $cookies);
	is_page($page);
	$cookies .= "; ".GetCookies($page);
	if(!preg_match("#'auth_hash':'([0-9a-zA-Z%=]+)'#",$page,$GetID)){
		html_error('Error in upload [0*01]');
	}
	if(!preg_match("#'session':[\r|\n|\s]+'([0-9a-zA-Z%]+)'#",$page,$session)){
		html_error('Error in upload [0*02]');
	}
	if(!preg_match("#'script'[\r|\n|\s]+:+[\r|\n|\s]+'([0-9a-zA-Z./:]+)'#",$page,$urlup)){
		html_error('Error in upload [0*03]');
	}
	$post['upload_folder'] = '';
	$post['auth_hash'] = $GetID[1];
	$post['session']= $session[1];
	$post['do_convert'] = 1;
	$url = parse_url($urlup[1]);
	$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), 'http://www.putlocker.com/upload_form.php', $cookies, $post, $lfile, $lname, 'Filedata');
	?>
    			<script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>
	<?php 
	is_page($upfiles);
	if(!preg_match("#upload_hash=([0-9a-zA-Z%]+)#",$cookies,$upID)){
		html_error('Cannot get upload hash');
	}
	$Url = parse_url('http://www.putlocker.com/upload_form.php?done='.$upID[1]);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.putlocker.com/upload_form.php",$cookies, 0, 0, $_GET["proxy"], $pauth);
	$Url = parse_url('http://www.putlocker.com/cp.php?uploaded='.$upID[1]);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 'http://www.putlocker.com/cp.php?uploaded='.$upID[1], $cookies, 0, 0, $_GET["proxy"], $pauth);
	if(!preg_match('#http\:\/\/www\.putlocker\.com\/file\/([^"]+)#',$page,$link)){
		html_error('Nothing has been uploaded.');
	}
	$download_link = 'http://www.putlocker.com/file/'.$link[1];
			}
/**
written by SD-88 12.01.2012
Fixed Login by SD-88 07.04.2012
Add support for cookie in login by SD-88 07.04.2012
Add support streaming for video by SD-88 08.04.2012
Fixed error get download link by SD-88 08.042012
**/   
?>