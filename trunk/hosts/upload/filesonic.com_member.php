<?php

####### Account Info. ###########
$filesonic_login = ""; 					//Set your filesonic.com user
$filesonic_pass = ""; 					//Set your filesonic.com password
##############################

$not_done=true;
$continue_up=false;
if ($filesonic_login && $filesonic_pass){
	$_REQUEST['my_login'] = $filesonic_login;
	$_REQUEST['my_pass'] = $filesonic_pass;
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
<tr><td nowrap>&nbsp;Email*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["filesonic.com"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to FileSonic.com</div>
<?php 
			$page = geturl("www.filesonic.com", 80, "/", 0, 0, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);			
			$cookie = GetCookies($page);

			$postlog = array();
			$postlog['email']  = $_REQUEST['my_login'];
			$postlog['password'] = $_REQUEST['my_pass'];
			$postlog['rememberMe'] = '1';
			
			$Url=parse_url('http://www.filesonic.com/user/login');
			$page = geturl($Url["host"], $url["port"] ? $url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://www.filesonic.com/', $cookie, $postlog, 0, $_GET["proxy"], $pauth);
			is_page($page);		
			$cookie = GetCookies($page);
			
			is_notpresent($cookie, 'nickname=', 'Error logging in - are your logins correct?');
			$page = geturl("www.filesonic.com", 80, "/", 'http://www.filesonic.com/dashboard', $cookie, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);	
	
		
			
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive test</div>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$sess_id = cut_str($cookie,'PHPSESSID=',';');
			$upload_url = cut_str($page,'class="webUpload" action="','" method="post" enctype="multipart/form-data">');
			$upload_id = rand(1300000000000, 2000000000000);
			$upload_id = $upload_id *(-1); 
			$upload_id2 = rand(10000, 99999);
			$post = array();
			$post['filename'] = $lname;
			$post[''] = '';
			$post['uploadFiles'] = '';
			$post['callbackUrl'] = 'http://www.filesonic.com/upload-completed/:linkId/:uploadProgressId/:error';
			$url = parse_url($upload_url.'/?X-Progress-ID=upload_'.$upload_id.'_'.$sess_id.'_'.$upload_id2.' ');
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://www.filesonic.com/', $cookie, $post, $lfile, $lname, "upload");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			$link = cut_str($upfiles,'Location:','\n');
			$link2 = explode('/', $link);
			if(!empty($link))
			{
				if($link2[4] != '0' AND !empty($link2[4]))
				{
					$download_link = 'http://www.filesonic.com/file/'.substr($link2[4], 1).'/'.$lname;
		  		}
		  		else
		  		{
		      			html_error("Unable to upload");
		    		}
			}
			else{html_error("Upload failed");}
	}
	//Create by pirat4
?>