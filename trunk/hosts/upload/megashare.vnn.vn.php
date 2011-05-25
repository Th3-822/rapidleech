<?php

####### Account Info. ###########
$megasharevnn_login = "xxxxx"; //Set your megasharevnntory email id (login)
$megasharevnn_pass = "xxxxx"; //Set your megasharevnntory password
##############################

$not_done=true;
$continue_up=false;
if ($megasharevnn_login && $megasharevnn_pass){
	$_REQUEST['my_login'] = $megasharevnn_login;
	$_REQUEST['my_pass'] = $megasharevnn_pass;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["megashare.vnn.vn"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to megashare.vnn.vn</div>
<?php
                        $page = geturl("megashare.vnn.vn", 80, "/", 0,0, 0, 0, $_GET["proxy"], $pauth);
	                is_page($page);
	                $cookie1 = GetCookies($page); 
	                $post = array();
	                $post['act'] = 'login';
	                $post['user'] = trim($_REQUEST['my_login']);
	                $post['login'] = 'Login';
	                $post['pass'] = trim($_REQUEST['my_pass']);
	                $page = geturl("megashare.vnn.vn", 80, "/login.php", 0, $cookie1, $post, 0, $_GET["proxy"], $pauth);
	                is_page($page);
	                $cookie2 = GetCookies($page);
	                is_notpresent($cookie2, 'vdc2_logined=1', 'Error logging in - are your logins correct?');
	                if (!preg_match('%(vdc2_passhash=.+)%', $cookie2, $lcookie)) html_error('Error getting login-cookie');
	                $lcook = cut_str($cookie2,'; vdc2_passhash=','; vdc2_last_click=');
	                $cookie = 'PHPSESSID='.cut_str($cookie1,'PHPSESSID=','; vdc2_logined=0;').'; vdc2_logined=1;'.cut_str($cookie2,'; vdc2_logined=1;',' vdc2_passhash=').cut_str($cookie2,$lcook.';',' vdc2_autologin=deleted').' vdc2_passhash='.$lcook;
	                $page = geturl("megashare.vnn.vn", 80, "/", 0,$cookie, 0, 0, $_GET["proxy"], $pauth);
	                is_page($page);
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 		
                        preg_match('%<form name="uploadform" action="(.*)" method="post" target="uploadframe" enctype="multipart/form-data">%',$page, $match);
                        $upload_form = trim($match[1]);
                        $url = parse_url($upload_form);
?>

<?php 	
                        $sessionid = cut_str($page, '<input type="hidden" name="sessionid" value="','" />');
                        $UploadSession = cut_str($page, '<input type="hidden" name="UploadSession" value="','" />');
                        $phpuploadscript = cut_str($page,'<input type="hidden" name="phpuploadscript" value="','" />');
                        $maxfilesize = cut_str($page,'<input type="hidden" name="maxfilesize" value="','" />');
                        $returnurl = cut_str($page,'<input type="hidden" name="returnurl" value="','" />');
                        $AccessKey = cut_str($page, '<input type="hidden" name="AccessKey" value="', '"');
			$fpost = array();
			$fpost["sessionid"] = $sessionid;
			$fpost["UploadSession"] = $UploadSession;
			$fpost["AccessKey"] = $AccessKey;
			$fpost["maxfilesize"] = $maxfilesize;
			$fpost["phpuploadscript"] = $phpuploadscript;
			$fpost["returnurl"] = $returnurl;
			$fpost["uploadmode"] = '1';
			$fpost["file_descr[0]"] = 'LeechViet';
			$fpost["file_password[0]"] = '';
			$fpost["file_publish[0]"] = '1';		
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://megashare.vnn.vn/", 0, $fpost, $lfile, $lname, "uploadfile_0");
?>

<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			$ref = cut_str($upfiles,'<p>The document has moved <a href="','">here</a>');
			if (!$ref) html_error('Error getting return url');
			$ref = str_replace('&amp;','&',$ref);
			$Url=parse_url($ref);
			$page = geturl($Url["host"], $url["port"] ? $url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://megashare.vnn.vn/", $cookie, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
			$linkmoi = cut_str($page, '<meta HTTP-EQUIV=Refresh CONTENT="1; URL=','">');
			$Url=parse_url($linkmoi);
			$page = geturl($Url["host"], $url["port"] ? $url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://megashare.vnn.vn/", $cookie, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
			$epost = array();
			$epost["UploadSession"] = $UploadSession;
			$epost["AccessKey"] = str_replace("=","%3D",$AccessKey);
			$epost["uploadmode"] = '1';
			$epost["submitnums"] = '0';
			$epost["fromemail"] ='';
			$epost["toemail"] = '';	
			$Url=parse_url("http://megashare.vnn.vn/emaillinks.php");
			$page = geturl($Url["host"], $url["port"] ? $url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://megashare.vnn.vn", $cookie, $epost, 0, $_GET["proxy"], $pauth);
			is_page($page);
			$download_link = cut_str($page, '<A id=downloadhref href="', '" target=_blank>');
                        $delete_link  = cut_str($page, '<A id=filedelhref href="', '" target=_blank>');
	}
//szal14-Jun-09
//Fix 28.10.2010_VinhNhaTrang
?>