<table width=600 align=center> 
</td></tr> 
<tr><td align=center> 
<?php
			$page = geturl("www.putlocker.com", 80, "/", 0, 0, 0, 0, $_GET["proxy"], $pauth);
			$cookies = GetCookies($page);
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
/**
written by simplesdescarga 12/01/2012
**/   
?>