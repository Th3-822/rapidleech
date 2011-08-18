<?php
####### Account Info. ###########
$fs_login = "";
$fs_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($fs_login & $fs_pass){
	$_REQUEST['bin_login'] = $fs_login;
	$_REQUEST['bin_pass'] = $fs_pass;
	$_REQUEST['action'] = "FORM";
	echo "\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<form method="post">
<table border="1" style="width:270px;" cellspacing="0" align="center">
<input type="hidden" name="action" value="FORM"><input type="hidden" value="uploaded value<?php $_REQUEST[uploaded] ?>">
<input type="hidden" name="filename" value="<?php echo base64_encode($_REQUEST[filename]); ?>">
<tr><td nowrap>&nbsp;Login<td>&nbsp;<input name="bin_login" value="" style="width:195px;">&nbsp;</tr>
<tr><td nowrap>&nbsp;Password<td>&nbsp;<input name="bin_pass" value="" style="width:195px;">&nbsp;</tr>
<tr><td colspan="2" align="center"><a href="http://fileserve.com/signup.php" target="_blank">Registration link</a></tr>
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
<div id=login width=100% align=center></div> 
<?php
			$Url=parse_url('http://fileserve.com/login.php');
			if ($_REQUEST['action'] == "FORM")
			{
				$post["loginUserName"]=$_REQUEST['bin_login'];
				$post["loginUserPassword"]=$_REQUEST['bin_pass'];
				$post["autoLogin"]="on";
				$post["recaptcha_response_field"]="";
				$post["recaptcha_challenge_field"]="";
				$post["recaptcha_shortencode_field"]="on";
				$post["loginFormSubmit"]="Login";
			
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://fileserve.com/login.php", 0, $post, 0, $_GET["proxy"], $pauth);
			if (!preg_match('#Set-Cookie: cookie=([0-9a-zA-Z%]+);#', $page))
				html_error ('Not logged in. Check your login details!');
			$cookies = GetCookies($page);
			}	
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$Url=parse_url('http://fileserve.com/');
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://fileserve.com/", $cookies, 0, 0, $_GET["proxy"], $pauth);
			
			preg_match('#action="(http:\/\/upload\.fileserve\.com\/upload\/.+)"#',$page,$uploadForm);
			$first = str_replace('-','',mt_rand(1111111111111, 9999999999999));
			$second = str_replace('-','',mt_rand(1111111111111, 9999999999999));
			$url = '?callback=jsonp'.$first.'&_='.$second;
			$upload_url = $uploadForm[1] . $url;

			$Url=parse_url($upload_url);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://fileserve.com/", $cookies, 0, 0, $_GET["proxy"], $pauth);
			preg_match("#sessionId:'(.+)'}#",$page,$sessionId);
			$url = $uploadForm[1] . $sessionId[1] . '/';
			
			$url=parse_url($url);
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), 0, $cookies, 0, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			preg_match('#shortenCode":"(.+)"}#',$upfiles,$ddl);
			preg_match('#deleteCode":"(.+)","sessionId#',$upfiles,$del);
			if (!empty($ddl[1]))
			$download_link = 'http://www.fileserve.com/file/' . $ddl[1] . '/' . $lname;
			else
			html_error ('Didn\'t find downloadlink!');
			if (!empty($del[1]))
			$delete_link= 'http://www.fileserve.com/file/' . $ddl[1] . '/delete/' . $del[1];
			else
			html_error ('Didn\'t find deletelink!');
}
/**
written by defport 11/08/2011
**/   
?>