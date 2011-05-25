<?php
####### Account Info. ###########
$share_megaplus_vn_login = "xxxxxx"; //Set your user id (login)
$share_megaplus_vn_pass = "xxxxxx"; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($share_megaplus_vn_login && $share_megaplus_vn_pass){
	$_REQUEST['my_login'] = $share_megaplus_vn_login;
	$_REQUEST['my_pass'] = $share_megaplus_vn_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;User*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["share.megaplus.vn"];exit; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to Share.megaplus.vn</div>
<?php 
			$page = geturl("share.megaplus.vn", 80, "/", "http://share.megaplus.vn/index.php", 0, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
                        $cookie = GetCookies($page);
                        $page = geturl("share.megaplus.vn", 80, "/megavnnplus.php?service=login", 'http://share.megaplus.vn/', $cookie, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
                        preg_match('%ocation: (.+)\r\n%', $page, $redir);
		        $geturl = rtrim($redir["1"]);
                        $geturl = "$geturl&locale=en";
                        $page = ssl_curl($geturl, 0, $cookie);
	                $cookies = GetCookies($page);
                        $post_path = cut_str($page,'<form id="fm1" class="fm-v clearfix" action="','" method="post">');
                        $post_url = "https://id.megaplus.vn";
                        $post_url = "$post_url$post_path";     
                        $post = array();
			$post['username'] = trim($_REQUEST['my_login']);
			$post['password'] = trim($_REQUEST['my_pass']);			
			$post['lt'] = cut_str($page,'<input type="hidden" name="lt" value="','" />');
			$post['_eventId'] = 'submit';
			$post['submit'] = 'LOGIN';
                        $page = ssl_curl($post_url, $post, $cookies);
                        $cookies = GetCookies($page);
                        preg_match('%ocation: (.+)\r\n%', $page, $redir);
                        $geturl = rtrim($redir["1"]);
                        if(!$geturl){
                        is_notpresent($page, 'ticket=', 'Error logging in - are your logins correct!');
                        }
                        $Url = parse_url($geturl);
                        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
                        $cookie = GetCookies($page);
                        $cookies = substr($cookie,37) ;
                        $Url = parse_url("http://share.megaplus.vn/index.php");
                        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://share.megaplus.vn/", $cookies, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
                        $PHPSESSID =  str_replace('PHPSESSID=','',$cookies);       
	?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive test</div>
<script>document.getElementById('info').style.display='none';</script>
<?php 
			$url = parse_url('http://share.megaplus.vn/upload.php');
                        $upagent = 'Shockwave Flash';
			$fpost = array();
			$fpost['Filename'] = $lname;
                        $fpost['description'] = 'LeechViet Premium Link';
			$fpost['secCode'] = 'abc';
			$fpost['public'] = 'true';
                        $fpost['PHPSESSID'] = $PHPSESSID;
			$fpost['Upload'] = 'Submit Query';
		        $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://share.megaplus.vn/", $cookies, $fpost, $lfile, $lname, "Filedata",$upagent);
                        
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			if(!preg_match("%http://share.megaplus.vn/dl.php/(.*)%", $upfiles, $preg)) html_error("Error get direct link");
			$download_link=$preg[0];
			
	}
function ssl_curl($link, $post = 0, $cookie = 0, $refer = 0)
{
	$mm = !empty($post) ? 1 : 0;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U;Windows NT 5.1; de;rv:1.8.0.1)\r\nGecko/20060111\r\nFirefox/1.5.0.1');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if ($mm == 1)
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, formpostdata($post));
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_REFERER, $refer);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie) ;
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	$contents .= curl_exec($ch);
	curl_close($ch);
	return $contents;
}
/*************************\  
written by VinhNhaTrang 31/10/2010
\*************************/
?>