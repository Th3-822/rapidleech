<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
				$refm='http://www.megashares.com/';
				$cookies='';
				if (empty($megashares_login) || empty($megashares_pass))echo"<br>No enter Login/Password<br><br>";
				else{
					$ref1='http://www.megashares.com/myms_login.php';
					$Url=parse_url($ref1);
					$post['mymslogin_name']=$megashares_login;
					$post['mymspassword']=$megashares_pass;
					$post['myms_login']='Login';
					$post['myms_reg_login_name']='';
					$post['mymspassword_reg']='';
					$post['mymspassword_reg_confirm']='';
					$post['myms_reg_email']='';
					$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref1, 0, $post, 0, $_GET["proxy"],$pauth);
					$location = trim(cut_str($page,"Location: ","\n"));
					if (!$location) html_error ('Login error');
					$cookies=GetCookies($page);
					$refm='http://'.$megashares_login.'.megashares.com/index.php?html=1';
				}
				$Url=parse_url($refm);
				$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $refm, $cookies, 0, 0, $_GET["proxy"],$pauth);
				is_page($page);
?>
	<script>document.getElementById('info').style.display='none';</script>
<?php
//				$ref='http://www.megashares.com/';
				$action_url=cut_str($page,"form_upload').action='","'");
				if (!$action_url) html_error ('Error get action url');
				$form=cut_str($page,'<form','</form>');
				$post['msup_id']=cut_str($form,'msup_id" value="','"');
				$post['uploadFileDescription']='';
				$post['uploadFileCategory']='doc';
				$post['passProtectUpload']='';
				$post['emailAddress']='';
				$post['downloadProgressURL']=cut_str($form,'"downloadProgressURL" value="','"');
				$post['checkTOS']='';
				$url=parse_url($action_url);
		
				$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : "") ,$refm, $cookies, $post, $lfile, $lname, "upfile_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php		
				is_page($upfiles);
				$loc=trim(cut_str($upfiles,'parent.location = "','"'));
				$Url=parse_url($loc);
				$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $action_url, $cookies, 0, 0, $_GET["proxy"],$pauth);
				is_page($page);
				$tmp=cut_str($page,'Download Link','</a>');
				$download_link=cut_str($tmp,'href="','"');

// sert 06.07.2008
?>