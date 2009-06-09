<?php
$not_done=true;
$continue_up=false;
if ($_REQUEST['action'] == "FORM")
	{
		$continue_up=true;
	}
		else
	{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM'><input type=hidden value=uploaded value'<?php $_REQUEST['uploaded']?>'>
<input type=hidden name=filename value='<?php echo base64_encode($_REQUEST['filename']); ?>'>
<tr><td nowrap>&nbsp;Login<td>&nbsp;<input name=bin_login value='' style="width:160px;">&nbsp;</tr>
<tr><td nowrap>&nbsp;Password<td>&nbsp;<input type=password name=bin_pass value='' style="width:160px;">&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload'></tr>
</table>
</form>
<?php
	}

if ($continue_up)
	{
		$not_done=false;
		$login = trim($_REQUEST['bin_login']);
		$pass = trim($_REQUEST['bin_pass']);
            
//			if (empty($login)) html_error("Enter uafile.net login!");
//            if (empty($pass)) html_error("Enter uafile.net password!");
			
?> 
<table width=600 align=center> 
</td></tr> 
<tr><td align=center> 
<div id=login width=100% align=center>Login to site</div> 
<?php 
			$ref='http://files.ge/';
			$Url=parse_url($ref);
			if ($login && $pass){
            $post["act"]='login';
            $post["user"]=$login;
            $post["pass"]=$pass;
            $post["login"]="Login";
            $post["folder_autologin"]=1;
//            $post["xxx"]="";
}
			
			@$page = geturl($Url["host"],80,"/login.php",$ref,0,$post);
			
//			is_notpresent($page,"location: http://uafile.net/members.php","Error login to uafile.net. Error password/login");
			
			$cookie = "PHPSESSID=".cut_str($page,'Cookie: PHPSESSID=',";")."; ";
			$cookie .= "yab_passhash=".cut_str($page,'Cookie: yab_passhash=',";")."; ";
			$cookie .= "yab_sess_id=".cut_str($page,'Cookie: yab_sess_id=',";")."; ";
			$cookie .= "yab_last_click=".cut_str($page,'Cookie: yab_last_click=',";")."; ";
			$cookie .= "yab_uid=".cut_str(cut_str($page,'yab_uid',true),'Cookie: yab_uid=',';');
			$cookie .= "; yab_logined=1; yab_mylang=en";
			
?>
<script>document.getElementById('login').style.display='none';</script> 
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
				
            $page = geturl($Url["host"],80,"/",$ref,$cookie);
 
            $temp = cut_str($page,"uploadform",">"); 
            $action_url = cut_str($temp,'action="','"'); 
            $url = parse_url($action_url); 
            unset($post);
            $post["sessionid"]=cut_str($temp,"sid=","&"); 
            $post["UploadSession"] = $post["sessionid"]; 
            $post["AccessKey"] = cut_str($page,'AccessKey" value="','"'); 
            $post["maxfilesize"]= cut_str($temp,'maxfilesize" value="','"'); 
            $post["phpuploadscript"]=cut_str($page,'phpuploadscript" value="','"'); 
            $post["returnurl"]=cut_str($page,'returnurl" value="','"'); 
            $post["uploadmode"]=1; 
             
?> 
<script>document.getElementById('info').style.display='none';</script> 
<?php 
            $upfiles=upfile($url['host'],defport($url),$url['path']."?".$url["query"],$ref, 0, $post, $lfile, $lname, "uploadfile_0"); 

?> 
<script>document.getElementById('progressblock').style.display='none';</script> 
<div id=info2 width=100% align=center>Get links</div> 
<?php     
            is_page($upfiles); 

            $temp2 = trim(cut_str($upfiles,"Location: ","\n")); 
            $locat = parse_url($temp2); 
            sleep(5); 
            flush(); 
            $page2=geturl($locat["host"],80,$locat['path']."?".$locat["query"],$ref,0,0,0,0,0); 
            $path="/emaillinks.php?UploadSession=".$post["UploadSession"]."&AccessKey=".$post["AccessKey"]."&uploadmode=1&submitnums=0&fromemail=&toemail="; 
         
            $page3=geturl($Url["host"],80,$path,$ref,0,0,0,0,0); 
            is_page($page3); 
            $download_link = trim(cut_str($page3,'downloadurl">','<')); 
            $delete_link = trim(cut_str($page3,'filedel">','<')); 
?> 
<script>document.getElementById('info2').style.display='none';</script>
<?php }

// sert 20.08.2008
?>